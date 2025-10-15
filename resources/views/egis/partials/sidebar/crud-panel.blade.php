{{-- resources/views/egis/partials/sidebar/crud-panel.blade.php --}}
{{-- 
    Pannello CRUD per editing (solo per creator)
    ORIGINE: righe 129-381 di show.blade.php
    VARIABILI: $egi, $canUpdateEgi, $canDeleteEgi, $isPriceLocked, $canModifyPrice, $displayPrice, $displayUser, $highestPriorityReservation
--}}

{{-- Center: CRUD Box --}}
@if($canUpdateEgi)
<div
        class="overflow-y-auto border-l border-r lg:col-span-3 xl:col-span-2 bg-gradient-to-b from-emerald-900/20 to-emerald-900/10 backdrop-blur-xl border-emerald-700/30">
        {{-- CRUD Box Content --}}
        <div class="p-4 lg:p-6">
        <div
            class="p-6 border bg-gradient-to-br from-emerald-800/20 to-emerald-900/20 rounded-xl border-emerald-700/30">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-emerald-400">
                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    {{ __('egi.crud.edit_egi') }}
                </h3>
                <button id="egi-edit-toggle"
                    class="p-2 transition-colors duration-200 rounded-full text-emerald-400 hover:bg-emerald-800/30"
                    title="{{ __('egi.crud.toggle_edit_mode') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- 🔒 BLOCKCHAIN IMMUTABILITY WARNING --}}
            @if($egi->token_EGI)
            <div class="p-4 mb-4 border rounded-lg border-amber-500/30 bg-amber-500/10">
                <div class="flex items-start space-x-3">
                    <svg class="flex-shrink-0 w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-amber-300">🔒 {{ __('egi.crud.blockchain_warning_title') }}</h4>
                        <p class="mt-1 text-sm text-amber-200/80">
                            {!! __('egi.crud.blockchain_warning_message', ['asa_id' => $egi->token_EGI]) !!}
                        </p>
                        <a href="https://testnet.explorer.perawallet.app/asset/{{ $egi->token_EGI }}" 
                           target="_blank"
                           class="inline-flex items-center mt-2 text-xs font-medium transition-colors text-amber-300 hover:text-amber-200">
                            {{ __('egi.crud.blockchain_verify_link') }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Edit Form --}}
            <form id="egi-edit-form" action="{{ route('egis.update', $egi->id) }}" method="POST"
                class="space-y-4" style="display: none;">
                @csrf
                @method('PUT')

                {{-- Title Field --}}
                <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                    <label for="title" class="block mb-2 text-sm font-medium text-emerald-300">
                        {{ __('egi.crud.title') }}
                        @if($egi->token_EGI)
                        <svg class="inline w-4 h-4 ml-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </label>
                    <input type="text" id="title" name="title"
                        value="{{ old('title', $egi->title) }}"
                        {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                        class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg {{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }}"
                        placeholder="{{ __('egi.crud.title_placeholder') }}" maxlength="60"
                        {{ $egi->token_EGI ? '' : 'required' }}>
                    <div class="mt-1 text-xs {{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }}">
                        @if($egi->token_EGI)
                        🔒 {{ __('egi.crud.field_immutable_hint') }}
                        @else
                        {{ __('egi.crud.title_hint') }}
                        @endif
                    </div>
                </div>

                {{-- Description Field --}}
                <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                    <label for="description"
                        class="block mb-2 text-sm font-medium text-emerald-300">
                        {{ __('egi.crud.description') }}
                        @if($egi->token_EGI)
                        <svg class="inline w-4 h-4 ml-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </label>
                    <textarea id="description" name="description" rows="4"
                        {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                        class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg resize-none {{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }}"
                        placeholder="{{ __('egi.crud.description_placeholder') }}">{{ old('description', $egi->description) }}</textarea>
                    <div class="mt-1 text-xs {{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }}">
                        @if($egi->token_EGI)
                        🔒 {{ __('egi.crud.field_immutable_hint') }}
                        @else
                        {{ __('egi.crud.description_hint') }}
                        @endif
                    </div>
                </div>

                {{-- Price Field --}}
                <div>
                    <label for="price"
                        class="block mb-2 text-sm font-medium text-emerald-300 {{ $isPriceLocked ? 'opacity-60' : '' }}">
                        {{ __('egi.crud.price') }}
                        @if($isPriceLocked)
                        <svg class="inline w-4 h-4 ml-1 text-yellow-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                        @endif
                    </label>
                    <div class="relative">
                        <input type="number" id="price" name="price"
                            value="{{ old('price', $egi->price) }}" step="0.01" min="0" {{
                            $isPriceLocked ? 'disabled readonly' : '' }}
                            class="w-full px-3 py-2 text-white placeholder-gray-400 border rounded-lg {{ $isPriceLocked ? 'bg-black/10 opacity-60 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }}"
                            placeholder="{{ __('egi.crud.price_placeholder') }}">
                        <span
                            class="absolute text-sm {{ $isPriceLocked ? 'text-gray-500' : 'text-gray-400' }} right-3 top-2">ALGO</span>
                        @if($isPriceLocked)
                        <div
                            class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/20">
                            <svg class="w-6 h-6 text-yellow-500 opacity-80" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div
                        class="mt-1 text-xs {{ $isPriceLocked ? 'text-yellow-400' : 'text-gray-400' }}">
                        @if($isPriceLocked)
                        🔒 {{ __('egi.crud.price_locked_message') }}
                        @else
                        {{ __('egi.crud.price_hint') }} (Prezzo base in ALGO)
                        @endif
                    </div>
                </div>

                {{-- Creation Date Field --}}
                <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                    <label for="creation_date"
                        class="block mb-2 text-sm font-medium text-emerald-300">
                        {{ __('egi.crud.creation_date') }}
                        @if($egi->token_EGI)
                        <svg class="inline w-4 h-4 ml-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </label>
                    <input type="date" id="creation_date" name="creation_date"
                        value="{{ old('creation_date', $egi->creation_date?->format('Y-m-d')) }}"
                        {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                        class="w-full px-3 py-2 text-white border rounded-lg {{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }}">
                    <div class="mt-1 text-xs {{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }}">
                        @if($egi->token_EGI)
                        🔒 {{ __('egi.crud.field_immutable_hint') }}
                        @else
                        {{ __('egi.crud.creation_date_hint') }}
                        @endif
                    </div>
                </div>

                {{-- Published Toggle --}}
                <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                    <label class="flex items-center">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" id="is_published" name="is_published" value="1" 
                            {{ old('is_published', $egi->is_published) ? 'checked' : '' }}
                            {{ $egi->token_EGI ? 'disabled' : '' }}
                            class="w-4 h-4 rounded text-emerald-600 {{ $egi->token_EGI ? 'bg-black/10 border-gray-600 cursor-not-allowed' : 'bg-black/30 border-emerald-700/50 focus:ring-emerald-500 focus:ring-2' }}">
                        <span class="ml-3 text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.is_published') }}
                            @if($egi->token_EGI)
                            <svg class="inline w-4 h-4 ml-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </span>
                    </label>
                    <div class="mt-1 text-xs ml-7 {{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }}">
                        @if($egi->token_EGI)
                        🔒 {{ __('egi.crud.field_immutable_hint') }}
                        @else
                        {{ __('egi.crud.is_published_hint')}}
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="inline-flex items-center justify-center flex-1 px-4 py-2 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        {{ __('egi.crud.save_changes') }}
                    </button>

                    @if($canDeleteEgi)
                    <button type="button" id="egi-delete-btn"
                        class="px-4 py-2 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                        title="{{ __('egi.crud.delete_egi') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </button>
                    @endif
                </div>
            </form>

            {{-- View Mode (Default) --}}
            <div id="egi-view-mode" class="space-y-4">
                <div class="p-4 rounded-lg bg-black/20">
                    <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_title') }}
                    </div>
                    <div class="font-medium text-white">{{ $egi->title ?: __('egi.crud.no_title') }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-black/20">
                    <div class="mb-1 text-sm text-emerald-300">
                        {{ $highestPriorityReservation ? __('egi.price.highest_bid') :
                        __('egi.crud.current_price') }}
                    </div>
                    <div class="font-medium text-white">
                        @if($displayPrice)
                        <x-currency-price :price="$displayPrice"
                            :egi="$egi"
                            :reservation="$highestPriorityReservation" />
                        @else
                        {{ __('egi.crud.price_not_set') }}
                        @endif
                    </div>
                    @if($displayUser || $highestPriorityReservation)
                    @php
                    $isWeakReservation = $highestPriorityReservation &&
                    $highestPriorityReservation->type === 'weak';
                    $textColor = $isWeakReservation ? 'text-amber-400' : 'text-emerald-400';
                    @endphp

                    <div class="flex items-center gap-1 mt-2 text-xs {{ $textColor }}">
                        @if ($isWeakReservation)
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('egi.reservation.by') }}: {{ $highestPriorityReservation->fegi_code ??
                        'FG#******' }}
                        @else
                        @php
                        $activatorDisplay = formatActivatorDisplay($displayUser);
                        @endphp

                        @if ($activatorDisplay['is_commissioner'] && $activatorDisplay['avatar'])
                        {{-- Commissioner with avatar --}}
                        <img src="{{ $activatorDisplay['avatar'] }}"
                            alt="{{ $activatorDisplay['name'] }}"
                            class="object-cover w-3 h-3 border rounded-full border-emerald-400/30">
                        @else
                        {{-- Regular collector or commissioner without avatar --}}
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        @endif

                        {{ __('egi.reservation.by') }}: {{ $activatorDisplay['name'] }}
                        @endif
                    </div>
                    @endif
                </div>

                <div class="p-4 rounded-lg bg-black/20">
                    <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_status') }}
                    </div>
                    <div class="flex items-center">
                        <span
                            class="w-2 h-2 rounded-full mr-2 {{ $egi->is_published ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                        <span class="font-medium text-white">
                            {{ $egi->is_published ? __('egi.crud.status_published') :
                            __('egi.crud.status_draft') }}
                        </span>
                    </div>
                </div>

                <button id="egi-edit-start"
                    class="inline-flex items-center justify-center w-full px-4 py-3 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-emerald-600/80 to-emerald-700/80 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    {{ __('egi.crud.start_editing') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif
