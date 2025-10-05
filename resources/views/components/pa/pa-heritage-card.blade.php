{{--
/**
 * PA Heritage Card Component
 *
 * @package App\View\Components\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose Heritage item preview card for PA dashboard and heritage list with CoA status
 *
 * @props
 * - egi: Egi (required) - EGI model instance
 * - showCoa: bool (optional) - Show CoA badge (default: true)
 * - layout: string (optional) - Card layout: 'grid'|'list' (default: 'grid')
 * - showActions: bool (optional) - Show action buttons (default: true)
 *
 * @example
 * <x-pa.pa-heritage-card :egi="$egi" :showCoa="true" layout="grid" />
 */
--}}

@props(['egi', 'showCoa' => true, 'layout' => 'grid', 'showActions' => true])

@php
    // Determine CoA status
    $coaStatus = 'none';
    if (isset($egi->coa)) {
        $coaStatus = $egi->coa->status ?? 'none';
    }

    // Get thumbnail image or placeholder (using Egi model accessor)
    $imageUrl = $egi->thumbnail_image_url ?: $egi->main_image_url ?: asset('images/placeholder-heritage.jpg');

    // Category badge color
    $categoryColor = match ($egi->category ?? 'other') {
        'artwork' => 'bg-[#8E44AD]',
        'monument' => 'bg-[#1B365D]',
        'artifact' => 'bg-[#E67E22]',
        'document' => 'bg-[#2D5016]',
        default => 'bg-[#6B6B6B]',
    };

    // Layout classes
    $layoutClasses = [
        'grid' => 'flex-col',
        'list' => 'flex-row gap-4',
    ];
    $containerClass = $layoutClasses[$layout] ?? $layoutClasses['grid'];
@endphp

<div {{ $attributes->merge(['class' => "heritage-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 hover:border-[#D4A574] flex $containerClass"]) }}
    role="article" aria-label="{{ __('pa_heritage.card_aria_item') }} {{ $egi->title }}">
    {{-- Image Section --}}
    <div
        class="heritage-image {{ $layout === 'list' ? 'w-48 flex-shrink-0' : 'w-full' }} relative aspect-video bg-gray-100">
        <img src="{{ $imageUrl }}" alt="{{ $egi->title }}" class="object-cover w-full h-full" loading="lazy" />

        {{-- Category Badge (top-left overlay) --}}
        <div class="absolute left-2 top-2">
            <span
                class="{{ $categoryColor }} rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow-md">
                {{ __('categories.' . ($egi->category ?? 'Other')) }}
            </span>
        </div>

        {{-- CoA Badge (top-right overlay) --}}
        @if ($showCoa)
            <div class="absolute right-2 top-2">
                <x-pa.pa-coa-badge :status="$coaStatus" size="sm" />
            </div>
        @endif
    </div>

    {{-- Content Section --}}
    <div class="flex flex-col flex-grow p-4 heritage-content">
        {{-- Title --}}
        <h3 class="mb-2 line-clamp-2 text-lg font-bold text-[#1B365D] transition-colors hover:text-[#D4A574]">
            <a href="{{ route('pa.heritage.show', $egi->id) }}" class="hover:underline">
                {{ $egi->title }}
            </a>
        </h3>

        {{-- Artist/Creator (if available) --}}
        @if (!empty($egi->artist))
            <p class="flex items-center gap-1 mb-2 text-sm text-gray-600">
                <span class="text-base material-symbols-outlined" aria-hidden="true">palette</span>
                <span>{{ $egi->artist }}</span>
            </p>
        @endif

        {{-- Description --}}
        @if (!empty($egi->description))
            <p class="flex-grow mb-3 text-sm text-gray-700 line-clamp-2">
                {{ Str::limit($egi->description, 120) }}
            </p>
        @endif

        {{-- Metadata Row --}}
        <div class="flex items-center gap-3 pt-2 mb-3 text-xs text-gray-500 border-t border-gray-100 heritage-meta">
            {{-- Created Date --}}
            <span class="flex items-center gap-1" title="{{ __('pa_heritage.card_created_date_title') }}">
                <span class="text-sm material-symbols-outlined" aria-hidden="true">calendar_today</span>
                {{ $egi->created_at->format('d/m/Y') }}
            </span>

            {{-- Collection (if available) --}}
            @if ($egi->collection)
                <span class="flex items-center gap-1" title="{{ __('pa_heritage.card_collection_title') }}">
                    <span class="text-sm material-symbols-outlined" aria-hidden="true">folder</span>
                    {{ Str::limit($egi->collection->collection_name, 20) }}
                </span>
            @endif

            {{-- Status (if published) --}}
            @if ($egi->is_published)
                <span class="flex items-center gap-1 text-[#2D5016]"
                    title="{{ __('pa_heritage.card_published_title') }}">
                    <span class="text-sm material-symbols-outlined" aria-hidden="true">check_circle</span>
                    {{ __('pa_heritage.card_published_status') }}
                </span>
            @endif
        </div>

        {{-- Actions --}}
        @if ($showActions)
            <div class="flex gap-2 heritage-actions">
                {{-- View Details Button --}}
                <a href="{{ route('pa.heritage.show', $egi->id) }}"
                    class="inline-flex flex-1 items-center justify-center gap-1 rounded-lg bg-[#1B365D] px-4 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#D4A574]"
                    aria-label="{{ __('pa_heritage.card_aria_view_details') }} {{ $egi->title }}">
                    <span class="text-base material-symbols-outlined" aria-hidden="true">visibility</span>
                    <span>{{ __('pa_heritage.card_btn_details') }}</span>
                </a>

                {{-- Download CoA Button (if CoA exists) --}}
                @if ($coaStatus === 'valid' && isset($egi->coa))
                    <a href="{{ route('coa.pdf.download', $egi->coa->id) }}"
                        class="inline-flex items-center justify-center gap-1 rounded-lg border-2 border-[#2D5016] bg-white px-4 py-2 text-sm font-semibold text-[#2D5016] transition-colors duration-200 hover:bg-[#2D5016] hover:text-white"
                        aria-label="{{ __('pa_heritage.card_aria_download_coa') }} {{ $egi->title }}"
                        target="_blank">
                        <span class="text-base material-symbols-outlined" aria-hidden="true">download</span>
                        <span class="hidden sm:inline">{{ __('pa_heritage.card_btn_coa') }}</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- Optional slot for custom actions --}}
        {{ $slot }}
    </div>
</div>
