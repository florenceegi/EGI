{{-- resources/views/home.blade.php --}}
{{-- 📜 Oracode View: FlorenceEGI Homepage NFT-Style Edition --}}

@vite(['resources/css/home-nft.css'])

<x-guest-layout :title="__('guest_home.page_title')" :metaDescription="__('guest_home.meta_description')">

    <x-slot name="platformInfoButtons">
        <x-platform-info-buttons />
    </x-slot>

    <x-slot name="platformStats">
        <div class="flex justify-center py-2 bg-gray-900/50 backdrop-blur-sm">
            {{-- Desktop: Componente statistiche completo --}}
            <div class="hidden sm:block">
                <x-payment-distribution-stats />
            </div>

            {{-- Mobile: Componente statistiche carousel --}}
            <div class="block w-full px-4 sm:hidden">
                <x-payment-distribution-stats-mobile />
            </div>
        </div>
    </x-slot>

    <x-slot name="platformStats">

        <x-collection-hero-banner :collections="$featuredCollections" id="mainHeroCarousel" />

    </x-slot>

    {{-- Mobile: Toggle between Carousel and List modes --}}
    <x-slot name="egiCarousel">
        <div class="block lg:hidden">
            <x-list-switcher :collections="$featuredCollections" defaultTab="collections" />
        </div>

        <x-desktop-egi-carousel :egis="$hyperEgis" />
    </x-slot>

    {{-- Desktop: EGI Cards Carousel - SOLO desktop --}}
    <x-slot name="belowHeroContent">

        {{-- Creators carousel (era qui prima) --}}
        <div class="hidden lg:block">
            <x-desktop-egi-carousel :egis="$featuredEgis" />
            <x-creators-carousel :creators="$featuredCreators" title="{{ __('guest_home.featured_creators_title') }}"
                bgClass="bg-gray-900" marginClass="mb-12" />
        </div>
    </x-slot>

    {{-- Carousel Top Collectors - Nuovo slot - NASCOSTO su mobile --}}
    <x-slot name="belowHeroContent_0_5">
        <div class="hidden lg:block">
            <x-collector-carousel :collectors="$topCollectors" />
        </div>
    </x-slot>

    {{-- Nuovo slot per il carousel dei Creator - NASCOSTO su mobile --}}
    <x-slot name="belowHeroContent_1">
        <div class="hidden lg:block">

            <x-collections-carousel :collections="$featuredCollections" bgClass="bg-gray-900" marginClass="mb-12" />
        </div>
    </x-slot>


    {{-- Sezione: Protagonisti e Attori dell'Ecosistema --}}
    {{-- POPOLIAMO IL NUOVO SLOT $actorContent CON IL NOSTRO COMPONENTE actors-section --}}
    <x-slot name="actorContent">
        <x-actors-section />
    </x-slot>

    {{-- Sezione: Progetti Ambientali (EPP) NFT Style --}}

    <x-epp-cta-banner :title="__('guest_home.epp_banner_title')" :subtitle="__('guest_home.epp_banner_subtitle')"
        :message="__('guest_home.epp_banner_message_v2')" {{-- Usa un messaggio specifico che enfatizzi
        protezione/recupero --}} :ctaText="__('guest_home.epp_banner_cta')" ctaLink="{{ route('archetypes.patron') }}"
        {{-- Scegli un'immagine di sfondo appropriata per gli EPP --}} {{--
        backgroundImage="{{ asset('images/banners/forest_regeneration.jpg') }}" --}}
        heightClass="min-h-[50vh] md:min-h-[65vh]"
        overlayColor="bg-gradient-to-br from-gray-900/80 via-verde-rinascita/50 to-gray-900/80" {{-- Overlay più
        brandizzato --}} />


    @vite(['resources/js/home-nft.js'])

</x-guest-layout>
