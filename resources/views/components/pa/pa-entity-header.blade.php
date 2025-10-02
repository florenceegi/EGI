{{--
/**
 * PA Entity Header Component
 *
 * @package App\View\Components\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose PA entity information header with institutional branding and contact info
 *
 * @props
 * - entity: User (required) - PA entity user model
 * - collection: Collection (optional) - Associated collection (if applicable)
 * - showContact: bool (optional) - Show contact information (default: true)
 * - showStats: bool (optional) - Show entity statistics (default: false)
 * - compact: bool (optional) - Compact layout for secondary pages (default: false)
 *
 * @example
 * <x-pa.pa-entity-header
 *     :entity="$paEntity"
 *     :showContact="true"
 *     :showStats="true"
 * />
 */
--}}

@props(['entity', 'collection' => null, 'showContact' => true, 'showStats' => false, 'compact' => false])

@php
    // Get entity name (use profile name or fallback to user name)
    $entityName = $entity->profile->institution_name ?? ($entity->name ?? 'Ente PA');

    // Get logo/avatar (use profile avatar or default)
    $logoUrl = $entity->profile->avatar_url ?? ($entity->profile_photo_url ?? asset('images/pa-entity-logo.svg'));

    // Municipality/Location
    $municipality = $entity->profile->municipality ?? ($entity->profile->city ?? 'Firenze');

    // Contact info
    $email = $entity->email ?? null;
    $phone = $entity->profile->phone ?? null;
    $website = $entity->profile->website ?? null;

    // Statistics (if enabled)
    $stats = [];
    if ($showStats) {
        $stats = [
            'heritage_count' =>
                $entity->collections()->whereType('pa_heritage')->withCount('egis')->get()->sum('egis_count') ?? 0,
            'coa_issued' => $entity->coas()->whereStatus('valid')->count() ?? 0,
            'collections' => $entity->collections()->count() ?? 0,
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'pa-entity-header bg-gradient-to-r from-[#1B365D] to-[#0F2342] rounded-xl shadow-lg overflow-hidden border-2 border-[#D4A574]']) }}
    role="banner" aria-label="Informazioni {{ $entityName }}">
    <div class="{{ $compact ? 'md:p-4' : 'md:p-8' }} p-6">
        <div class="flex flex-col items-start gap-6 md:flex-row md:items-center">
            {{-- Logo/Avatar Section --}}
            <div class="flex-shrink-0">
                <div
                    class="{{ $compact ? 'md:w-16 md:h-16' : 'md:w-24 md:h-24' }} h-20 w-20 rounded-full bg-white p-2 shadow-xl">
                    <img src="{{ $logoUrl }}" alt="Logo {{ $entityName }}" class="h-full w-full object-contain" />
                </div>
            </div>

            {{-- Info Section --}}
            <div class="flex-grow">
                {{-- Entity Name --}}
                <h1 class="{{ $compact ? 'md:text-xl' : 'md:text-3xl' }} mb-2 text-2xl font-bold text-white">
                    {{ $entityName }}
                </h1>

                {{-- Municipality --}}
                <div class="mb-3 flex items-center gap-2 text-[#D4A574]">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">location_on</span>
                    <span class="text-sm font-medium">{{ $municipality }}</span>
                </div>

                {{-- Collection Info (if provided) --}}
                @if ($collection)
                    <div class="mb-3 rounded-lg bg-white/10 p-3">
                        <p class="mb-1 text-xs uppercase tracking-wide text-white/70">Collezione</p>
                        <p class="text-sm font-semibold text-white">{{ $collection->name }}</p>
                    </div>
                @endif

                {{-- Contact Info --}}
                @if ($showContact && ($email || $phone || $website))
                    <div class="flex flex-wrap items-center gap-4 text-sm text-white/90">
                        @if ($email)
                            <a href="mailto:{{ $email }}"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Email {{ $entityName }}">
                                <span class="material-symbols-outlined text-base" aria-hidden="true">mail</span>
                                <span class="hidden sm:inline">{{ $email }}</span>
                            </a>
                        @endif

                        @if ($phone)
                            <a href="tel:{{ $phone }}"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Telefono {{ $entityName }}">
                                <span class="material-symbols-outlined text-base" aria-hidden="true">call</span>
                                <span>{{ $phone }}</span>
                            </a>
                        @endif

                        @if ($website)
                            <a href="{{ $website }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Sito web {{ $entityName }}">
                                <span class="material-symbols-outlined text-base" aria-hidden="true">language</span>
                                <span class="hidden sm:inline">Sito Web</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Statistics Section (optional) --}}
            @if ($showStats && !empty($stats))
                <div
                    class="{{ $compact ? 'md:gap-3' : 'md:gap-6' }} grid flex-shrink-0 grid-cols-3 gap-4 rounded-lg bg-white/10 p-4">
                    {{-- Heritage Count --}}
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#D4A574]">{{ $stats['heritage_count'] }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-white/70">Beni</p>
                    </div>

                    {{-- CoA Issued --}}
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#D4A574]">{{ $stats['coa_issued'] }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-white/70">CoA</p>
                    </div>

                    {{-- Collections --}}
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#D4A574]">{{ $stats['collections'] }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-white/70">Collezioni</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Optional slot for custom content --}}
        @if ($slot->isNotEmpty())
            <div class="mt-6 border-t border-white/10 pt-6">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
