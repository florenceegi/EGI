<?php

namespace App\Http;

use App\Http\Middleware\CheckTeamPermission;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // ... altri middleware
        // 'team' => CheckTeamPermission::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        // 'web' => [
        //     // ... altri middleware
        //     // \App\Http\Middleware\DisableCache::class,
        //     SetLanguage::class,
        //     RoleOrPermissionMiddleware::class, // Assicurati che questo sia presente
        // ],
    ];
}
