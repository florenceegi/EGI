<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.5) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI HUB - Ultra Excellence Enterprise)
 * @date 2025-11-22
 * @purpose Create tenant_groups table for cross-application tenant grouping
 * 
 * CONTEXT: EGI HUB manages tenant groups for ALL applications (NATAN_LOC, future apps)
 * ADR: docs/adr/0001-multi-tenant-query-architecture.md
 * 
 * PURPOSE:
 * - Group tenants by category (pa_comuni, pa_province, cmpy_, noprof_, marketplace)
 * - Enable cross-tenant queries (e.g., "all Tuscan municipalities" in NATAN_LOC)
 * - Support both system-defined and user-defined groups
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_groups', function (Blueprint $table) {
            $table->id();
            
            // Identificazione gruppo
            $table->string('code', 100)->unique()->comment('Unique code: pa_comuni_toscana, cmpy_group_1, mkt_all');
            $table->string('name', 255)->comment('Human-readable name');
            $table->text('description')->nullable()->comment('Group description');
            
            // Membri del gruppo (JSON array di tenant IDs)
            $table->json('tenant_ids')->comment('Array of tenant IDs: [1, 2, 5, 8, ...]');
            
            // Tipo gruppo
            $table->boolean('is_system')->default(false)->comment('System-defined (true) vs User-defined (false)');
            $table->string('category', 50)->nullable()->comment('Category: comune, provincia, regione, company, marketplace, custom');
            
            // Owner (NULL per gruppi di sistema)
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who created this group (NULL for system groups)');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes (OS3.5 Performance Excellence)
            $table->index('code', 'idx_tenant_groups_code');
            $table->index('category', 'idx_tenant_groups_category');
            $table->index('is_system', 'idx_tenant_groups_system');
            $table->index('created_by_user_id', 'idx_tenant_groups_creator');
        });
        
        // Insert system-defined groups (esempi base per EGI HUB)
        DB::table('tenant_groups')->insert([
            [
                'code' => 'mkt_florenceegi',
                'name' => 'FlorenceEGI Marketplace',
                'description' => 'Gruppo che include il tenant marketplace FlorenceEGI',
                'tenant_ids' => json_encode([1]), // ID 1 = FlorenceEGI
                'is_system' => true,
                'category' => 'marketplace',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pa_comuni_italia',
                'name' => 'Tutti i Comuni Italiani',
                'description' => 'Gruppo che include tutti i comuni italiani registrati (NATAN_LOC)',
                'tenant_ids' => json_encode([]), // Populated by cron job
                'is_system' => true,
                'category' => 'comune',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pa_province_italia',
                'name' => 'Tutte le Province Italiane',
                'description' => 'Gruppo che include tutte le province italiane (NATAN_LOC)',
                'tenant_ids' => json_encode([]),
                'is_system' => true,
                'category' => 'provincia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pa_regioni_italia',
                'name' => 'Tutte le Regioni Italiane',
                'description' => 'Gruppo che include tutte le regioni italiane (NATAN_LOC)',
                'tenant_ids' => json_encode([]),
                'is_system' => true,
                'category' => 'regione',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_groups');
    }
};


