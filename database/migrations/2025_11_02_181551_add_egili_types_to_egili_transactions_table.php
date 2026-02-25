<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Foundation)
 * @date 2025-11-02
 * @purpose Extend egili_transactions to support Lifetime vs Gift Egili with expiration and priority logic
 */
return new class extends Migration {
      /**
       * Run the migrations.
       */
      public function up(): void {
            Schema::table('egili_transactions', function (Blueprint $table) {
                  // Egili Type (lifetime vs gift)
                  $table->enum('egili_type', ['lifetime', 'gift'])
                        ->default('lifetime')
                        ->after('status')
                        ->comment('Type of Egili: lifetime (obtained via AI package purchase — ToS v3.0.0) or gift (platform-granted with expiration)');

                  // Expiration for Gift Egili
                  $table->timestamp('expires_at')
                        ->nullable()
                        ->after('egili_type')
                        ->comment('Expiration date for gift Egili (NULL for lifetime)');

                  $table->boolean('is_expired')
                        ->default(false)
                        ->after('expires_at')
                        ->comment('Flag to mark gift Egili as expired (cron job)');

                  // Admin Grant (for manual gift grants)
                  $table->unsignedBigInteger('granted_by_admin_id')
                        ->nullable()
                        ->after('is_expired')
                        ->comment('Admin user who manually granted gift Egili');

                  $table->string('grant_reason', 255)
                        ->nullable()
                        ->after('granted_by_admin_id')
                        ->comment('Reason for gift grant (e.g., contest winner, contribution reward)');

                  // Priority for spend logic
                  $table->integer('priority_order')
                        ->default(0)
                        ->after('grant_reason')
                        ->comment('Priority for spend logic: Gift first (expiring first), then Lifetime FIFO');

                  // Foreign Key
                  $table->foreign('granted_by_admin_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');

                  // Indexes for performance
                  $table->index('egili_type', 'idx_egili_type');
                  $table->index(['expires_at', 'is_expired'], 'idx_expires_at');
                  $table->index('priority_order', 'idx_priority');
            });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void {
            Schema::table('egili_transactions', function (Blueprint $table) {
                  // Drop indexes
                  $table->dropIndex('idx_egili_type');
                  $table->dropIndex('idx_expires_at');
                  $table->dropIndex('idx_priority');

                  // Drop foreign key
                  $table->dropForeign(['granted_by_admin_id']);

                  // Drop columns
                  $table->dropColumn([
                        'egili_type',
                        'expires_at',
                        'is_expired',
                        'granted_by_admin_id',
                        'grant_reason',
                        'priority_order',
                  ]);
            });
      }
};
