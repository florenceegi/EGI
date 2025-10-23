<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egis', function (Blueprint $table) {
            $table->id();

            // Relazione con la tabella collections
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Campi esistenti
            $table->unsignedBigInteger('key_file')->nullable()->index();
            $table->string('token_EGI', 255)->nullable();
            $table->json('jsonMetadata')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('auction_id')->nullable()->index();
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->unsignedBigInteger('drop_id')->nullable()->index();
            $table->string('upload_id', 255)->nullable();
            $table->string('creator', 255)->nullable();
            $table->string('owner_wallet', 255)->nullable();
            $table->string('drop_title', 255)->nullable();
            $table->string('title', 255)->index()->nullable();
            $table->text('description')->nullable();
            $table->string('extension', 10)->nullable();
            $table->boolean('media')->default(false)->nullable();
            $table->string('type', 10)->nullable();
            $table->integer('bind')->nullable();
            $table->integer('paired')->nullable();
            $table->decimal('price', 20, 2)->nullable();
            $table->decimal('floorDropPrice', 20, 2)->nullable();
            $table->integer('position')->nullable();
            $table->date('creation_date')->nullable();
            $table->text('size')->nullable();
            $table->text('dimension')->nullable();
            $table->boolean('is_published')->default(false)->nullable();
            $table->boolean('mint')->default(false)->nullable();
            $table->boolean('rebind')->default(true)->nullable();
            $table->text('file_crypt')->nullable();
            $table->text('file_hash')->nullable();
            $table->text('file_IPFS')->nullable();
            $table->text('file_mime')->nullable();

            // Nuovi campi suggeriti
            $table->string('status', 20)->default('draft');
            $table->boolean('hyper')->default(false)->comment('Indicates if this is a hyper EGI');
            $table->boolean('is_public')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // Soft delete
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi');
    }
};
