<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Web Search tracking fields to natan_chat_messages table
 *
 * N.A.T.A.N. v3.0 Feature: Web Search Integration
 * Tracks when and how web search was used to augment responses
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search)
 * @date 2025-10-26
 * @purpose Track web search usage for analytics and GDPR audit
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            // Web Search Tracking (NEW v3.0)
            $table->boolean('web_search_enabled')->default(false)->after('rag_method');
            $table->string('web_search_provider', 50)->nullable()->after('web_search_enabled'); // 'perplexity', 'google'
            $table->json('web_search_results')->nullable()->after('web_search_provider'); // Array of URLs + snippets
            $table->unsignedInteger('web_search_count')->default(0)->after('web_search_results'); // Number of web results
            $table->boolean('web_search_from_cache')->default(false)->after('web_search_count');

            // Index for analytics queries
            $table->index('web_search_enabled');
            $table->index(['web_search_provider', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            $table->dropIndex(['web_search_provider', 'created_at']);
            $table->dropIndex(['web_search_enabled']);

            $table->dropColumn([
                'web_search_enabled',
                'web_search_provider',
                'web_search_results',
                'web_search_count',
                'web_search_from_cache',
            ]);
        });
    }
};
