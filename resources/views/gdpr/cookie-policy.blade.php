{{--
    * @package App\Views\Gdpr
    * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
    * @version 2.0.0 (Cookie Policy Page)
    * @date 2025-02-15
--}}

<x-app-layout :pageTitle="__('gdpr.cookie_policy')">

    {{-- Header Slot: Definisce l'intestazione principale della pagina --}}
    <x-slot name="header">
        <h1 id="cookie-policy-title" class="text-3xl font-bold text-gray-900">
            {{ __('gdpr.cookie_policy') }}
        </h1>
        <p class="mt-2 text-gray-600" id="cookie-policy-desc">
            {{ __('gdpr.cookie_policy_description', ['default' => 'Informazioni sull\'utilizzo dei cookie e tecnologie di tracciamento']) }}
        </p>
    </x-slot>

    {{-- Main Content: Contenuto principale della pagina --}}
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            {{-- 🧱 Componente per le informazioni di versione e le azioni rapide (Download, Stampa) --}}
            <x-gdpr.version-info-card :policyData="$policyData" class="mb-8" />

            {{-- 🧱 Componente per la tabella dei contenuti --}}
            <x-gdpr.table-of-contents :policyContent="$policyContent" class="mb-8" />

            {{-- 🧱 Componente per il corpo principale della policy --}}
            <x-gdpr.policy-content :policyContent="$policyContent" />

            {{-- 🧱 Link per gestire le preferenze cookie --}}
            <div class="mt-8 rounded-xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
                <h3 class="mb-3 text-lg font-semibold text-gray-900">
                    {{ __('gdpr.manage_cookie_preferences', ['default' => 'Gestisci le tue preferenze']) }}
                </h3>
                <p class="mb-4 text-gray-600">
                    {{ __('gdpr.cookie_preferences_description', ['default' => 'Puoi modificare le tue preferenze sui cookie in qualsiasi momento.']) }}
                </p>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-cookie-settings'))"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ __('gdpr.open_cookie_settings', ['default' => 'Apri impostazioni cookie']) }}
                </button>
            </div>

            {{-- 🧱 Componente per la cronologia delle versioni --}}
            @if ($versionHistory->count() > 1)
                <x-gdpr.version-history :versionHistory="$versionHistory" :currentPolicy="$currentPolicy" class="mt-8" />
            @endif

        </div>
    </div>

    {{-- Stili e Script specifici per la pagina --}}
    @push('styles')
        <style media="print">
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12pt;
            }

            .prose {
                max-width: 100%;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .bg-white\/80 {
                background: white;
                box-shadow: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Smooth scroll for table of contents
                const tocLinks = document.querySelectorAll('nav a[href^="#"]');

                tocLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href').slice(1);
                        const targetElement = document.getElementById(targetId);

                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });

                            // Update URL without scrolling
                            history.pushState(null, null, '#' + targetId);

                            // Set focus for accessibility
                            targetElement.setAttribute('tabindex', '-1');
                            targetElement.focus();
                        }
                    });
                });

                // Highlight current section in view
                const sections = document.querySelectorAll('article section[id]');
                const observerOptions = {
                    root: null,
                    rootMargin: '-20% 0px -70% 0px',
                    threshold: 0
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const id = entry.target.getAttribute('id');
                        const tocLink = document.querySelector(`nav a[href="#${id}"]`);

                        if (entry.isIntersecting && tocLink) {
                            // Remove all active classes
                            document.querySelectorAll('nav a').forEach(link => {
                                link.classList.remove('font-bold', 'text-blue-800');
                            });

                            // Add active class to current
                            tocLink.classList.add('font-bold', 'text-blue-800');
                        }
                    });
                }, observerOptions);

                sections.forEach(section => {
                    observer.observe(section);
                });

                // Copy policy link functionality
                const copyButton = document.createElement('button');
                copyButton.className =
                    'fixed bottom-4 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 no-print';
                copyButton.setAttribute('aria-label', '{{ __('gdpr.copy_policy_link') }}');
                copyButton.innerHTML =
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-2.392 0-4.744.175-6.284.516m9.032 10.568A8.961 8.961 0 0112 21c-2.392 0-4.744-.975-6.284-2.416"></path></svg>';

                copyButton.addEventListener('click', function() {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        // Show success message
                        const toast = document.createElement('div');
                        toast.className =
                            'fixed bottom-20 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg';
                        toast.setAttribute('role', 'status');
                        toast.setAttribute('aria-live', 'polite');
                        toast.textContent = '{{ __('gdpr.link_copied') }}';
                        document.body.appendChild(toast);

                        setTimeout(() => toast.remove(), 3000);
                    });
                });

                document.body.appendChild(copyButton);
            });
        </script>
    @endpush

</x-app-layout>
