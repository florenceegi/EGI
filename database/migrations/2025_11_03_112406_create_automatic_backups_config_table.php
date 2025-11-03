<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-03
 * @purpose Creare tabella configurazione backup automatici
 * 
 * CONTESTO: Sistema backup automatico con cron job
 * PERCORSO FILE: database/migrations/2025_11_03_112406_create_automatic_backups_config_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automatic_backups_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->string('interval_type')->default('hours'); // hours, days, weeks
            $table->integer('interval_value')->default(24); // ogni X ore/giorni/settimane
            $table->time('start_time')->default('02:00'); // Ora di inizio backup
            $table->integer('retention_days')->default(30); // Giorni di retention
            $table->integer('max_backups')->default(10); // Numero massimo backup da mantenere
            $table->timestamp('last_backup_at')->nullable();
            $table->timestamp('next_backup_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        // Inserisci configurazione di default
        DB::table('automatic_backups_config')->insert([
            'is_enabled' => false,
            'interval_type' => 'hours',
            'interval_value' => 24,
            'start_time' => '02:00',
            'retention_days' => 30,
            'max_backups' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('automatic_backups_config');
    }
};
