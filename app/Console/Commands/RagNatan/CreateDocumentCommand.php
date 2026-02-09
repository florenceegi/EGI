<?php

namespace App\Console\Commands\RagNatan;

use App\Models\RagNatan\Category;
use App\Services\RagNatan\DocumentIndexingService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * RAG Document Creation Command
 *
 * Creates and indexes RAG documents from content.
 * Workflow: Content → DB → Chunking → Embeddings → .md backup
 *
 * @package App\Console\Commands\RagNatan
 */
class CreateDocumentCommand extends Command
{
    protected $signature = 'rag:create-document
                            {category : Category slug}
                            {title : Document title}
                            {content : Document content or path to .md file}
                            {--description= : Optional description}
                            {--author= : Optional author name}
                            {--save-md : Save content to .md file in docs/rag/}';

    protected $description = 'Create and index a new RAG document';

    public function __construct(
        private DocumentIndexingService $indexingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $categorySlug = $this->argument('category');
        $title = $this->argument('title');
        $contentInput = $this->argument('content');
        $description = $this->option('description');
        $author = $this->option('author') ?? 'Padmin D. Curtis (AI Partner OS3.0)';
        $saveMd = $this->option('save-md');

        $this->info("Creating RAG document: {$title}");
        $this->info("Category: {$categorySlug}");

        // 1. Find category
        $category = Category::where('slug', $categorySlug)->first();
        if (!$category) {
            $this->error("Category '{$categorySlug}' not found!");
            return self::FAILURE;
        }

        // 2. Get content (from file or direct input)
        if (file_exists($contentInput)) {
            $this->info("Reading content from file: {$contentInput}");
            $content = file_get_contents($contentInput);
        } else {
            $content = $contentInput;
        }

        if (empty($content)) {
            $this->error('Content is empty!');
            return self::FAILURE;
        }

        // 3. Generate slug
        $slug = Str::slug($title);

        // 4. Index document (creates document + chunks + embeddings)
        try {
            $this->info('Indexing document...');

            $document = $this->indexingService->indexDocument(
                title: $title,
                content: $content,
                categoryId: $category->id,
                metadata: [
                    'slug' => $slug,
                    'description' => $description,
                    'author' => $author,
                    'version' => '1.0.0',
                    'created_via' => 'rag:create-document command',
                ],
                language: 'it'
            );

            $this->info("✓ Document created: ID {$document->id}");
            $this->info("✓ Chunks created: {$document->chunks()->count()}");
            $this->info("✓ Embeddings generated");

        } catch (\Exception $e) {
            $this->error("Failed to index document: {$e->getMessage()}");
            return self::FAILURE;
        }

        // 5. Optionally save to .md file as backup
        if ($saveMd) {
            $mdPath = $this->saveMdFile($slug, $title, $content, $category, $description, $author);
            $this->info("✓ Saved backup to: {$mdPath}");
        }

        $this->info("\n✅ Document created successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $document->id],
                ['UUID', $document->uuid],
                ['Title', $document->title],
                ['Category', $category->slug],
                ['Chunks', $document->chunks()->count()],
                ['Language', $document->language],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Save content to .md file as official backup documentation.
     */
    private function saveMdFile(
        string $slug,
        string $title,
        string $content,
        Category $category,
        ?string $description,
        string $author
    ): string {
        $docsPath = base_path('docs/rag');
        $categoryPath = $docsPath . '/' . $category->slug;

        // Create directories if they don't exist
        if (!is_dir($categoryPath)) {
            mkdir($categoryPath, 0755, true);
        }

        $filename = $slug . '.md';
        $filepath = $categoryPath . '/' . $filename;

        // Build markdown content with frontmatter
        $mdContent = "---\n";
        $mdContent .= "title: \"{$title}\"\n";
        $mdContent .= "category: {$category->slug}\n";
        if ($description) {
            $mdContent .= "description: \"{$description}\"\n";
        }
        $mdContent .= "author: \"{$author}\"\n";
        $mdContent .= "version: \"1.0.0\"\n";
        $mdContent .= "date: " . now()->format('Y-m-d') . "\n";
        $mdContent .= "language: it\n";
        $mdContent .= "---\n\n";
        $mdContent .= $content;

        file_put_contents($filepath, $mdContent);

        return $filepath;
    }
}
