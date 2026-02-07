<?php

namespace App\Console\Commands\RagNatan;

use App\Models\RagNatan\Category;
use App\Services\RagNatan\DocumentIndexingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Index FlorenceEGI Documentation into RAG System
 *
 * Indexes all markdown files from docs/FlorenceEGI/ (root level only)
 * into the RAG knowledge base for NATAN AI queries.
 */
class IndexFlorenceEgiDocsCommand extends Command
{
    protected $signature = 'rag:index-florence-docs
                            {--fresh : Delete existing documents and reindex from scratch}
                            {--category= : Category name (default: FlorenceEGI Documentation)}';

    protected $description = 'Index FlorenceEGI documentation files into RAG knowledge base';

    public function __construct(
        private DocumentIndexingService $indexingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🚀 FlorenceEGI Documentation Indexing');
        $this->newLine();

        // Get or create category
        $categoryName = $this->option('category') ?? 'FlorenceEGI Documentation';
        $category = $this->getOrCreateCategory($categoryName);

        $this->info("📁 Category: {$category->name_key} (ID: {$category->id})");
        $this->newLine();

        // Handle --fresh option
        if ($this->option('fresh')) {
            $this->warn('⚠️  Fresh indexing: deleting existing documents...');
            $deleted = $category->documents()->count();
            foreach ($category->documents as $doc) {
                $this->indexingService->deleteDocument($doc);
            }
            $this->info("✅ Deleted {$deleted} existing documents");
            $this->newLine();
        }

        // Get markdown files
        $docsPath = base_path('docs/FlorenceEGI');
        $files = File::files($docsPath);
        $markdownFiles = collect($files)->filter(fn($file) => $file->getExtension() === 'md');

        $this->info("📚 Found {$markdownFiles->count()} markdown files");
        $this->newLine();

        // Index each document
        $progressBar = $this->output->createProgressBar($markdownFiles->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($markdownFiles as $file) {
            $fileName = $file->getFilename();
            $progressBar->setMessage("Processing {$fileName}...");
            $progressBar->advance();

            try {
                $content = File::get($file->getPathname());
                $title = $this->extractTitle($fileName, $content);

                $document = $this->indexingService->indexDocument(
                    title: $title,
                    content: $content,
                    categoryId: $category->id,
                    language: 'it',
                    metadata: [
                        'file_name' => $fileName,
                        'file_path' => $file->getRelativePathname(),
                        'indexed_at' => now()->toIso8601String(),
                    ],
                    tags: $this->extractTags($fileName),
                    keywords: $this->extractKeywords($content),
                    source: 'docs/FlorenceEGI/' . $fileName,
                    author: 'FlorenceEGI Team'
                );

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'file' => $fileName,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info('✅ Indexing completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total files', $markdownFiles->count()],
                ['Successfully indexed', $results['success']],
                ['Failed', $results['failed']],
            ]
        );

        if ($results['failed'] > 0) {
            $this->newLine();
            $this->error('❌ Errors occurred:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error['file']}: {$error['error']}");
            }
        }

        $this->newLine();
        $this->info('🎉 RAG knowledge base updated successfully!');
        $this->comment('💡 You can now query these documents using the RAG API or NATAN interface.');

        return self::SUCCESS;
    }

    /**
     * Get or create category.
     */
    private function getOrCreateCategory(string $name): Category
    {
        $slug = \Illuminate\Support\Str::slug($name);

        return Category::firstOrCreate(
            ['slug' => $slug],
            [
                'name_key' => $name,
                'description_key' => 'Official FlorenceEGI platform documentation covering architecture, compliance, payments, and technical implementation.',
                'icon' => 'book',
                'color' => '#3B82F6',
                'sort_order' => 0,
                'is_active' => true,
                'metadata' => [
                    'source' => 'docs/FlorenceEGI/',
                    'auto_created' => true,
                ],
            ]
        );
    }

    /**
     * Extract title from filename or content.
     */
    private function extractTitle(string $fileName, string $content): string
    {
        // Try to get title from first # header
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        // Fall back to filename (remove number prefix and .md extension)
        return str_replace('_', ' ', pathinfo($fileName, PATHINFO_FILENAME));
    }

    /**
     * Extract tags from filename.
     */
    private function extractTags(string $fileName): array
    {
        $tags = ['documentation', 'florenceegi'];

        // Add specific tags based on filename
        $tagMap = [
            'Payment' => 'payments',
            'Pagamenti' => 'payments',
            'NATAN' => 'ai',
            'Intelligenza' => 'ai',
            'Compliance' => 'compliance',
            'Governance' => 'governance',
            'Fiscale' => 'fiscal',
            'Blockchain' => 'blockchain',
            'Wallet' => 'wallet',
            'Rebind' => 'rebind',
            'Royalty' => 'royalty',
            'Architettura' => 'architecture',
            'Oracode' => 'oracode',
            'Glossario' => 'glossary',
        ];

        foreach ($tagMap as $keyword => $tag) {
            if (stripos($fileName, $keyword) !== false) {
                $tags[] = $tag;
            }
        }

        return array_unique($tags);
    }

    /**
     * Extract keywords from content.
     */
    private function extractKeywords(string $content): array
    {
        // Extract keywords from headers (h2 and h3)
        preg_match_all('/^##\s+(.+)$/m', $content, $h2Matches);
        preg_match_all('/^###\s+(.+)$/m', $content, $h3Matches);

        $keywords = array_merge(
            $h2Matches[1] ?? [],
            $h3Matches[1] ?? []
        );

        // Clean and limit
        $keywords = array_map('trim', $keywords);
        $keywords = array_filter($keywords);
        $keywords = array_unique($keywords);

        return array_slice($keywords, 0, 20); // Max 20 keywords
    }
}
