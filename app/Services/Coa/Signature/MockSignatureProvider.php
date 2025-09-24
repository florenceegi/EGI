<?php

namespace App\Services\Coa\Signature;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services\Coa\Signature
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - QES Sandbox)
 * @date 2025-09-24
 * @purpose Provider MOCK sandbox: simula firme PAdES e marca temporale senza valore legale
 */
class MockSignatureProvider implements SignatureProviderInterface {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * {@inheritdoc}
     */
    public function signPdf(string $pdfPath, array $options = []): array {
        $role = $options['role'] ?? 'author';
        $algo = $options['hash_algo'] ?? 'sha256';

        try {
            if (!is_file($pdfPath) || !is_readable($pdfPath)) {
                return [
                    'success' => false,
                    'error' => 'PDF not found or unreadable',
                ];
            }

            $signedPath = $this->buildVersionedPath($pdfPath, ".mock-signed-{$role}-" . time());
            $this->safeCopy($pdfPath, $signedPath);

            $sigInfo = [
                'provider' => 'mock',
                'role' => $role,
                'cert_cn' => 'MOCK SANDBOX CN',
                'cert_serial' => 'MOCK-' . strtoupper(substr(hash($algo, $signedPath), 0, 12)),
                'signature_time' => gmdate('c'),
                'status' => 'valid',
            ];

            $this->logger->info('[MockSignatureProvider] signPdf ok', [
                'src' => $pdfPath,
                'dst' => $signedPath,
                'role' => $role,
            ]);

            return [
                'success' => true,
                'signed_pdf_path' => $signedPath,
                'signature_info' => $sigInfo,
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_MOCK_SIGN_ERROR', [
                'context' => 'MockSignatureProvider::signPdf',
                'pdfPath' => $pdfPath,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addCountersignature(string $signedPdfPath, array $options = []): array {
        $role = $options['role'] ?? 'inspector';
        $algo = $options['hash_algo'] ?? 'sha256';

        try {
            if (!is_file($signedPdfPath) || !is_readable($signedPdfPath)) {
                return [
                    'success' => false,
                    'error' => 'Signed PDF not found or unreadable',
                ];
            }

            $newPath = $this->buildVersionedPath($signedPdfPath, ".mock-cosign-{$role}-" . time());
            $this->safeCopy($signedPdfPath, $newPath);

            $sigInfo = [
                'provider' => 'mock',
                'role' => $role,
                'cert_cn' => 'MOCK SANDBOX CN',
                'cert_serial' => 'MOCK-' . strtoupper(substr(hash($algo, $newPath), 0, 12)),
                'signature_time' => gmdate('c'),
                'status' => 'valid',
            ];

            $this->logger->info('[MockSignatureProvider] addCountersignature ok', [
                'src' => $signedPdfPath,
                'dst' => $newPath,
                'role' => $role,
            ]);

            return [
                'success' => true,
                'signed_pdf_path' => $newPath,
                'signature_info' => $sigInfo,
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_MOCK_COSIGN_ERROR', [
                'context' => 'MockSignatureProvider::addCountersignature',
                'pdfPath' => $signedPdfPath,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addTimestamp(string $signedPdfPath, array $options = []): array {
        try {
            if (!is_file($signedPdfPath) || !is_readable($signedPdfPath)) {
                return [
                    'success' => false,
                    'error' => 'Signed PDF not found or unreadable',
                ];
            }

            $newPath = $this->buildVersionedPath($signedPdfPath, '.mock-ts-' . time());
            $this->safeCopy($signedPdfPath, $newPath);

            $tsInfo = [
                'tsa' => 'mock',
                'tsa_policy' => $options['policy_oid'] ?? null,
                'timestamp_time' => gmdate('c'),
                'status' => 'valid',
            ];

            $this->logger->info('[MockSignatureProvider] addTimestamp ok', [
                'src' => $signedPdfPath,
                'dst' => $newPath,
            ]);

            return [
                'success' => true,
                'signed_pdf_path' => $newPath,
                'timestamp_info' => $tsInfo,
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_MOCK_TS_ERROR', [
                'context' => 'MockSignatureProvider::addTimestamp',
                'pdfPath' => $signedPdfPath,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verifySignatures(string $pdfPath): array {
        try {
            if (!is_file($pdfPath) || !is_readable($pdfPath)) {
                return [
                    'success' => false,
                    'errors' => ['PDF not found or unreadable'],
                ];
            }

            $filename = basename($pdfPath);
            $signatures = [];

            if (str_contains($filename, '.mock-signed-')) {
                $signatures[] = [
                    'provider' => 'mock',
                    'role' => str_contains($filename, '-inspector-') ? 'inspector' : 'author',
                    'status' => 'valid',
                ];
            }
            if (str_contains($filename, '.mock-cosign-')) {
                $signatures[] = [
                    'provider' => 'mock',
                    'role' => 'inspector',
                    'status' => 'valid',
                ];
            }

            $timestamp = null;
            if (str_contains($filename, '.mock-ts-')) {
                $timestamp = [
                    'tsa' => 'mock',
                    'status' => 'valid',
                ];
            }

            return [
                'success' => true,
                'signatures' => $signatures,
                'timestamp' => $timestamp,
            ];
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_QES_MOCK_VERIFY_ERROR', [
                'context' => 'MockSignatureProvider::verifySignatures',
                'pdfPath' => $pdfPath,
                'error' => $e->getMessage(),
            ], $e);
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    private function buildVersionedPath(string $src, string $suffix): string {
        $dir = dirname($src);
        $name = pathinfo($src, PATHINFO_FILENAME);
        $ext = '.' . pathinfo($src, PATHINFO_EXTENSION);
        return $dir . DIRECTORY_SEPARATOR . $name . $suffix . $ext;
    }

    private function safeCopy(string $src, string $dst): void {
        $dir = dirname($dst);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \Exception('Failed to create directory: ' . $dir);
            }
        }
        if (!copy($src, $dst)) {
            throw new \Exception('Failed to copy PDF to destination');
        }
    }
}
