<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('natan_unified_context', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->index()->comment('UUID per raggruppare risultati di una query');
            $table->text('content')->comment('Testo del chunk (max 4000 char + 500 overlap)');
            // Usa JSON per embedding (compatibile con MariaDB)
            $table->json('embedding')->comment('OpenAI text-embedding-3-small vector (1536 dimensions)');
            $table->enum('source_type', ['act', 'web', 'memory', 'file'])->index()->comment('Tipo di fonte dati');
            $table->unsignedBigInteger('source_id')->nullable()->comment('FK a egis/messages/project_documents');
            $table->string('source_url', 1000)->nullable()->comment('URL citabile della fonte');
            $table->string('source_title', 500)->comment('Titolo della fonte per citazione');
            $table->json('metadata')->nullable()->comment('Data, author, protocol, etc.');
            $table->decimal('similarity_score', 5, 4)->nullable()->comment('Cosine similarity con query (0-1)');
            $table->timestamp('expires_at')->comment('TTL: acts 30d, web 6h, memory 7d, files 90d');
            $table->timestamps();

            // Indici compositi per performance
            $table->index(['session_id', 'similarity_score']);
            $table->index(['source_type', 'expires_at']);
            $table->index(['expires_at']); // Per cleanup automatico
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('natan_unified_context');
    }
};
