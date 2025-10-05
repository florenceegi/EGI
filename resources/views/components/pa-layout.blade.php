{{--
    Component: pa-layout
    Package: FlorenceEGI PA/Enterprise
    Author: Padmin D. Curtis (AI Partner OS3.0)
    Version: 2.0.0 (Con Livewire Sidebar Scalabile)
    Date: 2025-10-02
    Purpose: Enterprise Blade Component for PA entities using scalable Livewire sidebar

    Usage: <x-pa-layout title="Page Title">content</x-pa-layout>
--}}
@props(['title' => 'Dashboard PA'])

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

    <!-- Material Symbols Outlined -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/components/create-collection-modal.js'])
    @livewireStyles

    <script>
        console.log("PA Layout Loaded");
    </script>

    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif !important;
            background: #F8F9FA;
        }

        /* PA Sidebar Width */
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

        /* PA Logo Area */
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

        .pa-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #D4A574;
            color: #1B365D;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 12px;
        }

        /* PA Menu Items - Oro Accent */
        .drawer-side .collapse summary {
            color: rgba(255, 255, 255, 0.9);
        }

        .drawer-side .collapse summary:hover {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        .drawer-side .collapse[open] summary,
        .drawer-side .bg-primary {
            background: rgba(212, 165, 116, 0.15) !important;
            color: #D4A574 !important;
            border-left: 3px solid #D4A574;
        }

        .drawer-side a:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Main Content Area */
        .drawer-content {
            background: #F8F9FA;
        }

        .pa-header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .pa-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pa-breadcrumb {
            font-size: 14px;
            color: #6B6B6B;
        }

        .pa-user-email {
            font-size: 14px;
            color: #6B6B6B;
        }

        .pa-content {
            padding: 24px;
        }

        .pa-title {
            font-size: 24px;
            font-weight: 700;
            color: #1B365D;
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

        /* Responsive */
        @media (max-width: 640px) {
            .pa-content {
                padding: 16px;
            }

            .pa-title {
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
            <header class="pa-header">
                <div class="pa-header-content">
                    <div class="flex items-center gap-4">
                        <label for="main-drawer" class="btn btn-square btn-ghost lg:hidden">
                            <i class="fas fa-bars"></i>
                        </label>
                        <div class="pa-breadcrumb">{{ $breadcrumb ?? 'Dashboard' }}</div>
                    </div>
                    <div class="pa-user-email">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->email }}
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="pa-content">
                @if (isset($pageTitle))
                    <h1 class="pa-title">{{ $pageTitle }}</h1>
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

                {{ $slot }}
            </main>
        </div>

        <!-- Enterprise Sidebar - Contextual Navigation -->
        <div class="drawer-side">
            <label for="main-drawer" class="drawer-overlay"></label>
            <x-enterprise-sidebar logo="FlorenceEGI" badge="Ente PA" theme="pa" />
        </div>
    </div>

    <!-- Collection Creation Modal (universal terminology system) -->
    @php
        $collectionTerms = \App\Services\Terminology\CollectionTerminologyService::getTerminology(auth()->user());
        $currentRoute = Route::currentRouteName();
        $routeSegments = explode('.', $currentRoute);
        $detectedContext = count($routeSegments) >= 2 ? $routeSegments[0] . '.' . $routeSegments[1] : $routeSegments[0] ?? 'unknown';
    @endphp
    <x-create-collection-modal :terminology="$collectionTerms" />

    @stack('scripts')
    {{ $scripts ?? '' }}
</body>

</html>
