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
        Schema::create('notification_payload_shippings', function (Blueprint $table) {
            $table->id();
            
            // Link to the purchase (Blockchain Record)
            $table->unsignedBigInteger('egi_blockchain_id');
            $table->foreign('egi_blockchain_id')->references('id')->on('egi_blockchain')->onDelete('cascade');
            
            // Participants
            $table->unsignedBigInteger('seller_id'); // Who receives the "Sold" notification
            $table->unsignedBigInteger('buyer_id');  // Who bought (for reference)
            
            // Shipping Data (Snapshot for the notification context)
            $table->json('shipping_address_snapshot')->nullable();
            
            // Response Data (Tracking)
            $table->string('carrier')->nullable();
            $table->string('tracking_code')->nullable();
            $table->timestamp('shipped_at')->nullable();
            
            // Status
            $table->string('status')->default('pending'); // pending, shipped
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_payload_shippings');
    }
};
