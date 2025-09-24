<?php

namespace App\Services\Coa\Signature;

use App\Models\CoaFile;
use App\Services\Coa\HashingService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class SignatureService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $audit;
    private HashingService $hashing;
    private SignatureProviderInterface $provider;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $audit,
        HashingService $hashing
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->audit = $audit;
        $this->hashing = $hashing;

        $this->provider = $this->resolveProvider();
    }

    private function resolveProvider(): SignatureProviderInterface {
        $enabled = (bool) Config::get('coa.signature.enabled', false);
        $providerKey = Config::get('coa.signature.provider', 'mock');

        if (!$enabled) {
            return new MockSignatureProvider($this->logger, $this->errorManager);
        }

        switch ($providerKey) {
            case 'namirial':
            case 'infocert':
                return new MockSignatureProvider($this->logger, $this->errorManager);
            case 'mock':
            default:
                return new MockSignatureProvider($this->logger, $this->errorManager);
        }
    }

    public function provider(): SignatureProviderInterface {
        return $this->provider;
    }

    public function signAuthor(CoaFile $coaFile, array $meta = []): array {
        $hashAlgo = Config::get('coa.signature.hash_algo', 'sha256');
        $absPath = Storage::path($coaFile->file_path);

        try {
            $result = $this->provider->signPdf($absPath, [
                'role' => 'author',
                'reason' => $meta['reason'] ?? 'Author signature',
                'hash_algo' => $hashAlgo,
                'metadata' => $meta,
            ]);

            if (!($result['success'] ?? false)) {
                $this->errorManager->handle('COA_QES_AUTHOR_SIGN_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $signedAbs = $result['signed_pdf_path'];
            $newRel = $this->storeAsNewVersion($coaFile, $signedAbs, 'pdf_signed_author');

            $signInfo = $result['signature_info'] ?? [];
            $this->logger->info('[SignatureService] Author signed version stored', [
                'file_id' => $coaFile->id,
                'new_path' => $newRel,
            ]);

            return [
                'success' => true,
                'file_path' => $newRel,
                'signature_info' => $signInfo,
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_AUTHOR_SIGN_EXCEPTION', [
                'context' => 'SignatureService::signAuthor',
                'file_id' => $coaFile->id,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function countersignInspector(CoaFile $coaFile, array $meta = []): array {
        $hashAlgo = Config::get('coa.signature.hash_algo', 'sha256');
        $absPath = Storage::path($coaFile->file_path);
        try {
            $result = $this->provider->addCountersignature($absPath, [
                'role' => 'inspector',
                'reason' => $meta['reason'] ?? 'Inspector countersign',
                'hash_algo' => $hashAlgo,
                'metadata' => $meta,
            ]);
            if (!($result['success'] ?? false)) {
                $this->errorManager->handle('COA_QES_INSPECTOR_SIGN_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $signedAbs = $result['signed_pdf_path'];
            $newRel = $this->storeAsNewVersion($coaFile, $signedAbs, 'pdf_signed_inspector');

            return [
                'success' => true,
                'file_path' => $newRel,
                'signature_info' => $result['signature_info'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_INSPECTOR_SIGN_EXCEPTION', [
                'context' => 'SignatureService::countersignInspector',
                'file_id' => $coaFile->id,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function timestamp(CoaFile $coaFile, array $meta = []): array {
        $absPath = Storage::path($coaFile->file_path);
        try {
            $result = $this->provider->addTimestamp($absPath, [
                'policy_oid' => $meta['policy_oid'] ?? null,
                'tsa' => Config::get('coa.tsa.provider', null),
            ]);
            if (!($result['success'] ?? false)) {
                $this->errorManager->handle('COA_QES_TIMESTAMP_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $tsAbs = $result['signed_pdf_path'];
            $newRel = $this->storeAsNewVersion($coaFile, $tsAbs, 'pdf_signed_ts');

            return [
                'success' => true,
                'file_path' => $newRel,
                'timestamp_info' => $result['timestamp_info'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_TIMESTAMP_EXCEPTION', [
                'context' => 'SignatureService::timestamp',
                'file_id' => $coaFile->id,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verify(string $absPdfPath): array {
        try {
            return $this->provider->verifySignatures($absPdfPath);
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_VERIFY_EXCEPTION', [
                'context' => 'SignatureService::verify',
                'pdf' => $absPdfPath,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    private function storeAsNewVersion(CoaFile $origin, string $absPath, string $type): string {
        $content = file_get_contents($absPath);
        $hash = $this->hashing->generateHash($content);
        $filename = pathinfo($origin->file_name, PATHINFO_FILENAME) . '-' . substr($hash, 0, 8) . '.pdf';
        $dir = dirname($origin->file_path);
        $newRel = $dir . '/' . $filename;

        Storage::put($newRel, $content);

        $origin->replicate(['id'])->fill([
            'file_path' => $newRel,
            'file_name' => $filename,
            'file_type' => $type,
            'file_size' => strlen($content),
            'file_hash' => $hash,
        ])->save();

        return $newRel;
    }
}