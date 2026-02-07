<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Category;
use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Document;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Document Indexing Service
 *
 * Handles document ingestion, chunking, and indexing for RAG (Retrieval-Augmented Generation).
 * Supports multiple chunking strategies and metadata extraction.
 * Adheres to Ultra Standards for logging, error handling, and GDPR compliance.
 *
 * @package App\Services\RagNatan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 */
class DocumentIndexingService
{
    private const DEFAULT_CHUNK_SIZE = 512; // tokens
    private const DEFAULT_CHUNK_OVERLAP = 128; // tokens
    private const CHARS_PER_TOKEN = 4; // Rough approximation

    public function __construct(
        private EmbeddingService $embeddingService,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService
    ) {}

    /**
     * Index a new document.
     *
     * @throws \Exception
     */
    public function indexDocument(
        string $title,
        string $content,
        ?int $categoryId = null,
        ?string $language = 'it',
        ?array $metadata = [],
        ?array $tags = [],
        ?array $keywords = [],
        ?string $source = null,
        ?string $author = null
    ): Document {
        try {
            $this->logger->info('rag.indexing.started', [
                'title' => $title,
                'language' => $language,
                'category_id' => $categoryId
            ]);

            // TEMPORARY: Removed DB::transaction to debug blocking issue
            // Will re-add once the issue is resolved
            $this->logger->info('rag.indexing.creating_document', ['title' => $title]);

            // Clean content for UTF-8
            $cleanContent = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

            // Create document
            $document = Document::create([
                'uuid' => Str::uuid()->toString(),
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => $cleanContent,
                'language' => $language,
                'tags' => $tags,
                'keywords' => $keywords,
                'metadata' => $metadata,
                'source' => $source,
                'author' => $author,
                'char_count' => strlen($content),
                'token_count' => $this->estimateTokens($content),
                'version' => 1,
                'is_indexed' => false,
            ]);

            $this->logger->info('rag.indexing.document_created', [
                'document_id' => $document->id,
                'title' => $title
            ]);

            // Create chunks
            $this->logger->info('rag.indexing.creating_chunks', ['document_id' => $document->id]);
            $chunks = $this->createChunks($document);
            $this->logger->info('rag.indexing.chunks_created', [
                'document_id' => $document->id,
                'chunks_count' => $chunks->count()
            ]);

            // Generate embeddings
            $this->logger->info('rag.indexing.generating_embeddings', [
                'document_id' => $document->id,
                'chunks_count' => $chunks->count()
            ]);
            $this->embeddingService->embedChunks($chunks);
            $this->logger->info('rag.indexing.embeddings_generated', [
                'document_id' => $document->id
            ]);

            // Mark as indexed
            $document->update(['is_indexed' => true]);

            // GDPR Audit: Log system action for document indexing
            $this->auditService->logSystemAction(
                null,
                'rag_document_indexed',
                [
                    'document_id' => $document->id,
                    'title' => $title,
                    'chunks_count' => $chunks->count(),
                    'language' => $language,
                    'category_id' => $categoryId
                ],
                GdprActivityCategory::AI_PROCESSING
            );

            $this->logger->info('rag.indexing.completed', [
                'document_id' => $document->id,
                'title' => $title,
                'chunks_count' => $chunks->count(),
            ]);

            return $document->fresh();
        } catch (\Exception $e) {
            // Log error with structured context
            $this->logger->error('rag.indexing.failed', [
                'title' => $title,
                'language' => $language,
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Log with ErrorManager for monitoring (but don't use return value)
            $this->errorManager->handle('RAG_INDEX_DOCUMENT_FAILED', [
                'title' => $title,
                'language' => $language,
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ], $e);

            // Re-throw exception for caller to handle
            throw $e;
        }
    }

    /**
     * Re-index existing document.
     *
     * @throws \Exception
     */
    public function reindexDocument(Document $document): Document
    {
        try {
            $this->logger->info('rag.reindexing.started', [
                'document_id' => $document->id,
                'title' => $document->title,
                'current_version' => $document->version
            ]);

            return DB::transaction(function () use ($document) {
                // Delete old chunks and embeddings
                $this->logger->info('rag.reindexing.deleting_old_chunks', [
                    'document_id' => $document->id,
                    'chunks_count' => $document->chunks->count()
                ]);

                foreach ($document->chunks as $chunk) {
                    $chunk->embedding?->delete();
                    $chunk->delete();
                }

                // Update document metadata
                $document->update([
                    'char_count' => strlen($document->content),
                    'token_count' => $this->estimateTokens($document->content),
                    'version' => $document->version + 1,
                    'is_indexed' => false,
                ]);

                $this->logger->info('rag.reindexing.creating_new_chunks', [
                    'document_id' => $document->id,
                    'new_version' => $document->version
                ]);

                // Create new chunks
                $chunks = $this->createChunks($document);

                // Generate embeddings
                $this->logger->info('rag.reindexing.generating_embeddings', [
                    'document_id' => $document->id,
                    'chunks_count' => $chunks->count()
                ]);
                $this->embeddingService->embedChunks($chunks);

                // Mark as indexed
                $document->update(['is_indexed' => true]);

                // GDPR Audit: Log system action for document re-indexing
                $this->auditService->logSystemAction(
                    null,
                    'rag_document_reindexed',
                    [
                        'document_id' => $document->id,
                        'title' => $document->title,
                        'version' => $document->version,
                        'chunks_count' => $chunks->count(),
                    ],
                    GdprActivityCategory::AI_PROCESSING
                );

                $this->logger->info('rag.reindexing.completed', [
                    'document_id' => $document->id,
                    'version' => $document->version,
                    'chunks_count' => $chunks->count(),
                ]);

                return $document->fresh();
            });
        } catch (\Exception $e) {
            $this->logger->error('rag.reindexing.failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_REINDEX_DOCUMENT_FAILED', [
                'document_id' => $document->id,
                'title' => $document->title,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create chunks from document content.
     */
    private function createChunks(
        Document $document,
        int $chunkSize = null,
        int $overlap = null
    ): \Illuminate\Support\Collection {
        $chunkSize = $chunkSize ?? self::DEFAULT_CHUNK_SIZE;
        $overlap = $overlap ?? self::DEFAULT_CHUNK_OVERLAP;

        $chunks = collect();
        $content = $document->content;

        // Try to split by sections first (if content has clear structure)
        $sections = $this->extractSections($content);

        $this->logger->debug('rag.chunking.sections_count_check', ['count' => count($sections)]);

        if (count($sections) > 1) {
            // Process each section separately
            $this->logger->debug('rag.chunking.processing_multiple_sections');
            $chunkOrder = 0;
            foreach ($sections as $sectionIdx => $section) {
                $this->logger->debug('rag.chunking.processing_section', ['index' => $sectionIdx]);
                $sectionChunks = $this->chunkText(
                    $section['content'],
                    $chunkSize,
                    $overlap,
                    $section['title']
                );
                $this->logger->debug('rag.chunking.section_chunked', ['chunks' => count($sectionChunks)]);

                foreach ($sectionChunks as $chunkData) {
                    $chunks->push($this->createChunk(
                        $document,
                        $chunkData['text'],
                        $chunkOrder++,
                        $chunkData['char_start'],
                        $chunkData['char_end'],
                        $section['title']
                    ));
                }
            }
        } else {
            // Single section, chunk normally
            $this->logger->debug('rag.chunking.processing_single_section');
            $textChunks = $this->chunkText($content, $chunkSize, $overlap);
            $this->logger->debug('rag.chunking.text_chunked', ['chunks' => count($textChunks)]);

            foreach ($textChunks as $index => $chunkData) {
                $chunks->push($this->createChunk(
                    $document,
                    $chunkData['text'],
                    $index,
                    $chunkData['char_start'],
                    $chunkData['char_end']
                ));
            }
        }

        return $chunks;
    }

    /**
     * Extract sections from content (markdown-style headers).
     */
    private function extractSections(string $content): array
    {
        $this->logger->debug('rag.chunking.extracting_sections', ['content_length' => strlen($content)]);

        // Look for markdown headers (# Title)
        $lines = explode("\n", $content);
        $this->logger->debug('rag.chunking.lines_count', ['count' => count($lines)]);

        $sections = [];
        $currentSection = ['title' => null, 'content' => ''];

        foreach ($lines as $lineNum => $line) {
            if (preg_match('/^#+\s+(.+)$/', $line, $matches)) {
                // New section found
                if (!empty($currentSection['content'])) {
                    $sections[] = $currentSection;
                }
                $currentSection = [
                    'title' => trim($matches[1]),
                    'content' => '',
                ];
            } else {
                $currentSection['content'] .= $line . "\n";
            }
        }

        // Add last section
        if (!empty($currentSection['content'])) {
            $sections[] = $currentSection;
        }

        $this->logger->debug('rag.chunking.sections_extracted', ['sections_count' => count($sections)]);

        return $sections ?: [['title' => null, 'content' => $content]];
    }

    /**
     * Chunk text into smaller pieces.
     */
    private function chunkText(
        string $text,
        int $chunkSize,
        int $overlap,
        ?string $sectionTitle = null
    ): array {
        $chunks = [];
        $chunkSizeChars = $chunkSize * self::CHARS_PER_TOKEN;
        $overlapChars = $overlap * self::CHARS_PER_TOKEN;

        $start = 0;
        $textLength = strlen($text);

        while ($start < $textLength) {
            $end = min($start + $chunkSizeChars, $textLength);

            // Try to break at sentence boundary
            if ($end < $textLength) {
                $sentenceEnd = $this->findSentenceBoundary($text, $end, $chunkSizeChars);
                if ($sentenceEnd !== false) {
                    $end = $sentenceEnd;
                }
            }

            $chunkText = trim(substr($text, $start, $end - $start));

            if (!empty($chunkText)) {
                $chunks[] = [
                    'text' => $chunkText,
                    'char_start' => $start,
                    'char_end' => $end,
                ];
            }

            // Ensure we always advance (prevent infinite loop)
            $newStart = $end - $overlapChars;
            $minAdvance = max(100, $chunkSizeChars / 4); // Advance at least 25% of chunk size
            if ($newStart <= $start) {
                $newStart = $start + $minAdvance;
            }
            // If we've reached the end, stop
            if ($end >= $textLength) {
                break;
            }
            $start = $newStart;
        }

        return $chunks;
    }

    /**
     * Find nearest sentence boundary.
     */
    private function findSentenceBoundary(string $text, int $position, int $maxDistance): int|false
    {
        // Look for sentence endings (. ! ? followed by space or newline)
        $sentenceEnders = ['. ', '! ', '? ', ".\n", "!\n", "?\n"];

        $minPos = max(0, $position - $maxDistance / 2);
        $maxPos = min(strlen($text), $position + $maxDistance / 2);

        // Search backwards first (prefer breaking earlier)
        for ($i = $position; $i >= $minPos; $i--) {
            foreach ($sentenceEnders as $ender) {
                if (substr($text, $i, strlen($ender)) === $ender) {
                    return $i + strlen($ender);
                }
            }
        }

        // Search forward if nothing found backwards
        for ($i = $position; $i <= $maxPos; $i++) {
            foreach ($sentenceEnders as $ender) {
                if (substr($text, $i, strlen($ender)) === $ender) {
                    return $i + strlen($ender);
                }
            }
        }

        return false;
    }

    /**
     * Create a chunk record.
     */
    private function createChunk(
        Document $document,
        string $text,
        int $chunkOrder,
        int $charStart,
        int $charEnd,
        ?string $sectionTitle = null
    ): Chunk {
        $this->logger->debug('rag.chunking.creating_chunk', ['order' => $chunkOrder, 'text_length' => strlen($text)]);

        // Clean text for UTF-8
        $cleanText = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $cleanText = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $cleanText);

        $chunk = Chunk::create([
            'uuid' => Str::uuid()->toString(),
            'document_id' => $document->id,
            'text' => $cleanText,
            'section_title' => $sectionTitle,
            'chunk_order' => $chunkOrder,
            'char_start' => $charStart,
            'char_end' => $charEnd,
            'token_count' => $this->estimateTokens($text),
            'language' => $document->language,
        ]);

        $this->logger->debug('rag.chunking.chunk_created', ['id' => $chunk->id]);

        return $chunk;
    }

    /**
     * Estimate token count.
     */
    private function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / self::CHARS_PER_TOKEN);
    }

    /**
     * Bulk index multiple documents.
     */
    public function bulkIndex(array $documents): array
    {
        $this->logger->info('rag.bulk_indexing.started', ['documents_count' => count($documents)]);

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($documents as $docData) {
            try {
                $document = $this->indexDocument(
                    $docData['title'],
                    $docData['content'],
                    $docData['category_id'] ?? null,
                    $docData['language'] ?? 'it',
                    $docData['metadata'] ?? [],
                    $docData['tags'] ?? [],
                    $docData['keywords'] ?? [],
                    $docData['source'] ?? null,
                    $docData['author'] ?? null
                );

                $results[] = [
                    'success' => true,
                    'document_id' => $document->id,
                    'title' => $document->title,
                ];
                $successCount++;
            } catch (\Exception $e) {
                $this->logger->error('rag.bulk_indexing.document_failed', [
                    'title' => $docData['title'],
                    'error' => $e->getMessage(),
                ]);

                $this->errorManager->handle('RAG_BULK_INDEX_DOCUMENT_FAILED', [
                    'title' => $docData['title'],
                    'error' => $e->getMessage()
                ], $e);

                $results[] = [
                    'success' => false,
                    'title' => $docData['title'],
                    'error' => $e->getMessage(),
                ];
                $failureCount++;
            }
        }

        // GDPR Audit: Log bulk indexing operation
        $this->auditService->logSystemAction(
            null,
            'rag_bulk_index_completed',
            [
                'total_documents' => count($documents),
                'success_count' => $successCount,
                'failure_count' => $failureCount
            ],
            GdprActivityCategory::AI_PROCESSING
        );

        $this->logger->info('rag.bulk_indexing.completed', [
            'total' => count($documents),
            'success' => $successCount,
            'failed' => $failureCount
        ]);

        return $results;
    }

    /**
     * Delete document and all associated data.
     *
     * @throws \Exception
     */
    public function deleteDocument(Document $document): bool
    {
        try {
            $this->logger->info('rag.delete.started', [
                'document_id' => $document->id,
                'title' => $document->title,
                'chunks_count' => $document->chunks->count()
            ]);

            $documentId = $document->id;
            $documentTitle = $document->title;
            $chunksCount = $document->chunks->count();

            $result = DB::transaction(function () use ($document) {
                // Delete embeddings
                foreach ($document->chunks as $chunk) {
                    $chunk->embedding?->delete();
                }

                // Delete chunks (will cascade)
                $document->chunks()->delete();

                // Delete document
                return $document->delete();
            });

            // GDPR Audit: Log document deletion
            $this->auditService->logSystemAction(
                null,
                'rag_document_deleted',
                [
                    'document_id' => $documentId,
                    'title' => $documentTitle,
                    'chunks_deleted' => $chunksCount
                ],
                GdprActivityCategory::DATA_DELETION
            );

            $this->logger->info('rag.delete.completed', [
                'document_id' => $documentId,
                'title' => $documentTitle
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('rag.delete.failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_DELETE_DOCUMENT_FAILED', [
                'document_id' => $document->id,
                'title' => $document->title,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }
}
