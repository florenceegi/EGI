<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add tenant_id to users table for multi-tenant support
 * 
 * This migration adds the tenant_id foreign key to associate users
 * with their primary tenant in the multi-tenant architecture.
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-02
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tenants')
                    ->nullOnDelete()
                    ->comment('Primary tenant for this user');
                
                $table->index('tenant_id', 'idx_users_tenant');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                // Drop foreign key first
                $table->dropForeign(['tenant_id']);
                $table->dropIndex('idx_users_tenant');
                $table->dropColumn('tenant_id');
            }
        });
    }
};
