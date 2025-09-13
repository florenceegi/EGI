{{-- resources/views/components/collection/holders-list.blade.php --}}
@props(['collection'])

@php
// Ottieni tutti i holder della collezione basandosi sulle prenotazioni valide
$holders = DB::table('reservations')
    ->join('egis', 'egis.id', '=', 'reservations.egi_id')
    ->where('egis.collection_id', $collection->id)
    ->where('reservations.is_highest', true)
    ->where('reservations.is_current', true)
    ->whereNull('egis.deleted_at')
    ->select([
        'reservations.user_id',
        DB::raw('COUNT(*) as items_count'),
        DB::raw('SUM(reservations.amount_eur) as total_spent')
    ])
    ->groupBy('reservations.user_id')
    ->orderBy('items_count', 'desc')
    ->orderBy('total_spent', 'desc')
    ->get();

// Calcola le statistiche
$totalItems = $collection->egis_count ?? 0;
$totalHolders = $holders->count();
$totalVolume = $holders->sum('total_spent');
@endphp

<div class="space-y-6">
    {{-- Header con statistiche --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="p-4 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold text-white">{{ $totalHolders }}</div>
            <div class="text-sm text-gray-400">{{ __('collection.holders.total_holders') }}</div>
        </div>

        <div class="p-4 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold text-white">{{ $totalItems }}</div>
            <div class="text-sm text-gray-400">{{ __('collection.holders.total_items') }}</div>
        </div>

        <div class="p-4 bg-gray-800 rounded-lg">
            <div class="text-2xl font-bold text-white">€{{ number_format($totalVolume, 2) }}</div>
            <div class="text-sm text-gray-400">{{ __('collection.holders.total_volume') }}</div>
        </div>
    </div>

    {{-- Lista Holders --}}
    <div class="overflow-hidden bg-gray-800 rounded-lg">
        @if($holders->isNotEmpty())
            {{-- Header tabella --}}
            <div class="grid grid-cols-12 gap-4 p-4 text-sm font-medium text-gray-400 border-b border-gray-700">
                <div class="col-span-1 text-center">#</div>
                <div class="col-span-6 sm:col-span-5">{{ __('collection.holders.holder') }}</div>
                <div class="col-span-2 text-center">{{ __('collection.holders.items') }}</div>
                <div class="col-span-3 text-right sm:col-span-2">{{ __('collection.holders.percentage') }}</div>
                <div class="hidden text-right sm:block sm:col-span-2">{{ __('collection.holders.total_spent') }}</div>
            </div>

            {{-- Lista holders --}}
            @foreach($holders as $index => $holder)
                @php
                    $percentage = $totalItems > 0 ? round(($holder->items_count / $totalItems) * 100, 1) : 0;
                    $user = App\Models\User::find($holder->user_id);
                @endphp

                <div class="grid grid-cols-12 gap-4 p-4 transition-colors border-b hover:bg-gray-700/50 border-gray-700/50 last:border-b-0">
                    {{-- Ranking --}}
                    <div class="flex items-center justify-center col-span-1">
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

                    {{-- Holder Info --}}
                    <div class="flex items-center col-span-6 space-x-3 sm:col-span-5">
                        @if($user)
                            {{-- Avatar --}}
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                     class="object-cover w-8 h-8 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-500">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif                            {{-- Nome e link --}}
                            <div class="flex-1 min-w-0">
                                @if($user->hasRole('creator'))
                                    <a href="{{ route('creator.home', $user->id) }}"
                                       class="block font-medium text-white truncate transition-colors duration-200 hover:text-blue-300">
                                        {{ $user->name }}
                                    </a>
                                @else
                                    <span class="block font-medium text-white truncate">{{ $user->name }}</span>
                                @endif

                                {{-- Badge se verificato --}}
                                @if($user->usertype === 'verified')
                                    <span class="inline-flex items-center">
                                        <span class="text-sm text-blue-400 material-symbols-outlined">verified</span>
                                    </span>
                                @endif
                            </div>
                        @else
                            {{-- Fallback per utenti non trovati --}}
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-600 rounded-full">
                                <span class="text-sm text-gray-300 material-symbols-outlined">person</span>
                            </div>
                            <span class="text-gray-400">{{ __('collection.holders.unknown_user') }}</span>
                        @endif
                    </div>

                    {{-- Items Count --}}
                    <div class="flex items-center justify-center col-span-2">
                        <span class="font-semibold text-white">{{ $holder->items_count }}</span>
                    </div>

                    {{-- Percentage --}}
                    <div class="flex items-center justify-end col-span-3 sm:col-span-2">
                        <div class="text-right">
                            <div class="font-semibold text-white">{{ $percentage }}%</div>
                            {{-- Barra percentuale --}}
                            <div class="w-full bg-gray-700 rounded-full h-1.5 mt-1">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-1.5 rounded-full transition-all duration-300"
                                     style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Spent (desktop only) --}}
                    <div class="items-center justify-end hidden sm:flex sm:col-span-2">
                        <span class="font-medium text-gray-300">€{{ number_format($holder->total_spent, 2) }}</span>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Empty state --}}
            <div class="p-12 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full">
                    <span class="text-2xl text-gray-400 material-symbols-outlined">group</span>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-white">{{ __('collection.holders.no_holders_yet') }}</h3>
                <p class="text-gray-400">{{ __('collection.holders.no_holders_message') }}</p>
            </div>
        @endif
    </div>
</div>
