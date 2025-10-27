<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Models\ProjectDocument;
use App\Models\ProjectDocumentChunk;
use App\Services\EmbeddingService;
use App\Services\PdfParserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Document Processing Service
 * 
 * Extracts text from documents, chunks content, generates embeddings.
 * 
 * SUPPORTED FORMATS:
 * - PDF: Via PdfParserService (smalot/pdfparser)
 * - DOCX: Via PhpOffice\PhpWord
 * - TXT/MD: Native PHP
 * - CSV: Native PHP (converts to text)
 * - XLSX: TODO (future - phpoffice/phpspreadsheet)
 * 
 * CHUNKING STRATEGY:
 * - Max tokens: 1000 (configurable)
 * - Overlap: 200 tokens (configurable)
 * - Preserves paragraph boundaries when possible
 * 
 * EMBEDDING GENERATION:
 * - Uses EmbeddingService (OpenAI ada-002)
 * - Stores in project_document_chunks table
 * - 1536 dimensions vector
 * 
 * @package App\Services\Projects
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects RAG System)
 * @date 2025-10-27
 * @purpose Process uploaded documents for RAG search
 */
class DocumentProcessingService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected EmbeddingService $embeddingService;
    protected PdfParserService $pdfParser;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EmbeddingService $embeddingService,
        PdfParserService $pdfParser
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->embeddingService = $embeddingService;
        $this->pdfParser = $pdfParser;
    }

    /**
     * Process uploaded document: extract text, chunk, generate embeddings
     * 
     * @param ProjectDocument $document
     * @return bool Success status
     */
    public function processDocument(ProjectDocument $document): bool
    {
        $logContext = [
            'service' => 'DocumentProcessingService',
            'document_id' => $document->id,
            'project_id' => $document->project_id,
            'filename' => $document->filename,
            'mime_type' => $document->mime_type,
        ];

        try {
            $this->logger->info('[DocumentProcessing] Starting document processing', $logContext);

            // Mark as processing
            $document->markAsProcessing();

            // STEP 1: Extract text from document
            $this->logger->info('[DocumentProcessing] Extracting text...', $logContext);
            $text = $this->extractTextFromDocument($document);

            if (empty($text)) {
                throw new \Exception('No text extracted from document');
            }

            $wordCount = str_word_count($text);
            $this->logger->info('[DocumentProcessing] Text extracted', [
                ...$logContext,
                'text_length' => strlen($text),
                'word_count' => $wordCount,
            ]);

            // STEP 2: Chunk text
            $this->logger->info('[DocumentProcessing] Chunking text...', $logContext);
            $chunks = $this->chunkText($text);

            $this->logger->info('[DocumentProcessing] Text chunked', [
                ...$logContext,
                'chunks_count' => count($chunks),
            ]);

            // STEP 3: Generate embeddings for each chunk
            $this->logger->info('[DocumentProcessing] Generating embeddings...', $logContext);
            $embeddingsGenerated = 0;

            foreach ($chunks as $index => $chunkData) {
                $embedding = $this->generateEmbeddingForChunk($chunkData['text']);

                if ($embedding) {
                    // Save chunk with embedding
                    ProjectDocumentChunk::create([
                        'project_document_id' => $document->id,
                        'chunk_index' => $index,
                        'chunk_text' => $chunkData['text'],
                        'embedding' => $embedding,
                        'embedding_model' => config('services.openai.embedding_model'),
                        'tokens_count' => $chunkData['tokens'],
                        'page_number' => $chunkData['page'] ?? null,
                        'metadata' => [
                            'start_char' => $chunkData['start'],
                            'end_char' => $chunkData['end'],
                        ],
                    ]);

                    $embeddingsGenerated++;
                }
            }

            $this->logger->info('[DocumentProcessing] Embeddings generated', [
                ...$logContext,
                'embeddings_generated' => $embeddingsGenerated,
            ]);

            // STEP 4: Update document metadata and mark as ready
            $document->update([
                'metadata' => [
                    'words' => $wordCount,
                    'chunks_count' => count($chunks),
                    'embeddings_count' => $embeddingsGenerated,
                    'extraction_method' => $this->getExtractionMethod($document->mime_type),
                ],
            ]);

            $document->markAsReady();

            $this->logger->info('[DocumentProcessing] Document processing completed', $logContext);

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('[DocumentProcessing] Processing failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            // Mark document as failed
            $document->markAsFailed($e->getMessage());

            $this->errorManager->handle('DOCUMENT_PROCESSING_FAILED', $logContext, $e);

            return false;
        }
    }

    /**
     * Extract text from document based on MIME type
     * 
     * @param ProjectDocument $document
     * @return string Extracted text
     * @throws \Exception If extraction fails
     */
    protected function extractTextFromDocument(ProjectDocument $document): string
    {
        $filePath = Storage::disk('local')->path($document->file_path);

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $mimeType = $document->mime_type;

        // PDF
        if (str_starts_with($mimeType, 'application/pdf')) {
            return $this->pdfParser->extractText($filePath);
        }

        // DOCX
        if (in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ])) {
            return $this->extractTextFromDocx($filePath);
        }

        // Plain text / Markdown
        if (str_starts_with($mimeType, 'text/')) {
            return file_get_contents($filePath);
        }

        // CSV
        if ($mimeType === 'text/csv') {
            return $this->extractTextFromCsv($filePath);
        }

        throw new \Exception("Unsupported MIME type: {$mimeType}");
    }

    /**
     * Extract text from DOCX file
     * 
     * @param string $filePath
     * @return string
     * @throws \Exception
     */
    protected function extractTextFromDocx(string $filePath): string
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            return trim($text);

        } catch (\Throwable $e) {
            throw new \Exception("Failed to extract text from DOCX: {$e->getMessage()}");
        }
    }

    /**
     * Extract text from CSV file
     * 
     * @param string $filePath
     * @return string
     */
    protected function extractTextFromCsv(string $filePath): string
    {
        $text = '';
        $handle = fopen($filePath, 'r');

        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                $text .= implode(' | ', $row) . "\n";
            }
            fclose($handle);
        }

        return trim($text);
    }

    /**
     * Chunk text into smaller pieces for embedding
     * 
     * STRATEGY:
     * - Target: 1000 tokens per chunk
     * - Overlap: 200 tokens between chunks
     * - Preserves paragraph boundaries when possible
     * 
     * @param string $text Full document text
     * @param int $maxTokens Max tokens per chunk (default: 1000)
     * @param int $overlapTokens Overlap between chunks (default: 200)
     * @return array Array of chunks with metadata
     */
    protected function chunkText(string $text, int $maxTokens = 1000, int $overlapTokens = 200): array
    {
        // Simple token estimation: ~4 chars per token (rough approximation)
        $charsPerToken = 4;
        $maxChars = $maxTokens * $charsPerToken;
        $overlapChars = $overlapTokens * $charsPerToken;

        $chunks = [];
        $paragraphs = explode("\n\n", $text);
        $currentChunk = '';
        $currentTokens = 0;
        $charPosition = 0;

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) {
                continue;
            }

            $paragraphChars = strlen($paragraph);
            $paragraphTokens = (int)ceil($paragraphChars / $charsPerToken);

            // If adding this paragraph exceeds max tokens, save current chunk
            if ($currentTokens + $paragraphTokens > $maxTokens && !empty($currentChunk)) {
                $startChar = $charPosition;
                $endChar = $charPosition + strlen($currentChunk);

                $chunks[] = [
                    'text' => trim($currentChunk),
                    'tokens' => $currentTokens,
                    'start' => $startChar,
                    'end' => $endChar,
                ];

                // Start new chunk with overlap
                $words = explode(' ', $currentChunk);
                $overlapWords = array_slice($words, -($overlapTokens * 2)); // Rough overlap
                $currentChunk = implode(' ', $overlapWords) . "\n\n" . $paragraph;
                $currentTokens = (int)ceil(strlen($currentChunk) / $charsPerToken);
                $charPosition = $endChar - strlen(implode(' ', $overlapWords));

            } else {
                // Add paragraph to current chunk
                $currentChunk .= ($currentChunk ? "\n\n" : '') . $paragraph;
                $currentTokens += $paragraphTokens;
            }
        }

        // Save last chunk
        if (!empty($currentChunk)) {
            $chunks[] = [
                'text' => trim($currentChunk),
                'tokens' => $currentTokens,
                'start' => $charPosition,
                'end' => $charPosition + strlen($currentChunk),
            ];
        }

        return $chunks;
    }

    /**
     * Generate embedding for a text chunk
     * 
     * @param string $text Chunk text
     * @return array|null Vector (1536 floats) or null on failure
     */
    protected function generateEmbeddingForChunk(string $text): ?array
    {
        try {
            $result = $this->embeddingService->callOpenAIEmbedding($text);

            if (!$result) {
                return null;
            }

            // Handle both old format (array) and new format (array with 'vector' key)
            if (isset($result['vector'])) {
                return $result['vector'];
            }

            return $result;

        } catch (\Throwable $e) {
            $this->logger->error('[DocumentProcessing] Embedding generation failed', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text),
            ]);

            return null;
        }
    }

    /**
     * Get extraction method name for metadata
     * 
     * @param string $mimeType
     * @return string
     */
    protected function getExtractionMethod(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'application/pdf')) {
            return 'pdfparser';
        }

        if (in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ])) {
            return 'phpword';
        }

        if ($mimeType === 'text/csv') {
            return 'csv_native';
        }

        return 'native';
    }
}
