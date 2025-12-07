<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Changes binary columns to text for PostgreSQL compatibility.
     * Binary data will be stored as base64-encoded strings.
     */
    public function up(): void {
        Schema::table('wallets', function (Blueprint $table) {
            // Change from binary (bytea) to text for PostgreSQL compatibility
            // Data is stored as base64-encoded strings
            $table->text('secret_ciphertext')->nullable()->change();
            $table->text('secret_nonce')->nullable()->change();
            $table->text('secret_tag')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('wallets', function (Blueprint $table) {
            // Revert to binary columns
            $table->binary('secret_ciphertext')->nullable()->change();
            $table->binary('secret_nonce')->nullable()->change();
            $table->binary('secret_tag')->nullable()->change();
        });
    }
};
