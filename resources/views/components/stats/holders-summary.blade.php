{{-- resources/views/components/portfolio/holders-summary.blade.php --}}
@props(['creatorId', 'period' => 'month'])

@php
    use App\Services\StatisticsService;

    // Utilizza il servizio per ottenere i dati dei holders con periodo temporale
    $statisticsService = app(StatisticsService::class);
    $holdersData = $statisticsService->getCreatorHoldersStats($creatorId, $period);

    $holders = $holdersData['holders'] ?? [];
    $aggregatedHolders = collect($holdersData['aggregated'] ?? []);
    $summary = $holdersData['summary'] ?? [
        'total_collectors' => 0,
        'total_items_held' => 0,
        'total_revenue' => 0,
        'avg_per_collector' => 0,
    ];

    // Usa i dati dal summary invece di calcoli locali
    $totalHolders = $summary['total_collectors'];
    $totalItems = $summary['total_items_held'];
    $totalVolume = $summary['total_revenue'];
    $uniqueCollections = count($holders); // Numero di collezioni diverse
@endphp

<div class="rounded-xl bg-white bg-opacity-10 p-6 backdrop-blur-md">
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <h3 class="flex items-center space-x-2 text-xl font-semibold text-white">
            <span class="material-symbols-outlined">group</span>
            <span>{{ __('creator.portfolio.holders.title') }}</span>
        </h3>
        <div class="text-sm text-gray-300">
            {{ $uniqueCollections }} {{ __('creator.portfolio.holders.collections') }}
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="mb-6 grid grid-cols-3 gap-4">
        <div class="text-center">
            <div class="text-oro-fiorentino text-lg font-bold">{{ $totalHolders }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_holders') }}</div>
        </div>
        <div class="text-center">
            <div class="text-oro-fiorentino text-lg font-bold">{{ $totalItems }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_items') }}</div>
        </div>
        <div class="text-center">
            <div class="text-oro-fiorentino text-lg font-bold">€{{ number_format($totalVolume, 0) }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_volume') }}</div>
        </div>
    </div>

    {{-- Top Holders List --}}
    @if ($aggregatedHolders->isNotEmpty())
        <div class="space-y-3">
            @foreach ($aggregatedHolders->take(10) as $index => $holder)
                @php
                    $user = App\Models\User::find($holder['user_id'] ?? null);
                @endphp

                <div
                    class="flex items-center rounded-lg bg-white bg-opacity-5 p-3 transition-colors hover:bg-opacity-10">
                    {{-- Ranking Badge --}}
                    <div class="mr-3 flex-shrink-0">
                        @if ($index < 3)
                            <div
                                class="{{ $index === 0 ? 'bg-yellow-500 text-yellow-900' : '' }} {{ $index === 1 ? 'bg-gray-300 text-gray-800' : '' }} {{ $index === 2 ? 'bg-amber-600 text-amber-100' : '' }} flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold">
                                {{ $index + 1 }}
                            </div>
                        @else
                            <span class="text-sm text-gray-400">{{ $index + 1 }}</span>
                        @endif
                    </div>

                    {{-- User Info --}}
                    <div class="flex min-w-0 flex-1 items-center space-x-3">
                        @if ($user)
                            {{-- Avatar --}}
                            @if ($user->profile_photo_url ?? false)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                    class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-sm font-bold text-white">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            {{-- Name and details --}}
                            <div class="min-w-0 flex-1">
                                @php
                                    // Switcher per la route corretta basata sul usertype dell'holder
$holderRoute = match ($user->usertype ?? 'creator') {
    'creator' => route('creator.home', $user->id),
    'collector' => route('collector.home', $user->id),
    'commissioner' => route(
        'profile.show',
    ), // Commissioner non ha pagina pubblica specifica
    default => route('creator.home', $user->id), // Fallback a creator
                                    };
                                @endphp

                                <a href="{{ $holderRoute }}"
                                    class="block truncate font-medium text-white transition-colors duration-200 hover:text-blue-300">
                                    {{ $user->name }}
                                </a>

                                {{-- Collections summary --}}
                                <div class="text-xs text-gray-400">
                                    {{ $holder['collections_count'] ?? 0 }}
                                    {{ ($holder['collections_count'] ?? 0) == 1 ? __('creator.portfolio.holders.collection') : __('creator.portfolio.holders.collections') }}
                                    @if (($user->usertype ?? '') === 'verified')
                                        <span
                                            class="material-symbols-outlined ml-1 text-xs text-blue-400">verified</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Fallback per utenti non trovati --}}
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-600">
                                <span class="material-symbols-outlined text-sm text-gray-300">person</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-gray-400">{{ __('creator.portfolio.holders.unknown_user') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div class="text-right">
                        @php
                            $holderItems = $holder['total_items'] ?? 0;
                            $holderSpent = $holder['total_spent'] ?? 0;
                            $percentage = $totalItems > 0 ? round(($holderItems / $totalItems) * 100, 1) : 0;

                            // Determina il gradiente in base alla percentuale
                            if ($percentage >= 50) {
                                $gradientColors = 'from-green-400 to-emerald-600';
                            } elseif ($percentage >= 25) {
                                $gradientColors = 'from-yellow-400 to-orange-500';
                            } elseif ($percentage >= 10) {
                                $gradientColors = 'from-blue-400 to-indigo-600';
                            } else {
                                $gradientColors = 'from-gray-400 to-gray-600';
                            }
                        @endphp
                        <div class="font-semibold text-white">{{ $holderItems }}</div>
                        <div class="mb-2 text-xs text-gray-400">
                            €{{ number_format($holderSpent, 0) }}
                        </div>
                        {{-- Barra percentuale con gradiente dinamico --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">{{ $percentage }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-700">
                                <div class="{{ $gradientColors }} h-2 rounded-full bg-gradient-to-r shadow-sm transition-all duration-500"
                                    style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- View All Link --}}
        @if ($totalHolders > 10)
            <div class="mt-4 text-center">
                <button class="text-sm text-blue-400 transition-colors hover:text-blue-300" onclick="showAllHolders()">
                    {{ __('creator.portfolio.holders.view_all') }} ({{ $totalHolders }})
                </button>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="py-8 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-700">
                <span class="material-symbols-outlined text-xl text-gray-400">group</span>
            </div>
            <h4 class="mb-1 text-sm font-medium text-white">{{ __('creator.portfolio.holders.no_holders_yet') }}</h4>
            <p class="text-xs text-gray-400">{{ __('creator.portfolio.holders.no_holders_message') }}</p>
        </div>
    @endif
</div>

<script>
    function showAllHolders() {
        // Implementa la logica per mostrare tutti gli holder (modal o nuova pagina)
        console.log('Show all holders modal/page');
    }
</script>
