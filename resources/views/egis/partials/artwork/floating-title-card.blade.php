{{-- resources/views/egis/partials/artwork/floating-title-card.blade.php --}}
{{--
    Card floating con titolo e azioni rapide
    ORIGINE: righe 124-175 di show.blade.php (Floating Title Card)
    VARIABILI: $egi, $collection, $isCreator
--}}

{{-- Floating Title Card - Responsive Positioning --}}
<div
    class="absolute bottom-2 left-2 right-2 sm:bottom-3 sm:left-3 sm:right-3 md:bottom-4 md:left-4 md:right-4 lg:bottom-6 lg:left-6 lg:right-6">
    <div
        class="rounded-md border border-white/5 bg-black/10 p-2 shadow-2xl backdrop-blur-sm sm:rounded-lg sm:p-3 md:p-3.5 lg:p-4">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
                <h1
                    class="mb-1 truncate text-base font-bold tracking-tight text-white drop-shadow-2xl sm:text-lg md:text-xl lg:text-2xl xl:text-3xl">
                    {{ $egi->title }}</h1>
                <div
                    class="flex items-center space-x-1.5 text-[10px] text-gray-100 drop-shadow-lg sm:space-x-2 sm:text-xs md:text-xs lg:text-sm">
                    <a href="{{ route('home.collections.show', $collection->id) }}"
                        class="max-w-[120px] truncate font-medium transition-colors duration-200 hover:text-white sm:max-w-[150px] md:max-w-none">
                        {{ $collection->collection_name }}
                    </a>
                    <span class="h-0.5 w-0.5 flex-shrink-0 rounded-full bg-gray-500 sm:h-1 sm:w-1"></span>
                    <span
                        class="truncate">{{ __('egi.by_author', [
                            'name' => $egi->user->name ?? ($collection->creator->name ?? __('egi.unknown_creator')),
                        ]) }}</span>
                </div>
            </div>

            {{-- Quick Actions in Title Area - Responsive --}}
            <div class="flex flex-shrink-0 items-center space-x-1 sm:space-x-1.5 md:space-x-2">
                {{-- Like Button - Responsive Size --}}
                @if (!$isCreator)
                    <button
                        class="like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }} rounded-full border border-white/20 bg-white/10 p-1.5 backdrop-blur-sm transition-all duration-200 hover:bg-white/20 sm:p-2 md:p-2.5 lg:p-3"
                        data-resource-type="egi" data-resource-id="{{ $egi->id }}"
                        title="{{ __('egi.like_button_title') }}">
                        <svg class="icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }} h-3 w-3 sm:h-4 sm:w-4 md:h-4 md:w-4 lg:h-5 lg:w-5"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif

                {{-- Share Button - Responsive Size --}}
                <button
                    class="rounded-full border border-white/20 bg-white/10 p-1.5 backdrop-blur-sm transition-all duration-200 hover:bg-white/20 sm:p-2 md:p-2.5 lg:p-3"
                    title="{{ __('egi.share_button_title') }}">
                    <svg class="h-3 w-3 text-white sm:h-4 sm:w-4 md:h-4 md:w-4 lg:h-5 lg:w-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
