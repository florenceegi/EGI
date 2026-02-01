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
        Schema::table('egi_blockchain', function (Blueprint $table) {
            $table->json('shipping_address_snapshot')->nullable()->after('reservation_id');
            $table->string('tracking_code')->nullable()->after('shipping_address_snapshot');
            $table->string('carrier')->nullable()->after('tracking_code');
            $table->timestamp('shipped_at')->nullable()->after('carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egi_blockchain', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_address_snapshot',
                'tracking_code',
                'carrier',
                'shipped_at'
            ]);
        });
    }
};
