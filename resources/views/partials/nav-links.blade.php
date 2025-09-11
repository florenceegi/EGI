{{-- Navigation Script con Link Nascosti per Pagina Corrente --}}

@php
// Fallback per authType se non passato dall'include
$authType = $authType ?? App\Helpers\FegiAuth::getAuthType();

$navLinkClasses = $isMobile
? 'text-gray-300 hover:bg-emerald-600/80 hover:text-white block px-3 py-2.5 rounded-md text-base font-medium transition-all duration-300'
: 'text-gray-300 hover:text-white transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium hover:bg-emerald-600/80';
@endphp

{{-- Home Link - Su mobile sempre visibile, su desktop solo se non siamo in home --}}
@if ($isMobile || !(request()->routeIs('home') || request()->is('/')))
<a href="{{ url('/') }}" class="{{ $navLinkClasses }}"
    aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
    {{ __('guest_layout.home') }}
</a>
@endif

{{-- Creators Link - Su mobile sempre visibile, su desktop solo se non siamo in creators --}}
@if ($isMobile || !(request()->routeIs('creator.index') || request()->is('/')))
<a href="{{ url('/creator') }}" class="{{ $navLinkClasses }}"
    aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
    {{ __('guest_layout.creators') }}
</a>
@endif

{{-- Collections Link - Su mobile sempre visibile, su desktop solo se non siamo nelle collections --}}
@if ($isMobile || !request()->routeIs('home.collections.*'))
<a href="{{ route('home.collections.index') }}" id="generic-collections-link-{{ $isMobile ? 'mobile' : 'desktop' }}"
    class="{{ $navLinkClasses }}"
    aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collections_link_aria_label') }}">
    {{ __('guest_layout.collections') }}
</a>
@endif

{{-- Collectors Link - Su mobile sempre visibile, su desktop solo se non siamo nei collectors --}}
@if ($isMobile || !request()->routeIs('collector.*'))
<a href="{{ route('collector.index') }}" class="{{ $navLinkClasses }}"
    aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collectors_link_aria_label') }}">
    {{ __('guest_layout.collectors') }}
</a>
@endif

{{-- EPPs Link - Su mobile sempre visibile, su desktop solo se non siamo negli EPPs --}}
@if ($isMobile || !request()->routeIs('epps.*'))
{{-- Reindirizzato a pagina "work in progress" --}}
<a href="{{ route('under_construction', ['key' => 'epps']) }}" class="{{ $navLinkClasses }}"
    aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'epps_link_aria_label') }}">
    {{ __('guest_layout.epps') }}
</a>
@endif

{{-- Create EGI Button - Sempre visibile, la logica di azione è gestita da JS in base allo stato utente ANCHE PER UTENTI WEAK --}}
<button type="button"
    class="js-create-egi-contextual-button {{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'inline-flex items-center gap-1' }}"
    data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}"
    aria-label="{{ __('guest_layout.create_egi') }}">
    @if ($isMobile)
    {{-- Versione Mobile - icona + testo allineati a sinistra --}}
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path
                d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
        </svg>
        <span class="js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
    </span>
    @else
    {{-- Versione Desktop - layout inline --}}
    <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
        <path
            d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
    </svg>
    <span class="js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
    @endif
</button>

{{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
@can('create_collection')
@if ($isMobile)
{{-- Versione Mobile - stile menu normale --}}
<button type="button" data-action="open-create-collection-modal"
    class="{{ $navLinkClasses }} w-full text-left flex items-center gap-1"
    aria-label="{{ __('collection.create_collection') }}">
    <span class="text-base material-symbols-outlined" aria-hidden="true">add</span>
    <span>{{ __('collection.create_collection') }}</span>
</button>
@else
{{-- Versione Desktop - stile menu normale --}}
<button type="button" data-action="open-create-collection-modal"
    class="{{ $navLinkClasses }} inline-flex items-center gap-1" aria-label="{{ __('collection.create_collection') }}">
    <span class="text-sm material-symbols-outlined" aria-hidden="true">add</span>
    <span class="hidden sm:inline">{{ __('collection.create_collection') }}</span>
    <span class="sm:hidden">{{ __('collection.create') }}</span>
</button>
@endif
@endcan

@if(!$isMobile)
{{-- Desktop Universal Search Trigger --}}
<button type="button" class="{{ $navLinkClasses }} inline-flex items-center gap-1" aria-label="Apri ricerca avanzata" onclick="window.UniversalSearch ? window.UniversalSearch.open() : window.dispatchEvent(new CustomEvent('universal-search-open'))">
    <span class="material-symbols-outlined text-base" aria-hidden="true">search</span>
    <span>Cerca</span>
</button>
@endif
