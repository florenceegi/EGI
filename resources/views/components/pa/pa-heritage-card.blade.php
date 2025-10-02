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

@props([
    'egi',
    'showCoa' => true,
    'layout' => 'grid',
    'showActions' => true,
])

@php
// Determine CoA status
$coaStatus = 'none';
if (isset($egi->coa)) {
    $coaStatus = $egi->coa->status ?? 'none';
}

// Get thumbnail image or placeholder (using Egi model accessor)
$imageUrl = $egi->thumbnail_image_url ?: $egi->main_image_url ?: asset('images/placeholder-heritage.jpg');

// Category badge color
$categoryColor = match($egi->category ?? 'other') {
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

<div
    {{ $attributes->merge(['class' => "heritage-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 hover:border-[#D4A574] flex $containerClass"]) }}
    role="article"
    aria-label="Bene culturale: {{ $egi->title }}"
>
    {{-- Image Section --}}
    <div class="heritage-image {{ $layout === 'list' ? 'w-48 flex-shrink-0' : 'w-full' }} relative aspect-video bg-gray-100">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $egi->title }}"
            class="w-full h-full object-cover"
            loading="lazy"
        />

        {{-- Category Badge (top-left overlay) --}}
        <div class="absolute top-2 left-2">
            <span class="{{ $categoryColor }} text-white text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wide shadow-md">
                {{ __('categories.' . ($egi->category ?? 'other')) }}
            </span>
        </div>

        {{-- CoA Badge (top-right overlay) --}}
        @if ($showCoa)
            <div class="absolute top-2 right-2">
                <x-pa.pa-coa-badge :status="$coaStatus" size="sm" />
            </div>
        @endif
    </div>

    {{-- Content Section --}}
    <div class="heritage-content p-4 flex flex-col flex-grow">
        {{-- Title --}}
        <h3 class="text-lg font-bold text-[#1B365D] mb-2 line-clamp-2 hover:text-[#D4A574] transition-colors">
            <a href="{{ route('pa.heritage.show', $egi->id) }}" class="hover:underline">
                {{ $egi->title }}
            </a>
        </h3>

        {{-- Artist/Creator (if available) --}}
        @if (!empty($egi->artist))
            <p class="text-sm text-gray-600 mb-2 flex items-center gap-1">
                <span class="material-symbols-outlined text-base" aria-hidden="true">palette</span>
                <span>{{ $egi->artist }}</span>
            </p>
        @endif

        {{-- Description --}}
        @if (!empty($egi->description))
            <p class="text-sm text-gray-700 mb-3 line-clamp-2 flex-grow">
                {{ Str::limit($egi->description, 120) }}
            </p>
        @endif

        {{-- Metadata Row --}}
        <div class="heritage-meta flex items-center gap-3 text-xs text-gray-500 mb-3 pt-2 border-t border-gray-100">
            {{-- Created Date --}}
            <span class="flex items-center gap-1" title="Data creazione">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">calendar_today</span>
                {{ $egi->created_at->format('d/m/Y') }}
            </span>

            {{-- Collection (if available) --}}
            @if ($egi->collection)
                <span class="flex items-center gap-1" title="Collezione">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">folder</span>
                    {{ Str::limit($egi->collection->name, 20) }}
                </span>
            @endif

            {{-- Status (if published) --}}
            @if ($egi->is_published)
                <span class="flex items-center gap-1 text-[#2D5016]" title="Pubblicato">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">check_circle</span>
                    Pubblicato
                </span>
            @endif
        </div>

        {{-- Actions --}}
        @if ($showActions)
            <div class="heritage-actions flex gap-2">
                {{-- View Details Button --}}
                <a
                    href="{{ route('pa.heritage.show', $egi->id) }}"
                    class="flex-1 inline-flex items-center justify-center gap-1 px-4 py-2 bg-[#1B365D] hover:bg-[#D4A574] text-white text-sm font-semibold rounded-lg transition-colors duration-200"
                    aria-label="Visualizza dettagli {{ $egi->title }}"
                >
                    <span class="material-symbols-outlined text-base" aria-hidden="true">visibility</span>
                    <span>Dettagli</span>
                </a>

                {{-- Download CoA Button (if CoA exists) --}}
                @if ($coaStatus === 'valid' && isset($egi->coa))
                    <a
                        href="{{ route('coa.download', $egi->coa->id) }}"
                        class="inline-flex items-center justify-center gap-1 px-4 py-2 bg-white hover:bg-[#2D5016] text-[#2D5016] hover:text-white border-2 border-[#2D5016] text-sm font-semibold rounded-lg transition-colors duration-200"
                        aria-label="Scarica CoA {{ $egi->title }}"
                        target="_blank"
                    >
                        <span class="material-symbols-outlined text-base" aria-hidden="true">download</span>
                        <span class="hidden sm:inline">CoA</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- Optional slot for custom actions --}}
        {{ $slot }}
    </div>
</div>
