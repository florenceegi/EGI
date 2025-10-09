<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Create User Domain Separation Tables (Generated)
 * 🎯 Purpose: Split User model into focused domain tables with personal/org separation
 * 🛡️ Privacy: Enables granular GDPR compliance with separate personal/business data
 * 🧱 Core Logic: Maintains referential integrity while optimizing data categorization
 */
return new class extends Migration
{
    public function up(): void
    {
        // user_profiles table
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('job_role')->nullable();
            $table->string('site_url')->nullable();
            $table->string('facebook')->nullable();
            $table->string('social_x')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('instagram')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('twitch')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('discord')->nullable();
            $table->string('telegram')->nullable();
            $table->string('other')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->text('annotation')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // user_personal_data table
        Schema::create('user_personal_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('province', 10)->nullable();
            $table->string('home_phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_say'])->nullable();
            $table->string('fiscal_code')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->boolean('allow_personal_data_processing')->default(false);
            $table->json('processing_purposes')->nullable();
            $table->timestamp('consent_updated_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
            $table->index('fiscal_code');
        });

        // user_organization_data table
        Schema::create('user_organization_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('org_name')->nullable();
            $table->string('org_email')->nullable();
            $table->string('org_street')->nullable();
            $table->string('org_city')->nullable();
            $table->string('org_region')->nullable();
            $table->string('org_state')->nullable();
            $table->string('org_zip')->nullable();
            $table->string('org_site_url')->nullable();
            $table->string('org_phone_1')->nullable();
            $table->string('org_phone_2')->nullable();
            $table->string('org_phone_3')->nullable();
            $table->string('rea')->nullable();
            $table->string('org_fiscal_code')->nullable();
            $table->string('org_vat_number')->nullable();
            $table->boolean('is_seller_verified')->default(false);
            $table->boolean('can_issue_invoices')->default(false);
            $table->enum('business_type', ['individual', 'sole_proprietorship', 'partnership', 'corporation', 'non_profit', 'pa_entity'])->nullable();
            $table->timestamps();
            $table->unique('user_id');
            $table->index('org_vat_number');
        });

        // user_documents table
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('doc_typo')->nullable();
            $table->string('doc_num')->nullable();
            $table->date('doc_issue_date')->nullable();
            $table->date('doc_expired_date')->nullable();
            $table->string('doc_issue_from')->nullable();
            $table->string('doc_photo_path_f')->nullable();
            $table->string('doc_photo_path_r')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->boolean('is_encrypted')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // user_invoice_preferences table
        Schema::create('user_invoice_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_name')->nullable();
            $table->string('invoice_fiscal_code')->nullable();
            $table->string('invoice_vat_number')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('invoice_city')->nullable();
            $table->string('invoice_country', 2)->nullable();
            $table->boolean('can_issue_invoices')->default(false);
            $table->boolean('auto_request_invoice')->default(false);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_invoice_preferences');
        Schema::dropIfExists('user_documents');
        Schema::dropIfExists('user_organization_data');
        Schema::dropIfExists('user_personal_data');
        Schema::dropIfExists('user_profiles');
    }
};
