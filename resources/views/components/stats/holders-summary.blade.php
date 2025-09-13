{{-- resources/views/components/portfolio/holders-summary.blade.php --}}
@props(['creatorId'])

@php
// Ottieni tutti i holder delle collezioni del creator (per ora solo come owner per semplicità)
$holders = DB::table('reservations')
    ->join('egis', 'egis.id', '=', 'reservations.egi_id')
    ->join('collections', 'collections.id', '=', 'egis.collection_id')
    ->where('collections.creator_id', $creatorId) // Solo collezioni di cui è owner
    ->where('reservations.is_highest', true)
    ->where('reservations.is_current', true)
    ->whereNull('egis.deleted_at')
    ->whereNull('collections.deleted_at')
    ->select([
        'reservations.user_id',
        'collections.collection_name',
        'collections.id as collection_id',
        DB::raw('COUNT(*) as items_count'),
        DB::raw('SUM(reservations.amount_eur) as total_spent')
    ])
    ->groupBy('reservations.user_id', 'collections.id', 'collections.collection_name')
    ->orderBy('total_spent', 'desc')
    ->orderBy('items_count', 'desc')
    ->get();

// Aggrega i dati per utente
$aggregatedHolders = $holders->groupBy('user_id')->map(function($userHoldings) {
    return [
        'user_id' => $userHoldings->first()->user_id,
        'total_items' => $userHoldings->sum('items_count'),
        'total_spent' => $userHoldings->sum('total_spent'),
        'collections_count' => $userHoldings->count(),
        'collections' => $userHoldings->map(function($holding) {
            return [
                'name' => $holding->collection_name,
                'items' => $holding->items_count,
                'spent' => $holding->total_spent
            ];
        })
    ];
})->sortByDesc('total_spent')->values()->take(10); // Aggiungi ->values() per resettare gli indici

// Calcola le statistiche totali
$totalHolders = $aggregatedHolders->count();
$totalItems = $aggregatedHolders->sum('total_items');
$totalVolume = $aggregatedHolders->sum('total_spent');
$uniqueCollections = $holders->unique('collection_id')->count();
@endphp

<div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-white flex items-center space-x-2">
            <span class="material-symbols-outlined">group</span>
            <span>{{ __('creator.portfolio.holders.title') }}</span>
        </h3>
        <div class="text-sm text-gray-300">
            {{ $uniqueCollections }} {{ __('creator.portfolio.holders.collections') }}
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">{{ $totalHolders }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_holders') }}</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">{{ $totalItems }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_items') }}</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-oro-fiorentino">€{{ number_format($totalVolume, 0) }}</div>
            <div class="text-xs text-gray-400">{{ __('creator.portfolio.holders.total_volume') }}</div>
        </div>
    </div>

    {{-- Top Holders List --}}
    @if($aggregatedHolders->isNotEmpty())
        <div class="space-y-3">
            @foreach($aggregatedHolders as $index => $holder)
                @php
                    $user = App\Models\User::find($holder['user_id']);
                @endphp

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
                        @if($user)
                            {{-- Avatar --}}
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                     class="object-cover w-8 h-8 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-500">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            {{-- Name and details --}}
                            <div class="flex-1 min-w-0">
                                @if($user->hasRole('creator'))
                                    <a href="{{ route('creator.home', $user->id) }}"
                                       class="block font-medium text-white truncate transition-colors duration-200 hover:text-blue-300">
                                        {{ $user->name }}
                                    </a>
                                @else
                                    <span class="block font-medium text-white truncate">{{ $user->name }}</span>
                                @endif

                                {{-- Collections summary --}}
                                <div class="text-xs text-gray-400">
                                    {{ $holder['collections_count'] }} {{ $holder['collections_count'] == 1 ? __('creator.portfolio.holders.collection') : __('creator.portfolio.holders.collections') }}
                                    @if($user->usertype === 'verified')
                                        <span class="ml-1 text-blue-400 material-symbols-outlined text-xs">verified</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Fallback per utenti non trovati --}}
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-600 rounded-full">
                                <span class="text-sm text-gray-300 material-symbols-outlined">person</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-gray-400">{{ __('creator.portfolio.holders.unknown_user') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div class="text-right">
                        <div class="font-semibold text-white">{{ $holder['total_items'] }}</div>
                        <div class="text-xs text-gray-400">
                            €{{ number_format($holder['total_spent'], 0) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- View All Link --}}
        @if($totalHolders > 10)
            <div class="mt-4 text-center">
                <button class="text-sm text-blue-400 transition-colors hover:text-blue-300"
                        onclick="showAllHolders()">
                    {{ __('creator.portfolio.holders.view_all') }} ({{ $totalHolders }})
                </button>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="py-8 text-center">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-gray-700 rounded-full">
                <span class="text-xl text-gray-400 material-symbols-outlined">group</span>
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
