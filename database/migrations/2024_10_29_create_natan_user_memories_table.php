<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('natan_user_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('memory_content'); // Il contenuto della memoria
            $table->string('memory_type')->default('general'); // general, preference, context, fact
            $table->text('keywords')->nullable(); // Keywords per ricerca semantica
            $table->integer('usage_count')->default(0); // Quante volte è stata usata
            $table->timestamp('last_used_at')->nullable(); // Ultima volta che è stata recuperata
            $table->boolean('is_active')->default(true); // Per disattivare senza cancellare
            $table->timestamps();

            // Indici per performance
            $table->index('user_id');
            $table->index(['user_id', 'is_active']);
            $table->index('memory_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('natan_user_memories');
    }
};
