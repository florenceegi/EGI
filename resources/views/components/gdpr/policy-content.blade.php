@props(['policyContent'])

{{--
    * @package App\Views\Components\Gdpr
    * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
    * @version 2.2.0 (Color Contrast & Accessibility Fix)
    * @deadline 2025-06-30
--}}

{{-- 🎯 CORREZIONE CONTRASTO:
    Aggiunte classi Tailwind specifiche per `prose` per forzare un colore scuro sul testo.
    Questo risolve il problema di ereditarietà dal tema scuro globale su questo componente con sfondo chiaro.
    - `prose-p:text-gray-700`: Paragrafi
    - `prose-li:text-gray-700`: Voci delle liste
    - `prose-strong:text-gray-900`: Testo in grassetto
    - `prose-headings:text-gray-900`: Tutti i titoli all'interno
    - `prose-table:text-gray-700`: Testo nelle tabelle
    - `prose-th:text-gray-900`: Header delle tabelle
    - `prose-td:text-gray-700`: Celle delle tabelle
--}}
<article
    {{ $attributes->merge([
        'class' => 'p-8 prose prose-lg max-w-none
                    border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50
                    prose-headings:text-gray-900 prose-p:text-gray-700 prose-li:text-gray-700 prose-strong:text-gray-900
                    prose-a:text-blue-600 hover:prose-a:text-blue-800
                    prose-table:text-gray-700 prose-th:text-gray-900 prose-th:bg-gray-100 prose-td:text-gray-700
                    prose-thead:border-gray-300 prose-tbody:border-gray-200',
    ]) }}
    aria-describedby="privacy-policy-desc">

    @foreach ($policyContent['sections'] as $index => $section)
        <section id="{{ $section['anchor'] }}" class="mb-8 scroll-mt-24"
            aria-labelledby="{{ $section['anchor'] }}-heading">

            {{-- L'intestazione della sezione viene generata qui per coerenza --}}
            <h2 id="{{ $section['anchor'] }}-heading" class="mb-4 text-2xl font-bold">
                {{ $section['index'] }}. {{ $section['title'] }}
            </h2>

            {{-- Il contenuto viene renderizzato da Markdown, e ora erediterà i colori corretti --}}
            @if (isset($section['content']) && !empty($section['content']))
                {!! Illuminate\Support\Str::markdown($section['content']) !!}
            @endif

        </section>

        @if (!$loop->last)
            <hr class="my-8 border-gray-200" role="separator">
        @endif
    @endforeach

    {{-- Sezione Informazioni di Contatto --}}
    <section class="-mx-2 mt-12 rounded-lg bg-gray-50 p-6" aria-labelledby="contact-section-heading">
        <h2 id="contact-section-heading" class="mb-4 text-xl font-bold text-gray-900">
            {{ __('gdpr.contact_information') }}
        </h2>
        <address class="not-italic text-gray-700">
            <p class="mb-2">
                <strong>{{ __('gdpr.data_controller') }}:</strong> {{ config('app.company_name') }}
            </p>
            <p class="mb-2">
                <strong>{{ __('gdpr.email') }}:</strong>
                <a href="mailto:{{ config('gdpr.privacy_email') }}"
                    class="text-blue-600 underline hover:text-blue-800">
                    {{ config('gdpr.privacy_email') }}
                </a>
            </p>
            <p class="mb-2">
                <strong>{{ __('gdpr.address') }}:</strong> {{ config('app.company_address') }}
            </p>
            @if (config('gdpr.dpo_email'))
                <p>
                    <strong>{{ __('gdpr.dpo_email') }}:</strong>
                    <a href="mailto:{{ config('gdpr.dpo_email') }}"
                        class="text-blue-600 underline hover:text-blue-800">
                        {{ config('gdpr.dpo_email') }}
                    </a>
                </p>
            @endif
        </address>
    </section>
</article>
