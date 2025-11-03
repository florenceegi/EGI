<?php

use App\Notifications\NotificationExpired;
use App\Services\Notifications\Wallets\CeckAndSetExpired;
use App\Services\Notifications\Wallets\CheckAndSetExpired;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\CustomDatabaseNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

### 📌 1️⃣ COMANDO ARTISAN: NOTIFICHE ATTIVE ###
Artisan::command('notifications:summary', function () {
    $pendingCount = CustomDatabaseNotification::where('outcome', 'pending')->count();
    $expiringSoon = CustomDatabaseNotification::where('outcome', 'pending')
        ->where('created_at', '<', Carbon::now()->subHours(config('app.notifications.expiration_hours', 72) - 5))
        ->count();

    $this->info("📌 Notifiche in sospeso: {$pendingCount}");
    $this->info("⏳ Notifiche che scadranno nelle prossime 5 ore: {$expiringSoon}");
})->purpose('Mostra un riepilogo delle notifiche attive')->hourly();


### 📌 2️⃣ JOB AUTOMATICO: SCADENZA DELLE NOTIFICHE ###
Schedule::call(function () {
    app()->make(CheckAndSetExpired::class)->checkAndSetExpired();
})
    ->name('check-and-set-expired')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();

### 📌 3️⃣ JOB AUTOMATICO: NOTIFICHE SCADUTE ###
Schedule::command('reservations:process-rankings')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/rankings.log'));

### 📌 4️⃣ JOB AUTOMATICO: EGI ORACLE POLLING ###
// Polling Oracle per SmartContract EGI Living (ogni 5 minuti)
Schedule::command('oracle:poll')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->when(function () {
        // Attiva solo se il feature flag è abilitato
        return config('egi_living.feature_flags.oracle_polling_enabled', false);
    })
    ->appendOutputTo(storage_path('logs/oracle.log'));

### 📌 5️⃣ JOB AUTOMATICO: EXCHANGE RATE UPDATE ###
// Update USD to EUR exchange rate daily for AI credits pricing
Schedule::command('ai-credits:update-exchange-rate')
    ->daily()
    ->at('02:00') // Run at 2 AM (when ECB updates rates)
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/exchange-rate.log'));

### 📌 6️⃣ JOB AUTOMATICO: UNIFIED CONTEXT CLEANUP ###
// Cleanup expired chunks from natan_unified_context table
// TTL: acts 30 days, web 6 hours, memory 7 days, files 90 days
Schedule::command('natan:cleanup-unified-context --force')
    ->hourly() // Run every hour to keep DB lean
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/unified-context-cleanup.log'));

### 📌 7️⃣ JOB AUTOMATICO: FEATURED/HYPER SCHEDULING ###
// Activate scheduled Featured/Hyper slots (daily at 00:01)
Schedule::call(function () {
    app()->make(\App\Services\FeaturedSchedulingService::class)->activateScheduledSlots();
})
    ->name('featured-activate-slots')
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/featured-scheduling.log'));

// Deactivate expired Featured/Hyper slots (daily at 23:59)
Schedule::call(function () {
    app()->make(\App\Services\FeaturedSchedulingService::class)->deactivateExpiredSlots();
})
    ->name('featured-deactivate-slots')
    ->dailyAt('23:59')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/featured-scheduling.log'));

### 📌 8️⃣ JOB AUTOMATICO: GIFT EGILI EXPIRATION ###
// Expire Gift Egili (daily at 00:05)
Schedule::call(function () {
    app()->make(\App\Services\EgiliService::class)->expireGiftEgili();
})
    ->name('egili-expire-gifts')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/egili-expiration.log'));

### 📌 9️⃣ JOB AUTOMATICO: BATCH CHARGE CONSUMPTION DEBT ###
// Batch charge pending feature consumption debt (daily at 02:00)
Schedule::command('egili:batch-charge-consumption')
    ->name('egili-batch-charge-consumption')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/consumption-batch-charge.log'));
