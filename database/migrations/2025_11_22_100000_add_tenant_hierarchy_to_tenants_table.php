<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.5) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI HUB - Ultra Excellence Enterprise)
 * @date 2025-11-22
 * @purpose Add tenant hierarchy and classification to tenants (EGI HUB manages ALL tenant types)
 * 
 * CONTEXT: EGI = HUB for ALL tenant types (FlorenceEGI marketplace, NATAN_LOC PA, future apps)
 * ADR: docs/adr/0004-tenant-hierarchy-and-grouping.md
 * 
 * PURPOSE:
 * - Support hierarchical tenant structure (region → province → municipality)
 * - Enable tenant classification (pa_comune, pa_provincia, cmpy_, noprof_)
 * - Allow queries across tenant hierarchies (multi-tenant feature for NATAN_LOC and future apps)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Tenant classification
            if (!Schema::hasColumn('tenants', 'tenant_type')) {
                $table->string('tenant_type', 50)
                    ->nullable()
                    ->after('entity_type')
                    ->comment('Specific type: pa_comune, pa_provincia, pa_regione, cmpy_sede, noprof_ong, marketplace');
            }
            
            if (!Schema::hasColumn('tenants', 'tenant_prefix')) {
                $table->string('tenant_prefix', 20)
                    ->nullable()
                    ->after('tenant_type')
                    ->comment('Prefix for grouping: pa_, cmpy_, noprof_, mkt_ (marketplace)');
            }
            
            // Hierarchical structure
            if (!Schema::hasColumn('tenants', 'parent_tenant_id')) {
                $table->foreignId('parent_tenant_id')
                    ->nullable()
                    ->after('tenant_prefix')
                    ->comment('Parent tenant ID (e.g., municipality → province → region)');
            }
            
            if (!Schema::hasColumn('tenants', 'level')) {
                $table->unsignedTinyInteger('level')
                    ->default(0)
                    ->after('parent_tenant_id')
                    ->comment('Hierarchy level: 0=root/marketplace, 1=region, 2=province, 3=municipality, 4=sub-entity');
            }
            
            // ISTAT codes (for PA entities)
            if (!Schema::hasColumn('tenants', 'region_code')) {
                $table->string('region_code', 10)->nullable()->after('level')->comment('ISTAT region code');
            }
            
            if (!Schema::hasColumn('tenants', 'province_code')) {
                $table->string('province_code', 10)->nullable()->after('region_code')->comment('ISTAT province code');
            }
            
            if (!Schema::hasColumn('tenants', 'municipality_code')) {
                $table->string('municipality_code', 10)->nullable()->after('province_code')->comment('ISTAT municipality code');
            }
        });
        
        // Aggiungi FK e indici in separata (Laravel best practice)
        Schema::table('tenants', function (Blueprint $table) {
            // Foreign key constraint
            if (Schema::hasColumn('tenants', 'parent_tenant_id')) {
                // Check if FK exists using DB query (Laravel 11 compatible)
                $fkExists = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'tenants' 
                    AND CONSTRAINT_NAME = 'fk_tenants_parent'
                ");
                
                if (empty($fkExists)) {
                    $table->foreign('parent_tenant_id')
                        ->references('id')
                        ->on('tenants')
                        ->onDelete('set null')
                        ->name('fk_tenants_parent');
                }
            }
            
            // Indexes (OS3.5 Performance Excellence) - Check using DB query
            $indexesToCreate = [
                'idx_tenants_type' => 'tenant_type',
                'idx_tenants_prefix' => 'tenant_prefix',
                'idx_tenants_parent' => 'parent_tenant_id',
                'idx_tenants_level' => 'level',
                'idx_tenants_region' => 'region_code',
                'idx_tenants_province' => 'province_code',
                'idx_tenants_municipality' => 'municipality_code',
            ];
            
            foreach ($indexesToCreate as $indexName => $columnName) {
                $indexExists = DB::select("
                    SELECT INDEX_NAME 
                    FROM information_schema.STATISTICS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'tenants' 
                    AND INDEX_NAME = ?
                ", [$indexName]);
                
                if (empty($indexExists)) {
                    $table->index($columnName, $indexName);
                }
            }
            
            // Compound index (prefix + level)
            $compoundIndexExists = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'tenants' 
                AND INDEX_NAME = 'idx_tenants_prefix_level'
            ");
            
            if (empty($compoundIndexExists)) {
                $table->index(['tenant_prefix', 'level'], 'idx_tenants_prefix_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_tenants_type');
            $table->dropIndex('idx_tenants_prefix');
            $table->dropIndex('idx_tenants_parent');
            $table->dropIndex('idx_tenants_level');
            $table->dropIndex('idx_tenants_prefix_level');
            $table->dropIndex('idx_tenants_region');
            $table->dropIndex('idx_tenants_province');
            $table->dropIndex('idx_tenants_municipality');
            
            // Drop foreign key
            $table->dropForeign('fk_tenants_parent');
            
            // Drop columns
            $table->dropColumn([
                'tenant_type',
                'tenant_prefix',
                'parent_tenant_id',
                'level',
                'region_code',
                'province_code',
                'municipality_code',
            ]);
        });
    }
};

