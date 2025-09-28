<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/collection.js'])
        <script>console.log('resources/views/layouts/coll.blade.php');</script>

         {{-- Icone di MaterialIcons --}}
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>

        <!-- file di stile in Public/css/collection-detail.css -->

        <!-- Styles -->
        @livewireStyles
    </head>

    <body class="font-sans antialiased bg-base-200">
        <!-- Inizio drawer -->
        <!-- lg:drawer-open => Su schermi lg o maggiori, la sidebar è sempre visibile -->
        <!-- drawer => classe DaisyUI per attivare il layout a scomparsa -->
        <div class="drawer lg:drawer-open">
            <!-- Questo checkbox controlla lo stato del drawer -->
            <input id="main-drawer" type="checkbox" class="drawer-toggle" />

            <div class="flex flex-col min-h-screen drawer-content">

                <livewire:navigation-menu />

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="shadow bg-base-100">
                        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Main Content -->
                <main class="flex-1 p-4 lg:p-8">
                    {{ $slot }}
                </main>
            </div>

            <!-- Sidebar -->
            <!-- Sidebar rimane invariata, semplicemente sta dentro .drawer-side -->
            <livewire:sidebar/>

            @stack('modals')

            @if(app()->environment('local'))
                {!! $debugInfo ?? '' !!}
            @endif
        </div>

        <script>
            window.appConfig = @json(config('app'));
        </script>

        {{-- <script src="{{ url('utils/translations.js') }}"></script> --}}

    </body>

    @include('components.info-footer')

</html>
