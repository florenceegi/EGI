<?php

namespace App\Providers;

use App\Auth\Guards\FegiGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\Collection;
use App\Models\User;

/**
 * @Oracode Service Provider: Extended Auth with FEGI Guard
 * 🎯 Purpose: Register custom FEGI auth guard in Laravel
 * 🧱 Core Logic: Extend Laravel's authentication system
 *
 * @package App\Providers
 * @author Padmin D. Curtis
 * @version 1.1.0 (Fixed Registration Order)
 * @date 2025-05-29
 */
class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

        // Register collection-based permissions gate
        Gate::define('collection-permission', function ($user, $permission, $collection) {
            // Se non autenticato, nega
            if (!$user) {
                return false;
            }

            // Se non è fornita una collection specifica, fallback al controllo globale del permesso
            if (!$collection) {
                return $user->can($permission);
            }

            // Get user's role in this specific collection
            $collectionUser = $collection->users()
                ->where('users.id', $user->id)
                ->first();

            if (!$collectionUser) {
                return false; // User is not part of this collection
            }

            // Get the role from pivot
            $userRole = $collectionUser->pivot->role;

            // Check if the role exists in Spatie and has the permission
            $role = \Spatie\Permission\Models\Role::where('name', $userRole)->first();

            if (!$role) {
                return false; // Role doesn't exist in Spatie
            }

            return $role->hasPermissionTo($permission);
        });

        // Simple test - just check if user can manage this specific collection
        Gate::define('can-manage-collection', function ($user) {
            // Hard-coded for testing - collection ID 1
            $collection = \App\Models\Collection::find(1);
            $collectionUser = $collection->users()->where('users.id', $user->id)->first();
            if (!$collectionUser) return false;
            $userRole = $collectionUser->pivot->role;
            $role = \Spatie\Permission\Models\Role::where('name', $userRole)->first();
            return $role && $role->hasPermissionTo('create_team');
        });

        // Debug: Log che il driver è stato registrato
        // Log::info('FegiGuard driver registered successfully');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        // CRITICAL: Register FEGI auth driver in register() method
        // This ensures it's available BEFORE other service providers boot
        $this->app->extend('auth', function ($auth, $app) {
            $auth->extend('fegi', function ($app, $name, array $config) {
                // Create user provider
                $userProvider = Auth::createUserProvider($config['provider'] ?? 'users');

                // Return new FegiGuard instance with FIXED constructor
                return new FegiGuard(
                    $userProvider,
                    $app['request']  // REMOVED session injection - uses Laravel session() helper
                );
            });

            return $auth;
        });
    }
}