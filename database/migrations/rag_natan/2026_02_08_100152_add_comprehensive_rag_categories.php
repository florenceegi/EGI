<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories organized by priority and functional area
        // Following Oracode i18n pattern: name_key and description_key point to translation files
        $categories = [
            // TIER 1 - CRITICAL CATEGORIES
            ['slug' => 'getting-started', 'name_key' => 'rag.categories.getting_started.name', 'description_key' => 'rag.categories.getting_started.description', 'sort_order' => 1, 'is_active' => true],
            ['slug' => 'security', 'name_key' => 'rag.categories.security.name', 'description_key' => 'rag.categories.security.description', 'sort_order' => 2, 'is_active' => true],
            ['slug' => 'privacy-gdpr', 'name_key' => 'rag.categories.privacy_gdpr.name', 'description_key' => 'rag.categories.privacy_gdpr.description', 'sort_order' => 3, 'is_active' => true],
            ['slug' => 'troubleshooting', 'name_key' => 'rag.categories.troubleshooting.name', 'description_key' => 'rag.categories.troubleshooting.description', 'sort_order' => 4, 'is_active' => true],
            ['slug' => 'support', 'name_key' => 'rag.categories.support.name', 'description_key' => 'rag.categories.support.description', 'sort_order' => 5, 'is_active' => true],

            // CORE FUNCTIONAL CATEGORIES (from existing docs)
            ['slug' => 'platform', 'name_key' => 'rag.categories.platform.name', 'description_key' => 'rag.categories.platform.description', 'sort_order' => 10, 'is_active' => true],
            ['slug' => 'architecture', 'name_key' => 'rag.categories.architecture.name', 'description_key' => 'rag.categories.architecture.description', 'sort_order' => 11, 'is_active' => true],
            ['slug' => 'payments', 'name_key' => 'rag.categories.payments.name', 'description_key' => 'rag.categories.payments.description', 'sort_order' => 12, 'is_active' => true],
            ['slug' => 'billing', 'name_key' => 'rag.categories.billing.name', 'description_key' => 'rag.categories.billing.description', 'sort_order' => 13, 'is_active' => true],
            ['slug' => 'royalty', 'name_key' => 'rag.categories.royalty.name', 'description_key' => 'rag.categories.royalty.description', 'sort_order' => 14, 'is_active' => true],
            ['slug' => 'fiscal', 'name_key' => 'rag.categories.fiscal.name', 'description_key' => 'rag.categories.fiscal.description', 'sort_order' => 15, 'is_active' => true],
            ['slug' => 'blockchain', 'name_key' => 'rag.categories.blockchain.name', 'description_key' => 'rag.categories.blockchain.description', 'sort_order' => 16, 'is_active' => true],
            ['slug' => 'wallet', 'name_key' => 'rag.categories.wallet.name', 'description_key' => 'rag.categories.wallet.description', 'sort_order' => 17, 'is_active' => true],
            ['slug' => 'rebind', 'name_key' => 'rag.categories.rebind.name', 'description_key' => 'rag.categories.rebind.description', 'sort_order' => 18, 'is_active' => true],
            ['slug' => 'collections', 'name_key' => 'rag.categories.collections.name', 'description_key' => 'rag.categories.collections.description', 'sort_order' => 19, 'is_active' => true],

            // TIER 2 - IMPORTANT CATEGORIES
            ['slug' => 'media-management', 'name_key' => 'rag.categories.media_management.name', 'description_key' => 'rag.categories.media_management.description', 'sort_order' => 20, 'is_active' => true],
            ['slug' => 'verification-kyc', 'name_key' => 'rag.categories.verification_kyc.name', 'description_key' => 'rag.categories.verification_kyc.description', 'sort_order' => 21, 'is_active' => true],
            ['slug' => 'search-discovery', 'name_key' => 'rag.categories.search_discovery.name', 'description_key' => 'rag.categories.search_discovery.description', 'sort_order' => 22, 'is_active' => true],
            ['slug' => 'quality-standards', 'name_key' => 'rag.categories.quality_standards.name', 'description_key' => 'rag.categories.quality_standards.description', 'sort_order' => 23, 'is_active' => true],
            ['slug' => 'legal-compliance', 'name_key' => 'rag.categories.legal_compliance.name', 'description_key' => 'rag.categories.legal_compliance.description', 'sort_order' => 24, 'is_active' => true],
            ['slug' => 'refunds-disputes', 'name_key' => 'rag.categories.refunds_disputes.name', 'description_key' => 'rag.categories.refunds_disputes.description', 'sort_order' => 25, 'is_active' => true],

            // TIER 3 - NICE-TO-HAVE CATEGORIES
            ['slug' => 'export-import', 'name_key' => 'rag.categories.export_import.name', 'description_key' => 'rag.categories.export_import.description', 'sort_order' => 30, 'is_active' => true],
            ['slug' => 'social-features', 'name_key' => 'rag.categories.social_features.name', 'description_key' => 'rag.categories.social_features.description', 'sort_order' => 31, 'is_active' => true],
            ['slug' => 'promotions', 'name_key' => 'rag.categories.promotions.name', 'description_key' => 'rag.categories.promotions.description', 'sort_order' => 32, 'is_active' => true],
            ['slug' => 'mobile-app', 'name_key' => 'rag.categories.mobile_app.name', 'description_key' => 'rag.categories.mobile_app.description', 'sort_order' => 33, 'is_active' => true],
            ['slug' => 'api-advanced', 'name_key' => 'rag.categories.api_advanced.name', 'description_key' => 'rag.categories.api_advanced.description', 'sort_order' => 34, 'is_active' => true],
            ['slug' => 'accessibility', 'name_key' => 'rag.categories.accessibility.name', 'description_key' => 'rag.categories.accessibility.description', 'sort_order' => 35, 'is_active' => true],

            // SPECIALIZED CATEGORIES
            ['slug' => 'ai-natan', 'name_key' => 'rag.categories.ai_natan.name', 'description_key' => 'rag.categories.ai_natan.description', 'sort_order' => 40, 'is_active' => true],
            ['slug' => 'oracode', 'name_key' => 'rag.categories.oracode.name', 'description_key' => 'rag.categories.oracode.description', 'sort_order' => 41, 'is_active' => true],
            ['slug' => 'development', 'name_key' => 'rag.categories.development.name', 'description_key' => 'rag.categories.development.description', 'sort_order' => 42, 'is_active' => true],
            ['slug' => 'glossary', 'name_key' => 'rag.categories.glossary.name', 'description_key' => 'rag.categories.glossary.description', 'sort_order' => 50, 'is_active' => true],
        ];

        // Insert categories
        foreach ($categories as $category) {
            DB::table('rag_natan.categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all categories except 'general'
        DB::table('rag_natan.categories')
            ->where('slug', '!=', 'general')
            ->delete();
    }
};
