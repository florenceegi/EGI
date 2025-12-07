<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - PostgreSQL Migration)
 * @date 2025-12-06
 * @purpose Change ip_address from inet to varchar for PostgreSQL compatibility
 *          The ip_address is masked for GDPR compliance (e.g., 127.0.0.xxx)
 *          which is not valid for PostgreSQL inet type
 */
return new class extends Migration {
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void {
        // Check if we're using PostgreSQL
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Change inet to varchar(45)
            // 45 chars is enough for IPv6 mapped IPv4 addresses
            DB::statement('ALTER TABLE user_activities ALTER COLUMN ip_address TYPE varchar(45)');
        } else {
            // MySQL/MariaDB: Change to VARCHAR(45)
            Schema::table('user_activities', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Change back to inet (will fail if data contains masked IPs)
            // First, clean up any masked IPs
            DB::statement("UPDATE user_activities SET ip_address = NULL WHERE ip_address LIKE '%xxx%' OR ip_address LIKE '%xxxx%'");
            DB::statement('ALTER TABLE user_activities ALTER COLUMN ip_address TYPE inet USING ip_address::inet');
        } else {
            // MySQL/MariaDB: Keep as VARCHAR, ipAddress() method creates VARCHAR anyway
            Schema::table('user_activities', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->change();
            });
        }
    }
};
