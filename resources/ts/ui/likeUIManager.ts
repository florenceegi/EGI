/**
 * @Oracode Like UI Manager
 * 🎯 Purpose: Manages UI updates for like operations
 * 🧱 Core Logic: DOM manipulation, state synchronization
 *
 * @package FlorenceEGI/UI
 * @author Padmin D. Curtis
 * @version 1.0.0
 * @date 2025-05-15
 */

import likeService, { LikeableResource, LikeResponse } from '../services/likeService';
import { AppConfig, appTranslate } from '../config/appConfig';
import { UEM_Client_TS_Placeholder as UEM } from '../services/uemClientService';
import { getAuthStatus } from '../features/auth/authService';

interface LikeButton extends HTMLButtonElement {
    dataset: {
        resourceType: string;
        resourceId: string;
        likeUrl?: string;
    };
}

export class LikeUIManager {
    private static instance: LikeUIManager;
    private initialized: boolean = false;
    private processingButtons: Set<string> = new Set();
    private config: AppConfig | null = null;

    private constructor() { }

    public static getInstance(): LikeUIManager {
        if (!LikeUIManager.instance) {
            LikeUIManager.instance = new LikeUIManager();
        }
        return LikeUIManager.instance;
    }

    /**
     * Initialize like functionality
     */
    public initialize(config: AppConfig): void {
        if (this.initialized) {
            console.log('[LikeUIManager] Already initialized');
            return;
        }

        this.config = config;
        console.log('[LikeUIManager] Initializing...');

        // Use event delegation for all like buttons
        document.addEventListener('click', this.handleDocumentClick.bind(this));

        // Listen for collection changes to refresh UI
        document.addEventListener('collection-changed', this.handleCollectionChanged.bind(this));

        this.initialized = true;
        console.log('[LikeUIManager] Initialized successfully');
    }

    /**
     * Handle document click with event delegation
     */
    private handleDocumentClick(event: Event): void {
        const target = event.target as HTMLElement;
        const likeButton = target.closest('.like-button') as LikeButton | null;

        if (likeButton) {
            console.log('[LikeUIManager] Document clicked:', target);
            event.preventDefault();
            event.stopPropagation();
            this.handleLikeClick(likeButton);
        }
    }

    /**
     * Handle collection changed event
     */
    private handleCollectionChanged(event: Event): void {
        // Refresh like counts when collection changes
        document.querySelectorAll('.like-button').forEach(button => {
            const likeButton = button as LikeButton;
            const resource: LikeableResource = {
                type: likeButton.dataset.resourceType as 'collection' | 'egi',
                id: parseInt(likeButton.dataset.resourceId, 10)
            };

            // Qui potresti fare una chiamata API per aggiornare il conteggio
            // Per ora lasciamo vuoto finché non implementiamo l'endpoint GET dei like
        });
    }

    /**
     * Handle like button click
     */
    private async handleLikeClick(button: LikeButton): Promise<void> {

        console.log('[LikeUIManager] Like button clicked:', button);

        if (!this.config) {
            console.error('[LikeUIManager] Config not initialized');
            return;
        }

        // Verifica che l'utente sia almeno "connected"
        const authStatus = getAuthStatus(this.config);
        if (authStatus === 'disconnected') {
            // Mostra messaggio o apri modal wallet connect
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('like.auth_required_title', this.config.translations),
                    text: appTranslate('like.auth_required_for_like', this.config.translations),
                    confirmButtonText: appTranslate('wallet_connect_button', this.config.translations),
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Trigger apertura modale wallet
                        document.dispatchEvent(new CustomEvent('open-wallet-modal'));
                    }
                });
            }
            return;
        }

        const resource: LikeableResource = {
            type: button.dataset.resourceType as 'collection' | 'egi',
            id: parseInt(button.dataset.resourceId, 10)
        };

        const key = `${resource.type}-${resource.id}`;

        // Prevent double-clicks
        if (this.processingButtons.has(key)) {
            console.log(`[LikeUIManager] Already processing ${key}`);
            return;
        }

        this.processingButtons.add(key);
        button.disabled = true;

        // Add loading state
        button.classList.add('loading');

        try {
            const response = await likeService.toggleLike(resource, this.config);
            this.updateUI(button, response);
            this.updateRelatedElements(resource, response);
            this.showLikeToast(resource, response);

        } catch (error) {
            // Error già gestito da service con UEM
            console.error('[LikeUIManager] Failed to toggle like:', error);
        } finally {
            button.disabled = false;
            button.classList.remove('loading');
            this.processingButtons.delete(key);
        }
    }

    /**
     * Update button UI after like toggle
     */
    private updateUI(button: LikeButton, response: LikeResponse): void {
        const { is_liked, likes_count } = response;

        // Toggle button state
        button.classList.toggle('is-liked', is_liked);

        // Update heart icon with 3-state logic
        const icon = button.querySelector('[data-heart-icon]') as HTMLElement;
        if (icon) {
            // Remove all color classes first
            icon.classList.remove(
                'text-red-500', 'fill-current',
                'text-blue-500',
                'text-gray-400'
            );

            const hasLikes = likes_count && likes_count > 0;

            if (is_liked) {
                // User has liked it - RED
                icon.classList.add('text-red-500', 'fill-current');
            } else if (hasLikes) {
                // Has likes but not from user - BLUE
                icon.classList.add('text-blue-500', 'fill-current');
            } else {
                // No likes at all - NEUTRAL/GRAY
                icon.classList.add('text-gray-400');
            }
        }

        // Add like animation if liked
        if (is_liked) {
            this.playLikeAnimation(button);
        }

        // Update aria-label and title
        const newTitle = is_liked ? 'Unlike this item' : 'Like this item';
        button.setAttribute('title', newTitle);
        button.setAttribute('aria-label', newTitle);

        // Update counter
        this.updateCounter(button, likes_count);

        // Update text usando appTranslate dal tuo sistema
        const text = button.querySelector('.like-text');
        if (text && this.config) {
            const likedText = appTranslate('liked', this.config.translations);
            const likeText = appTranslate('like', this.config.translations);
            text.textContent = is_liked ? likedText : likeText;
        }
    }

    /**
     * Play like animation
     */
    private playLikeAnimation(button: HTMLElement): void {
        // Add animation class
        button.classList.add('animate-like');

        // Remove animation class after animation completes
        setTimeout(() => {
            button.classList.remove('animate-like');
        }, 1000);
    }

    /**
     * Show toast notification for like/unlike action
     */
    private showLikeToast(resource: LikeableResource, response: LikeResponse): void {
        if (!this.config || !window.Swal) return;

        const { is_liked } = response;

        // Get resource name from DOM or use fallback
        const resourceName = this.getResourceName(resource);

        // Prepare toast message
        const action = is_liked ? 'liked' : 'unliked';
        const resourceTypeKey = resource.type === 'egi' ? 'egi' : 'collection';

        const title = is_liked
            ? appTranslate(`like.toast.${resourceTypeKey}.liked_title`, this.config.translations)
            : appTranslate(`like.toast.${resourceTypeKey}.unliked_title`, this.config.translations);

        const message = is_liked
            ? appTranslate(`like.toast.${resourceTypeKey}.liked_message`, this.config.translations, { name: resourceName })
            : appTranslate(`like.toast.${resourceTypeKey}.unliked_message`, this.config.translations, { name: resourceName });

        // Show toast
        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: is_liked ? 'success' : 'info',
            title: title,
            text: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'like-toast-popup',
                title: 'like-toast-title',
                content: 'like-toast-content'
            },
            background: is_liked ? '#1f2937' : '#374151',
            color: '#f3f4f6'
        });
    }

    /**
     * Get resource name from DOM or database
     */
    private getResourceName(resource: LikeableResource): string {
        // Try to find resource name in DOM first
        const titleElement = document.querySelector(`[data-${resource.type}-id="${resource.id}"] .resource-title,
                                                    [data-${resource.type}-id="${resource.id}"] h1,
                                                    [data-${resource.type}-id="${resource.id}"] h2,
                                                    [data-${resource.type}-id="${resource.id}"] h3`);

        if (titleElement) {
            return titleElement.textContent?.trim() || '';
        }

        // Try to find in meta tags
        const metaTitle = document.querySelector('meta[property="og:title"]') as HTMLMetaElement;
        if (metaTitle && metaTitle.content) {
            return metaTitle.content;
        }

        // Fallback to page title
        const pageTitle = document.title;
        if (pageTitle && pageTitle.includes('|')) {
            return pageTitle.split('|')[0].trim();
        }

        // Ultimate fallback
        return resource.type === 'egi' ? 'questo EGI' : 'questa collezione';
    }

    /**
     * Update like counter
     */
    private updateCounter(button: HTMLElement, count: number): void {
        // Find the counter badge within the button
        let counter = button.querySelector('[data-like-counter]') as HTMLElement;

        if (count > 0) {
            if (!counter) {
                // Create counter badge if it doesn't exist
                counter = document.createElement('span');
                counter.className = 'absolute -top-1.5 -right-1.5 text-xs font-semibold min-w-[14px] h-3.5 leading-none bg-red-500 text-white rounded-full px-1 flex items-center justify-center transition-all duration-300 ease-in-out transform group-hover:scale-110 shadow-sm border border-white/20';
                counter.setAttribute('data-like-counter', '');
                counter.style.fontSize = '10px';
                counter.style.lineHeight = '1';
                button.appendChild(counter);
            }

            // Update counter text and animate
            const displayCount = count > 99 ? '99+' : count.toString();
            counter.textContent = displayCount;
            counter.style.display = 'flex';

            // Add bounce animation for count change
            counter.classList.add('like-counter-bounce');
            setTimeout(() => {
                counter.classList.remove('like-counter-bounce');
            }, 400);
        } else {
            // Hide counter when count is 0
            if (counter) {
                counter.style.display = 'none';
            }
        }
    }

    /**
     * Update all related elements on the page
     */
    private updateRelatedElements(resource: LikeableResource, response: LikeResponse): void {
        const { is_liked, likes_count } = response;

        // Find all elements showing this resource's like count
        const selector = `[data-resource-type="${resource.type}"][data-resource-id="${resource.id}"]`;
        const relatedElements = document.querySelectorAll(selector);

        relatedElements.forEach(element => {
            // Skip the button that was just clicked
            if (element === document.activeElement) return;

            // Update other like buttons for the same resource
            if (element.classList.contains('like-button')) {
                const button = element as HTMLElement;

                // Toggle button state
                button.classList.toggle('is-liked', is_liked);

                // Update heart icon with 3-state logic
                const icon = button.querySelector('[data-heart-icon]') as HTMLElement;
                if (icon) {
                    // Remove all color classes first
                    icon.classList.remove(
                        'text-red-500', 'fill-current',
                        'text-blue-500',
                        'text-gray-400'
                    );

                    const hasLikes = likes_count && likes_count > 0;

                    if (is_liked) {
                        // User has liked it - RED
                        icon.classList.add('text-red-500', 'fill-current');
                    } else if (hasLikes) {
                        // Has likes but not from user - BLUE
                        icon.classList.add('text-blue-500', 'fill-current');
                    } else {
                        // No likes at all - NEUTRAL/GRAY
                        icon.classList.add('text-gray-400');
                    }
                }

                // Update counter for this button too
                this.updateCounter(button, likes_count);
            }

            // Update standalone like count displays
            if (element.classList.contains('like-count-display')) {
                element.textContent = likes_count > 0 ? likes_count.toString() : '';

                // Add bounce animation
                element.classList.add('animate-bounce');
                setTimeout(() => {
                    element.classList.remove('animate-bounce');
                }, 600);
            }
        });
    }
}

export default LikeUIManager.getInstance();
