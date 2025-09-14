{{-- resources/views/components/stats/platform-holders-summary.blade.php --}}
@props(['limit' => 10])

@php
// Ottieni tutti gli holders della piattaforma
$holders = DB::table('reservations')
    ->join('egis', 'egis.id', '=', 'reservations.egi_id')
    ->join('collections', 'collections.id', '=', 'egis.collection_id')
    ->join('users', 'users.id', '=', 'reservations.user_id')
    ->where('reservations.is_highest', true)
    ->where('reservations.is_current', true)
    ->whereNull('reservations.superseded_by_id')
    ->whereNull('egis.deleted_at')
    ->whereNull('collections.deleted_at')
    ->select([
        'reservations.user_id',
        'users.name as user_name',
        'users.profile_photo_path',
        'users.usertype',
        'collections.collection_name',
        'collections.id as collection_id',
        'collections.creator_id',
        DB::raw('COUNT(*) as items_count'),
        DB::raw('SUM(reservations.offer_amount_fiat) as total_spent')
    ])
    ->groupBy('reservations.user_id', 'users.name', 'users.profile_photo_path', 'users.usertype', 'collections.id', 'collections.collection_name', 'collections.creator_id')
    ->orderBy('total_spent', 'desc')
    ->orderBy('items_count', 'desc')
    ->get();

// Aggrega i dati per utente
$aggregatedHolders = $holders->groupBy('user_id')->map(function($userHoldings) {
    $userInfo = $userHoldings->first();
    return [
        'user_id' => $userInfo->user_id,
        'user_name' => $userInfo->user_name,
        'profile_photo_path' => $userInfo->profile_photo_path,
        'usertype' => $userInfo->usertype,
        'total_items' => $userHoldings->sum('items_count'),
        'total_spent' => $userHoldings->sum('total_spent'),
        'collections_count' => $userHoldings->count(),
        'creators_supported' => $userHoldings->unique('creator_id')->count(),
        'collections' => $userHoldings->map(function($holding) {
            return [
                'name' => $holding->collection_name,
                'items' => $holding->items_count,
                'spent' => $holding->total_spent,
                'creator_id' => $holding->creator_id
            ];
        })
    ];
})->sortByDesc('total_spent')->values();

// Calcola le statistiche totali
$totalHolders = $aggregatedHolders->count();
$totalItems = $aggregatedHolders->sum('total_items');
$totalVolume = $aggregatedHolders->sum('total_spent');
$uniqueCollections = $holders->unique('collection_id')->count();
$uniqueCreators = $holders->unique('creator_id')->count();

// Genera un ID unico per questo componente
$componentId = 'platform-holders-' . uniqid();
@endphp

<div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
    {{-- Header con Toggle --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="flex items-center space-x-2 text-xl font-semibold text-white">
            <span class="material-symbols-outlined">leaderboard</span>
            <span>{{ __('platform.holders.title') }}</span>
        </h3>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-300">
                {{ $totalHolders }} {{ __('platform.holders.total_collectors') }}
            </div>
            {{-- Toggle Button --}}
            <button id="{{ $componentId }}-toggle" type="button"
                class="flex items-center p-2 text-white transition-all duration-300 rounded-lg bg-verde-rinascita hover:bg-verde-rinascita-dark focus:outline-none focus:ring-2 focus:ring-verde-rinascita focus:ring-offset-2 focus:ring-offset-gray-900"
                aria-label="{{ __('platform.holders.toggle_view') }}"
                aria-expanded="false">
                <span class="text-lg material-symbols-outlined">expand_more</span>
            </button>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4">
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">{{ $totalHolders }}</div>
            <div class="text-xs text-gray-400">{{ __('platform.holders.total_holders') }}</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">{{ $totalItems }}</div>
            <div class="text-xs text-gray-400">{{ __('platform.holders.total_items') }}</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">€{{ number_format($totalVolume, 0) }}</div>
            <div class="text-xs text-gray-400">{{ __('platform.holders.total_volume') }}</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">{{ $uniqueCreators }}</div>
            <div class="text-xs text-gray-400">{{ __('platform.holders.supported_creators') }}</div>
        </div>
    </div>

    {{-- Compact View (Default) --}}
    <div id="{{ $componentId }}-compact" class="transition-all duration-300">
        @if($aggregatedHolders->isNotEmpty())
            <div class="space-y-3">
                @foreach($aggregatedHolders->take($limit) as $index => $holder)
                    <div class="flex items-center p-3 transition-colors bg-white rounded-lg bg-opacity-5 hover:bg-opacity-10">
                        {{-- Ranking Badge --}}
                        <div class="flex-shrink-0 mr-3">
                            @if($index < 3)
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $index === 0 ? 'bg-yellow-500 text-yellow-900' : '' }}
                                    {{ $index === 1 ? 'bg-gray-300 text-gray-800' : '' }}
                                    {{ $index === 2 ? 'bg-amber-600 text-amber-100' : '' }}">
                                    {{ $index + 1 }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">{{ $index + 1 }}</span>
                            @endif
                        </div>

                        {{-- User Info --}}
                        <div class="flex items-center flex-1 min-w-0 space-x-3">
                            {{-- Avatar --}}
                            @php
                                $user = App\Models\User::find($holder['user_id']);
                                $profilePhotoUrl = $user ? $user->profile_photo_url : null;
                            @endphp
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="{{ $holder['user_name'] }}"
                                     class="object-cover w-8 h-8 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-500">
                                    {{ strtoupper(substr($holder['user_name'] ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            {{-- Name and details --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('collector.home', $holder['user_id']) }}"
                                   class="block font-medium text-white truncate transition-colors duration-200 hover:text-blue-300">
                                    {{ $holder['user_name'] }}
                                </a>

                                {{-- Quick stats --}}
                                <div class="text-xs text-gray-400">
                                    {{ $holder['collections_count'] }} {{ $holder['collections_count'] == 1 ? __('platform.holders.collection') : __('platform.holders.collections') }}
                                    • {{ $holder['creators_supported'] }} {{ __('platform.holders.creators') }}
                                    @if($holder['usertype'] === 'verified')
                                        <span class="ml-1 text-xs text-blue-400 material-symbols-outlined">verified</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="text-right min-w-[120px]">
                            @php
                                $percentage = $totalItems > 0 ? round(($holder['total_items'] / $totalItems) * 100, 1) : 0;

                                // Determina il gradiente in base alla percentuale
                                if ($percentage >= 50) {
                                    $gradientColors = 'from-green-400 to-emerald-600'; // Verde per alte percentuali
                                } elseif ($percentage >= 25) {
                                    $gradientColors = 'from-yellow-400 to-orange-500'; // Giallo-Arancione per medie percentuali
                                } elseif ($percentage >= 10) {
                                    $gradientColors = 'from-blue-400 to-indigo-600'; // Blu per basse percentuali
                                } else {
                                    $gradientColors = 'from-gray-400 to-gray-600'; // Grigio per percentuali molto basse
                                }
                            @endphp
                            <div class="font-semibold text-white">{{ $holder['total_items'] }}</div>
                            <div class="mb-2 text-xs text-gray-400">
                                €{{ number_format($holder['total_spent'], 0) }}
                            </div>
                            {{-- Barra percentuale con gradiente dinamico --}}
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400 font-medium">{{ $percentage }}%</span>
                                </div>
                                <div class="w-full h-3 bg-gray-700 rounded-full shadow-inner">
                                    <div class="bg-gradient-to-r {{ $gradientColors }} h-3 rounded-full transition-all duration-500 shadow-lg"
                                         style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-8 text-center">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-gray-700 rounded-full">
                    <span class="text-xl text-gray-400 material-symbols-outlined">leaderboard</span>
                </div>
                <h4 class="mb-1 text-sm font-medium text-white">{{ __('platform.holders.no_holders_yet') }}</h4>
                <p class="text-xs text-gray-400">{{ __('platform.holders.no_holders_message') }}</p>
            </div>
        @endif
    </div>

    {{-- Extended View (Hidden by default) --}}
    <div id="{{ $componentId }}-extended" class="hidden transition-all duration-300">
        @if($aggregatedHolders->isNotEmpty())
            <div class="space-y-4">
                @foreach($aggregatedHolders as $index => $holder)
                    <div class="p-4 transition-colors bg-white rounded-lg bg-opacity-5 hover:bg-opacity-10">
                        {{-- User Header --}}
                        <div class="flex items-center mb-3 space-x-3">
                            {{-- Ranking Badge --}}
                            <div class="flex-shrink-0">
                                @if($index < 3)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                        {{ $index === 0 ? 'bg-yellow-500 text-yellow-900' : '' }}
                                        {{ $index === 1 ? 'bg-gray-300 text-gray-800' : '' }}
                                        {{ $index === 2 ? 'bg-amber-600 text-amber-100' : '' }}">
                                        {{ $index + 1 }}
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-gray-300 bg-gray-600 rounded-full">
                                        {{ $index + 1 }}
                                    </div>
                                @endif
                            </div>

                            {{-- Avatar --}}
                            @php
                                $user = App\Models\User::find($holder['user_id']);
                                $profilePhotoUrl = $user ? $user->profile_photo_url : null;
                            @endphp
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="{{ $holder['user_name'] }}"
                                     class="object-cover w-10 h-10 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-10 h-10 text-lg font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-500">
                                    {{ strtoupper(substr($holder['user_name'] ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            {{-- User Info --}}
                            <div class="flex-1">
                                <a href="{{ route('collector.home', $holder['user_id']) }}"
                                   class="block text-lg font-semibold text-white transition-colors duration-200 hover:text-blue-300">
                                    {{ $holder['user_name'] }}
                                    @if($holder['usertype'] === 'verified')
                                        <span class="ml-1 text-sm text-blue-400 material-symbols-outlined">verified</span>
                                    @endif
                                </a>
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm text-gray-400">
                                        {{ $holder['total_items'] }} {{ __('platform.holders.egis_owned') }}
                                        • €{{ number_format($holder['total_spent'], 0) }} {{ __('platform.holders.invested') }}
                                    </div>
                                    {{-- Barra percentuale con gradiente dinamico --}}
                                    @php
                                        $percentage = $totalItems > 0 ? round(($holder['total_items'] / $totalItems) * 100, 1) : 0;

                                        // Determina il gradiente in base alla percentuale
                                        if ($percentage >= 50) {
                                            $gradientColors = 'from-green-400 to-emerald-600'; // Verde per alte percentuali
                                        } elseif ($percentage >= 25) {
                                            $gradientColors = 'from-yellow-400 to-orange-500'; // Giallo-Arancione per medie percentuali
                                        } elseif ($percentage >= 10) {
                                            $gradientColors = 'from-blue-400 to-indigo-600'; // Blu per basse percentuali
                                        } else {
                                            $gradientColors = 'from-gray-400 to-gray-600'; // Grigio per percentuali molto basse
                                        }
                                    @endphp
                                    <div class="flex items-center flex-1 space-x-4">
                                        <div class="flex-1 h-3 bg-gray-700 rounded-full">
                                            <div class="bg-gradient-to-r {{ $gradientColors }} h-3 rounded-full transition-all duration-500 shadow-lg"
                                                 style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-300 min-w-[3rem] font-medium">{{ $percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Collections Breakdown --}}
                        <div class="space-y-2 ml-14">
                            <h5 class="text-sm font-medium text-gray-300">{{ __('platform.holders.collections_breakdown') }}:</h5>
                            <div class="space-y-2">
                                @foreach($holder['collections']->sortByDesc('spent') as $collection)
                                    @php
                                        // Calcola la percentuale rispetto al totale dell'holder
                                        $collectionPercentage = $holder['total_items'] > 0 ? round(($collection['items'] / $holder['total_items']) * 100, 1) : 0;

                                        // Determina il gradiente in base alla percentuale della collezione
                                        if ($collectionPercentage >= 70) {
                                            $collectionGradient = 'from-emerald-400 to-green-600';
                                        } elseif ($collectionPercentage >= 40) {
                                            $collectionGradient = 'from-blue-400 to-cyan-600';
                                        } elseif ($collectionPercentage >= 20) {
                                            $collectionGradient = 'from-purple-400 to-pink-600';
                                        } else {
                                            $collectionGradient = 'from-orange-400 to-red-600';
                                        }
                                    @endphp
                                    <div class="p-2 space-y-1 bg-white rounded bg-opacity-5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-white truncate">{{ $collection['name'] }}</span>
                                            <span class="text-sm text-gray-400">
                                                {{ $collection['items'] }} (€{{ number_format($collection['spent'], 0) }})
                                            </span>
                                        </div>
                                        {{-- Barra percentuale per collezione --}}
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1 bg-gray-600 rounded-full h-1.5">
                                                <div class="bg-gradient-to-r {{ $collectionGradient }} h-1.5 rounded-full transition-all duration-500"
                                                     style="width: {{ min($collectionPercentage, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400 min-w-[2.5rem]">{{ $collectionPercentage }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('{{ $componentId }}-toggle');
    const compactView = document.getElementById('{{ $componentId }}-compact');
    const extendedView = document.getElementById('{{ $componentId }}-extended');
    const toggleIcon = toggleButton.querySelector('.material-symbols-outlined');
    let isExtended = false;

    toggleButton.addEventListener('click', function() {
        isExtended = !isExtended;

        if (isExtended) {
            // Mostra vista estesa
            compactView.classList.add('hidden');
            extendedView.classList.remove('hidden');
            toggleIcon.textContent = 'expand_less';
            toggleButton.setAttribute('aria-expanded', 'true');
        } else {
            // Mostra vista compatta
            extendedView.classList.add('hidden');
            compactView.classList.remove('hidden');
            toggleIcon.textContent = 'expand_more';
            toggleButton.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
@endpush
