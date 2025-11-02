<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Pricing & Promotions)
 * @date 2025-11-02
 * @purpose Create feature_promotions table for managing promotional discounts
 * 
 * Supports:
 * - Temporal promotions (Black Friday, etc)
 * - Feature-specific or global promotions
 * - Bundle promotions
 * - Usage limits (total and per-user)
 * - Stats tracking (uses, savings)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_promotions', function (Blueprint $table) {
            $table->id();
            
            // === PROMO IDENTITY ===
            $table->string('promo_code', 50)->unique()
                ->comment('Unique promo code (e.g., BLACK_FRIDAY_2025)');
            
            $table->string('promo_name', 255)
                ->comment('Display name (e.g., "Black Friday Sale")');
            
            $table->text('promo_description')->nullable()
                ->comment('Marketing description for users');
            
            // === SCOPE ===
            $table->boolean('is_global')->default(false)
                ->comment('True if applies to all features, false if feature-specific');
            
            $table->string('feature_code', 100)->nullable()
                ->comment('Specific feature code (NULL if global)');
            
            $table->foreign('feature_code')
                ->references('feature_code')
                ->on('ai_feature_pricing')
                ->onDelete('cascade');
            
            $table->string('feature_category', 100)->nullable()
                ->comment('Apply to all features in category (e.g., "ai_services")');
            
            // === DISCOUNT ===
            $table->enum('discount_type', ['percentage', 'fixed_amount'])
                ->default('percentage')
                ->comment('Discount type: percentage (e.g., 50%) or fixed Egili amount');
            
            $table->decimal('discount_value', 10, 2)
                ->comment('Discount value: percentage (0-100) or fixed Egili amount');
            
            // === TEMPORAL ===
            $table->timestamp('start_at')
                ->nullable()
                ->comment('Promo start date/time');
            
            $table->timestamp('end_at')
                ->nullable()
                ->comment('Promo end date/time');
            
            // === LIMITS ===
            $table->integer('max_uses')->nullable()
                ->comment('Max total uses of this promo (NULL = unlimited)');
            
            $table->integer('max_uses_per_user')->nullable()
                ->comment('Max uses per single user (NULL = unlimited)');
            
            $table->integer('current_uses')->default(0)
                ->comment('Current total uses count');
            
            // === DISPLAY ===
            $table->boolean('is_active')->default(true)
                ->comment('Promo currently active (can be disabled manually)');
            
            $table->boolean('is_featured')->default(false)
                ->comment('Show prominently in UI (e.g., homepage banner)');
            
            $table->string('badge_text', 50)->nullable()
                ->comment('Badge text to show (e.g., "-50% BLACK FRIDAY")');
            
            // === ADMIN ===
            $table->foreignId('created_by_admin_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Admin who created this promo');
            
            $table->text('admin_notes')->nullable()
                ->comment('Internal admin notes');
            
            // === STATS ===
            $table->bigInteger('total_egili_saved')->default(0)
                ->comment('Total Egili saved by users using this promo');
            
            $table->integer('total_purchases_with_promo')->default(0)
                ->comment('Total number of purchases that used this promo');
            
            // === TIMESTAMPS ===
            $table->timestamps();
            
            // === INDEXES ===
            $table->index(['is_active', 'start_at', 'end_at'], 'idx_active_dates');
            $table->index('feature_code', 'idx_feature_code');
            $table->index('is_global', 'idx_global');
            $table->index('is_featured', 'idx_featured');
            $table->index('promo_code', 'idx_promo_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_promotions');
    }
};
