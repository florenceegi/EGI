{{--
@Oracode Component: Cookie Consent Banner
🎯 Purpose: Universal GDPR-compliant cookie consent banner for all visitors
🛡/* 🎨 FlorenceEGI Cookie Banner - Modern Design */
.gdpr-cookie-banner {
    position: fixed;
    z-index: 99999;
    left: 16px;
    right: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    line-height: 1.6;
    color: white;
    max-width: 480px;
    margin: 0 auto;
    animation: gdpr-banner-slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}for both authenticated and anonymous users
🧱 Architecture: Vanilla JavaScript, responsive design, accessibility compliant

@package Components\GDPR
@author Padmin D. Curtis (AI Partner) for Fabio Cherici
@version 1.0.0 (FlorenceEGI - Cookie Consent Implementation)
@date 2025-09-17
--}}

@props([
    'position' => 'bottom', // 'bottom' | 'top' | 'overlay'
    'theme' => 'light', // 'light' | 'dark'
    'showOnConsented' => false, // Show banner even if already consented
])

<div id="gdpr-cookie-banner"
    class="gdpr-cookie-banner gdpr-cookie-banner--{{ $position }} gdpr-cookie-banner--{{ $theme }}"
    style="display: none;" role="dialog" aria-labelledby="cookie-banner-title" aria-describedby="cookie-banner-description"
    {{ $attributes }}>
    <!-- Contenuto del Banner (Layout Orizzontale) -->
    <div class="gdpr-cookie-banner__info">
        <h2 id="cookie-banner-title" class="gdpr-cookie-banner__title">
            <i class="gdpr-cookie-banner__icon" aria-hidden="true">🍪</i>
            {{ __('gdpr.cookie.banner.title') }}
        </h2>
        <p id="cookie-banner-description" class="gdpr-cookie-banner__description">
            {{ __('gdpr.cookie.banner.description') }}
            <a href="{{ route('gdpr.cookie-policy') }}" class="gdpr-cookie-banner__link" target="_blank"
                rel="noopener noreferrer">
                {{ __('gdpr.cookie.banner.privacy_policy_link') }}
            </a>
        </p>
    </div>

    <!-- Controlli Granulari (nascosti inizialmente) -->
    <div id="cookie-preferences" class="gdpr-cookie-banner__preferences" style="display: none;" aria-expanded="false">
        <div class="gdpr-cookie-banner__categories">
            <!-- Le categorie verranno popolate dinamicamente via JavaScript -->
        </div>
    </div>

    <!-- Pulsanti di Azione -->
    <div class="gdpr-cookie-banner__actions">
        <button id="cookie-accept-all" type="button"
            class="gdpr-cookie-banner__button gdpr-cookie-banner__button--primary"
            aria-describedby="cookie-banner-description">
            {{ __('gdpr.cookie.banner.accept_all') }}
        </button>

        <button id="cookie-reject-optional" type="button"
            class="gdpr-cookie-banner__button gdpr-cookie-banner__button--secondary">
            {{ __('gdpr.cookie.banner.reject_optional') }}
        </button>

        <button id="cookie-customize" type="button"
            class="gdpr-cookie-banner__button gdpr-cookie-banner__button--tertiary" aria-expanded="false"
            aria-controls="cookie-preferences">
            {{ __('gdpr.cookie.banner.customize') }}
        </button>
    </div>

    <!-- Pulsanti delle Preferenze (mostrati solo quando espanse) -->
    <div id="cookie-preferences-actions" class="gdpr-cookie-banner__preferences-actions" style="display: none;">
        <button id="cookie-save-preferences" type="button"
            class="gdpr-cookie-banner__button gdpr-cookie-banner__button--primary">
            {{ __('gdpr.cookie.banner.save_preferences') }}
        </button>

        <button id="cookie-close-preferences" type="button"
            class="gdpr-cookie-banner__button gdpr-cookie-banner__button--secondary" aria-expanded="true"
            aria-controls="cookie-preferences">
            {{ __('gdpr.cookie.banner.close_preferences') }}
        </button>
    </div>

    <!-- Pulsante Chiudi (per utenti che hanno già dato consenso) -->
    <button id="cookie-banner-close" type="button" class="gdpr-cookie-banner__close"
        aria-label="{{ __('gdpr.cookie.banner.close') }}" style="display: none;">
        <span aria-hidden="true">&times;</span>
    </button>

    <!-- Loading spinner per salvataggio -->
    <div id="cookie-banner-loading" class="gdpr-cookie-banner__loading" style="display: none;">
        <div class="gdpr-cookie-banner__spinner" aria-hidden="true"></div>
        <span class="gdpr-cookie-banner__loading-text">{{ __('gdpr.cookie.banner.saving') }}</span>
    </div>
</div>

{{-- Stili CSS del Banner --}}
<style>
    .gdpr-cookie-banner {
        position: fixed;
        z-index: 99999;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.25), 0 4px 12px rgba(0, 0, 0, 0.1);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: white;
        max-width: 1000px;
        width: calc(100% - 40px);
        margin: 0;
        animation: gdpr-banner-slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 20px 32px;
    }

    .gdpr-cookie-banner--bottom {
        bottom: 20px;
    }

    .gdpr-cookie-banner--top {
        top: 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .gdpr-cookie-banner--overlay {
        top: 50%;
        left: 50%;
        right: auto;
        bottom: auto;
        transform: translate(-50%, -50%);
        max-width: 600px;
        border-radius: 12px;
    }

    .gdpr-cookie-banner--dark {
        background: rgba(31, 41, 55, 0.95);
        color: #f9fafb;
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Layout Orizzontale del Banner */
    .gdpr-cookie-banner__info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .gdpr-cookie-banner__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        flex-shrink: 0;
    }



    .gdpr-cookie-banner__title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
    }

    .gdpr-cookie-banner__icon {
        font-size: 24px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .gdpr-cookie-banner__description {
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        line-height: 1.4;
        text-align: left;
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__description {
        color: rgba(249, 250, 251, 0.8);
    }

    .gdpr-cookie-banner__link {
        color: #2563eb;
        text-decoration: underline;
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__link {
        color: #60a5fa;
    }

    .gdpr-cookie-banner__preferences {
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 16px;
        background: rgba(248, 250, 252, 0.5);
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__preferences {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(17, 24, 39, 0.5);
    }

    .gdpr-cookie-banner__categories {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .gdpr-cookie-banner__category {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 6px;
        background: white;
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__category {
        border-color: rgba(255, 255, 255, 0.05);
        background: rgba(31, 41, 55, 0.5);
    }

    .gdpr-cookie-banner__category-toggle {
        margin: 0;
    }

    .gdpr-cookie-banner__category-info {
        flex: 1;
    }

    .gdpr-cookie-banner__category-name {
        font-weight: 600;
        margin: 0 0 4px 0;
        font-size: 14px;
        color: #1f2937;
        /* Testo scuro per essere visibile su sfondo bianco delle categorie */
    }

    .gdpr-cookie-banner__category-description {
        margin: 0;
        font-size: 12px;
        color: rgba(51, 51, 51, 0.7);
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__category-description {
        color: rgba(249, 250, 251, 0.7);
    }

    .gdpr-cookie-banner__category-required {
        font-size: 11px;
        color: #059669;
        font-weight: 500;
        margin-top: 2px;
    }

    .gdpr-cookie-banner__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .gdpr-cookie-banner__preferences-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-top: 8px;
    }

    .gdpr-cookie-banner__button {
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
        text-transform: none;
        letter-spacing: 0.025em;
        min-width: auto;
    }

    .gdpr-cookie-banner__button--primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .gdpr-cookie-banner__button--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }

    .gdpr-cookie-banner__button--secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .gdpr-cookie-banner__button--secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .gdpr-cookie-banner__button--tertiary {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 12px;
        padding: 8px 16px;
        backdrop-filter: blur(4px);
    }

    .gdpr-cookie-banner__button--tertiary:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-1px);
    }

    .gdpr-cookie-banner__close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 4px;
        line-height: 1;
    }

    .gdpr-cookie-banner__close:hover {
        color: #374151;
    }

    .gdpr-cookie-banner__loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        border-radius: inherit;
    }

    .gdpr-cookie-banner--dark .gdpr-cookie-banner__loading {
        background: rgba(31, 41, 55, 0.9);
    }

    .gdpr-cookie-banner__spinner {
        width: 20px;
        height: 20px;
        border: 2px solid #e5e7eb;
        border-top: 2px solid #2563eb;
        border-radius: 50%;
        animation: gdpr-spinner-rotate 1s linear infinite;
    }

    .gdpr-cookie-banner__loading-text {
        font-size: 14px;
        color: #6b7280;
    }

    /* 🎭 Modern Animations */
    @keyframes gdpr-banner-slide-up {
        from {
            transform: translateY(100px) scale(0.95);
            opacity: 0;
        }

        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .gdpr-cookie-banner--top {
        animation: gdpr-banner-slide-in-top 0.3s ease-out;
    }

    @keyframes gdpr-banner-slide-in-top {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .gdpr-cookie-banner--overlay {
        animation: gdpr-banner-fade-in 0.3s ease-out;
    }

    @keyframes gdpr-banner-fade-in {
        from {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.9);
        }

        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }

    @keyframes gdpr-spinner-rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Design */
    @media (min-width: 768px) {
        .gdpr-cookie-banner__content {
            flex-direction: row;
            align-items: flex-start;
            gap: 24px;
        }

        .gdpr-cookie-banner__header {
            flex: 1;
        }

        .gdpr-cookie-banner__actions {
            flex-shrink: 0;
            justify-content: flex-end;
        }

        .gdpr-cookie-banner__categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px;
        }
    }

    @media (max-width: 767px) {
        .gdpr-cookie-banner {
            flex-direction: column;
            gap: 16px;
            padding: 16px 20px;
            max-width: 100%;
            width: calc(100% - 32px);
            left: 16px;
            transform: none;
        }

        .gdpr-cookie-banner__info {
            text-align: center;
        }

        .gdpr-cookie-banner__actions {
            flex-direction: column;
            gap: 8px;
        }

        .gdpr-cookie-banner__button {
            width: 100%;
            text-align: center;
        }

        .gdpr-cookie-banner__preferences-actions {
            flex-direction: column;
        }
    }

    /* Accessibilità */
    @media (prefers-reduced-motion: reduce) {

        .gdpr-cookie-banner,
        .gdpr-cookie-banner__spinner {
            animation: none;
        }
    }

    /* Focus states per accessibilità */
    .gdpr-cookie-banner__button:focus,
    .gdpr-cookie-banner__close:focus {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
    }

    /* Nascosto quando JavaScript è disabilitato */
    .no-js .gdpr-cookie-banner {
        display: block !important;
    }

    .no-js .gdpr-cookie-banner__preferences {
        display: block !important;
    }
</style>

{{-- JavaScript per la gestione del banner --}}
<script>
    /**
     * @Oracode Script: Cookie Consent Banner Management
     * 🎯 Purpose: Handle cookie consent banner interactions and API calls
     * 🛡️ Privacy: GDPR compliant, works for all visitors
     * 🧱 Architecture: Vanilla JS, no external dependencies
     */
    document.addEventListener('DOMContentLoaded', function() {
        const CookieConsentBanner = {
            // Configuration
            config: {
                apiEndpoint: '/cookie-consent',
                storageKey: 'florenceegi_cookie_consent',
                showOnConsented: {{ $showOnConsented ? 'true' : 'false' }},
                position: '{{ $position }}',
                theme: '{{ $theme }}',
                translations: {
                    categories: {
                        essential: {
                            label: @json(__('gdpr.cookie.categories.essential.label')),
                            description: @json(__('gdpr.cookie.categories.essential.description'))
                        },
                        functional: {
                            label: @json(__('gdpr.cookie.categories.functional.label')),
                            description: @json(__('gdpr.cookie.categories.functional.description'))
                        },
                        analytics: {
                            label: @json(__('gdpr.cookie.categories.analytics.label')),
                            description: @json(__('gdpr.cookie.categories.analytics.description'))
                        },
                        marketing: {
                            label: @json(__('gdpr.cookie.categories.marketing.label')),
                            description: @json(__('gdpr.cookie.categories.marketing.description'))
                        },
                        profiling: {
                            label: @json(__('gdpr.cookie.categories.profiling.label')),
                            description: @json(__('gdpr.cookie.categories.profiling.description'))
                        }
                    },
                    required: @json(__('gdpr.cookie.banner.required'))
                }
            },

            // DOM Elements
            elements: {},

            // Current consent state
            consentState: {
                hasConsented: false,
                preferences: {},
                availableCategories: {}
            },

            // Initialize the banner
            init() {
                console.log('🎬 Cookie Banner Init Started');
                this.cacheElements();
                this.bindEvents();
                this.loadConsentStatus();
            },

            // Cache DOM elements
            cacheElements() {
                this.elements = {
                    banner: document.getElementById('gdpr-cookie-banner'),
                    acceptAll: document.getElementById('cookie-accept-all'),
                    rejectOptional: document.getElementById('cookie-reject-optional'),
                    customize: document.getElementById('cookie-customize'),
                    preferences: document.getElementById('cookie-preferences'),
                    preferencesActions: document.getElementById('cookie-preferences-actions'),
                    savePreferences: document.getElementById('cookie-save-preferences'),
                    closePreferences: document.getElementById('cookie-close-preferences'),
                    close: document.getElementById('cookie-banner-close'),
                    loading: document.getElementById('cookie-banner-loading'),
                    categoriesContainer: document.querySelector('.gdpr-cookie-banner__categories')
                };

                console.log('🔗 DOM Elements cached:', {
                    banner: !!this.elements.banner,
                    acceptAll: !!this.elements.acceptAll,
                    rejectOptional: !!this.elements.rejectOptional,
                    customize: !!this.elements.customize
                });
            },

            // Bind event listeners
            bindEvents() {
                if (this.elements.acceptAll) {
                    this.elements.acceptAll.addEventListener('click', () => this.acceptAll());
                }

                if (this.elements.rejectOptional) {
                    this.elements.rejectOptional.addEventListener('click', () => this.rejectOptional());
                }

                if (this.elements.customize) {
                    this.elements.customize.addEventListener('click', () => this.togglePreferences());
                }

                if (this.elements.savePreferences) {
                    this.elements.savePreferences.addEventListener('click', () => this.savePreferences());
                }

                if (this.elements.closePreferences) {
                    this.elements.closePreferences.addEventListener('click', () => this
                        .togglePreferences());
                }

                if (this.elements.close) {
                    this.elements.close.addEventListener('click', () => this.hideBanner());
                }

                // ESC key to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.elements.banner.style.display !== 'none') {
                        this.hideBanner();
                    }
                });
            },

            // Load current consent status from API
            async loadConsentStatus() {
                try {
                    const response = await fetch(`${this.config.apiEndpoint}/status`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        console.log('🔍 API Response Data:', {
                            fullApiResponse: data,
                            dataConsents: data.data.consents,
                            availableCategories: data.available_categories,
                            dataSource: data.data.source
                        });

                        this.consentState.availableCategories = data.available_categories || {};

                        // For anonymous users, check localStorage for saved preferences
                        if (data.data.source === 'anonymous') {
                            console.log('👤 Anonymous user detected, checking localStorage...');
                            const localConsents = this.getLocalConsents();
                            if (localConsents) {
                                console.log('📱 Found localStorage preferences:', localConsents);
                                this.consentState.preferences = localConsents;
                            } else {
                                console.log('📭 No localStorage preferences, using API defaults');
                                this.consentState.preferences = data.data.consents || {};
                            }
                        } else {
                            // For authenticated users, use API data
                            this.consentState.preferences = data.data.consents || {};
                        }

                        console.log('🏪 Final State Updated:', {
                            finalPreferences: this.consentState.preferences,
                            availableCategories: this.consentState.availableCategories,
                            source: data.data.source
                        });

                        // Check if user has already given consent (either in DB or localStorage)
                        const hasDbConsent = data.data.source && data.data.source !== 'anonymous';
                        const hasLocalConsent = this.hasLocalConsent();
                        this.consentState.hasConsented = hasDbConsent || hasLocalConsent;

                        console.log('Cookie Consent Debug:', {
                            hasDbConsent,
                            hasLocalConsent,
                            finalHasConsented: this.consentState.hasConsented,
                            source: data.data.source,
                            preferences: this.consentState.preferences
                        });

                        this.renderCategories();
                        this.updateBannerVisibility();
                    } else {
                        this.handleError('Failed to load consent status');
                    }
                } catch (error) {
                    console.error('Cookie Consent: Failed to load status', error);
                    this.loadFallbackState();
                }
            },

            // Get local consents from localStorage (for anonymous users)
            getLocalConsents() {
                try {
                    const stored = localStorage.getItem(this.config.storageKey);
                    if (stored) {
                        const parsedState = JSON.parse(stored);
                        console.log('📱 getLocalConsents found:', parsedState);
                        return parsedState.consents || null;
                    }
                    return null;
                } catch (error) {
                    console.warn('Cookie Consent: Failed to read localStorage consents', error);
                    return null;
                }
            },

            // Check if user has local consent (for anonymous users)
            hasLocalConsent() {
                try {
                    const stored = localStorage.getItem(this.config.storageKey);
                    if (stored) {
                        const parsedState = JSON.parse(stored);
                        const hasConsent = parsedState.hasConsented === true;
                        console.log('Local consent check:', {
                            storageKey: this.config.storageKey,
                            stored: !!stored,
                            parsedHasConsented: parsedState.hasConsented,
                            finalHasConsent: hasConsent
                        });
                        return hasConsent;
                    }
                    return false;
                } catch (error) {
                    console.error('Error checking local consent:', error);
                    return false;
                }
            },

            // Load fallback state if API fails
            loadFallbackState() {
                try {
                    const stored = localStorage.getItem(this.config.storageKey);
                    if (stored) {
                        const parsedState = JSON.parse(stored);
                        this.consentState.preferences = parsedState.consents || {};
                        this.consentState.hasConsented = parsedState.hasConsented === true;
                        console.log('Loaded fallback state:', parsedState);
                    } else {
                        console.log('No fallback state found in localStorage');
                    }
                } catch (error) {
                    console.error('Cookie Consent: Failed to load fallback state', error);
                }

                this.updateBannerVisibility();
            },

            // Render available categories in preferences
            renderCategories() {
                if (!this.elements.categoriesContainer || !this.consentState.availableCategories) {
                    console.warn('🚨 renderCategories: Missing container or categories', {
                        hasContainer: !!this.elements.categoriesContainer,
                        hasCategories: !!this.consentState.availableCategories
                    });
                    return;
                }

                console.log('🎨 renderCategories: Rendering categories', {
                    availableCategories: this.consentState.availableCategories,
                    currentPreferences: this.consentState.preferences
                });

                const categories = Object.entries(this.consentState.availableCategories);
                this.elements.categoriesContainer.innerHTML = categories.map(([key, category]) => {
                    const isChecked = this.consentState.preferences[key] === true;
                    const isRequired = category.required === true;

                    console.log(`🔲 Category ${key}:`, {
                        isChecked,
                        preferenceValue: this.consentState.preferences[key],
                        isRequired,
                        category
                    });

                    // Get translated text or fallback to API data
                    const translation = this.config.translations.categories[key];
                    const label = translation ? translation.label : (category.label || key);
                    const description = translation ? translation.description : (category
                        .description || '');

                    return `
                    <div class="gdpr-cookie-banner__category">
                        <input
                            type="checkbox"
                            id="cookie-category-${key}"
                            class="gdpr-cookie-banner__category-toggle"
                            ${isChecked ? 'checked' : ''}
                            ${isRequired ? 'disabled' : ''}
                            data-category="${key}"
                        />
                        <div class="gdpr-cookie-banner__category-info">
                            <h4 class="gdpr-cookie-banner__category-name">
                                ${this.escapeHtml(label)}
                                ${isRequired ? `<span class="gdpr-cookie-banner__category-required">(${this.config.translations.required})</span>` : ''}
                            </h4>
                            <p class="gdpr-cookie-banner__category-description">
                                ${this.escapeHtml(description)}
                            </p>
                        </div>
                    </div>
                `;
                }).join('');
            },

            // Update banner visibility based on consent state
            updateBannerVisibility() {
                const shouldShow = !this.consentState.hasConsented || this.config.showOnConsented;

                if (shouldShow) {
                    this.showBanner();
                } else {
                    this.hideBanner();
                }
            },

            // Show the banner
            showBanner() {
                if (this.elements.banner) {
                    this.elements.banner.style.display = 'block';

                    // Update close button visibility
                    if (this.elements.close) {
                        this.elements.close.style.display = this.consentState.hasConsented ? 'block' :
                            'none';
                    }
                }
            },

            // Hide the banner
            hideBanner() {
                if (this.elements.banner) {
                    this.elements.banner.style.display = 'none';
                }
            },

            // Accept all cookies
            async acceptAll() {
                console.log('✅ Accept All clicked');
                const allConsents = {};

                // Set all available categories to true
                Object.keys(this.consentState.availableCategories).forEach(key => {
                    allConsents[key] = true;
                });

                console.log('📋 Prepared consents for Accept All:', allConsents);
                await this.saveConsents(allConsents, 'banner');
            },

            // Reject optional cookies (keep only essential)
            async rejectOptional() {
                console.log('🚫 Reject Optional clicked');
                const essentialOnly = {};

                // Set only essential to true, others to false
                Object.entries(this.consentState.availableCategories).forEach(([key, category]) => {
                    essentialOnly[key] = category.required === true;
                });

                console.log('📋 Prepared consents for Essential Only:', essentialOnly);
                await this.saveConsents(essentialOnly, 'banner');
            },

            // Toggle preferences panel
            togglePreferences() {
                const isVisible = this.elements.preferences.style.display !== 'none';

                console.log('🔧 togglePreferences:', {
                    wasVisible: isVisible,
                    willBeVisible: !isVisible,
                    currentPreferences: this.consentState.preferences
                });

                this.elements.preferences.style.display = isVisible ? 'none' : 'block';
                this.elements.preferencesActions.style.display = isVisible ? 'none' : 'block';

                // RE-RENDER categories every time preferences are opened to ensure current state
                if (!isVisible) {
                    console.log(
                        '🔄 togglePreferences: Re-rendering categories because opening preferences');
                    this.renderCategories();
                }

                // Update ARIA attributes
                const expandedState = !isVisible;
                this.elements.customize.setAttribute('aria-expanded', expandedState);
                this.elements.closePreferences.setAttribute('aria-expanded', expandedState);
                this.elements.preferences.setAttribute('aria-expanded', expandedState);
            },

            // Save custom preferences
            async savePreferences() {
                const preferences = {};

                // Get preferences from checkboxes
                const checkboxes = this.elements.categoriesContainer.querySelectorAll(
                    'input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    const category = checkbox.dataset.category;
                    preferences[category] = checkbox.checked;
                });

                await this.saveConsents(preferences, 'preferences');
            },

            // Save consents to API and local storage
            async saveConsents(consents, source = 'api') {
                this.showLoading(true);

                try {
                    // Prepare consent data
                    const consentData = {
                        consents: consents,
                        source: source,
                        consent_version: '1.0'
                    };

                    // Prepare headers
                    const headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    };

                    // Add CSRF token if available
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                    }

                    console.log('🚀 Sending consent data:', {
                        url: `${this.config.apiEndpoint}/save`,
                        headers: headers,
                        data: consentData,
                        userAuthenticated: 'Checking if user is authenticated from previous API calls...'
                    });

                    // Save to API
                    const response = await fetch(`${this.config.apiEndpoint}/save`, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(consentData)
                    });

                    console.log('📡 API Save Response:', {
                        ok: response.ok,
                        status: response.status,
                        statusText: response.statusText,
                        headers: Object.fromEntries(response.headers.entries())
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('❌ API Error Response:', errorText);
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }

                    const result = await response.json();
                    console.log('📋 API Save JSON Response:', {
                        fullResponse: result,
                        success: result.success,
                        error: result.error,
                        data: result.data
                    });

                    if (result.success) {
                        // Update local state
                        this.consentState.preferences = consents;
                        this.consentState.hasConsented = true;

                        // Save to localStorage for all users (backup)
                        try {
                            const storageData = {
                                consents: consents,
                                timestamp: Date.now(),
                                source: source,
                                hasConsented: true
                            };
                            localStorage.setItem(this.config.storageKey, JSON.stringify(storageData));
                            console.log('Consent saved to localStorage:', storageData);
                        } catch (error) {
                            console.warn('Cookie Consent: Failed to save to localStorage', error);
                        }

                        // Hide banner and preferences
                        this.hideBanner();

                        // Trigger consent event for other scripts
                        this.triggerConsentEvent(consents);

                    } else {
                        this.handleError(result.error || 'Failed to save consent');
                    }
                } catch (error) {
                    console.error('Cookie Consent: Failed to save consents', error);
                    this.handleError('Network error while saving consent');
                } finally {
                    this.showLoading(false);
                }
            },

            // Show/hide loading state
            showLoading(show) {
                if (this.elements.loading) {
                    this.elements.loading.style.display = show ? 'flex' : 'none';
                }
            },

            // Handle errors
            handleError(message) {
                console.error('Cookie Consent Error:', message);

                // You could show a toast notification here
                // For now, we'll just log the error

                this.showLoading(false);
            },

            // Trigger custom event for other scripts to listen to
            triggerConsentEvent(consents) {
                const event = new CustomEvent('cookieConsentUpdated', {
                    detail: {
                        consents: consents,
                        timestamp: Date.now()
                    }
                });

                document.dispatchEvent(event);
            },

            // Utility: Escape HTML
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };

        // Initialize the banner
        CookieConsentBanner.init();

        // Expose to global scope for external access
        window.FlorenceEGI = window.FlorenceEGI || {};
        window.FlorenceEGI.CookieConsentBanner = CookieConsentBanner;

        // Expose simple function for menu links
        window.cookieBannerManager = {
            showBanner: function() {
                CookieConsentBanner.showBanner();
            },
            showPreferences: function() {
                CookieConsentBanner.showBanner();
                // Auto-expand preferences after showing banner
                setTimeout(() => {
                    const customizeBtn = document.getElementById('cookie-customize');
                    if (customizeBtn) {
                        customizeBtn.click();
                    }
                }, 100);
            }
        };
    });
</script>
