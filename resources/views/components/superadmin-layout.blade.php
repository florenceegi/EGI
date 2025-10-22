{{--
    Component: superadmin-layout
    Package: FlorenceEGI SuperAdmin
    Author: Fabio & AI Partner OS3.0
    Version: 1.0.0
    Date: 2025-10-22
    Purpose: Enterprise Blade Component for SuperAdmin using enterprise sidebar

    Usage: <x-superadmin-layout title="Page Title">content</x-superadmin-layout>
--}}
@props(['title' => 'Dashboard SuperAdmin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - FlorenceEGI</title>

    <!-- IBM Plex Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Material Icons (filled) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        console.log("SuperAdmin Layout Loaded");
    </script>

    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif !important;
            background: #F8F9FA;
        }

        /* SuperAdmin Sidebar Width */
        .drawer-side {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 50;
        }

        .drawer-side aside {
            width: 280px !important;
        }

        .drawer-side aside .border-b {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* SuperAdmin Logo Area */
        .drawer-side aside>div:first-of-type {
            padding: 24px 20px;
            text-align: center;
        }

        .drawer-side aside h1 {
            font-size: 22px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .superadmin-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #FFD700;
            color: #0B1F3A;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 12px;
        }

        /* SuperAdmin Menu Items - Gold Accent */
        .drawer-side .collapse summary {
            color: rgba(255, 255, 255, 0.9);
        }

        .drawer-side .collapse summary:hover {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        .drawer-side .collapse[open] summary,
        .drawer-side .bg-primary {
            background: rgba(255, 215, 0, 0.15) !important;
            color: #FFD700 !important;
            border-left: 3px solid #FFD700;
        }

        .drawer-side a:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Main Content Area */
        .drawer-content {
            background: #F8F9FA;
        }

        .superadmin-header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .superadmin-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .superadmin-breadcrumb {
            font-size: 14px;
            color: #6B6B6B;
        }

        .superadmin-user-email {
            font-size: 14px;
            color: #6B6B6B;
        }

        .superadmin-content {
            padding: 24px;
        }

        .superadmin-title {
            font-size: 24px;
            font-weight: 700;
            color: #0B1F3A;
            margin-bottom: 24px;
        }

        /* Flash Messages */
        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #D1FAE5;
            border: 1px solid #10B981;
            color: #065F46;
        }

        .alert-error {
            background: #FEE2E2;
            border: 1px solid #EF4444;
            color: #991B1B;
        }

        .alert-warning {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .superadmin-content {
                padding: 16px;
            }

            .superadmin-title {
                font-size: 20px;
            }
        }
    </style>

    {{ $styles ?? '' }}
</head>

<body>
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <!-- Main Content -->
        <div class="drawer-content">
            <!-- Header -->
            <header class="superadmin-header">
                <div class="superadmin-header-content">
                    <div class="flex items-center gap-4">
                        <label for="main-drawer" class="btn btn-square btn-ghost lg:hidden">
                            <i class="fas fa-bars"></i>
                        </label>
                        <div class="superadmin-breadcrumb">{{ $breadcrumb ?? 'Dashboard SuperAdmin' }}</div>
                    </div>
                    <div class="superadmin-user-email">
                        <i class="fas fa-crown text-yellow-500"></i> {{ Auth::user()->email }}
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="superadmin-content">
                @if (isset($pageTitle))
                    <h1 class="superadmin-title">{{ $pageTitle }}</h1>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <!-- Enterprise Sidebar - SuperAdmin Context -->
        <div class="drawer-side">
            <label for="main-drawer" class="drawer-overlay"></label>
            <x-enterprise-sidebar logo="FlorenceEGI" badge="SuperAdmin" theme="superadmin" />
        </div>
    </div>

    @stack('scripts')
    {{ $scripts ?? '' }}
</body>

</html>
