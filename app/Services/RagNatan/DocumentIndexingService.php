<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Category;
use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Document Indexing Service
 *
 * Handles document ingestion, chunking, and indexing for RAG.
 * Supports multiple chunking strategies and metadata extraction.
 */
class DocumentIndexingService
{
    private const DEFAULT_CHUNK_SIZE = 512; // tokens
    private const DEFAULT_CHUNK_OVERLAP = 128; // tokens
    private const CHARS_PER_TOKEN = 4; // Rough approximation

    public function __construct(
        private EmbeddingService $embeddingService
    ) {}

    /**
     * Index a new document.
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
        return DB::transaction(function () use (
            $title,
            $content,
            $categoryId,
            $language,
            $metadata,
            $tags,
            $keywords,
            $source,
            $author
        ) {
            // Create document
            $document = Document::create([
                'uuid' => Str::uuid()->toString(),
                'category_id' => $categoryId,
                'title' => $title,
                'content' => $content,
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

            // Create chunks
            $chunks = $this->createChunks($document);

            // Generate embeddings
            $this->embeddingService->embedChunks($chunks);

            // Mark as indexed
            $document->update(['is_indexed' => true]);

            Log::info('Document indexed', [
                'document_id' => $document->id,
                'title' => $title,
                'chunks_count' => $chunks->count(),
            ]);

            return $document->fresh();
        });
    }

    /**
     * Re-index existing document.
     */
    public function reindexDocument(Document $document): Document
    {
        return DB::transaction(function () use ($document) {
            // Delete old chunks and embeddings
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

            // Create new chunks
            $chunks = $this->createChunks($document);

            // Generate embeddings
            $this->embeddingService->embedChunks($chunks);

            // Mark as indexed
            $document->update(['is_indexed' => true]);

            Log::info('Document re-indexed', [
                'document_id' => $document->id,
                'version' => $document->version,
                'chunks_count' => $chunks->count(),
            ]);

            return $document->fresh();
        });
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

        if (count($sections) > 1) {
            // Process each section separately
            $chunkOrder = 0;
            foreach ($sections as $section) {
                $sectionChunks = $this->chunkText(
                    $section['content'],
                    $chunkSize,
                    $overlap,
                    $section['title']
                );

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
            $textChunks = $this->chunkText($content, $chunkSize, $overlap);
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
        // Look for markdown headers (# Title)
        $lines = explode("\n", $content);
        $sections = [];
        $currentSection = ['title' => null, 'content' => ''];

        foreach ($lines as $line) {
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

            $start = $end - $overlapChars;
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
        return Chunk::create([
            'uuid' => Str::uuid()->toString(),
            'document_id' => $document->id,
            'text' => $text,
            'section_title' => $sectionTitle,
            'chunk_order' => $chunkOrder,
            'char_start' => $charStart,
            'char_end' => $charEnd,
            'token_count' => $this->estimateTokens($text),
        ]);
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
        $results = [];

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
            } catch (\Exception $e) {
                Log::error('Failed to index document', [
                    'title' => $docData['title'],
                    'error' => $e->getMessage(),
                ]);

                $results[] = [
                    'success' => false,
                    'title' => $docData['title'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Delete document and all associated data.
     */
    public function deleteDocument(Document $document): bool
    {
        return DB::transaction(function () use ($document) {
            // Delete embeddings
            foreach ($document->chunks as $chunk) {
                $chunk->embedding?->delete();
            }

            // Delete chunks (will cascade)
            $document->chunks()->delete();

            // Delete document
            return $document->delete();
        });
    }
}
