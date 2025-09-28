{{-- resources/views/components/actors-section.blade.php --}}
@props([
    'showOnHomepageOnly' => false, // Prop per decidere se mostrarla solo su home o sempre
    // Potresti aggiungere altre props per personalizzare titoli/sottotitoli se necessario
])

{{-- Condizione per mostrare la sezione --}}
{{-- Se vogliamo mostrarla sempre nel guest layout, rimuoviamo questa condizione o la gestiamo diversamente --}}
<!-- @if (!$showOnHomepageOnly || request()->routeIs('home'))
-->

<section id="ecosystem-actors" class="border-b border-t border-gray-700 bg-gray-800 py-16 md:py-24"
    aria-labelledby="ecosystem-actors-heading">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 id="ecosystem-actors-heading"
                class="mb-4 font-display text-3xl font-bold text-white md:text-4xl lg:text-5xl">
                {{ __('guest_layout.actors_section_title') }} {{-- Chiave di traduzione generica per guest layout --}}
            </h2>
            <p class="mx-auto mb-12 max-w-3xl font-body text-lg text-gray-300 md:mb-16 lg:mb-20">
                {{ __('guest_layout.actors_section_subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:gap-6 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7">

            {{-- Card 1: Artisti / Creator --}}
            <x-actor-card icon="brush" :title="__('guest_home.actor_creator_title')" :message="__('guest_home.actor_creator_message')" :ctaText="__('guest_home.actor_creator_cta')"
                ctaLink="{{ route('register') }}?role=creator" ctaIcon="add_photo_alternate"
                accentColorClass="border-t-4 border-viola-innovazione" iconColorClass="text-viola-innovazione"
                ctaBgColorClass="bg-viola-innovazione" ctaTextColorClass="text-white"
                ctaHoverBgColorClass="hover:bg-purple-700" />

            {{-- Card 2: Mecenati / Patron --}}
            <x-actor-card icon="account_balance" :title="__('guest_home.actor_patron_title')" :message="__('guest_home.actor_patron_message')" :ctaText="__('guest_home.actor_patron_cta')"
                ctaLink="{{ route('archetypes.patron') }}" ctaIcon="storefront"
                accentColorClass="border-t-4 border-florence-gold" iconColorClass="text-florence-gold"
                ctaBgColorClass="bg-florence-gold" ctaTextColorClass="text-florence-gold-text"
                ctaHoverBgColorClass="hover:bg-florence-gold-dark" />

            {{-- Card 3: Acquirenti / Collezionisti --}}
            <x-actor-card icon="shopping_cart_checkout" :title="__('guest_home.actor_collector_title')" :message="__('guest_home.actor_collector_message')" :ctaText="__('guest_home.actor_collector_cta')"
                ctaLink="{{ route('archetypes.collector') }}" ctaIcon="travel_explore"
                accentColorClass="border-t-4 border-blu-algoritmo" iconColorClass="text-blu-algoritmo"
                ctaBgColorClass="bg-blu-algoritmo" ctaTextColorClass="text-blu-algoritmo-text"
                ctaHoverBgColorClass="hover:bg-blu-algoritmo-dark" />

            {{-- Card 4: Imprenditori / Aziende --}}
            <x-actor-card icon="business_center" :title="__('guest_home.actor_business_title')" :message="__('guest_home.actor_business_message')" :ctaText="__('guest_home.actor_business_cta')"
                ctaLink="{{ route('register') }}?role=business" ctaIcon="trending_up"
                accentColorClass="border-t-4 border-arancio-energia" iconColorClass="text-arancio-energia"
                ctaBgColorClass="bg-arancio-energia" ctaTextColorClass="text-white"
                ctaHoverBgColorClass="hover:bg-orange-600" />

            {{-- Card 5: Trader Pro EGI pt --}}
            <x-actor-card icon="monitoring" :title="__('guest_home.actor_trader_pro_title')" :message="__('guest_home.actor_trader_pro_message')" :ctaText="__('guest_home.actor_trader_pro_cta')"
                ctaLink="{{ route('register') }}?role=trader_pro" ctaIcon="trending_up"
                accentColorClass="border-t-4 border-verde-trading" iconColorClass="text-verde-trading"
                ctaBgColorClass="bg-verde-trading" ctaTextColorClass="text-white"
                ctaHoverBgColorClass="hover:bg-green-600" />

            {{-- Card 6: Commissioner --}}
            <x-actor-card icon="star" :title="__('guest_home.actor_commissioner_title')" :message="__('guest_home.actor_commissioner_message')" :ctaText="__('guest_home.actor_commissioner_cta')"
                ctaLink="{{ route('archetypes.collector') }}" ctaIcon="workspace_premium"
                accentColorClass="border-t-4 border-purple-500" iconColorClass="text-purple-500"
                ctaBgColorClass="bg-purple-500" ctaTextColorClass="text-white"
                ctaHoverBgColorClass="hover:bg-purple-600" />

            {{-- Card 7: PA (Pubbliche Amministrazioni) --}}
            <x-actor-card icon="account_balance" :title="__('guest_home.actor_pa_title')" :message="__('guest_home.actor_pa_message')" :ctaText="__('guest_home.actor_pa_cta')"
                ctaLink="{{ route('archetypes.pa-entity') }}" ctaIcon="gavel"
                accentColorClass="border-t-4 border-institutional-blue" iconColorClass="text-institutional-blue"
                ctaBgColorClass="bg-institutional-blue" ctaTextColorClass="text-white"
                ctaHoverBgColorClass="hover:bg-blue-800" />
        </div>
    </div>
</section>
<!--
@endif -->
