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
        Schema::create('natan_chat_messages', function (Blueprint $table) {
            $table->id();
            
            // User & Session
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'natan_chat_messages_user_id_foreign')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->index('user_id');
            
            $table->string('session_id', 100)->index(); // Group messages by conversation session
            
            // Message Content
            $table->enum('role', ['user', 'assistant'])->index();
            $table->text('content'); // The actual message text
            
            // Persona Information (for assistant messages)
            $table->string('persona_id', 50)->nullable()->index(); // 'strategic', 'technical', etc.
            $table->string('persona_name', 100)->nullable(); // Display name
            $table->decimal('persona_confidence', 3, 2)->nullable(); // 0.00 to 1.00
            $table->enum('persona_selection_method', ['manual', 'auto', 'keyword', 'ai', 'default'])->nullable();
            $table->text('persona_reasoning')->nullable(); // Why this persona was chosen
            
            // Alternative Personas (JSON array of alternatives)
            $table->json('persona_alternatives')->nullable(); // [{'persona_id': 'technical', 'confidence': 0.75}, ...]
            
            // RAG Context (for assistant messages)
            $table->json('rag_sources')->nullable(); // Array of act IDs that were used
            $table->unsignedInteger('rag_acts_count')->default(0); // Number of acts retrieved
            $table->enum('rag_method', ['semantic', 'keyword', 'none'])->nullable(); // Which RAG strategy was used
            
            // API Metadata (for assistant messages)
            $table->string('ai_model', 100)->nullable(); // e.g., 'claude-3-5-sonnet-20241022'
            $table->unsignedInteger('tokens_input')->nullable();
            $table->unsignedInteger('tokens_output')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable(); // Milliseconds
            
            // Analytics
            $table->boolean('was_helpful')->nullable(); // User feedback (thumbs up/down)
            $table->text('user_feedback')->nullable(); // Optional user comment
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['user_id', 'session_id', 'created_at']);
            $table->index(['persona_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('natan_chat_messages');
    }
};
