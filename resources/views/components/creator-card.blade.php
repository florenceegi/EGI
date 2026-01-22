{{-- resources/views/components/creator-card.blade.php --}}
{{--
 * @package App\View\Components
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Creator Card Blur Mobile Only)
 * @date 2025-07-31
 * @purpose Creator card with Blur-style mobile layout, desktop unchanged
 --}}

@props(['creator', 'imageType' => 'card', 'displayType' => 'default'])

@php
    $logo = config('app.logo');
    $imageUrl = '';

    if ($creator) {
        if ($creator->profile_photo_url) {
            $imageUrl = $creator->profile_photo_url;
        } else {
            $imageUrl = asset("images/logo/$logo");
        }
    } else {
        $imageUrl = asset("images/logo/$logo");
    }
@endphp

@if ($creator)
    {{-- NFT-STYLE CARD (Blur-inspired) - iPhone First Responsive --}}
    <a href="{{ route('creator.home', ['id' => $creator->id]) }}"
        class="group block w-full overflow-hidden rounded-lg bg-gray-900 shadow-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl sm:rounded-xl"
        aria-label="{{ sprintf(__('View creator profile %s'), $creator->name) }}">

        {{-- Creator Image Section --}}
        <div class="relative aspect-square w-full bg-gradient-to-br from-gray-800 to-gray-900">
            <img src="{{ $imageUrl }}" alt="{{ $creator->name }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy"
                decoding="async">

            {{-- Corner Badge "C" - Più piccolo su mobile --}}
            <div class="absolute left-2 top-2 sm:left-3 sm:top-3">
                <span
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-orange-500 text-xs font-bold text-white shadow-lg sm:h-7 sm:w-7 sm:text-sm">
                    C
                </span>
            </div>

            {{-- Creator ID - Nascosto su mobile per card più pulita --}}
            <div class="absolute right-2 top-2 hidden sm:right-3 sm:top-3 sm:block">
                <span class="rounded-lg bg-black bg-opacity-75 px-2 py-1 text-sm font-bold text-white backdrop-blur-sm">
                    #{{ $creator->id }}
                </span>
            </div>
        </div>

        {{-- Info Section - Padding ridotto su mobile --}}
        <div class="bg-gray-800 p-2 sm:p-4">
            {{-- Creator Name - Font più piccolo su mobile --}}
            <h3
                class="mb-1 truncate text-sm font-bold text-white transition-colors duration-200 group-hover:text-orange-400 sm:mb-3 sm:text-lg">
                {{ $creator->name }}
            </h3>

            {{-- Stats Section (NFT-style) - Più compatto su mobile --}}
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase tracking-wide text-gray-400 sm:text-xs">CREATOR</span>
                    <span class="text-xs font-semibold text-white sm:text-sm">
                        {{ $creator->collections_count ?? 0 }} ⚡
                    </span>
                </div>

                <div class="flex flex-col text-right">
                    <span class="text-[10px] uppercase tracking-wide text-gray-400 sm:text-xs">WORKS</span>
                    <span class="text-xs font-semibold text-white sm:text-sm">
                        {{ $creator->artworks_count ?? 0 }} 🎨
                    </span>
                </div>
            </div>
        </div>
    </a>
@else
    <div
        class="flex h-full w-full items-center justify-center rounded-lg bg-gray-800 p-2 text-center text-sm text-gray-500 sm:rounded-xl sm:p-4 sm:text-base">
        {{ __('Creator data not available.') }}
    </div>
@endif
