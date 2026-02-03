/**
 * 🔔 Real-time Notification Updater
 * 🎯 Purpose: Handle real-time push notifications manually (User Request)
 * 🧱 Core Logic: Listen to Echo channel and trigger SweetAlert2 Toast
 * 
 * Adapted from legacy logic of EgiStructureUpdater.ts
 */

export class NotificationUpdater {

    static init() {
        // 1. Get User ID from Meta
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        if (!userIdMeta) {
            console.log('🔕 NotificationUpdater: No user ID found (Guest mode).');
            return;
        }

        const userId = userIdMeta.getAttribute('content');
        if (!userId) return;

        console.log(`🔔 NotificationUpdater: Initializing for User ${userId}...`);

        // 2. Subscribe to Private Channel
        // Note: The event name typically includes the namespace unless prefixed with '.'
        // Laravel broadcasts standard notifications as: Illuminate\Notifications\Events\BroadcastNotificationCreated
        if (window.Echo) {
            window.Echo.private(`App.Models.User.${userId}`)
                .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (notification) => {
                    this.handleNotification(notification);
                });
        } else {
            console.error('❌ NotificationUpdater: Echo not initialized.');
        }
    }

    /**
     * Handle incoming notification payload
     */
    static handleNotification(notification) {
        console.log('📨 Notification Received:', notification);

        // 3. Visual Feedback (Toast) - mirroring EgiStructureUpdater logic
        if (window.Swal) {
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info', // Can be dynamic based on notification.type
                title: notification.title || 'Nuova Notifica',
                text: notification.body || '',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', window.Swal.stopTimer)
                    toast.addEventListener('mouseleave', window.Swal.resumeTimer)
                    toast.addEventListener('click', () => {
                        if(notification.action_url) {
                            window.location.href = notification.action_url;
                        }
                    });
                },
                customClass: {
                    popup: 'cursor-pointer'
                }
            });
        }

        // 4. Update DOM / Dispatch Event for Livewire
        // This notifies Dashboard.php (if active) to reload the list
        window.dispatchEvent(new CustomEvent('notification-received', { detail: notification }));
        
        // Manual Badge Update (Optional - if we want to manipulate DOM directly)
        this.updateBadgeCount();
    }

    static updateBadgeCount() {
        const badge = document.querySelector('.notification-badge'); // Adjust selector as needed
        if (badge) {
            let count = parseInt(badge.innerText) || 0;
            badge.innerText = count + 1;
            badge.style.display = 'block';
        }
    }
}
