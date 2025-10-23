<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds reference_message_id to enable iterative elaborations:
     * When a user asks to simplify/deepen/transform a previous response,
     * we track which message they're elaborating on.
     */
    public function up(): void
    {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_message_id')->nullable()->after('content');
            $table->foreign('reference_message_id')
                ->references('id')
                ->on('natan_chat_messages')
                ->nullOnDelete();
            $table->index('reference_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('natan_chat_messages', function (Blueprint $table) {
            $table->dropForeign(['reference_message_id']);
            $table->dropIndex(['reference_message_id']);
            $table->dropColumn('reference_message_id');
        });
    }
};
