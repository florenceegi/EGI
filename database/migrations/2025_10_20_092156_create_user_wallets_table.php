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
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['algorand', 'iban'])->index();
            $table->string('address')->nullable();
            $table->binary('secret_ciphertext')->nullable();
            $table->binary('secret_nonce')->nullable();
            $table->binary('secret_tag')->nullable();
            $table->binary('dek_encrypted')->nullable();
            $table->text('iban_encrypted')->nullable();
            $table->string('iban_hash', 64)->nullable()->index();
            $table->string('iban_last4', 8)->nullable();
            $table->json('meta')->nullable();
            $table->string('cipher_algo', 32)->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};
