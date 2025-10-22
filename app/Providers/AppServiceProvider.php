<?php

namespace App\Providers;

use App\Auth\Guards\FegiGuard;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Repositories\IconRepository;
use App\Services\FileStorageService;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Collection;
use App\Policies\ProfilePolicy;
use App\Policies\TeamWalletPolicy as WalletPolicy;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\CustomDatabaseChannel;
use App\Services\CollectionService;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Services\Notifications\WalletService;
use App\View\Components\EppHighlight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\CollectorCarouselService;
use Ultra\UltraLogManager\UltraLogManager;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Services\Gdpr\LegalContentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Spatie\ImageOptimizer\OptimizerChain;
use App\Services\ImageOptimizationManager;
use App\Contracts\ImageOptimizationManagerInterface;

class AppServiceProvider extends ServiceProvider {

    protected $policies = [
        User::class => ProfilePolicy::class,
        Wallet::class => WalletPolicy::class,
        // Egi::class => EgiPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void {


        $this->app->singleton(IconRepository::class);
        $this->app->singleton(CollectorCarouselService::class);

        // Singletone di Ultralogmanager
        // $this->app->singleton(UltraLogManager::class);

        // Registra il servizio di storage dei file
        $this->app->singleton(FileStorageService::class, function ($app) {
            return new FileStorageService();
        });

        $this->app->singleton(IconRepository::class, function ($app) {
            return new IconRepository(
                $app->make(UltraLogManager::class)
            );
        });

        // Register enhanced RegisteredUserController with complete DI
        $this->app->bind(RegisteredUserController::class, function ($app) {
            return new RegisteredUserController(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class),
                $app->make(ConsentService::class),
                $app->make(AuditLogService::class),
                $app->make(CollectionService::class),
                $app->make(WalletServiceInterface::class),
                $app->make(UserRoleServiceInterface::class),
                $app->make(LegalContentService::class),
                $app->make(\App\Services\Auth\AuthRedirectService::class),
                $app->make(\App\Services\Wallet\WalletProvisioningService::class)
            );
        });

        // Register enhanced AuthenticatedSessionController with complete DI
        $this->app->bind(AuthenticatedSessionController::class, function ($app) {
            return new AuthenticatedSessionController(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class),
                $app->make(ConsentService::class),
                $app->make(AuditLogService::class),
                $app->make(\App\Services\Auth\AuthRedirectService::class)
            );
        });

        // Register ImageOptimizationManager with proper DI
        $this->app->bind(ImageOptimizationManagerInterface::class, function ($app) {
            return new ImageOptimizationManager(
                $app->make(UltraLogManager::class),
                $app->make(ErrorManagerInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {

        // Debug log (only in local environment)
        // if (app()->environment('local')) {
        //     Log::channel('florenceegi')->info('FEGI Guard registered early in AppServiceProvider::register() with FIXED session access');
        // }

        // 🎯 SOLUZIONE DEFINITIVA: Forziamo il sistema a usare sempre
        //    una catena di ottimizzatori VUOTA, bypassando la logica della Factory.
        // $this->app->bind(OptimizerChain::class, fn () => new OptimizerChain()); // <-- 2. AGGIUNGI QUESTA RIGA


        // 🎯 2. AGGIUNGI QUESTO BLOCCO DI CODICE QUI
        Event::listen(JobFailed::class, function (JobFailed $event) {
            Log::channel('florenceegi')->error('--- JOB FAILED: DETAILED REPORT (L11) ---', [
                'connection' => $event->connectionName,
                'job' => $event->job->resolveName(),
                'exception_class' => get_class($event->exception),
                'exception_message' => $event->exception->getMessage(),
                'exception_file' => $event->exception->getFile(),
                'exception_line' => $event->exception->getLine(),
                'payload' => $event->job->payload(),
                'exception_trace' => $event->exception->getTraceAsString(),
            ]);
        });

        // Override Fortify's default login handling
        Fortify::authenticateUsing(function ($request) {
            // Custom authentication logic if needed
            return null; // Let Fortify handle normally
        });

        // Use custom views
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Blade::component('epp-highlight', EppHighlight::class);
        Blade::component('collections-carousel', \App\View\Components\CollectionsCarousel::class);
        Blade::component('environmental-stats', \App\View\Components\EnvironmentalStats::class);
        Blade::component('egi-carousel', \App\View\Components\EgiCarousel::class);
        Blade::component('egi-stat-card', \App\View\Components\EgiStatCard::class);
        Blade::component('EgiStatsSection', \App\View\Components\EgiStatsSection::class);
        Blade::component('gdpr-layout', \App\View\Components\GdprLayout::class);
        Blade::component('responsive-image', \App\View\Components\ResponsiveImage::class);
        Blade::component('universal-search-modal', \App\View\Components\UniversalSearchModal::class);
        Blade::component('app-layout', \App\View\Components\AppLayout::class);
        Blade::component('environmental-stats', \App\View\Components\EnvironmentalStats::class);

        // Registriamo un driver nominato "custom_database"
        Notification::extend('custom_database', function ($app) {
            return new CustomDatabaseChannel();
        });

        // Register GDPR middleware
        $this->app['router']->aliasMiddleware('gdpr.consent', \App\Http\Middleware\GdprConsentMiddleware::class);

        // Configure rate limiters for enhanced registration
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(2)->by($request->ip()); // Stricter for ecosystem setup
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Enhanced registration rate limiter
        RateLimiter::for('enhanced-registration', function (Request $request) {
            return [
                Limit::perMinute(2)->by($request->ip()),
                Limit::perHour(10)->by($request->ip()), // Daily limit for ecosystem creation
            ];
        });

        // Collection creation rate limiter
        RateLimiter::for('collection-creation', function (Request $request) {
            return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
        });
    }
}
