<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add project_id to natan_chat_messages
 *
 * Link chat messages to projects for context-aware RAG
 *
 * @package FlorenceEGI
 * @subpackage Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Link chat history to projects for searchable knowledge base
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            // Add project_id foreign key (nullable - existing chats have no project)
            $table->foreignId('project_id')
                ->nullable()
                ->after('user_id')
                ->constrained('projects')
                ->onDelete('set null'); // If project deleted, keep chat but orphan it

            // Index for project chat history search
            $table->index('project_id');
            $table->index(['project_id', 'user_id', 'created_at'], 'idx_chat_project_user_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex('natan_chat_messages_project_id_index');
            $table->dropIndex('idx_chat_project_user_date');
            $table->dropColumn('project_id');
        });
    }
};
