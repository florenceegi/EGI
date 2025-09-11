/**
 * Vanilla Mobile Navigation Menu - Pure JavaScript Implementation
 * Revolutionary mobile experience with touch gestures and smooth animations
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Vanilla Mobile Menu JS loaded');
    initializeMobileMenu();
    initializeTouchGestures();
    initializeCollectionDropdown();
    initializeActionButtons();
    initializeCarouselButtons();
    initializeCarouselScrolling();
});

function initializeMobileMenu() {
    const trigger = document.querySelector('[data-mobile-menu-trigger]');
    const menu = document.querySelector('[data-mobile-menu]');
    const overlay = document.querySelector('[data-mobile-overlay]');
    const closeArea = document.querySelector('[data-mobile-close-area]');
    const content = document.querySelector('[data-mobile-content]');
    const closeBtn = document.querySelector('[data-mobile-close]');
    const hamburgerIcon = trigger?.querySelector('.hamburger-icon');
    const closeIcon = trigger?.querySelector('.close-icon');

    if (!trigger || !menu || !content) return;

    let isOpen = false;

    // Toggle menu
    function toggleMenu() {
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    // Open menu
    function openMobileMenu() {
        isOpen = true;

        // Show menu container
        menu.classList.remove('hidden');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';

        // Animate content slide in
        requestAnimationFrame(() => {
            content.classList.remove('translate-x-full');
            content.classList.add('translate-x-0');
        });

        // Forza setup bottoni e layout carosello dopo animazione
        console.log('🚀 Menu aperto - forzo setup bottoni carousel');
        setTimeout(() => {
            forceCarouselButtonsSetup();
            ensureGuestCarouselLayout();
        }, 300);

        setTimeout(() => {
            forceCarouselButtonsSetup();
            ensureGuestCarouselLayout();
        }, 500);

        // Focus management
        setTimeout(() => {
            const firstFocusable = content.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) {
                firstFocusable.focus();
            }
        }, 300);
    }

    // Close menu
    function closeMobileMenu() {
        isOpen = false;

        // Animate content slide out
        content.classList.remove('translate-x-0');
        content.classList.add('translate-x-full');

        // Restore body scroll
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';

        // Update button icons
        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }

        // Remove card animations
        const cards = content.querySelectorAll('.mobile-card');
        cards.forEach(card => {
            card.style.animationDelay = '';
            card.classList.remove('animate-slide-in-left');
        });

        // Hide menu container after animation
        setTimeout(() => {
            if (!isOpen) { // Double check it wasn't reopened
                menu.classList.add('hidden');
            }
        }, 300);

        // Return focus to trigger
        trigger.focus();
    }

    // Event listeners
    trigger.addEventListener('click', toggleMenu);

    // Chiudi menu quando si apre la universal search (evento legacy o API nuova)
    window.addEventListener('universal-search-open', () => {
        if(isOpen) closeMobileMenu();
    });
    window.addEventListener('keydown', (e)=>{
        if((e.metaKey||e.ctrlKey) && e.key.toLowerCase()==='k' && isOpen){
            // Shortcut aprirà la search, chiudi prima
            closeMobileMenu();
        }
    });
    // Se viene chiamata l'API nuova globale, intercettiamo via MutationObserver dello stato hidden sulla modale? Non necessario, evento sufficiente.

    if (closeBtn) {
        closeBtn.addEventListener('click', closeMobileMenu);
    }

    if (closeArea) {
        closeArea.addEventListener('click', function(e) {
            console.log('🔥 CLOSE AREA: Click per chiudere menu');
            closeMobileMenu();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function(e) {
            // Non chiudere se il click è su un bottone del carousel
            if (e.target.closest('#btn-left-main-carousel') ||
                e.target.closest('#btn-right-main-carousel') ||
                e.target.closest('#btn-left-guest-carousel') ||
                e.target.closest('#btn-right-guest-carousel') ||
                e.target.closest('[data-scroll-direction]')) {
                console.log('🔥 OVERLAY: Click su bottone carousel - NON chiudo menu!');
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            console.log('🔥 OVERLAY: Click normale - chiudo menu');
            closeMobileMenu();
        });
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeMobileMenu();
        }
    });

    // Close on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 640 && isOpen) {
            closeMobileMenu();
        }
    });

    // Store methods globally for external access
    window.VanillaMobileMenu = {
        open: openMobileMenu,
        close: closeMobileMenu,
        toggle: toggleMenu,
        isOpen: () => isOpen
    };
}

// Initialize carousel buttons with proper logic
function initializeCarouselButtons() {
    console.log('🎠 Initializing carousel buttons...');

    // Main carousel buttons
    const mainLeftBtn = document.getElementById('btn-left-main-carousel');
    const mainRightBtn = document.getElementById('btn-right-main-carousel');

    // Guest carousel buttons
    const guestLeftBtn = document.getElementById('btn-left-guest-carousel');
    const guestRightBtn = document.getElementById('btn-right-guest-carousel');

    console.log('Main buttons found:', {left: !!mainLeftBtn, right: !!mainRightBtn});
    console.log('Guest buttons found:', {left: !!guestLeftBtn, right: !!guestRightBtn});

    // Main carousel LEFT
    if (mainLeftBtn) {
        // Click event per desktop
        mainLeftBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            scrollByButton(this, 'left');
        });
        // Pointerup per device touch
        mainLeftBtn.addEventListener('pointerup', function(e) {
            if (e.pointerType === 'touch') {
                scrollByButton(this, 'left');
            }
        }, { passive: true });
    }

    // Main carousel RIGHT
    if (mainRightBtn) {
        // Click event per desktop
        mainRightBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            scrollByButton(this, 'right');
        });
        // Pointerup per device touch
        mainRightBtn.addEventListener('pointerup', function(e) {
            if (e.pointerType === 'touch') {
                scrollByButton(this, 'right');
            }
        }, { passive: true });
    }

    // Guest carousel LEFT
    if (guestLeftBtn) {
        // Click (mouse e tap sintetizzato)
        guestLeftBtn.addEventListener('click', function() {
            console.log('🖱️ GUEST LEFT CLICK');
            scrollByButton(this, 'left');
        });
        // Pointerup per device touch che talvolta non generano click
        guestLeftBtn.addEventListener('pointerup', function(e) {
            if (e.pointerType === 'touch') {
                console.log('👆 GUEST LEFT POINTERUP');
                scrollByButton(this, 'left');
            }
        }, { passive: true });
    }

    // Guest carousel RIGHT
    if (guestRightBtn) {
        // Click (mouse e tap sintetizzato)
        guestRightBtn.addEventListener('click', function() {
            console.log('🖱️ GUEST RIGHT CLICK');
            scrollByButton(this, 'right');
        });
        // Pointerup per device touch che talvolta non generano click
        guestRightBtn.addEventListener('pointerup', function(e) {
            if (e.pointerType === 'touch') {
                console.log('👆 GUEST RIGHT POINTERUP');
                scrollByButton(this, 'right');
            }
        }, { passive: true });
    }
}

// Initialize carousel scrolling functionality
function initializeCarouselScrolling() {
    console.log('🎠 Initializing carousel scrolling...');

    // Main carousel
    const mainCarousel = document.querySelector('.carousel-container-menu');
    if (mainCarousel) {
        console.log('✅ Main carousel found, setting up scrolling');
        setupCarouselScrolling(mainCarousel, 'main');
    }

    // Guest carousel
    const guestCarousel = document.querySelector('.menu-guest-collections-carousel .carousel-container');
    if (guestCarousel) {
        console.log('✅ Guest carousel found, setting up scrolling');
        setupCarouselScrolling(guestCarousel, 'guest');
    }
}

function setupCarouselScrolling(carousel, type) {
    // Mouse wheel scrolling - convert vertical to horizontal
    carousel.addEventListener('wheel', function(e) {
        if (e.target.closest('#btn-left-main-carousel, #btn-right-main-carousel, #btn-left-guest-carousel, #btn-right-guest-carousel')) return;
        e.preventDefault();
        e.stopPropagation();
        this.scrollLeft += e.deltaY;
    }, { passive: false });

    // Touch/drag scrolling
    let isDown = false;
    let startX;
    let scrollLeft;

    // Mouse events
    carousel.addEventListener('mousedown', function(e) {
        if (e.target.closest('#btn-left-main-carousel, #btn-right-main-carousel, #btn-left-guest-carousel, #btn-right-guest-carousel')) return;
        isDown = true;
        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
        carousel.style.cursor = 'grabbing';
        e.preventDefault();
    });

    carousel.addEventListener('mouseleave', function() {
        isDown = false;
        carousel.style.cursor = 'grab';
    });

    carousel.addEventListener('mouseup', function() {
        isDown = false;
        carousel.style.cursor = 'grab';
    });

    carousel.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        if (e.target.closest('#btn-left-main-carousel, #btn-right-main-carousel, #btn-left-guest-carousel, #btn-right-guest-carousel')) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2;
        carousel.scrollLeft = scrollLeft - walk;
    });

    // Touch events for mobile scrolling (not for buttons)
    let touchStartX = 0;
    let touchScrollLeft = 0;

    carousel.addEventListener('touchstart', function(e) {
        // Ignore touch on buttons - let button handlers manage them
        if (e.target.closest('#btn-left-main-carousel, #btn-right-main-carousel, #btn-left-guest-carousel, #btn-right-guest-carousel')) {
            console.log(`🎠 ${type} carousel: Ignoring touchstart on button, letting button handler manage it`);
            return;
        }

        touchStartX = e.touches[0].pageX - carousel.offsetLeft;
        touchScrollLeft = carousel.scrollLeft;
    });

    carousel.addEventListener('touchmove', function(e) {
        // Ignore touch on buttons
        if (e.target.closest('#btn-left-main-carousel, #btn-right-main-carousel, #btn-left-guest-carousel, #btn-right-guest-carousel')) return;

        if (!touchStartX) return;
        e.preventDefault();
        const x = e.touches[0].pageX - carousel.offsetLeft;
        const walk = (x - touchStartX) * 2;
        carousel.scrollLeft = touchScrollLeft - walk;
    }, { passive: false });

    carousel.addEventListener('touchend', function() {
        if (!touchStartX) return;
        touchStartX = 0;
    });

    // Set cursor
    carousel.style.cursor = 'grab';

    console.log(`✅ ${type} carousel scrolling initialized`);
}

// Funzioni per gestire il carousel CORRETTAMENTE
function findScrollableCarousel() {
    const allCarousels = document.querySelectorAll('[class*="carousel-container"]');
    let workingCarousel = null;

    allCarousels.forEach((c) => {
        if (c.scrollWidth > c.clientWidth && !workingCarousel) {
            workingCarousel = c;
        }
    });

    return workingCarousel;
}

// Scroll centralizzato per i bottoni guest che gestisce layout, click/touch e fallback
let lastGuestScrollTs = 0;
function scrollGuest(direction) {
    const now = Date.now();
    if (now - lastGuestScrollTs < 120) return; // de-duplica click/pointerup
    lastGuestScrollTs = now;

    const amount = direction === 'left' ? -320 : 320;

    // 1) Prova il selettore previsto
    let carousel = document.querySelector('.menu-guest-collections-carousel .carousel-container');
    if (!carousel) {
        console.warn('❓ Carousel guest non trovato con selettore previsto, cerco alternativo...');
        carousel = findScrollableCarousel();
    }
    if (!carousel) {
        console.error('❌ Nessun carousel scrollabile trovato');
        return;
    }

    // 2) Forza layout e misura
    ensureGuestCarouselLayout(carousel);
    const pre = {
        scrollWidth: carousel.scrollWidth,
        clientWidth: carousel.clientWidth,
        offsetWidth: carousel.offsetWidth,
        rect: carousel.getBoundingClientRect()
    };
    const isZero = pre.scrollWidth === 0 || pre.clientWidth === 0 || pre.offsetWidth === 0 || pre.rect.width === 0;

    if (!isZero) {
        console.log(`🚀 GUEST ${direction.toUpperCase()} scroll ${amount} (pre: sw=${pre.scrollWidth}, cw=${pre.clientWidth})`);
        carousel.scrollBy({ left: amount, behavior: 'smooth' });
        return;
    }

    console.warn(`⚠️ Dimensioni 0 al click - forzo reflow e fallback (sw=${pre.scrollWidth}, cw=${pre.clientWidth}, ow=${pre.offsetWidth}, bw=${pre.rect.width})`);

    // 3) requestAnimationFrame + piccolo delay per fine animazioni, poi ritenta
    requestAnimationFrame(() => {
        setTimeout(() => {
            ensureGuestCarouselLayout(carousel);
            const mid = {
                scrollWidth: carousel.scrollWidth,
                clientWidth: carousel.clientWidth,
                offsetWidth: carousel.offsetWidth,
                rect: carousel.getBoundingClientRect()
            };
            const stillZero = mid.scrollWidth === 0 || mid.clientWidth === 0 || mid.offsetWidth === 0 || mid.rect.width === 0;

            if (!stillZero) {
                console.log(`✅ Reflow ok, scrollo ${amount} (sw=${mid.scrollWidth}, cw=${mid.clientWidth})`);
                carousel.scrollBy({ left: amount, behavior: 'smooth' });
                return;
            }

            // 4) Prova un carousel alternativo se quello atteso è ancora 0
            const alt = findScrollableCarousel();
            if (alt && alt !== carousel && (alt.scrollWidth > 0 && alt.clientWidth > 0)) {
                console.log('🧭 Uso carousel alternativo trovato da findScrollableCarousel()', alt);
                alt.scrollBy({ left: amount, behavior: 'smooth' });
                return;
            }

            // 5) Fallback finale: translateX del contenitore flex
            const flex = carousel.querySelector('.flex');
            if (flex) {
                const prev = parseInt(flex.getAttribute('data-translate-x') || '0', 10);
                const next = prev + (direction === 'left' ? 320 : -320);
                flex.style.transform = `translateX(${next}px)`;
                flex.setAttribute('data-translate-x', String(next));
                console.log(`🎨 Fallback transform applicato: translateX(${next}px)`);
            } else {
                console.warn('🟨 Fallback transform non applicabile: .flex non trovato dentro il carousel');
            }
        }, 50);
    });
}

function initializeTouchGestures() {
    const content = document.querySelector('[data-mobile-content]');
    if (!content) return;

    let startX = 0;
    let currentX = 0;
    let startY = 0;
    let currentY = 0;
    let isDragging = false;
    let isVerticalScroll = false;
    let isTouchingCarouselButton = false;

    const handleTouchStart = (e) => {
        const target = e.target;
        const isCarouselButton = target.closest('[data-scroll-direction]');

        if (isCarouselButton) {
            console.log('👆 Touch START su bottone carousel. Blocco lo swipe.');
            isTouchingCarouselButton = true;
            // Non chiamare e.preventDefault() qui, altrimenti il 'click' non scatterà
            return;
        }

        isTouchingCarouselButton = false;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        isVerticalScroll = false;
        content.style.transition = 'none';
    };

    const handleTouchMove = (e) => {
        if (isTouchingCarouselButton) {
            return;
        }
        if (!isDragging) return;

        currentX = e.touches[0].clientX;
        currentY = e.touches[0].clientY;

        const deltaX = currentX - startX;
        const deltaY = currentY - startY;

        if (!isVerticalScroll && Math.abs(deltaY) > Math.abs(deltaX)) {
            isVerticalScroll = true;
            return;
        }

        if (!isVerticalScroll && deltaX > 0) {
            e.preventDefault();
            const progress = Math.min(deltaX / content.offsetWidth, 1);
            content.style.transform = `translateX(${deltaX}px)`;
            content.style.opacity = Math.max(0.3, 1 - progress * 0.7);
        }
    };

    const handleTouchEnd = (e) => {
        if (isTouchingCarouselButton) {
            console.log('👆 Touch END su bottone. Eseguo azione carousel e resetto.');
            const btn = e.target.closest('[data-scroll-direction]');
            if (btn) {
                const dir = btn.getAttribute('data-scroll-direction') === 'left' ? 'left' : 'right';
                scrollByButton(btn, dir);
            }
            isTouchingCarouselButton = false;
            return;
        }

        if (!isDragging || isVerticalScroll) {
            isDragging = false;
            return;
        }

        isDragging = false;
        content.style.transition = '';

        const deltaX = currentX - startX;
        const threshold = content.offsetWidth * 0.3;

        if (deltaX > threshold) {
            if (window.VanillaMobileMenu) window.VanillaMobileMenu.close();
        } else {
            content.style.transform = 'translateX(0)';
            content.style.opacity = '1';
        }
    };

    content.addEventListener('touchstart', handleTouchStart, { passive: true });
    content.addEventListener('touchmove', handleTouchMove, { passive: false });
    content.addEventListener('touchend', handleTouchEnd, { passive: false });
}

// Initialize Collection Dropdown functionality
function initializeCollectionDropdown() {
    // Guest layout dropdown
    const guestDropdownButton = document.getElementById('mobile-collection-list-dropdown-button');
    const guestDropdownMenu = document.getElementById('mobile-collection-list-dropdown-menu');

    // App layout dropdown
    const appDropdownButton = document.getElementById('mobile-collection-list-dropdown-button-app');
    const appDropdownMenu = document.getElementById('mobile-collection-list-dropdown-menu-app');

    // Initialize dropdown for guest layout
    if (guestDropdownButton && guestDropdownMenu) {
        initDropdownBehavior(guestDropdownButton, guestDropdownMenu, 'guest');
    }

    // Initialize dropdown for app layout
    if (appDropdownButton && appDropdownMenu) {
        initDropdownBehavior(appDropdownButton, appDropdownMenu, 'app');
    }
}

function initDropdownBehavior(button, menu, layout) {
    let isOpen = false;

    function toggleDropdown() {
        isOpen = !isOpen;
        button.setAttribute('aria-expanded', isOpen);

        if (isOpen) {
            menu.classList.remove('hidden');
            loadUserCollections(layout);
        } else {
            menu.classList.add('hidden');
        }
    }

    button.addEventListener('click', toggleDropdown);

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!button.contains(e.target) && !menu.contains(e.target) && isOpen) {
            isOpen = false;
            button.setAttribute('aria-expanded', false);
            menu.classList.add('hidden');
        }
    });
}

async function loadUserCollections(layout) {
    const loadingEl = document.getElementById(`mobile-collection-list-loading${layout === 'app' ? '-app' : ''}`);
    const emptyEl = document.getElementById(`mobile-collection-list-empty${layout === 'app' ? '-app' : ''}`);
    const errorEl = document.getElementById(`mobile-collection-list-error${layout === 'app' ? '-app' : ''}`);
    const menuEl = document.getElementById(`mobile-collection-list-dropdown-menu${layout === 'app' ? '-app' : ''}`);

    // Show loading state
    loadingEl?.classList.remove('hidden');
    emptyEl?.classList.add('hidden');
    errorEl?.classList.add('hidden');

    try {
        // This would typically fetch from your collections API
        // For now, simulate the API call
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Hide loading and show empty (you'd replace this with real data)
        loadingEl?.classList.add('hidden');
        emptyEl?.classList.remove('hidden');

    } catch (error) {
        console.error('Failed to load collections:', error);
        loadingEl?.classList.add('hidden');
        errorEl?.classList.remove('hidden');
    }
}

// Initialize Action Buttons functionality
function initializeActionButtons() {
    // Create EGI buttons
    const createEgiButtons = document.querySelectorAll('.js-create-egi-contextual-button');
    createEgiButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const authType = this.getAttribute('data-auth-type');
            handleCreateEgiAction(authType);
        });
    });

    // Create Collection buttons
    const createCollectionButtons = document.querySelectorAll('[data-action="open-create-collection-modal"]');
    createCollectionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleCreateCollectionAction();
        });
    });
}

function handleCreateEgiAction(authType) {
    console.log('Create EGI clicked, auth type:', authType);

    if (authType === 'authenticated') {
        // User is logged in, proceed with EGI creation
        console.log('Opening EGI creation for authenticated user');

        // Chiudi il menu mobile prima di navigare
        if (window.VanillaMobileMenu) {
            window.VanillaMobileMenu.close();
        }

        // Triggera l'evento per aprire il modal/flow di creazione EGI
        // Se esiste una funzione globale per creare EGI, la chiama
        if (typeof window.openCreateEgiModal === 'function') {
            window.openCreateEgiModal();
        } else if (typeof window.createEgiFlow === 'function') {
            window.createEgiFlow();
        } else {
            // No fallback redirect - evita navigazione indesiderata
            console.log('No EGI creation function found - check your global functions');
        }
    } else {
        // User is guest, show login/register options
        console.log('Showing auth options for guest user');

        // Chiudi il menu mobile
        if (window.VanillaMobileMenu) {
            window.VanillaMobileMenu.close();
        }

        // Mostra modal di login o redirect
        if (typeof window.showLoginModal === 'function') {
            window.showLoginModal();
        } else {
            // No fallback redirect - evita navigazione indesiderata
            console.log('No auth modal function found - check your global functions');
        }
    }
}

function handleCreateCollectionAction() {
    console.log('Create Collection clicked');

    // Chiudi il menu mobile
    if (window.VanillaMobileMenu) {
        window.VanillaMobileMenu.close();
    }

    // Triggera l'evento per aprire il modal di creazione collezione
    if (typeof window.openCreateCollectionModal === 'function') {
        window.openCreateCollectionModal();
    } else if (typeof window.createCollectionFlow === 'function') {
        window.createCollectionFlow();
    } else {
        // No fallback redirect - evita navigazione indesiderata
        console.log('No collection creation function found - check your global functions');
    }
}// Handle orientation change
window.addEventListener('orientationchange', function() {
    setTimeout(() => {
        if (window.VanillaMobileMenu && window.VanillaMobileMenu.isOpen()) {
            // Re-adjust for new orientation
            const content = document.querySelector('[data-mobile-content]');
            if (content) {
                content.style.transform = 'translateX(0)';
                content.style.opacity = '1';
            }
        }
    }, 100);
});

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slide-in-left {
        animation: slideInLeft 0.4s ease-out backwards;
    }

    /* Enhanced transitions for mobile content */
    [data-mobile-content] {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        background: white !important;
        color: black !important;
    }

    /* Ensure content is visible */
    .mobile-menu-container {
        background: white !important;
        color: black !important;
        z-index: 99999 !important;
    }

    /* Icon transitions */
    [data-mobile-menu-trigger] svg {
        transition: opacity 0.2s ease-in-out;
    }

    /* Touch feedback */
    [data-mobile-menu-trigger]:active {
        transform: scale(0.95);
    }

    /* Improve touch targets */
    .mobile-nav-item {
        min-height: 44px; /* iOS accessibility guideline */
    }

    /* Smooth scrolling for mobile content */
    .mobile-menu-content {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
`;
document.head.appendChild(style);

// Garantisce che il carosello guest abbia layout calcolato (non zero) quando il menu è visibile
function ensureGuestCarouselLayout(containerArg) {
    const container = containerArg || document.querySelector('.menu-guest-collections-carousel .carousel-container');
    if (!container) return;

    const computed = window.getComputedStyle(container);
    const isHidden = computed.display === 'none' || computed.visibility === 'hidden' || container.offsetWidth === 0;
    if (!isHidden) return; // già ok

    // Forza visibilità temporanea per calcolare il layout
    const prev = {
        display: container.style.display,
        visibility: container.style.visibility,
        position: container.style.position,
        left: container.style.left,
        right: container.style.right,
        width: container.style.width,
    };

    // Usa position fixed offscreen per evitare flicker visivo
    container.style.display = 'block';
    container.style.visibility = 'hidden';
    container.style.position = 'fixed';
    container.style.left = '-9999px';
    container.style.width = '100%';

    // Trigger reflow
    // eslint-disable-next-line no-unused-expressions
    container.offsetHeight;

    // Ripristina
    container.style.display = prev.display || '';
    container.style.visibility = prev.visibility || '';
    container.style.position = prev.position || '';
    container.style.left = prev.left || '';
    container.style.right = prev.right || '';
    container.style.width = prev.width || '';
}

// Funzione per forzare la configurazione dei bottoni carousel quando il menu è visibile
function forceCarouselButtonsSetup() {
    console.log('🔧 FORZANDO setup bottoni carousel...');

    const guestLeftBtn = document.getElementById('btn-left-guest-carousel');
    const guestRightBtn = document.getElementById('btn-right-guest-carousel');

    console.log('🔍 Bottoni guest trovati:', { left: !!guestLeftBtn, right: !!guestRightBtn });

    if (guestLeftBtn) {
        console.log('🔧 Forzando setup guest left button');

        // ⚡ FIX CRITICO: Forza la visibilità del bottone con setProperty important
        guestLeftBtn.style.setProperty('visibility', 'visible', 'important');
        guestLeftBtn.style.setProperty('pointer-events', 'auto', 'important');
        guestLeftBtn.style.setProperty('display', 'block', 'important');
        guestLeftBtn.style.setProperty('opacity', '1', 'important');
        guestLeftBtn.style.setProperty('z-index', '9999999', 'important');

        // Aggiungi classe CSS per override aggressivo
        guestLeftBtn.classList.add('force-visible-carousel-btn');

        console.log('🔧 Visibility forzata per left button');

        // NON AGGIUNGERE LISTENER QUI - Lasciamo che initializeCarouselButtons se ne occupi
        console.log('✅ Visibilità guest left button forzata');
    }

    if (guestRightBtn) {
        console.log('🔧 Forzando setup guest right button');

        // ⚡ FIX CRITICO: Forza la visibilità del bottone con setProperty important
        guestRightBtn.style.setProperty('visibility', 'visible', 'important');
        guestRightBtn.style.setProperty('pointer-events', 'auto', 'important');
        guestRightBtn.style.setProperty('display', 'block', 'important');
        guestRightBtn.style.setProperty('opacity', '1', 'important');
        guestRightBtn.style.setProperty('z-index', '9999999', 'important');

        // Aggiungi classe CSS per override aggressivo
        guestRightBtn.classList.add('force-visible-carousel-btn');

        console.log('🔧 Visibility forzata per right button');

        // NON AGGIUNGERE LISTENER QUI - Lasciamo che initializeCarouselButtons se ne occupi
        console.log('✅ Visibilità guest right button forzata');
    }
}

function forceScrollCarousel(direction) {
    console.log('🎯 FORZANDO SCROLL:', direction);

    const carousel = document.querySelector('.menu-guest-collections-carousel .carousel-container');

    if (carousel) {
        const amount = direction === 'left' ? -320 : 320;
        console.log('📦 Carousel trovato, scrolling:', amount);

        // Prova tutti i metodi possibili
        const currentScroll = carousel.scrollLeft;

        // Metodo 1: scrollBy
        carousel.scrollBy({ left: amount, behavior: 'smooth' });

        // Metodo 2: scrollLeft diretto
        setTimeout(() => {
            if (carousel.scrollLeft === currentScroll) {
                console.log('🔧 scrollBy non ha funzionato, provo scrollLeft diretto');
                carousel.scrollLeft = Math.max(0, currentScroll + amount);
            }
        }, 100);

        // Metodo 3: transform CSS se tutto il resto fallisce
        setTimeout(() => {
            if (carousel.scrollLeft === currentScroll) {
                console.log('🔧 Anche scrollLeft non ha funzionato, provo transform CSS');
                const flexContainer = carousel.querySelector('.flex');
                if (flexContainer) {
                    const currentTransform = flexContainer.style.transform || 'translateX(0px)';
                    const currentX = parseInt(currentTransform.match(/-?\d+/) || [0])[0];
                    const newX = currentX + amount;
                    flexContainer.style.transform = `translateX(${newX}px)`;
                    console.log('🎨 Applicato transform:', `translateX(${newX}px)`);
                }
            }
        }, 200);

    } else {
        console.log('❌ Carousel non trovato');
    }
}

// =============== Helpers di scoping per caroselli ===============
// Trova il container del carosello più vicino al bottone cliccato
function findLocalCarouselFromButton(btn) {
    const root = btn.closest('.mega-card') || document;
    // Priorità in base al tipo di bottone
    if (btn.id && btn.id.includes('main')) {
        const main = root.querySelector('.menu-collections-carousel .carousel-container-menu');
        if (main) return main;
    }
    if (btn.id && btn.id.includes('guest')) {
        const guest = root.querySelector('.menu-guest-collections-carousel .carousel-container');
        if (guest) return guest;
    }
    // Fallback: cerca qualunque container noto nel root
    return (
        root.querySelector('.menu-guest-collections-carousel .carousel-container') ||
        root.querySelector('.menu-collections-carousel .carousel-container-menu')
    );
}

let lastLocalScrollTs = 0;
function scrollCarousel(container, direction) {
    const now = Date.now();
    if (now - lastLocalScrollTs < 120) return;
    lastLocalScrollTs = now;

    if (!container) return;
    const amount = direction === 'left' ? -320 : 320;

    // Garantisce layout calcolato anche se nascosto
    ensureGuestCarouselLayout(container);

    const pre = {
        sw: container.scrollWidth,
        cw: container.clientWidth,
        ow: container.offsetWidth,
        rw: container.getBoundingClientRect().width,
    };
    const zero = pre.sw === 0 || pre.cw === 0 || pre.ow === 0 || pre.rw === 0;
    if (!zero) {
        container.scrollBy({ left: amount, behavior: 'smooth' });
        return;
    }

    // Reflow e fallback locale
    requestAnimationFrame(() => {
        setTimeout(() => {
            ensureGuestCarouselLayout(container);
            if (container.scrollWidth > 0 && container.clientWidth > 0) {
                container.scrollBy({ left: amount, behavior: 'smooth' });
                return;
            }
            const flex = container.querySelector('.flex');
            if (flex) {
                const prev = parseInt(flex.getAttribute('data-translate-x') || '0', 10);
                const next = prev + (direction === 'left' ? 320 : -320);
                flex.style.transform = `translateX(${next}px)`;
                flex.setAttribute('data-translate-x', String(next));
            }
        }, 50);
    });
}

function scrollByButton(btn, direction) {
    const container = findLocalCarouselFromButton(btn);
    if (!container) {
        console.warn('⚠️ Nessun container locale trovato per il bottone', btn);
        return;
    }
    scrollCarousel(container, direction);
}
