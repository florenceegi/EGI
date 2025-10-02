<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\{User, Collection, Egi, Coa};
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

/**
 * PA/Enterprise Demo Data Seeder - REVISED APPROACH
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - PA/Enterprise System MVP)
 * @date 2025-10-02
 * @purpose Seed demo data for PA Entity with ADMINISTRATIVE DOCUMENTS (NOT cultural heritage)
 *
 * CRITICAL: Focus on SERVICE demonstration, not specific content
 * APPROACH: Neutral administrative documents to show certification benefits
 *
 * Data Created:
 * - 1 PA Entity User (Comune di Firenze - Ufficio Amministrativo)
 * - 1 Collection (Archivio Atti Amministrativi - Demo Sistema)
 * - 8 EGI (Administrative documents: determine, delibere, autorizzazioni, certificati)
 * - 6 CoA (Certificates issued for some documents)
 * - 1 Inspector user (assigned via collection_user pivot)
 *
 * Benefits Demonstrated:
 * - Timestamp immutability (anti-backdating)
 * - Document authenticity verification (QR code)
 * - Audit trail transparency (blockchain)
 * - Anti-falsification security
 *
 * Notes:
 * - Uses existing 'artwork' type + metadata JSON for MVP
 * - No factory dependencies (direct model creation)
 * - Safe for production (uses find()->delete before recreate)
 */
class PAEnterpriseDemoSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $this->command->info('🏛️ Seeding PA/Enterprise demo data...');

        // 1. Create PA Entity User (Comune di Firenze)
        $this->command->info('Creating PA Entity user...');
        $paUser = $this->createPAUser();

        // 2. Create Inspector User
        $this->command->info('Creating Inspector user...');
        $inspector = $this->createInspectorUser();

        // 3. Create Collection
        $this->command->info('Creating administrative documents collection...');
        $collection = $this->createCollection($paUser);

        // 4. Assign Inspector to Collection
        $this->command->info('Assigning inspector to collection...');
        $this->assignInspector($collection, $inspector, $paUser);

        // 5. Create Administrative Documents EGI
        $this->command->info('Creating administrative document items...');
        $egis = $this->createAdministrativeDocuments($collection, $paUser);

        // 6. Create CoA for some documents
        $this->command->info('Creating CoA certificates...');
        $this->createCoA($egis, $paUser, $inspector);

        $this->command->info('✅ PA/Enterprise demo data seeded successfully!');
        $this->command->info('   Focus: ADMINISTRATIVE DOCUMENTS (neutral, service-oriented)');
        $this->command->info("   PA User: {$paUser->email} / password");
        $this->command->info("   Inspector: {$inspector->email} / password");
    }

    /**
     * Create PA Entity user (Comune di Firenze)
     */
    protected function createPAUser(): User {
        // Find or create
        $user = User::where('email', 'pa.firenze@comune.fi.it')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Comune di Firenze',
                'email' => 'pa.firenze@comune.fi.it',
                'password' => Hash::make('password'),
                'usertype' => 'pa_entity',
                'email_verified_at' => now(),
            ]);

            // Assign pa_entity role
            $paRole = Role::findByName('pa_entity');
            $user->assignRole($paRole);
        }

        return $user;
    }

    /**
     * Create Inspector user
     */
    protected function createInspectorUser(): User {
        // Find or create
        $user = User::where('email', 'inspector.demo@florenceegi.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Dr. Marco Bianchi',
                'email' => 'inspector.demo@florenceegi.com',
                'password' => Hash::make('password'),
                'usertype' => 'inspector',
                'email_verified_at' => now(),
            ]);

            // Assign inspector role
            $inspectorRole = Role::findByName('inspector');
            $user->assignRole($inspectorRole);
        }

        return $user;
    }

    /**
     * Create administrative documents collection
     */
    protected function createCollection(User $paUser): Collection {
        // Find or create
        $collection = Collection::where('collection_name', 'Archivio Atti Amministrativi - Demo Sistema')->first();

        if ($collection) {
            return $collection;
        }

        return Collection::create([
            'creator_id' => $paUser->id,
            'owner_id' => $paUser->id,
            'collection_name' => 'Archivio Atti Amministrativi - Demo Sistema',
            'description' => 'Collezione dimostrativa del sistema di certificazione digitale FlorenceEGI. Include esempi di atti amministrativi (determine, delibere, autorizzazioni, certificati) per mostrare i benefici del servizio: timestamp immutabile, verifica QR code, audit trail blockchain, anti-falsificazione.',
            'type' => 'artwork', // MVP: usa tipo esistente
            'is_default' => false,
            'status' => 'published',
            'is_published' => true,
            'featured_in_guest' => false,
            'metadata' => [
                'pa_entity_code' => 'C_H501',
                'institution_name' => 'Comune di Firenze',
                'department' => 'Ufficio Cultura e Patrimonio',
                'contact_email' => 'cultura@comune.fi.it',
                'contact_phone' => '+39 055 2768224',
                'heritage_type' => 'monumentale',
                'classification' => 'bene_culturale_mobile_immobile',
                'unesco_status' => false,
                'public_access' => true,
                'notes' => 'Patrimonio comunale certificato con CoA blockchain per tracciabilità e autenticità',
            ],
        ]);
    }

    /**
     * Assign inspector to collection via pivot
     */
    protected function assignInspector(Collection $collection, User $inspector, User $assignedBy): void {
        // Clean existing assignment
        DB::table('collection_user')
            ->where('collection_id', $collection->id)
            ->where('user_id', $inspector->id)
            ->delete();

        // Attach inspector
        $collection->users()->attach($inspector->id, [
            'role' => 'inspector',
            'is_owner' => false,
            'status' => 'active',
            'metadata' => json_encode([
                'assigned_by' => $assignedBy->id,
                'assignment_date' => now()->toDateTimeString(),
                'specialization' => 'certificazione_documenti_amministrativi',
                'compensation' => 500.00,
                'notes' => 'Specializzato in verifica autenticità atti amministrativi e compliance normativa',
            ]),
            'joined_at' => now(),
        ]);
    }

    /**
     * Create administrative documents EGI items
     */
    protected function createAdministrativeDocuments(Collection $collection, User $owner): array {
        $adminDocuments = [
            [
                'title' => 'Determina Dirigenziale n. 2024/DEMO-001',
                'description' => 'Determina dirigenziale tipo per affidamento servizio di manutenzione edifici pubblici (esempio dimostrativo). Beneficio: timestamp blockchain immutabile, anti-backdating, audit trail completo. Verifica autenticità tramite QR code.',
                'creation_date' => '2024-01-15',
            ],
            [
                'title' => 'Delibera Giunta n. 2024/DEMO-045',
                'description' => 'Delibera di Giunta tipo per approvazione programma attività culturali annuale (esempio dimostrativo). Beneficio: prova data certa incontestabile, trasparenza amministrativa, verifica istantanea validità.',
                'creation_date' => '2024-02-20',
            ],
            [
                'title' => 'Autorizzazione Evento Culturale n. 2024/DEMO-078',
                'description' => 'Autorizzazione tipo per manifestazione pubblica culturale (esempio dimostrativo). Beneficio: QR code verificabile da organizzatori e cittadini, anti-falsificazione, controllo forze ordine semplificato.',
                'creation_date' => '2024-03-10',
            ],
            [
                'title' => 'Certificato di Servizio n. 2024/DEMO-156',
                'description' => 'Certificato di servizio tipo rilasciato a dipendente pubblico (esempio dimostrativo). Beneficio: dipendente può verificare autenticità, ente può confermare validità, riduzione richieste duplicati.',
                'creation_date' => '2024-04-05',
            ],
            [
                'title' => 'Convenzione Ente Esterno n. 2024/DEMO-023',
                'description' => 'Convenzione tipo per collaborazione istituzionale con ente esterno (esempio dimostrativo). Beneficio: entrambe le parti hanno prova data certa, documento immutabile, riduzione contenzioso.',
                'creation_date' => '2024-05-12',
            ],
            [
                'title' => 'Patrocinio Regionale n. 2024/DEMO-089',
                'description' => 'Atto di concessione patrocinio a iniziativa culturale (esempio dimostrativo). Beneficio: beneficiario mostra QR per verifica immediata, impossibile uso improprio logo istituzionale, trasparenza.',
                'creation_date' => '2024-06-18',
            ],
            [
                'title' => 'Rendiconto Progetto PNRR n. 2024/DEMO-012',
                'description' => 'Documento rendicontazione spese fondi europei PNRR (esempio dimostrativo). Beneficio: audit trail blockchain per compliance EU, trasparenza totale, certificazione spesa incontestabile.',
                'creation_date' => '2024-07-22',
            ],
            [
                'title' => 'Contratto Prestazione Professionale n. 2024/DEMO-034',
                'description' => 'Contratto tipo per incarico professionale esterno (esempio dimostrativo). Beneficio: data certa firma contrattuale, prova vincolante per entrambe le parti, riduzione contestazioni.',
                'creation_date' => '2024-08-30',
            ],
        ];

        $egis = [];
        foreach ($adminDocuments as $index => $item) {
            // Find or create
            $egi = Egi::where('title', $item['title'])
                ->where('collection_id', $collection->id)
                ->first();

            if ($egi) {
                $egis[] = $egi;
                continue;
            }

            $egi = Egi::create([
                'collection_id' => $collection->id,
                'title' => $item['title'],
                'description' => $item['description'],
                'creation_date' => $item['creation_date'],
                'status' => 'published',
                'is_published' => true,
                'user_id' => $owner->id, // Creator
                'owner_id' => $owner->id, // Owner
                'position' => $index + 1, // Position in collection
            ]);

            $egis[] = $egi;
        }

        return $egis;
    }

    /**
     * Create CoA for some EGI
     */
    protected function createCoA(array $egis, User $issuer, User $inspector): void {
        // Issue CoA for first 6 EGI
        $egiWithCoA = array_slice($egis, 0, 6);

        foreach ($egiWithCoA as $index => $egi) {
            // Find or create
            $serial = 'COA-FI-' . now()->format('Y') . '-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT);

            $existingCoa = Coa::where('egi_id', $egi->id)->orWhere('serial', $serial)->first();

            if ($existingCoa) {
                continue;
            }

            Coa::create([
                'egi_id' => $egi->id,
                'serial' => $serial,
                'status' => $index < 4 ? 'valid' : 'valid', // All valid for demo
                'issuer_type' => 'platform', // author|archive|platform
                'issuer_name' => 'Comune di Firenze',
                'issuer_location' => 'Firenze, Italia',
                'issued_at' => now()->subDays(10 + $index * 5),
                'verification_hash' => hash('sha256', $serial . $egi->id . now()->timestamp),
                'metadata' => [
                    'issuer_user_id' => $issuer->id,
                    'inspector_id' => $inspector->id,
                    'inspector_name' => 'Dr. Marco Bianchi',
                    'heritage_classification' => 'bene_culturale',
                    'unesco_protected' => false,
                    'public_domain' => true,
                    'blockchain_hash' => hash('sha256', $serial . microtime()),
                ],
            ]);
        }
    }
}