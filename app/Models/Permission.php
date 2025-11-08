<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
    return;
}

try {
    $manageRoles = Permission::query()->firstOrCreate([
        'name' => 'manage_roles',
        'guard_name' => 'web',
    ]);

    $manageIcons = Permission::query()->firstOrCreate([
        'name' => 'manage_icons',
        'guard_name' => 'web',
    ]);

    $createCollection = Permission::query()->firstOrCreate([
        'name' => 'create_collection',
        'guard_name' => 'web',
    ]);

    $adminRole = Role::query()->firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);
    $adminRole->givePermissionTo(
        $manageRoles,
        $manageIcons,
        $createCollection,
    );

    $editorRole = Role::query()->firstOrCreate([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);
    $editorRole->givePermissionTo($manageIcons);
} catch (QueryException $exception) {
    if ($exception->getCode() !== '23000') {
        throw $exception;
    }
}