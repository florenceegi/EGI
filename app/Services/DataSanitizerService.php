<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Egi;
use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Service per sanitizzazione dati prima dell'invio all'AI
 *
 * GDPR DATA ISOLATION:
 * Questo servizio garantisce che SOLO i metadati pubblici vengano
 * estratti dagli atti PA e passati all'AI. Dati sensibili come
 * firme digitali, nominativi, file paths, IP vengono SEMPRE esclusi.
 *
 * AUDIT TRAIL:
 * Ogni operazione di sanitizzazione è loggata per compliance GDPR.
 */
class DataSanitizerService
{
    private UltraLogManager $logger;

    /**
     * Campi pubblici che POSSONO essere inviati all'AI
     */
    private const PUBLIC_FIELDS = [
        'id',
        'pa_protocol_number',
        'pa_date',
        'pa_type',
        'pa_title',
        'pa_amount',
        'pa_anchored',
        'pa_anchored_at',
        'pa_algorand_txid',
        'pa_algorand_network',
        'created_at',
        'updated_at',
    ];

    /**
     * Campi PRIVATI che NON devono MAI essere inviati all'AI
     */
    private const PRIVATE_FIELDS = [
        'file_path',                // Percorso filesystem
        'original_name',            // Potrebbe contenere nominativi
        'digital_signature',        // Firma digitale P7M
        'user_id',                  // ID utente che ha caricato
        'ip_address',               // IP di upload
        'user_agent',               // Browser info
        'session_id',               // Session data
        'metadata',                 // Potrebbe contenere dati non filtrati
    ];

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sanitizza un singolo atto PA
     *
     * @param Egi $act L'atto PA da sanitizzare
     * @return array Dati pubblici sicuri per l'AI
     */
    public function sanitizeAct(Egi $act): array
    {
        $publicData = [
            'id' => $act->id,
            'protocol_number' => $act->pa_protocol_number,
            'date' => $act->pa_protocol_date?->format('Y-m-d'),
            'type' => $act->pa_act_type,
            'title' => $this->sanitizeTitle($act->title),
            'amount' => $act->jsonMetadata['amount'] ?? null,
            'blockchain_anchored' => $act->pa_anchored,
            'blockchain_txid' => $act->jsonMetadata['algorand_txid'] ?? null,
            'created_at' => $act->created_at?->format('Y-m-d H:i:s'),
        ];

        // Log audit
        $this->logger->info('[DataSanitizer] Act sanitized', [
            'act_id' => $act->id,
            'public_fields' => array_keys($publicData),
        ]);

        return $publicData;
    }

    /**
     * Sanitizza una collezione di atti PA
     *
     * @param Collection $acts Collezione di atti
     * @return array Array di atti sanitizzati
     */
    public function sanitizeActsCollection(Collection $acts): array
    {
        $sanitized = $acts->map(function (Egi $act) {
            return $this->sanitizeAct($act);
        })->toArray();

        $this->logger->info('[DataSanitizer] Collection sanitized', [
            'acts_count' => count($sanitized),
        ]);

        return $sanitized;
    }

    /**
     * Sanitizza il titolo dell'atto rimuovendo potenziali nominativi
     *
     * NOTE: Gli atti PA sono pubblici per natura (albo pretorio),
     * ma applichiamo comunque sanitizzazione conservativa.
     */
    private function sanitizeTitle(?string $title): ?string
    {
        if (empty($title)) {
            return null;
        }

        // Pattern per identificare possibili nominativi
        // Es: "Dott. Mario Rossi", "Sig.ra Maria Bianchi", "Dr. Giovanni Verdi"
        $patterns = [
            '/\b(Dott\.|Dr\.|Sig\.|Sig\.ra|Ing\.|Avv\.|Prof\.)\s+[A-Z][a-z]+\s+[A-Z][a-z]+\b/i',
            '/\b[A-Z][a-z]+\s+[A-Z][a-z]+\b/', // Nome Cognome generici
        ];

        $sanitized = $title;
        foreach ($patterns as $pattern) {
            $sanitized = preg_replace($pattern, '[NOME]', $sanitized);
        }

        // Se abbiamo sostituito qualcosa, logga
        if ($sanitized !== $title) {
            $this->logger->info('[DataSanitizer] Title redacted (nominatives removed)', [
                'original_length' => strlen($title),
                'sanitized_length' => strlen($sanitized),
            ]);
        }

        return $sanitized;
    }

    /**
     * Crea un riassunto testuale degli atti per il contesto AI
     *
     * @param Collection $acts Collezione di atti
     * @return string Riassunto testuale
     */
    public function createActsSummary(Collection $acts): string
    {
        if ($acts->isEmpty()) {
            return "Nessun atto PA presente nel sistema.";
        }

        $summary = "ATTI PA PRESENTI NEL SISTEMA:\n\n";

        foreach ($acts as $index => $act) {
            $sanitized = $this->sanitizeAct($act);
            $summary .= sprintf(
                "%d. [%s] %s\n   Protocollo: %s | Data: %s | Importo: %s€\n   Blockchain: %s\n\n",
                $index + 1,
                $sanitized['type'] ?? 'N/D',
                $sanitized['title'] ?? 'Senza titolo',
                $sanitized['protocol_number'] ?? 'N/D',
                $sanitized['date'] ?? 'N/D',
                $sanitized['amount'] ? number_format((float)$sanitized['amount'], 2, ',', '.') : 'N/D',
                $sanitized['blockchain_anchored'] ? '✓ Certificato' : '✗ Non certificato'
            );
        }

        return $summary;
    }

    /**
     * Crea statistiche aggregate sicure per l'AI
     *
     * @param Collection $acts Collezione di atti
     * @return array Statistiche pubbliche
     */
    public function createStatsContext(Collection $acts, ?int $userId = null): array
    {
        $stats = [
            'total_acts_in_sample' => $acts->count(),  // Atti nel campione recuperato
            'anchored_acts' => $acts->where('pa_anchored', true)->count(),
            'by_type' => $acts->groupBy('pa_act_type')->map(fn($group) => $group->count())->toArray(),
            'total_amount' => $acts->sum(fn($act) => $act->jsonMetadata['amount'] ?? 0),
            'date_range' => [
                'first' => $acts->min('pa_protocol_date')?->format('Y-m-d'),
                'last' => $acts->max('pa_protocol_date')?->format('Y-m-d'),
            ],
        ];

        // Aggiungi conteggio totale in database (se userId fornito)
        if ($userId) {
            $stats['total_acts_in_database'] = Egi::where('user_id', $userId)
                ->whereNotNull('pa_protocol_number')
                ->count();
        }

        $this->logger->info('[DataSanitizer] Stats context created', [
            'stats' => $stats,
        ]);

        return $stats;
    }

    /**
     * Valida che un array di dati non contenga campi privati
     *
     * @param array $data Dati da validare
     * @return bool True se i dati sono sicuri
     * @throws \RuntimeException Se sono presenti campi privati
     */
    public function validateSafeData(array $data): bool
    {
        foreach (self::PRIVATE_FIELDS as $privateField) {
            if (array_key_exists($privateField, $data)) {
                $this->logger->error('[DataSanitizer][GDPR VIOLATION] Private field detected in payload', [
                    'field' => $privateField,
                    'data_keys' => array_keys($data),
                ]);

                throw new \RuntimeException(
                    "GDPR VIOLATION: Private field '$privateField' detected in AI data payload"
                );
            }
        }

        return true;
    }
}
