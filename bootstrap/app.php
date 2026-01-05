<?php

use App\Http\Middleware\AuthenticateNatanAgent;
use App\Http\Middleware\CheckCollectionPermission;
use App\Http\Middleware\CheckPendingWallet;
use App\Http\Middleware\CheckUserType;
use App\Http\Middleware\CreatorNicknameRedirect;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnsureSuperadmin;
use App\Http\Middleware\SetLanguage;
use App\Http\Middleware\HandleCors;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use App\Helpers\EarlyEnvironmentHelper;


// 🔐 Carica le variabili di ambiente critiche prima del bootstrap
EarlyEnvironmentHelper::loadCriticalEnvironmentVariables(dirname(__DIR__));

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/menu_dashboard.php',
            __DIR__ . '/../routes/gdpr.php',
            __DIR__ . '/../routes/auth.php',
            __DIR__ . '/../routes/user-domains.php',
            __DIR__ . '/../routes/gdpr_legal.php',
            __DIR__ . '/../routes/creator.php',
            __DIR__ . '/../routes/company.php',
            __DIR__ . '/../routes/biography.php',
            __DIR__ . '/../routes/archetips.php',
            __DIR__ . '/../routes/superadmin.php'
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'collection_can'       => CheckCollectionPermission::class,
            'role'                 => RoleMiddleware::class,
            'permission'           => PermissionMiddleware::class,
            'role_or_permission'   => RoleOrPermissionMiddleware::class,
            'check.pending.wallet' => CheckPendingWallet::class,
            'check.user.type'      => CheckUserType::class,
            'creator.nickname'     => CreatorNicknameRedirect::class,
            'natan.agent'          => AuthenticateNatanAgent::class,
            'superadmin'           => EnsureSuperadmin::class,
        ]);

        $middleware->web(replace: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class => EncryptCookies::class,
        ]);

        $middleware->appendToGroup('web', [SetLanguage::class]);

        // P0 FIX: Exclude Stripe Webhooks from CSRF to prevent 419 Errors & Redirects
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'api/webhooks/*',
            'api/webhooks/stripe',
            'stripe/webhook'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // definisci eventuali eccezioni qui
    })
    ->create();
