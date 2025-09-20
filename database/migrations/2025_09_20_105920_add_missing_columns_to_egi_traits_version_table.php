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
        Schema::table('egi_traits_version', function (Blueprint $table) {
            $table->string('traits_hash', 64)->nullable()->after('traits_json')->comment('Hash dei traits per verifica integrità');
            $table->string('change_reason')->nullable()->after('traits_hash')->comment('Motivo del cambiamento');
            $table->json('changed_fields')->nullable()->after('change_reason')->comment('Campi modificati');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egi_traits_version', function (Blueprint $table) {
            $table->dropColumn(['traits_hash', 'change_reason', 'changed_fields', 'updated_at']);
        });
    }
};
