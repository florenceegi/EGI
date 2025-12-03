<?php

namespace Ultra\EgiModule\Providers;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\EgiModule\Handlers\EgiUploadHandler;
use Ultra\EgiModule\Http\Controllers\EgiUploadController;
use Ultra\EgiModule\Services\UserRoleService;
use Ultra\EgiModule\Services\WalletService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use App\Services\CollectionService;
use App\Contracts\IpfsServiceInterface;

/**
 * @Oracode ServiceProvider: EgiModule Enhanced DI Configuration (Corrected)
 * 🎯 Purpose: Registers all EGI module services with proper dependency injection
 * 🧱 Core Logic: Updated to support service-based EgiUploadHandler architecture
 * 🔧 Enhancement: Removed error code registration (handled in config/error-manager.php)
 *
 * @package Ultra\EgiModule\Providers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.1.0
 * @date 2025-05-25
 * @changelog 2.1.0 - Removed error code registration, cleaned up boot method
 */
class EgiModuleServiceProvider extends ServiceProvider {
    /**
     * Log channel for this provider
     * @var string
     */
    protected string $logChannel = 'upload';

    /**
     * Register any application services.
     * Enhanced to support service-based EgiUploadHandler
     */
    public function register(): void {
        // 1. Register interface bindings first
        $this->app->bind(WalletServiceInterface::class, WalletService::class);
        $this->app->bind(UserRoleServiceInterface::class, UserRoleService::class);

        // 2. Register WalletService with dependencies
        $this->app->bind(WalletService::class, function ($app) {
            return new WalletService(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class)
            );
        });

        // 3. Register UserRoleService with dependencies
        $this->app->bind(UserRoleServiceInterface::class, function ($app) {
            return new UserRoleService(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class)
            );
        });

        // 4. Register CollectionService with enhanced dependencies
        $this->app->bind(CollectionService::class, function ($app) {
            return new CollectionService(
                $app->make(UltraLogManager::class),
                $app->make(ErrorManagerInterface::class),
                $app->make(WalletServiceInterface::class),
                $app->make(UserRoleServiceInterface::class)
            );
        });

        // 5. Register EgiUploadHandler with SERVICE-BASED DEPENDENCIES
        $this->app->singleton(EgiUploadHandler::class, function ($app) {
            return new EgiUploadHandler(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class),
                $app->make(CollectionService::class),           // Service injection
                $app->make(WalletServiceInterface::class),       // Service injection
                $app->make(UserRoleServiceInterface::class),     // Service injection
                $app->make(\App\Contracts\ImageOptimizationManagerInterface::class),  // Image optimization service
                $app->make(IpfsServiceInterface::class)          // IPFS pinning service
            );
        });

        // 6. Register EgiUploadController with enhanced handler
        $this->app->singleton(EgiUploadController::class, function ($app) {
            return new EgiUploadController(
                $app->make(ErrorManagerInterface::class),
                $app->make(UltraLogManager::class),
                $app->make(Factory::class)
            );
        });

        // 7. Create convenient aliases
        $this->app->alias(CollectionService::class, 'egi.collection_service');
        $this->app->alias(UserRoleServiceInterface::class, 'egi.user_role_service');
        $this->app->alias(WalletServiceInterface::class, 'egi.wallet_service');
        $this->app->alias(EgiUploadHandler::class, 'egi.upload_handler');

        // 8. Log successful registration in development
        if ($this->app->environment('local', 'testing')) {
            // Log::channel($this->logChannel)->info('EgiModule services registered with enhanced DI', [
            //     'services' => [
            //         'EgiUploadHandler' => 'service-based architecture',
            //         'CollectionService' => 'full DI integration',
            //         'WalletService' => 'interface-based',
            //         'UserRoleService' => 'interface-based'
            //     ],
            //     'error_handling' => 'config/error-manager.php compliant'
            // ]);
        }
    }

    /**
     * Bootstrap any application services.
     * Enhanced and cleaned up for service-based architecture
     */
    public function boot(): void {
        // Load views with EGI module namespace
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'egimodule');

        // Load routes for EGI module
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        // Publishing configuration for console environments
        if ($this->app->runningInConsole()) {

            // Publish EGI module configuration
            $this->publishes([
                __DIR__ . '/../../config/egi.php' => config_path('egi.php'),
            ], 'egi-config');

            // Publish EGI module views
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/egimodule'),
            ], 'egi-views');

            // Publish EGI module assets
            $this->publishes([
                __DIR__ . '/../../resources/assets' => public_path('vendor/egimodule'),
            ], 'egi-assets');

            // Publish enhanced storage configuration
            $this->publishes([
                __DIR__ . '/../../config/storage.php' => config_path('egi-storage.php'),
            ], 'egi-storage-config');

            // Publish error translation files
            $this->publishes([
                __DIR__ . '/../../resources/lang/it/error-manager.php' => resource_path('lang/it/error-manager.php'),
                __DIR__ . '/../../resources/lang/en/error-manager.php' => resource_path('lang/en/error-manager.php'),
            ], 'egi-error-translations');
        }

        // Boot service-specific configurations
        $this->bootServiceConfigurations();

        // Note: Error codes are now defined in config/error-manager.php
        // No longer registered here to follow proper configuration pattern
    }

    /**
     * Boot service-specific configurations
     * Enhanced configuration for service-based architecture
     */
    protected function bootServiceConfigurations(): void {
        // Configure storage defaults for EGI uploads
        $this->app['config']->set('egi.storage.disks', ['public', 's3']);
        $this->app['config']->set('egi.storage.critical_disks', ['public']);
        $this->app['config']->set('egi.storage.visibility.public', 'public');
        $this->app['config']->set('egi.storage.visibility.s3', 'public');

        // Configure default EGI settings
        $this->app['config']->set('egi.default_floor_price', 0.0);
        $this->app['config']->set('egi.max_file_size', 50 * 1024 * 1024); // 50MB
        $this->app['config']->set('egi.max_collections_per_user', 10);

        // Configure service-specific settings
        $this->app['config']->set('egi.services.collection.auto_create_default', true);
        $this->app['config']->set('egi.services.wallet.auto_attach_defaults', true);
        $this->app['config']->set('egi.services.role.auto_assign_creator', true);

        // Configure enhanced error handling
        $this->app['config']->set('egi.error_handling.use_localized_messages', true);
        $this->app['config']->set('egi.error_handling.fallback_locale', 'en');
        $this->app['config']->set('egi.error_handling.log_service_errors', true);

        // Log configuration in development
        if ($this->app->environment('local', 'testing')) {
            // Log::channel($this->logChannel)->debug('EgiModule service configurations booted', [
            //     'storage_disks' => $this->app['config']->get('egi.storage.disks'),
            //     'critical_disks' => $this->app['config']->get('egi.storage.critical_disks'),
            //     'default_floor_price' => $this->app['config']->get('egi.default_floor_price'),
            //     'max_collections_per_user' => $this->app['config']->get('egi.max_collections_per_user'),
            //     'services_enabled' => [
            //         'collection_auto_create' => $this->app['config']->get('egi.services.collection.auto_create_default'),
            //         'wallet_auto_attach' => $this->app['config']->get('egi.services.wallet.auto_attach_defaults'),
            //         'role_auto_assign' => $this->app['config']->get('egi.services.role.auto_assign_creator')
            //     ],
            //     'error_handling' => [
            //         'localized_messages' => $this->app['config']->get('egi.error_handling.use_localized_messages'),
            //         'fallback_locale' => $this->app['config']->get('egi.error_handling.fallback_locale')
            //     ]
            // ]);
        }
    }

    /**
     * Get the services provided by the provider.
     * Enhanced list including new service dependencies
     *
     * @return array
     */
    public function provides(): array {
        return [
            EgiUploadHandler::class,
            EgiUploadController::class,
            CollectionService::class,
            WalletServiceInterface::class,
            UserRoleServiceInterface::class,
            WalletService::class,
            UserRoleService::class,
            'egi.collection_service',
            'egi.user_role_service',
            'egi.wallet_service',
            'egi.upload_handler'
        ];
    }
}
