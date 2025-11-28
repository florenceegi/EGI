// File: resources/ts/features/auth/realWalletConnect.ts
/**
 * 📜 Oracode TypeScript Module: Real Algorand Wallet Connect Handler
 * 🎯 Purpose: Universal modal for connecting real Algorand wallets
 * 🛡️ Security: Validates wallet format, verifies on-chain existence
 * 📋 Stack: Vanilla TypeScript (NO Alpine, NO Livewire, NO jQuery)
 *
 * @version 1.0.0 (Real Wallet Integration)
 * @date 2025-11-25
 * @author Padmin D. Curtis for Fabio Cherici
 */

import { getCsrfTokenTS } from '../../utils/csrf';

// --- TYPES ---
interface RealWalletVerifyResponse {
    success: boolean;
    status: 'wallet_found_user_exists' | 'wallet_found_no_user' | 'wallet_not_on_chain' | 'invalid_format';
    message: string;
    wallet_address?: string;
    user_name?: string;
    is_weak_auth?: boolean;
    can_connect?: boolean;
    warning?: string;
    options?: {
        register: string;
        continue_guest: string;
    };
    errors?: Record<string, string[]>;
}

interface RealWalletConnectResponse {
    success: boolean;
    message: string;
    wallet_address?: string;
    user_status?: string;
    user_name?: string;
    redirect?: string | null;
    info?: string;
}

// --- STATE ---
let currentWalletAddress: string = '';
let isProcessing: boolean = false;

// --- DOM ELEMENTS ---
const getElement = <T extends HTMLElement>(id: string): T | null => {
    return document.getElementById(id) as T | null;
};

const getElementAny = (id: string): Element | null => {
    return document.getElementById(id);
};

// --- MODAL MANAGEMENT ---

/**
 * Open the real wallet connect modal
 */
export function openRealWalletModal(): void {
    const modal = getElement<HTMLDivElement>('real-wallet-connect-modal');
    const content = getElement<HTMLDivElement>('real-wallet-content');

    if (!modal || !content) {
        console.error('RealWalletConnect: Modal elements not found');
        return;
    }

    // Reset state
    resetModalState();

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Animate in
    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });

    // Focus input
    const input = getElement<HTMLInputElement>('real_wallet_address');
    if (input) {
        setTimeout(() => input.focus(), 100);
    }

    // Setup event listeners
    setupEventListeners();
}

/**
 * Close the real wallet connect modal
 */
export function closeRealWalletModal(): void {
    const modal = getElement<HTMLDivElement>('real-wallet-connect-modal');
    const content = getElement<HTMLDivElement>('real-wallet-content');

    if (!modal || !content) return;

    // Animate out
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }, 300);
}

/**
 * Reset modal to initial state
 */
function resetModalState(): void {
    currentWalletAddress = '';
    isProcessing = false;

    // Clear input
    const input = getElement<HTMLInputElement>('real_wallet_address');
    if (input) {
        input.value = '';
    }

    // Reset char count
    const charCount = getElement<HTMLSpanElement>('real-wallet-current-chars');
    if (charCount) {
        charCount.textContent = '0';
    }

    // Hide all sections except input
    showSection('input');

    // Clear errors
    hideError();

    // Reset icons
    showIcon('wallet');
}

// --- SECTION MANAGEMENT ---

/**
 * Show specific modal section
 */
function showSection(section: 'input' | 'loading' | 'connect' | 'options' | 'success'): void {
    const sections = ['input', 'loading', 'connect', 'options', 'success'];

    sections.forEach(s => {
        const el = getElement<HTMLDivElement>(`real-wallet-section-${s}`);
        if (el) {
            if (s === section) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    });

    // Update header based on section
    updateHeader(section);
}

/**
 * Update modal header icons
 */
function showIcon(icon: 'wallet' | 'loading' | 'success' | 'options'): void {
    const icons = ['wallet', 'loading', 'success', 'options'];

    icons.forEach(i => {
        const el = getElementAny(`real-wallet-icon-${i}`);
        if (el) {
            if (i === icon) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    });
}

/**
 * Update header text based on section
 */
function updateHeader(section: string): void {
    const title = getElement<HTMLHeadingElement>('real-wallet-title');
    const description = getElement<HTMLParagraphElement>('real-wallet-description');

    // Header updates are handled by CSS/HTML for now
    // Future: could fetch translations dynamically

    switch (section) {
        case 'loading':
            showIcon('loading');
            break;
        case 'connect':
        case 'success':
            showIcon('success');
            break;
        case 'options':
            showIcon('options');
            break;
        default:
            showIcon('wallet');
    }
}

// --- ERROR HANDLING ---

/**
 * Show error message
 */
function showError(message: string): void {
    const container = getElement<HTMLDivElement>('real-wallet-error-container');
    const messageEl = getElement<HTMLParagraphElement>('real-wallet-error-message');

    if (container && messageEl) {
        messageEl.textContent = message;
        container.classList.remove('hidden');
    }
}

/**
 * Hide error message
 */
function hideError(): void {
    const container = getElement<HTMLDivElement>('real-wallet-error-container');
    if (container) {
        container.classList.add('hidden');
    }
}

// --- API CALLS ---

/**
 * Verify wallet address
 */
async function verifyWallet(walletAddress: string): Promise<void> {
    if (isProcessing) return;

    isProcessing = true;
    hideError();
    showSection('loading');

    try {
        const response = await fetch('/wallet/real/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            credentials: 'same-origin', // CRITICAL: Include session cookies
            body: JSON.stringify({ wallet_address: walletAddress })
        });

        const data: RealWalletVerifyResponse = await response.json();

        if (!response.ok || !data.success) {
            showError(data.message || 'Verification failed');
            showSection('input');
            return;
        }

        currentWalletAddress = data.wallet_address || walletAddress;

        switch (data.status) {
            case 'wallet_found_user_exists':
                // Show connect section
                populateConnectSection(data);
                showSection('connect');
                break;

            case 'wallet_found_no_user':
            case 'wallet_not_on_chain':
                // Show options section
                populateOptionsSection(data);
                showSection('options');
                break;

            default:
                showError(data.message || 'Unknown status');
                showSection('input');
        }

    } catch (error) {
        console.error('RealWalletConnect: Verify error', error);
        showError('Connection error. Please try again.');
        showSection('input');
    } finally {
        isProcessing = false;
    }
}

/**
 * Connect existing user
 */
async function connectExistingUser(): Promise<void> {
    if (isProcessing || !currentWalletAddress) return;

    isProcessing = true;
    hideError();
    showSection('loading');

    try {
        const response = await fetch('/wallet/real/connect', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            credentials: 'same-origin', // CRITICAL: Include session cookies
            body: JSON.stringify({ wallet_address: currentWalletAddress })
        });

        const data: RealWalletConnectResponse = await response.json();

        if (!response.ok || !data.success) {
            showError(data.message || 'Connection failed');
            showSection('connect');
            return;
        }

        // Show success
        populateSuccessSection(data);
        showSection('success');

        // Dispatch event for other components
        dispatchWalletConnected(data);

        // CRITICAL: Reload page after successful connection to use new session
        // Wait a moment to show success message, then reload
        setTimeout(() => {
            window.location.reload();
        }, 1500);

    } catch (error) {
        console.error('RealWalletConnect: Connect error', error);
        showError('Connection error. Please try again.');
        showSection('connect');
    } finally {
        isProcessing = false;
    }
}

/**
 * Create guest user with wallet
 */
async function createGuestUser(): Promise<void> {
    if (isProcessing || !currentWalletAddress) return;

    isProcessing = true;
    hideError();
    showSection('loading');

    try {
        const response = await fetch('/wallet/real/create-guest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            credentials: 'same-origin', // CRITICAL: Include session cookies
            body: JSON.stringify({ wallet_address: currentWalletAddress })
        });

        const data: RealWalletConnectResponse = await response.json();

        if (!response.ok || !data.success) {
            showError(data.message || 'Guest creation failed');
            showSection('options');
            return;
        }

        // Show success with upgrade info
        populateSuccessSection(data, true);
        showSection('success');

        // Dispatch event
        dispatchWalletConnected(data);

        // CRITICAL: Reload page after successful connection to use new session
        setTimeout(() => {
            window.location.reload();
        }, 1500);

    } catch (error) {
        console.error('RealWalletConnect: Create guest error', error);
        showError('Connection error. Please try again.');
        showSection('options');
    } finally {
        isProcessing = false;
    }
}

/**
 * Redirect to registration with wallet prefilled
 */
async function goToRegistration(): Promise<void> {
    if (isProcessing || !currentWalletAddress) return;

    isProcessing = true;

    try {
        const response = await fetch('/wallet/real/prepare-register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            credentials: 'same-origin', // CRITICAL: Include session cookies
            body: JSON.stringify({ wallet_address: currentWalletAddress })
        });

        const data = await response.json();

        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            showError('Could not redirect to registration');
        }

    } catch (error) {
        console.error('RealWalletConnect: Registration redirect error', error);
        showError('Connection error. Please try again.');
    } finally {
        isProcessing = false;
    }
}

// --- UI POPULATION ---

/**
 * Populate connect section with user data
 */
function populateConnectSection(data: RealWalletVerifyResponse): void {
    const userName = getElement<HTMLParagraphElement>('real-wallet-user-name');
    const address = getElement<HTMLParagraphElement>('real-wallet-display-address');

    if (userName) {
        userName.textContent = data.user_name || 'User';
    }
    if (address) {
        address.textContent = data.wallet_address || currentWalletAddress;
    }
}

/**
 * Populate options section
 */
function populateOptionsSection(data: RealWalletVerifyResponse): void {
    const address = getElement<HTMLParagraphElement>('real-wallet-options-address');
    const warning = getElement<HTMLDivElement>('real-wallet-not-funded-warning');

    if (address) {
        address.textContent = data.wallet_address || currentWalletAddress;
    }

    if (warning) {
        if (data.status === 'wallet_not_on_chain') {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }
}

/**
 * Populate success section
 */
function populateSuccessSection(data: RealWalletConnectResponse, showUpgradeInfo: boolean = false): void {
    const message = getElement<HTMLParagraphElement>('real-wallet-success-message');
    const upgradeInfo = getElement<HTMLDivElement>('real-wallet-upgrade-info');

    if (message) {
        message.textContent = data.message || 'Wallet connected successfully!';
    }

    if (upgradeInfo) {
        if (showUpgradeInfo || data.user_status === 'weak_auth') {
            upgradeInfo.classList.remove('hidden');
        } else {
            upgradeInfo.classList.add('hidden');
        }
    }
}

// --- EVENT DISPATCHING ---

/**
 * Dispatch wallet connected event
 */
function dispatchWalletConnected(data: RealWalletConnectResponse): void {
    const event = new CustomEvent('realWalletConnected', {
        detail: {
            wallet_address: data.wallet_address || currentWalletAddress,
            user_status: data.user_status,
            user_name: data.user_name
        }
    });
    document.dispatchEvent(event);
}

// --- EVENT LISTENERS ---

/**
 * Setup all event listeners
 */
function setupEventListeners(): void {
    // Close button
    const closeBtn = getElement<HTMLButtonElement>('close-real-wallet-modal');
    closeBtn?.addEventListener('click', closeRealWalletModal);

    // Backdrop click
    const backdrop = document.querySelector('.real-wallet-backdrop');
    backdrop?.addEventListener('click', closeRealWalletModal);

    // Form submit
    const form = getElement<HTMLFormElement>('real-wallet-form');
    form?.addEventListener('submit', handleFormSubmit);

    // Input character count
    const input = getElement<HTMLInputElement>('real_wallet_address');
    input?.addEventListener('input', handleInputChange);

    // Connect button
    const connectBtn = getElement<HTMLButtonElement>('real-wallet-connect-btn');
    connectBtn?.addEventListener('click', () => connectExistingUser());

    // Register button
    const registerBtn = getElement<HTMLButtonElement>('real-wallet-register-btn');
    registerBtn?.addEventListener('click', () => goToRegistration());

    // Guest button
    const guestBtn = getElement<HTMLButtonElement>('real-wallet-guest-btn');
    guestBtn?.addEventListener('click', () => createGuestUser());

    // Back buttons
    const backFromConnect = getElement<HTMLButtonElement>('real-wallet-back-from-connect');
    backFromConnect?.addEventListener('click', () => showSection('input'));

    const backFromOptions = getElement<HTMLButtonElement>('real-wallet-back-from-options');
    backFromOptions?.addEventListener('click', () => showSection('input'));

    // Success close
    const closeSuccess = getElement<HTMLButtonElement>('real-wallet-close-success');
    closeSuccess?.addEventListener('click', () => {
        closeRealWalletModal();
        // Reload page to reflect new session state
        window.location.reload();
    });

    // Switch to FEGI modal
    const switchToFegi = getElement<HTMLButtonElement>('real-wallet-switch-to-fegi');
    switchToFegi?.addEventListener('click', () => {
        closeRealWalletModal();
        // Dispatch event to open FEGI modal
        const event = new CustomEvent('openFegiWalletModal');
        document.dispatchEvent(event);
    });

    // Escape key
    document.addEventListener('keydown', handleEscapeKey);
}

/**
 * Handle form submit
 */
function handleFormSubmit(e: Event): void {
    e.preventDefault();

    const input = getElement<HTMLInputElement>('real_wallet_address');
    if (!input) return;

    const walletAddress = input.value.trim().toUpperCase();

    // Validate format
    if (walletAddress.length !== 58) {
        showError('Algorand address must be exactly 58 characters');
        return;
    }

    if (!/^[A-Z2-7]+$/.test(walletAddress)) {
        showError('Invalid characters. Only A-Z and 2-7 are allowed.');
        return;
    }

    verifyWallet(walletAddress);
}

/**
 * Handle input change for character count
 */
function handleInputChange(e: Event): void {
    const input = e.target as HTMLInputElement;
    const charCount = getElement<HTMLSpanElement>('real-wallet-current-chars');

    // Force uppercase
    input.value = input.value.toUpperCase();

    if (charCount) {
        charCount.textContent = String(input.value.length);
    }

    // Clear error on input
    hideError();
}

/**
 * Handle escape key
 */
function handleEscapeKey(e: KeyboardEvent): void {
    if (e.key === 'Escape') {
        const modal = getElement<HTMLDivElement>('real-wallet-connect-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closeRealWalletModal();
        }
    }
}

// --- INITIALIZATION ---

/**
 * Initialize module - called on DOMContentLoaded
 */
export function initRealWalletConnect(): void {
    // Listen for open modal event
    document.addEventListener('openRealWalletModal', () => {
        openRealWalletModal();
    });

    console.log('RealWalletConnect: Module initialized');
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRealWalletConnect);
} else {
    initRealWalletConnect();
}
