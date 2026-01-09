<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add project_id to tenants table
 * 
 * Links tenants to their parent project (NATAN_LOC, NATAN_COMPANY, etc.)
 * Only multitenant projects have tenants.
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2026-01-09
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('system_project_id')->nullable()->after('id');
            $table->foreign('system_project_id')->references('id')->on('system_projects')->onDelete('restrict');
            $table->index('system_project_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['system_project_id']);
            $table->dropIndex(['system_project_id']);
            $table->dropColumn('system_project_id');
        });
    }
};
