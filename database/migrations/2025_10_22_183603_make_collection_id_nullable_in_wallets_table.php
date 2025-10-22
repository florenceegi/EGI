<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Makes collection_id nullable in wallets table to support:
     * 1. User wallets created during registration (before collection exists)
     * 2. Standalone wallets not yet linked to a collection
     * 3. Wallet encryption during user signup flow
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // 1. Drop existing foreign key constraint
            $table->dropForeign(['collection_id']);
            
            // 2. Make collection_id nullable
            $table->unsignedBigInteger('collection_id')->nullable()->change();
            
            // 3. Re-add foreign key constraint with nullable support
            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // 1. Drop the nullable foreign key
            $table->dropForeign(['collection_id']);
            
            // 2. Make collection_id NOT NULL again
            $table->unsignedBigInteger('collection_id')->nullable(false)->change();
            
            // 3. Re-add foreign key constraint
            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onDelete('cascade');
        });
    }
};
