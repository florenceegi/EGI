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
        Schema::create('rag_natan.query_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->string('question_hash')->index();
            $table->foreignId('response_id')->nullable()->constrained('rag_natan.responses')->onDelete('cascade');
            $table->string('language', 10)->index();
            $table->string('context_hash')->nullable()->index();
            $table->integer('hit_count')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->boolean('is_stale')->default(false)->index();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_natan.query_cache');
    }
};
