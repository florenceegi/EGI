<?php

namespace App\Services\Coa\Signature;

use App\Models\CoaFile;
use App\Services\Coa\HashingService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class SignatureService
{
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

    private function resolveProvider(): SignatureProviderInterface
    {
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

    public function provider(): SignatureProviderInterface
    {
        return $this->provider;
    }

    public function signAuthor(CoaFile $coaFile, array $meta = []): array
    {
        $hashAlgo = Config::get('coa.signature.hash_algo', 'sha256');
        [$absPath, $isTempSource] = $this->resolveLocalPath($coaFile->path);

        try {
            $result = $this->provider->signPdf($absPath, [
                'role' => 'creator',
                'reason' => $meta['reason'] ?? 'Author signature',
                'hash_algo' => $hashAlgo,
                'metadata' => $meta,
            ]);

            if (!($result['success'] ?? false)) {
                $this->cleanupTemporaryPaths($absPath, $isTempSource, $result['signed_pdf_path'] ?? null);
                $this->errorManager->handle('COA_QES_AUTHOR_SIGN_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $signedAbs = $result['signed_pdf_path'];
            $newRecord = $this->storeAsNewVersion($coaFile, $signedAbs, 'pdf_signed_author');

            $signInfo = $result['signature_info'] ?? [];
            $this->logger->info('[SignatureService] Author signed version stored', [
                'file_id' => $coaFile->id,
                'new_path' => $newRecord->path,
            ]);

            $this->cleanupTemporaryPaths($absPath, $isTempSource, $signedAbs);

            return [
                'success' => true,
                'file_path' => $newRecord->path,
                'file_id' => $newRecord->id,
                'file' => $newRecord,
                'signature_info' => $signInfo,
            ];
        } catch (\Throwable $e) {
            $this->cleanupTemporaryPaths($absPath, $isTempSource);
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

    public function countersignInspector(CoaFile $coaFile, array $meta = []): array
    {
        $hashAlgo = Config::get('coa.signature.hash_algo', 'sha256');
        [$absPath, $isTempSource] = $this->resolveLocalPath($coaFile->path);
        try {
            $result = $this->provider->addCountersignature($absPath, [
                'role' => 'inspector',
                'reason' => $meta['reason'] ?? 'Inspector countersign',
                'hash_algo' => $hashAlgo,
                'metadata' => $meta,
            ]);
            if (!($result['success'] ?? false)) {
                $this->cleanupTemporaryPaths($absPath, $isTempSource, $result['signed_pdf_path'] ?? null);
                $this->errorManager->handle('COA_QES_INSPECTOR_SIGN_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $signedAbs = $result['signed_pdf_path'];
            $newRecord = $this->storeAsNewVersion($coaFile, $signedAbs, 'pdf_signed_inspector');

            $this->cleanupTemporaryPaths($absPath, $isTempSource, $signedAbs);

            return [
                'success' => true,
                'file_path' => $newRecord->path,
                'file_id' => $newRecord->id,
                'file' => $newRecord,
                'signature_info' => $result['signature_info'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->cleanupTemporaryPaths($absPath, $isTempSource);
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

    public function timestamp(CoaFile $coaFile, array $meta = []): array
    {
        [$absPath, $isTempSource] = $this->resolveLocalPath($coaFile->path);
        try {
            $result = $this->provider->addTimestamp($absPath, [
                'policy_oid' => $meta['policy_oid'] ?? null,
                'tsa' => Config::get('coa.signature.tsa', []),
            ]);
            if (!($result['success'] ?? false)) {
                $this->cleanupTemporaryPaths($absPath, $isTempSource, $result['signed_pdf_path'] ?? null);
                $this->errorManager->handle('COA_QES_TIMESTAMP_FAILED', [
                    'file_id' => $coaFile->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);
                return $result;
            }

            $tsAbs = $result['signed_pdf_path'];
            $newRecord = $this->storeAsNewVersion($coaFile, $tsAbs, 'pdf_signed_ts');

            $this->cleanupTemporaryPaths($absPath, $isTempSource, $tsAbs);

            return [
                'success' => true,
                'file_path' => $newRecord->path,
                'file_id' => $newRecord->id,
                'file' => $newRecord,
                'timestamp_info' => $result['timestamp_info'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->cleanupTemporaryPaths($absPath, $isTempSource);
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

    public function verify(string $absPdfPath): array
    {
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

    private function storeAsNewVersion(CoaFile $origin, string $absPath, string $kind): CoaFile
    {
        $content = file_get_contents($absPath);
        $hash = $this->hashing->generateHash($content);
        $dir = dirname($origin->path);
        $base = pathinfo($origin->path, PATHINFO_FILENAME);
        // Truncate base filename to avoid exceeding DB/storage limits
        $maxBaseLen = 80;
        if (mb_strlen($base) > $maxBaseLen) {
            $base = mb_substr($base, 0, $maxBaseLen);
        }
        $filename = $base . '-' . substr($hash, 0, 8) . '.pdf';
        $newRel = $dir . '/' . $filename;

        Storage::put($newRel, $content);

        $new = CoaFile::create([
            'coa_id' => $origin->coa_id,
            'kind' => $this->normalizeKind($origin->kind, $kind),
            'path' => $newRel,
            'sha256' => $hash,
            'bytes' => strlen($content),
            'created_at' => now(),
        ]);

        return $new;
    }

    private function normalizeKind(?string $originKind, string $desiredKind): string
    {
        $allowed = [
            'pdf',
            'scan_signed',
            'image_front',
            'image_back',
            'signature_detail',
            'core_pdf',
            'bundle_pdf',
            'annex_pack',
        ];

        if (in_array($desiredKind, $allowed, true)) {
            return $desiredKind;
        }

        if ($originKind && in_array($originKind, $allowed, true)) {
            return $originKind;
        }

        return 'pdf';
    }

    /**
     * Resolve local filesystem path for stored file (downloading when needed).
     *
     * @return array{0:string,1:bool} [absolutePath, isTemporaryCopy]
     */
    private function resolveLocalPath(string $storagePath): array
    {
        $disk = config('filesystems.default', 'local');
        $storage = Storage::disk($disk);

        try {
            $absolutePath = $storage->path($storagePath);
            return [$absolutePath, false];
        } catch (\Throwable $e) {
            $contents = $storage->get($storagePath);
            $temporaryPath = $this->createTemporaryCopy($storagePath, $contents);
            return [$temporaryPath, true];
        }
    }

    private function createTemporaryCopy(string $storagePath, string $contents): string
    {
        $extension = pathinfo($storagePath, PATHINFO_EXTENSION) ?: 'tmp';
        $temporaryPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('coa-sign-', true) . '.' . $extension;
        file_put_contents($temporaryPath, $contents);
        return $temporaryPath;
    }

    private function cleanupTemporaryPaths(string $sourcePath, bool $isTempSource, ?string $signedPath = null): void
    {
        if ($isTempSource && is_file($sourcePath)) {
            @unlink($sourcePath);
        }

        if ($signedPath && $this->isTemporaryPath($signedPath) && is_file($signedPath)) {
            @unlink($signedPath);
        }
    }

    private function isTemporaryPath(string $path): bool
    {
        $tempDir = sys_get_temp_dir();
        return Str::startsWith($path, $tempDir);
    }
}
