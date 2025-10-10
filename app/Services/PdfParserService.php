<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * PDF Parser Service for N.A.T.A.N.
 *
 * Extracts text content from PDF and P7M (signed PDF) documents
 * for AI-powered metadata extraction.
 *
 * Dependencies:
 * - smalot/pdfparser (already installed in project)
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. PDF Parsing)
 * @date 2025-10-10
 */
class PdfParserService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PdfParser $pdfParser;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->pdfParser = new PdfParser();
    }

    /**
     * Extract text from PDF or P7M file
     *
     * @param string $filePath Absolute path to PDF/P7M file
     * @return string Extracted text content
     * @throws \Exception
     */
    public function extractText(string $filePath): string
    {
        $logContext = [
            'service' => 'PdfParserService',
            'method' => 'extractText',
            'file_path' => basename($filePath),
            'file_size' => filesize($filePath)
        ];

        $this->logger->info('[PdfParserService] Starting text extraction', $logContext);

        try {
            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            // Detect file type
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if ($extension === 'p7m') {
                // P7M is a signed PDF, extract PDF first
                $pdfContent = $this->extractPdfFromP7m($filePath);
                $text = $this->parsePdfContent($pdfContent);
            } else {
                // Direct PDF parsing
                $text = $this->parsePdfFile($filePath);
            }

            $this->logger->info('[PdfParserService] Text extraction completed', [
                ...$logContext,
                'extracted_text_length' => strlen($text),
                'word_count' => str_word_count($text)
            ]);

            return $text;

        } catch (\Throwable $e) {
            $this->logger->error('[PdfParserService] Text extraction failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);

            $this->errorManager->handle('PDF_TEXT_EXTRACTION_FAILED', $logContext, $e);

            throw new \Exception("Impossibile estrarre testo dal documento: {$e->getMessage()}");
        }
    }

    /**
     * Parse PDF file and extract text
     *
     * @param string $filePath
     * @return string
     * @throws \Exception
     */
    protected function parsePdfFile(string $filePath): string
    {
        try {
            $pdf = $this->pdfParser->parseFile($filePath);
            $text = $pdf->getText();
            
            // Clean up extracted text
            $text = $this->cleanText($text);
            
            return $text;
        } catch (\Exception $e) {
            throw new \Exception("PDF parsing failed: {$e->getMessage()}");
        }
    }

    /**
     * Parse PDF content from binary string
     *
     * @param string $content
     * @return string
     * @throws \Exception
     */
    protected function parsePdfContent(string $content): string
    {
        try {
            $pdf = $this->pdfParser->parseContent($content);
            $text = $pdf->getText();
            
            // Clean up extracted text
            $text = $this->cleanText($text);
            
            return $text;
        } catch (\Exception $e) {
            throw new \Exception("PDF content parsing failed: {$e->getMessage()}");
        }
    }

    /**
     * Extract PDF content from P7M signed file
     *
     * P7M files contain a PKCS#7 signature wrapping the PDF.
     * We extract the PDF content using OpenSSL.
     *
     * @param string $p7mPath
     * @return string PDF binary content
     * @throws \Exception
     */
    protected function extractPdfFromP7m(string $p7mPath): string
    {
        $this->logger->info('[PdfParserService] Extracting PDF from P7M', [
            'p7m_file' => basename($p7mPath)
        ]);

        // Create temp file for extracted PDF
        $tempPdfPath = tempnam(sys_get_temp_dir(), 'egi_p7m_') . '.pdf';

        try {
            // Use OpenSSL to extract PDF from P7M signature
            $command = sprintf(
                'openssl cms -verify -in %s -inform DER -noverify -out %s 2>&1',
                escapeshellarg($p7mPath),
                escapeshellarg($tempPdfPath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("OpenSSL extraction failed: " . implode("\n", $output));
            }

            if (!file_exists($tempPdfPath) || filesize($tempPdfPath) === 0) {
                throw new \Exception("Extracted PDF is empty or missing");
            }

            // Read extracted PDF content
            $pdfContent = file_get_contents($tempPdfPath);

            // Clean up temp file
            @unlink($tempPdfPath);

            $this->logger->info('[PdfParserService] P7M extraction successful', [
                'extracted_pdf_size' => strlen($pdfContent)
            ]);

            return $pdfContent;

        } catch (\Exception $e) {
            // Clean up temp file on error
            @unlink($tempPdfPath);
            throw $e;
        }
    }

    /**
     * Clean extracted text (remove excessive whitespace, control chars, etc.)
     *
     * @param string $text
     * @return string
     */
    protected function cleanText(string $text): string
    {
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove excessive whitespace (but keep single spaces)
        $text = preg_replace('/[ \t]+/', ' ', $text);
        
        // Remove excessive newlines (max 2 consecutive)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Trim
        $text = trim($text);
        
        return $text;
    }

    /**
     * Check if file is a valid PDF or P7M
     *
     * @param string $filePath
     * @return bool
     */
    public function isValidDocument(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['pdf', 'p7m'])) {
            return false;
        }

        // Check file signature (magic bytes)
        $handle = fopen($filePath, 'rb');
        $header = fread($handle, 8);
        fclose($handle);

        // PDF starts with %PDF
        if (str_starts_with($header, '%PDF')) {
            return true;
        }

        // P7M is DER-encoded, typically starts with 0x30 (SEQUENCE tag)
        if ($extension === 'p7m' && ord($header[0]) === 0x30) {
            return true;
        }

        return false;
    }
}

