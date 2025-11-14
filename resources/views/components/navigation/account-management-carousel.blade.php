@props([
    'user' => null,
    'variant' => 'default',
    'containerClass' => '',
])

@php
    $currentUser = $user ?? Auth::user();
    $accountActions = [
        [
            'label' => __('menu.edit_personal_data'),
            'href' => route('user.domains.personal-data'),
        ],
    ];

    if ($currentUser?->can('manage_profile')) {
        $accountActions[] = [
            'label' => __('Profile'),
            'href' => route('profile.show'),
        ];
        $accountActions[] = [
            'label' => __('menu.profile_images'),
            'href' => route('gdpr.profile-images'),
        ];
        $accountActions[] = [
            'label' => __('menu.biography_items.manage'),
            'href' => route('biography.manage'),
        ];
        $accountActions[] = [
            'label' => __('statistics.statistics_dashboard'),
            'href' => route('statistics.index'),
        ];
    }

    $creatorOnboardingSummaryUrl = \Illuminate\Support\Facades\Route::has('creator.onboarding.summary')
        ? route('creator.onboarding.summary')
        : null;

    $pspActions = [
        [
            'type' => 'button',
            'label' => __('menu.psp_open_setup'),
            'variant' => 'primary',
            'action' => 'wallet',
        ],
    ];

    if ($creatorOnboardingSummaryUrl !== null) {
        $pspActions[] = [
            'type' => 'link',
            'label' => __('menu.psp_onboarding_summary'),
            'href' => $creatorOnboardingSummaryUrl,
        ];
    }

    $pspActions[] = [
        'type' => 'link',
        'label' => __('menu.psp_request_support'),
        'href' => 'mailto:support@florenceegi.it',
        'is_external' => true,
    ];

    $accountSlides = [
        [
            'key' => 'account',
            'title' => __('menu.manage_account'),
            'description' => null,
            'actions' => collect($accountActions)
                ->map(
                    fn($action) => [
                        'type' => 'link',
                        'label' => $action['label'],
                        'href' => $action['href'],
                    ],
                )
                ->toArray(),
        ],
        [
            'key' => 'psp',
            'title' => __('menu.psp_section_title'),
            'description' => __('menu.psp_section_hint'),
            'actions' => $pspActions,
        ],
    ];

    $accountSlidesCount = count($accountSlides);
@endphp

@php
    $variantClass = $variant === 'compact' ? 'navigation-account-carousel--compact' : '';
@endphp

@php
    $containerClasses = trim("navigation-account-carousel $variantClass mega-card $containerClass");
@endphp

<div class="{{ $containerClasses }}" data-account-carousel>
    <div class="navigation-account-carousel__header">
        <div class="navigation-account-carousel__title">
            <div class="navigation-account-carousel__icon">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="navigation-account-carousel__eyebrow">{{ __('menu.manage_account') }}</p>
                <h4 class="navigation-account-carousel__heading">{{ __('menu.manage_account') }}</h4>
            </div>
        </div>
        @if ($accountSlidesCount > 1)
            <div class="navigation-account-carousel__nav">
                <button type="button" data-account-carousel-prev aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" data-account-carousel-next aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

    <div class="navigation-account-carousel__viewport">
        <div class="navigation-account-carousel__track" data-account-carousel-track>
            @foreach ($accountSlides as $slideIndex => $slide)
                <div class="navigation-account-carousel__slide" data-account-carousel-slide>
                    <div class="navigation-account-carousel__slide-card">
                        <div class="navigation-account-carousel__slide-head">
                            <div>
                                <p class="navigation-account-carousel__slide-title">{{ $slide['title'] }}</p>
                                @if (!empty($slide['description']))
                                    <p class="navigation-account-carousel__slide-description">
                                        {{ $slide['description'] }}
                                    </p>
                                @endif
                            </div>
                            <span class="navigation-account-carousel__counter">{{ sprintf('%02d', $slideIndex + 1) }}
                                / {{ sprintf('%02d', $accountSlidesCount) }}</span>
                        </div>
                        <div class="navigation-account-carousel__actions">
                            @foreach ($slide['actions'] as $action)
                                @if (($action['type'] ?? 'link') === 'button')
                                    <button type="button"
                                        @if (($action['action'] ?? null) === 'wallet') onclick="window.openWalletWelcomeModalSafe && window.openWalletWelcomeModalSafe();" @endif
                                        class="navigation-account-carousel__action {{ ($action['variant'] ?? null) === 'primary' ? 'navigation-account-carousel__action--primary' : '' }}">
                                        <span>{{ $action['label'] }}</span>
                                        <span class="navigation-account-carousel__action-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </button>
                                @else
                                    <a href="{{ $action['href'] }}"
                                        @if (!empty($action['is_external'])) target="_blank" rel="noopener noreferrer" @endif
                                        class="navigation-account-carousel__action">
                                        <span>{{ $action['label'] }}</span>
                                        <span class="navigation-account-carousel__action-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($accountSlidesCount > 1)
        <div class="navigation-account-carousel__dots" data-account-carousel-dots>
            @foreach ($accountSlides as $slideIndex => $slide)
                <button type="button"
                    class="navigation-account-carousel__dot {{ $slideIndex === 0 ? 'navigation-account-carousel__dot--active' : '' }}"
                    data-account-carousel-dot="{{ $slideIndex }}" aria-label="{{ $slide['title'] }}">
                    <span class="sr-only">{{ $slide['title'] }}</span>
                </button>
            @endforeach
        </div>
    @endif
</div>

@once
    <style>
        .navigation-account-carousel {
            border-radius: 1.5rem;
            border: 1px solid rgba(16, 185, 129, 0.25);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(13, 148, 136, 0.08));
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dark .navigation-account-carousel {
            border-color: rgba(16, 185, 129, 0.35);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.16), rgba(15, 118, 110, 0.16));
        }

        .navigation-account-carousel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .navigation-account-carousel__title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navigation-account-carousel__icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.9rem;
            background: linear-gradient(135deg, #10b981, #0f766e);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.25);
        }

        .navigation-account-carousel__eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(236, 253, 245, 0.85);
        }

        .navigation-account-carousel__heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #e6f4ef;
        }

        .navigation-account-carousel__nav {
            display: flex;
            gap: 0.5rem;
        }

        .navigation-account-carousel__nav button {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.8rem;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .navigation-account-carousel__nav button:hover:not(:disabled) {
            background: rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .navigation-account-carousel__nav button:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .navigation-account-carousel__viewport {
            overflow: hidden;
        }

        .navigation-account-carousel__track {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navigation-account-carousel__slide {
            flex: 0 0 100%;
            padding-inline: 0.15rem;
        }

        .navigation-account-carousel__slide-card {
            border-radius: 1.25rem;
            padding: 1.25rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-height: 210px;
        }

        .dark .navigation-account-carousel__slide-card {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(16, 185, 129, 0.4);
        }

        .navigation-account-carousel__slide-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .navigation-account-carousel__slide-title {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-weight: 700;
            color: #065f46;
        }

        .dark .navigation-account-carousel__slide-title {
            color: #c1f0da;
        }

        .navigation-account-carousel__slide-description {
            font-size: 0.85rem;
            color: #047857;
            margin-top: 0.35rem;
        }

        .dark .navigation-account-carousel__slide-description {
            color: #b8ebd3;
        }

        .navigation-account-carousel__counter {
            font-size: 0.78rem;
            font-weight: 700;
            color: #047857;
        }

        .dark .navigation-account-carousel__counter {
            color: #6ee7b7;
        }

        .navigation-account-carousel__actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.65rem;
        }

        .navigation-account-carousel__action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.9rem;
            border-radius: 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #064e3b;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.35);
            transition: all 0.2s ease;
            text-align: left;
        }

        .navigation-account-carousel__action:hover {
            background: rgba(16, 185, 129, 0.18);
            transform: translateY(-1px);
        }

        .dark .navigation-account-carousel__action {
            color: #e2f4ec;
            background: rgba(16, 185, 129, 0.22);
            border-color: rgba(16, 185, 129, 0.45);
        }

        .dark .navigation-account-carousel__action:hover {
            background: rgba(16, 185, 129, 0.32);
        }

        .navigation-account-carousel__action--primary {
            background: linear-gradient(135deg, #10b981, #047857);
            color: #ecfdf5;
            border-color: transparent;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        }

        .navigation-account-carousel__action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 0.5rem;
        }

        .navigation-account-carousel__dots {
            display: flex;
            justify-content: center;
            gap: 0.45rem;
        }

        .navigation-account-carousel__dot {
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
        }

        .navigation-account-carousel__dot--active {
            width: 1.5rem;
            background: linear-gradient(135deg, #10b981, #0d9488);
        }

        .dark .navigation-account-carousel__dot {
            background: rgba(16, 185, 129, 0.45);
        }

        .dark .navigation-account-carousel__dot--active {
            background: linear-gradient(135deg, #34d399, #10b981);
        }

        .navigation-account-carousel--compact {
            padding: 1.1rem;
            border-radius: 1.25rem;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__icon {
            width: 2rem;
            height: 2rem;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__heading {
            font-size: 1rem;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__slide-card {
            padding: 1rem;
            min-height: auto;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__slide-title {
            font-size: 0.78rem;
            letter-spacing: 0.15em;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__slide-description {
            font-size: 0.8rem;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__actions {
            grid-template-columns: 1fr;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__action {
            font-size: 0.8rem;
            padding: 0.55rem 0.8rem;
        }

        .navigation-account-carousel--compact .navigation-account-carousel__nav button {
            width: 2rem;
            height: 2rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('[data-account-carousel]');

            carousels.forEach(carousel => {
                if (carousel.dataset.accountCarouselInitialized === 'true') {
                    return;
                }

                const viewport = carousel.querySelector('.navigation-account-carousel__viewport');
                const track = carousel.querySelector('[data-account-carousel-track]');
                const slides = carousel.querySelectorAll('[data-account-carousel-slide]');
                const prevBtn = carousel.querySelector('[data-account-carousel-prev]');
                const nextBtn = carousel.querySelector('[data-account-carousel-next]');
                const dots = carousel.querySelectorAll('[data-account-carousel-dot]');

                if (!track || !slides.length || !viewport) {
                    return;
                }

                let currentSlide = 0;
                const totalSlides = slides.length;

                const applyTransform = () => {
                    const slideWidth = viewport.getBoundingClientRect().width;
                    track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
                };

                const updateControls = () => {
                    if (prevBtn) {
                        prevBtn.disabled = currentSlide === 0;
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentSlide === totalSlides - 1;
                    }
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('navigation-account-carousel__dot--active',
                            index === currentSlide);
                    });
                };

                const goToSlide = index => {
                    if (index < 0 || index >= totalSlides) {
                        return;
                    }
                    currentSlide = index;
                    applyTransform();
                    updateControls();
                };

                prevBtn?.addEventListener('click', event => {
                    event.preventDefault();
                    goToSlide(currentSlide - 1);
                });

                nextBtn?.addEventListener('click', event => {
                    event.preventDefault();
                    goToSlide(currentSlide + 1);
                });

                dots.forEach((dot, index) => {
                    dot.addEventListener('click', event => {
                        event.preventDefault();
                        goToSlide(index);
                    });
                });

                window.addEventListener('resize', applyTransform);

                carousel.dataset.accountCarouselInitialized = 'true';
                goToSlide(0);
            });
        });
    </script>
@endonce
