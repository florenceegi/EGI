{{-- resources/views/components/notification-badge.blade.php --}}
{{-- 🔔 FlorenceEGI Notification Badge Component --}}
{{-- Pure TypeScript notification badge with translation support --}}

<div id="notification-badge-{{ uniqid() }}" class="relative notification-badge-container">
    {{-- Badge Button --}}
    <button
        class="relative p-2 text-gray-400 transition-colors duration-200 rounded-lg notification-badge-button hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        aria-label="{{ __('notification.badge.aria_label') }}" type="button">
        {{-- Bell Icon --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        {{-- Unread Count Badge --}}
        @if($unreadCount > 0)
        <span
            class="absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full notification-unread-badge -top-1 -right-1">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div
        class="absolute right-0 z-50 hidden mt-2 overflow-hidden bg-gray-800 border border-gray-700 rounded-lg shadow-xl notification-dropdown w-80 max-sm:fixed max-sm:left-1/2 max-sm:transform max-sm:-translate-x-1/2 max-sm:top-16 max-sm:w-80 max-sm:mx-4">
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-700 bg-gray-900">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-100">{{ __('notification.badge.title') }}</h3>
                @if($unreadCount > 0)
                <span class="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-full">
                    {{ $unreadCount }}
                </span>
                @endif
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="overflow-y-auto notification-list max-h-80 md:max-h-96">
            @if($hasNotifications)
                @foreach($notifications as $index => $notification)
                    @php
                    // Extract notification type from view field (e.g., "reservations.highest" -> "reservations")
                    $viewParts = explode('.', $notification['view'] ?? '');
                    $notificationType = $viewParts[0] ?? 'general';

                    $typeLabel = $notificationType;

                    // Determine badge color based on type
                    $badgeColors = [
                    'reservations' => 'bg-green-100 text-green-800',
                    'gdpr' => 'bg-blue-100 text-blue-800',
                    'collections' => 'bg-purple-100 text-purple-800',
                    'egis' => 'bg-yellow-100 text-yellow-800',
                    'wallets' => 'bg-orange-100 text-orange-800',
                    'invitations' => 'bg-pink-100 text-pink-800'
                    ];
                    $badgeColor = $badgeColors[$notificationType] ?? 'bg-gray-100 text-gray-800';
                    @endphp

                    <div class="px-4 py-3 transition-colors duration-150 border-b border-gray-100 cursor-pointer notification-item hover:bg-gray-50 last:border-b-0"
                        data-notification-index="{{ $index }}" data-notification-id="{{ $notification['id'] }}"
                        data-notification-url="{{ $notification['url'] }}" data-notification-type="{{ $notificationType }}"
                        tabindex="0">
                        <div class="flex items-start space-x-3">
                            {{-- Type Badge --}}
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                    {{ $typeLabel }}
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2">
                                    {{ $notification['message'] }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $notification['created_at'] }}
                                </p>
                            </div>

                            {{-- Read Status --}}
                            @if(!$notification['is_read'])
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- View All Link --}}
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <a href="{{ route('dashboard') }}"
                        class="block text-sm font-medium text-center text-indigo-600 transition-colors duration-150 notification-view-all hover:text-indigo-500">
                        {{ __('notification.badge.view_all') }}
                    </a>
                </div>
            @else
                {{-- Empty State --}}
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8v8a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('notification.badge.empty.title') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('notification.badge.empty.message') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Pure JavaScript Implementation (TypeScript-style) --}}
<script type="text/javascript">
    (function() {
    'use strict';

    class NotificationBadge {
        constructor(containerId) {
            this.container = document.getElementById(containerId);
            this.isOpen = false;
            this.selectedIndex = -1;
            this.notifications = @json($notifications);

            console.log('NotificationBadge initializing for container:', containerId);
            console.log('Notifications data:', this.notifications);

            if (!this.container) {
                console.error('NotificationBadge: Container not found:', containerId);
                return;
            }

            this.button = this.container.querySelector('.notification-badge-button');
            this.dropdown = this.container.querySelector('.notification-dropdown');
            this.notificationItems = this.container.querySelectorAll('.notification-item');

            console.log('Button found:', !!this.button);
            console.log('Dropdown found:', !!this.dropdown);
            console.log('Items found:', this.notificationItems.length);

            this.init();
        }

        init() {
            if (!this.button || !this.dropdown) {
                console.error('NotificationBadge: Required elements not found');
                return;
            }

            console.log('Adding event listeners...');

            // Button click handler
            this.button.addEventListener('click', (e) => {
                console.log('Button clicked!');
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Click outside to close
            document.addEventListener('click', (e) => {
                // Ignore clicks on trait action buttons
                if (e.target.closest('.trait-action-button')) {
                    return;
                }

                if (!this.container.contains(e.target)) {
                    this.closeDropdown();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (!this.isOpen) return;

                switch(e.key) {
                    case 'Escape':
                        this.closeDropdown();
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        this.navigateDown();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        this.navigateUp();
                        break;
                    case 'Enter':
                        e.preventDefault();
                        this.openSelectedNotification();
                        break;
                }
            });

            // Notification item click handlers
            this.notificationItems.forEach((item, index) => {
                item.addEventListener('click', () => {
                    this.selectNotification(index);
                    this.openSelectedNotification();
                });

                item.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.selectNotification(index);
                        this.openSelectedNotification();
                    }
                });
            });

            // View all link
            const viewAllLink = this.container.querySelector('.notification-view-all');
            if (viewAllLink) {
                viewAllLink.addEventListener('click', () => {
                    this.closeDropdown();
                });
            }
        }

        toggleDropdown() {
            console.log('Toggle dropdown called, current state:', this.isOpen);
            if (this.isOpen) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        }

        openDropdown() {
            console.log('Opening dropdown...');
            this.isOpen = true;
            this.dropdown.classList.remove('hidden');
            this.button.classList.add('active');
            this.selectedIndex = -1;

            // Focus first item if available
            if (this.notificationItems.length > 0) {
                setTimeout(() => {
                    this.selectNotification(0);
                }, 50);
            }
        }

        closeDropdown() {
            console.log('Closing dropdown...');
            this.isOpen = false;
            this.dropdown.classList.add('hidden');
            this.button.classList.remove('active');
            this.selectedIndex = -1;
            this.clearSelection();
        }

        selectNotification(index) {
            if (index < 0 || index >= this.notificationItems.length) return;

            this.clearSelection();
            this.selectedIndex = index;

            const item = this.notificationItems[index];
            item.classList.add('selected');
            item.focus();
        }

        clearSelection() {
            this.notificationItems.forEach(item => {
                item.classList.remove('selected');
            });
        }

        navigateDown() {
            if (this.notificationItems.length === 0) return;

            const newIndex = this.selectedIndex < this.notificationItems.length - 1
                ? this.selectedIndex + 1
                : 0;

            this.selectNotification(newIndex);
        }

        navigateUp() {
            if (this.notificationItems.length === 0) return;

            const newIndex = this.selectedIndex > 0
                ? this.selectedIndex - 1
                : this.notificationItems.length - 1;

            this.selectNotification(newIndex);
        }

        openSelectedNotification() {
            if (this.selectedIndex >= 0 && this.selectedIndex < this.notificationItems.length) {
                const item = this.notificationItems[this.selectedIndex];
                const url = item.getAttribute('data-notification-url');

                if (url) {
                    this.closeDropdown();
                    window.location.href = url;
                }
            }
        }
    }

    // Initialize when DOM is ready
    function initializeNotificationBadges() {
        console.log('Initializing notification badges...');
        const containers = document.querySelectorAll('.notification-badge-container');
        console.log('Found containers:', containers.length);

        containers.forEach(container => {
            if (container.id) {
                console.log('Initializing badge for container:', container.id);
                new NotificationBadge(container.id);
            }
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNotificationBadges);
    } else {
        initializeNotificationBadges();
    }
})();
</script>

{{-- Styles --}}
<style>
    .notification-item:focus {
        outline: 2px solid #4F46E5;
        outline-offset: -2px;
    }

    .notification-item.selected {
        background-color: #EBF8FF;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-badge-button.active {
        color: white;
        background-color: rgba(55, 65, 81, 0.5);
    }
</style>
