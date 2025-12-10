/**
 * Gold Price Refresh Alpine.js Component
 * 
 * Handles the manual refresh of gold prices (paid feature)
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('goldPriceRefresh', (config) => ({
        currency: config.currency || 'EUR',
        refreshCost: config.refreshCost || 1,
        egiId: config.egiId,
        isRefreshing: false,
        showConfirmModal: false,
        nextRefreshText: '',
        refreshInterval: null,

        init() {
            this.updateRefreshInfo();
            // Update every minute
            this.refreshInterval = setInterval(() => this.updateRefreshInfo(), 60000);
        },

        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        },

        async updateRefreshInfo() {
            try {
                const response = await fetch(`/api/gold/refresh-info?currency=${this.currency}`);
                const data = await response.json();
                
                if (data.success) {
                    this.nextRefreshText = this.$el.dataset.nextRefreshLabel?.replace(':time', data.data.next_auto_refresh) 
                        || `Next auto-refresh in ${data.data.next_auto_refresh}`;
                }
            } catch (error) {
                console.error('Failed to fetch refresh info:', error);
            }
        },

        confirmRefresh() {
            this.showConfirmModal = true;
        },

        async executeRefresh() {
            if (this.isRefreshing) return;
            
            this.isRefreshing = true;
            this.showConfirmModal = false;

            try {
                const response = await fetch('/api/gold/refresh', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        currency: this.currency
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success toast
                    this.showToast('success', data.message || 'Gold price refreshed!');
                    
                    // Reload the component to show new price
                    // You could also update the DOM directly here
                    window.location.reload();
                } else {
                    // Show error
                    this.showToast('error', data.message || 'Failed to refresh gold price');
                }
            } catch (error) {
                console.error('Refresh failed:', error);
                this.showToast('error', 'Network error. Please try again.');
            } finally {
                this.isRefreshing = false;
            }
        },

        showToast(type, message) {
            // Use existing toast system if available
            if (window.Toastify) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? '#10B981' : '#EF4444',
                }).showToast();
            } else if (window.toast) {
                window.toast[type](message);
            } else {
                // Fallback: alert
                alert(message);
            }
        }
    }));
});
