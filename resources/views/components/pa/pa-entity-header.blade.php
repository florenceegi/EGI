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
    // Get entity name (use organization name or fallback to user name)
    $entityName = $entity->organizationData->org_name ?? ($entity->name ?? __('pa_heritage.entity_default_name'));

    // Get logo/avatar (use Jetstream profile photo or default)
    $logoUrl = $entity->profile_photo_url ?? asset('images/pa-entity-logo.svg');

    // Municipality/Location (from organization data)
    $municipality = $entity->organizationData->org_city ?? 'Firenze';

    // Contact info (from organization data)
    $email = $entity->organizationData->org_email ?? ($entity->email ?? null);
    $phone = $entity->organizationData->org_phone_1 ?? null;
    $website = $entity->organizationData->org_site_url ?? null;

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
                    <img src="{{ $logoUrl }}" alt="Logo {{ $entityName }}" class="object-contain w-full h-full" />
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
                    <span class="text-lg material-symbols-outlined" aria-hidden="true">location_on</span>
                    <span class="text-sm font-medium">{{ $municipality }}</span>
                </div>

                {{-- Collection Info (if provided) --}}
                @if ($collection)
                    <div class="p-3 mb-3 rounded-lg bg-white/10">
                        <p class="mb-1 text-xs tracking-wide uppercase text-white/70">
                            {{ __('pa_heritage.entity_collection_label') }}</p>
                        <p class="text-sm font-semibold text-white">{{ $collection->collection_name }}</p>
                    </div>
                @endif

                {{-- Contact Info --}}
                @if ($showContact && ($email || $phone || $website))
                    <div class="flex flex-wrap items-center gap-4 text-sm text-white/90">
                        @if ($email)
                            <a href="mailto:{{ $email }}"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Email {{ $entityName }}">
                                <span class="text-base material-symbols-outlined" aria-hidden="true">mail</span>
                                <span class="hidden sm:inline">{{ $email }}</span>
                            </a>
                        @endif

                        @if ($phone)
                            <a href="tel:{{ $phone }}"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Telefono {{ $entityName }}">
                                <span class="text-base material-symbols-outlined" aria-hidden="true">call</span>
                                <span>{{ $phone }}</span>
                            </a>
                        @endif

                        @if ($website)
                            <a href="{{ $website }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 transition-colors hover:text-[#D4A574]"
                                aria-label="Sito web {{ $entityName }}">
                                <span class="text-base material-symbols-outlined" aria-hidden="true">language</span>
                                <span class="hidden sm:inline">{{ __('pa_heritage.entity_website_label') }}</span>
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
                        <p class="mt-1 text-xs tracking-wide uppercase text-white/70">
                            {{ __('pa_heritage.entity_stats_heritage') }}</p>
                    </div>

                    {{-- CoA Issued --}}
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#D4A574]">{{ $stats['coa_issued'] }}</p>
                        <p class="mt-1 text-xs tracking-wide uppercase text-white/70">
                            {{ __('pa_heritage.entity_stats_coa') }}</p>
                    </div>

                    {{-- Collections --}}
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#D4A574]">{{ $stats['collections'] }}</p>
                        <p class="mt-1 text-xs tracking-wide uppercase text-white/70">
                            {{ __('pa_heritage.entity_stats_collections') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Optional slot for custom content --}}
        @if ($slot->isNotEmpty())
            <div class="pt-6 mt-6 border-t border-white/10">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
