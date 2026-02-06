<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Document;
use App\Models\RagNatan\Embedding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Search Service
 *
 * Handles vector similarity search and full-text search.
 * Supports hybrid search combining both methods.
 */
class SearchService
{
    public function __construct(
        private EmbeddingService $embeddingService
    ) {}

    /**
     * Vector similarity search.
     *
     * @param array<float> $queryVector
     * @param int $limit
     * @param float|null $maxDistance Maximum cosine distance (0-2)
     * @return Collection<Chunk>
     */
    public function vectorSearch(array $queryVector, int $limit = 20, ?float $maxDistance = null): Collection
    {
        $vectorString = '[' . implode(',', $queryVector) . ']';

        $query = Embedding::query()
            ->selectRaw('embeddings.*, chunks.*, (embedding <-> ?) as distance', [$vectorString])
            ->join('rag_natan.chunks', 'embeddings.chunk_id', '=', 'chunks.id')
            ->orderBy('distance');

        if ($maxDistance !== null) {
            $query->havingRaw('(embedding <-> ?) <= ?', [$vectorString, $maxDistance]);
        }

        $results = $query->limit($limit)->get();

        // Load chunks with relationships
        $chunkIds = $results->pluck('chunk_id')->unique();
        return Chunk::with(['document', 'embedding'])
            ->whereIn('id', $chunkIds)
            ->get()
            ->map(function ($chunk) use ($results) {
                $result = $results->firstWhere('chunk_id', $chunk->id);
                $chunk->distance = $result->distance ?? null;
                $chunk->similarity_score = $result->distance !== null
                    ? round((1 - ($result->distance / 2)) * 100, 2)
                    : null;
                return $chunk;
            })
            ->sortBy('distance')
            ->values();
    }

    /**
     * Search by text query using embeddings.
     */
    public function searchByText(string $query, int $limit = 20, ?float $maxDistance = null): Collection
    {
        $queryVector = $this->embeddingService->generateEmbedding($query);
        return $this->vectorSearch($queryVector, $limit, $maxDistance);
    }

    /**
     * Full-text search on documents.
     *
     * @param string $searchTerm
     * @param string $language PostgreSQL text search language
     * @param int $limit
     * @return Collection<Document>
     */
    public function fullTextSearch(
        string $searchTerm,
        string $language = 'italian',
        int $limit = 20
    ): Collection {
        return Document::whereRaw(
            "search_vector @@ plainto_tsquery(?, ?)",
            [$language, $searchTerm]
        )
            ->selectRaw(
                "*, ts_rank(search_vector, plainto_tsquery(?, ?)) as rank",
                [$language, $searchTerm]
            )
            ->orderByDesc('rank')
            ->limit($limit)
            ->get();
    }

    /**
     * Full-text search on chunks.
     */
    public function fullTextSearchChunks(
        string $searchTerm,
        string $language = 'italian',
        int $limit = 20
    ): Collection {
        return Chunk::whereRaw(
            "to_tsvector(?, text) @@ plainto_tsquery(?, ?)",
            [$language, $language, $searchTerm]
        )
            ->selectRaw(
                "*, ts_rank(to_tsvector(?, text), plainto_tsquery(?, ?)) as rank",
                [$language, $language, $searchTerm]
            )
            ->with(['document', 'embedding'])
            ->orderByDesc('rank')
            ->limit($limit)
            ->get();
    }

    /**
     * Hybrid search: combines vector and full-text search.
     *
     * @param string $query
     * @param string $language
     * @param int $limit
     * @param float $vectorWeight Weight for vector search (0-1)
     * @param float $textWeight Weight for full-text search (0-1)
     * @return Collection<Chunk>
     */
    public function hybridSearch(
        string $query,
        string $language = 'italian',
        int $limit = 20,
        float $vectorWeight = 0.7,
        float $textWeight = 0.3
    ): Collection {
        // Get vector search results
        $vectorResults = $this->searchByText($query, $limit * 2);

        // Get full-text search results
        $textResults = $this->fullTextSearchChunks($query, $language, $limit * 2);

        // Combine and re-rank
        $combined = collect();
        $seen = [];

        foreach ($vectorResults as $chunk) {
            if (!isset($seen[$chunk->id])) {
                $chunk->hybrid_score = ($chunk->similarity_score ?? 0) * $vectorWeight;
                $combined->push($chunk);
                $seen[$chunk->id] = true;
            }
        }

        foreach ($textResults as $chunk) {
            $normalizedRank = ($chunk->rank ?? 0) * 100; // Normalize to 0-100
            if (isset($seen[$chunk->id])) {
                // Already in results, add text score
                $existing = $combined->firstWhere('id', $chunk->id);
                $existing->hybrid_score += $normalizedRank * $textWeight;
            } else {
                // New result from text search
                $chunk->hybrid_score = $normalizedRank * $textWeight;
                $combined->push($chunk);
                $seen[$chunk->id] = true;
            }
        }

        return $combined
            ->sortByDesc('hybrid_score')
            ->take($limit)
            ->values();
    }

    /**
     * Search within a specific document.
     */
    public function searchInDocument(int $documentId, string $query, int $limit = 10): Collection
    {
        $queryVector = $this->embeddingService->generateEmbedding($query);
        $vectorString = '[' . implode(',', $queryVector) . ']';

        $results = Embedding::query()
            ->selectRaw('embeddings.*, chunks.*, (embedding <-> ?) as distance', [$vectorString])
            ->join('rag_natan.chunks', 'embeddings.chunk_id', '=', 'chunks.id')
            ->where('chunks.document_id', $documentId)
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        $chunkIds = $results->pluck('chunk_id')->unique();
        return Chunk::with(['document'])
            ->whereIn('id', $chunkIds)
            ->get()
            ->map(function ($chunk) use ($results) {
                $result = $results->firstWhere('chunk_id', $chunk->id);
                $chunk->distance = $result->distance ?? null;
                $chunk->similarity_score = $result->distance !== null
                    ? round((1 - ($result->distance / 2)) * 100, 2)
                    : null;
                return $chunk;
            })
            ->sortBy('distance')
            ->values();
    }

    /**
     * Search within specific categories.
     */
    public function searchInCategories(
        array $categoryIds,
        string $query,
        int $limit = 20
    ): Collection {
        $queryVector = $this->embeddingService->generateEmbedding($query);
        $vectorString = '[' . implode(',', $queryVector) . ']';

        $results = Embedding::query()
            ->selectRaw('embeddings.*, chunks.*, (embedding <-> ?) as distance', [$vectorString])
            ->join('rag_natan.chunks', 'embeddings.chunk_id', '=', 'chunks.id')
            ->join('rag_natan.documents', 'chunks.document_id', '=', 'documents.id')
            ->whereIn('documents.category_id', $categoryIds)
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        $chunkIds = $results->pluck('chunk_id')->unique();
        return Chunk::with(['document'])
            ->whereIn('id', $chunkIds)
            ->get()
            ->map(function ($chunk) use ($results) {
                $result = $results->firstWhere('chunk_id', $chunk->id);
                $chunk->distance = $result->distance ?? null;
                $chunk->similarity_score = $result->distance !== null
                    ? round((1 - ($result->distance / 2)) * 100, 2)
                    : null;
                return $chunk;
            })
            ->sortBy('distance')
            ->values();
    }

    /**
     * Find similar chunks to a given chunk.
     */
    public function findSimilarChunks(Chunk $chunk, int $limit = 10): Collection
    {
        if (!$chunk->embedding) {
            return collect();
        }

        $vectorString = $chunk->embedding->embedding;

        $results = Embedding::query()
            ->selectRaw('embeddings.*, chunks.*, (embedding <-> ?) as distance', [$vectorString])
            ->join('rag_natan.chunks', 'embeddings.chunk_id', '=', 'chunks.id')
            ->where('chunks.id', '!=', $chunk->id)
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        $chunkIds = $results->pluck('chunk_id')->unique();
        return Chunk::with(['document'])
            ->whereIn('id', $chunkIds)
            ->get()
            ->map(function ($similarChunk) use ($results) {
                $result = $results->firstWhere('chunk_id', $similarChunk->id);
                $similarChunk->distance = $result->distance ?? null;
                $similarChunk->similarity_score = $result->distance !== null
                    ? round((1 - ($result->distance / 2)) * 100, 2)
                    : null;
                return $similarChunk;
            })
            ->sortBy('distance')
            ->values();
    }
}
