{{--
    * @package App\Views\Gdpr
    * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
    * @version 2.0.0 (Component-Based Refactoring)
    * @deadline 2025-06-30
--}}

<x-app-layout :pageTitle="__('gdpr.privacy_policy')">

    {{-- Header Slot: Definisce l'intestazione principale della pagina --}}
    <x-slot name="header">
        <h1 id="privacy-policy-title" class="text-3xl font-bold text-base-content">
            {{ __('gdpr.privacy_policy') }}
        </h1>
        <p class="mt-2 text-base-content/70" id="privacy-policy-desc">
            {{ __('gdpr.privacy_policy_description') }}
        </p>
    </x-slot>

    {{-- Main Content: Contenuto principale della pagina, orchestrato tramite componenti --}}
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            {{-- 🧱 Componente per le informazioni di versione e le azioni rapide (Download, Stampa) --}}
            <x-gdpr.version-info-card :policyData="$policyData" class="mb-8" />

            {{-- 🧱 Componente per la tabella dei contenuti --}}
            <x-gdpr.table-of-contents :policyContent="$policyContent" class="mb-8" />

            {{-- 🧱 Componente per il corpo principale dell'informativa --}}
            <x-gdpr.policy-content :policyContent="$policyContent" />

            {{-- 🧱 Componente per lo stato di accettazione dell'utente --}}
            <x-gdpr.acceptance-status :userAcceptance="$userAcceptance" class="mt-8" />

            {{-- 🧱 Componente per la cronologia delle versioni --}}
            @if ($versionHistory->count() > 1)
                <x-gdpr.version-history :versionHistory="$versionHistory" :currentPolicy="$currentPolicy" class="mt-8" />
            @endif

        </div>
    </div>

    {{-- Stili e Script specifici per la pagina, mantenuti con @push per modularità --}}
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
        {{-- Lo script esistente può essere mantenuto qui o spostato in un file JS dedicato --}}
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

                // Form validation for policy acceptance
                const acceptForm = document.querySelector('form[action*="accept"]');
                if (acceptForm) {
                    acceptForm.addEventListener('submit', function(e) {
                        const checkbox = this.querySelector('#accept_policy');

                        if (!checkbox.checked) {
                            e.preventDefault();
                            checkbox.setAttribute('aria-invalid', 'true');
                            checkbox.focus();

                            // Announce error
                            const error = document.createElement('span');
                            error.className = 'sr-only';
                            error.setAttribute('role', 'alert');
                            error.textContent = '{{ __('gdpr.must_accept_policy') }}';
                            this.appendChild(error);

                            setTimeout(() => error.remove(), 3000);
                        }
                    });
                }

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
