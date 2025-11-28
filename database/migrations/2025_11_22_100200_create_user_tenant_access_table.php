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
 * @purpose Create user_tenant_access for granular cross-tenant permissions (EGI HUB)
 * 
 * CONTEXT: EGI HUB manages ALL cross-tenant permissions for ALL applications
 * ADR: docs/adr/0002-cross-tenant-access-control.md
 * 
 * PURPOSE:
 * - Grant users access to specific tenants beyond their primary tenant
 * - Support permission levels: read, query, manage, admin
 * - Integrate with Spatie permissions for fine-grained control
 * - Used by NATAN_LOC for multi-tenant RAG queries and future apps
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_tenant_access', function (Blueprint $table) {
            $table->id();
            
            // Relazioni primarie
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who is granted access');
            
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->onDelete('cascade')
                ->comment('Tenant to which access is granted');
            
            // Livello di accesso
            $table->enum('access_level', ['read', 'query', 'manage', 'admin'])
                ->default('query')
                ->comment('read=view only, query=RAG queries (NATAN), manage=CRUD, admin=full control');
            
            // Permessi Spatie (granulari) - JSON array di permission names
            $table->json('spatie_permissions')
                ->nullable()
                ->comment('Array of Spatie permission names: ["tenant.view", "tenant.query.rag", ...]');
            
            // Metadata audit trail
            $table->foreignId('granted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who granted this access (audit trail)');
            
            $table->text('grant_reason')->nullable()->comment('Reason for granting access (GDPR audit)');
            
            // Expiration e attivazione
            $table->timestamp('expires_at')->nullable()->comment('Access expiration date (NULL = no expiration)');
            $table->boolean('is_active')->default(true)->comment('Can be temporarily disabled');
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint (un user non può avere accesso duplicato allo stesso tenant)
            $table->unique(['user_id', 'tenant_id'], 'uk_user_tenant_access');
            
            // Indexes (OS3.5 Performance Excellence)
            $table->index('user_id', 'idx_user_tenant_access_user');
            $table->index('tenant_id', 'idx_user_tenant_access_tenant');
            $table->index(['user_id', 'is_active'], 'idx_user_tenant_access_active');
            $table->index('access_level', 'idx_user_tenant_access_level');
            $table->index('expires_at', 'idx_user_tenant_access_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tenant_access');
    }
};


