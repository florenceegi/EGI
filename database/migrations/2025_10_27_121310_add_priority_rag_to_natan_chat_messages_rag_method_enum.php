<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Add 'priority_rag' to the enum values of 'rag_method' column
     * in natan_chat_messages table.
     *
     * CONTEXT: Projects System FASE 4 - Priority RAG implementation
     * The new value 'priority_rag' represents 3-tier weighted search:
     * - TIER 1: Project documents (weight 1.0)
     * - TIER 2: Chat history (weight 0.8)
     * - TIER 3: PA acts (weight 0.5)
     */
    public function up(): void {
        // MariaDB/MySQL: ALTER TABLE MODIFY COLUMN to change enum values
        DB::statement("
            ALTER TABLE `natan_chat_messages`
            MODIFY COLUMN `rag_method` ENUM('semantic', 'keyword', 'none', 'priority_rag') NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Remove 'priority_rag' from enum (data will be set to NULL if exists)
        DB::statement("
            UPDATE `natan_chat_messages`
            SET `rag_method` = NULL
            WHERE `rag_method` = 'priority_rag'
        ");

        DB::statement("
            ALTER TABLE `natan_chat_messages`
            MODIFY COLUMN `rag_method` ENUM('semantic', 'keyword', 'none') NULL
        ");
    }
};
