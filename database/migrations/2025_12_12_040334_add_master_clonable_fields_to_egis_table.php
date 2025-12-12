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
        Schema::table('egis', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id')->comment('Points to Master EGI if this is a child');
            $table->boolean('is_template')->default(false)->after('parent_id')->comment('True if this EGI is a Master Template');
            $table->boolean('is_sellable')->default(true)->after('is_template')->comment('False for Master Templates');
            $table->string('serial_number')->nullable()->after('is_sellable')->comment('Unique serial for children');
            
            $table->foreign('parent_id')->references('id')->on('egis')->nullOnDelete();
            $table->index('is_template');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_template', 'is_sellable', 'serial_number']);
        });
    }
};
