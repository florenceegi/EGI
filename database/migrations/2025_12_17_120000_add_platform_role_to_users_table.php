<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'platform_role')) {
                $table->string('platform_role')->nullable()->after('usertype');
            }
            if (!Schema::hasColumn('users', 'terms')) {
                $table->boolean('terms')->default(false)->after('usertype');
            }
            if (!Schema::hasColumn('users', 'gdpr_consents_given_at')) {
                $table->timestamp('gdpr_consents_given_at')->nullable()->after('terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'platform_role')) {
                $table->dropColumn('platform_role');
            }
            if (Schema::hasColumn('users', 'terms')) {
                $table->dropColumn('terms');
            }
            if (Schema::hasColumn('users', 'gdpr_consents_given_at')) {
                $table->dropColumn('gdpr_consents_given_at');
            }
        });
    }
};
