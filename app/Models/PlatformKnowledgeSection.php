<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Platform Knowledge Section Model
 *
 * Knowledge base for AI Platform Assistant. Stores structured information
 * about FlorenceEGI features, workflows, and functionalities that AI uses
 * to provide accurate help and guidance to users.
 *
 * @property int $id
 * @property string $section_key Unique section identifier
 * @property string $category Section category
 * @property string $title Section title
 * @property string $content Detailed content for AI context
 * @property array|null $keywords Searchable keywords
 * @property int $priority Display priority
 * @property bool $is_active Active flag
 * @property string $locale Language locale
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Art Advisor)
 * @date 2025-10-29
 * @purpose Knowledge base for AI platform guidance
 */
class PlatformKnowledgeSection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'platform_knowledge_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_key',
        'category',
        'title',
        'content',
        'keywords',
        'priority',
        'is_active',
        'locale',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: Only active sections
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by locale
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope: Search by keywords or content
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhereJsonContains('keywords', $search);
        });
    }

    /**
     * Get all sections formatted for AI context
     *
     * Returns formatted string with all knowledge sections for AI prompt.
     *
     * @param string|null $category Optional category filter
     * @param string $locale Locale filter (default: it)
     * @return string Formatted knowledge base for AI
     */
    public static function getFormattedForAI(?string $category = null, string $locale = 'it'): string
    {
        $query = self::active()->locale($locale)->orderBy('priority');

        if ($category) {
            $query->category($category);
        }

        $sections = $query->get();

        if ($sections->isEmpty()) {
            return "# PLATFORM KNOWLEDGE BASE\n\nNo knowledge sections available yet.";
        }

        $formatted = "# FLORENCEEGI PLATFORM KNOWLEDGE BASE\n\n";
        $formatted .= "Total sections: " . $sections->count() . "\n";
        $formatted .= "Locale: {$locale}\n\n";

        $currentCategory = null;

        foreach ($sections as $section) {
            // Add category header if changed
            if ($currentCategory !== $section->category) {
                $currentCategory = $section->category;
                $formatted .= "\n## " . strtoupper($currentCategory) . "\n\n";
            }

            // Add section
            $formatted .= "### {$section->title} ({$section->section_key})\n\n";
            $formatted .= $section->content . "\n\n";

            // Add keywords if present
            if (!empty($section->keywords)) {
                $formatted .= "**Keywords:** " . implode(', ', $section->keywords) . "\n\n";
            }

            $formatted .= "---\n\n";
        }

        return $formatted;
    }

    /**
     * Search relevant sections based on user question
     *
     * Uses simple keyword matching to find relevant sections.
     *
     * @param string $question User's question
     * @param string $locale Locale (default: it)
     * @param int $limit Max sections to return
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findRelevant(string $question, string $locale = 'it', int $limit = 5)
    {
        // Extract potential keywords from question
        $questionLower = strtolower($question);
        
        // Common words to skip
        $stopWords = ['come', 'cosa', 'dove', 'quando', 'perché', 'qual', 'quale', 'quanto', 
                      'posso', 'devo', 'voglio', 'fare', 'è', 'il', 'la', 'un', 'una'];

        $words = preg_split('/\s+/', $questionLower);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });

        // Search by keywords
        $query = self::active()->locale($locale);

        foreach ($keywords as $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhereJsonContains('keywords', $keyword);
            });
        }

        return $query->orderBy('priority')->limit($limit)->get();
    }
}

