/**
 * EGI Refresh Channel - Cross-Tab Communication (OS3 Compliant)
 * 
 * @purpose Enable automatic page refresh when EGI is created/updated in another tab
 * @architecture Uses BroadcastChannel API (native browser, no dependencies)
 * @pattern Vanilla JS, event-driven, OS3 P2-2 compliant
 * 
 * Usage:
 * 1. Include this script in layouts that display EGI lists
 * 2. When EGI is created, call: EgiChannel.notifyCreated(egiId)
 * 3. Listening pages will auto-refresh to show new EGI
 */

const EgiChannel = (function() {
    'use strict';
    
    const CHANNEL_NAME = 'egi-updates';
    const EVENTS = {
        CREATED: 'egi:created',
        UPDATED: 'egi:updated',
        DELETED: 'egi:deleted'
    };
    
    let channel = null;
    let listeners = [];
    
    /**
     * Initialize the broadcast channel
     */
    function init() {
        if (typeof BroadcastChannel === 'undefined') {
            console.warn('[EgiChannel] BroadcastChannel not supported in this browser');
            return false;
        }
        
        if (channel) {
            return true; // Already initialized
        }
        
        try {
            channel = new BroadcastChannel(CHANNEL_NAME);
            
            channel.onmessage = function(event) {
                handleMessage(event.data);
            };
            
            channel.onmessageerror = function(error) {
                console.error('[EgiChannel] Message error:', error);
            };
            
            console.log('[EgiChannel] Initialized successfully');
            return true;
        } catch (e) {
            console.error('[EgiChannel] Failed to initialize:', e);
            return false;
        }
    }
    
    /**
     * Handle incoming messages from other tabs
     */
    function handleMessage(data) {
        if (!data || !data.type) {
            return;
        }
        
        console.log('[EgiChannel] Received:', data);
        
        switch (data.type) {
            case EVENTS.CREATED:
                handleEgiCreated(data);
                break;
            case EVENTS.UPDATED:
                handleEgiUpdated(data);
                break;
            case EVENTS.DELETED:
                handleEgiDeleted(data);
                break;
        }
        
        // Notify custom listeners
        listeners.forEach(function(listener) {
            try {
                listener(data);
            } catch (e) {
                console.error('[EgiChannel] Listener error:', e);
            }
        });
    }
    
    /**
     * Handle EGI created event - refresh the page to show new EGI
     */
    function handleEgiCreated(data) {
        // Check if current page should refresh
        const currentPath = window.location.pathname;
        const shouldRefresh = 
            currentPath.includes('/egis') ||
            currentPath.includes('/dashboard') ||
            currentPath.includes('/home') ||
            currentPath.includes('/portfolio') ||
            currentPath.includes('/collections');
        
        if (shouldRefresh) {
            console.log('[EgiChannel] New EGI created, refreshing page...');
            showNotificationToast('Nuovo EGI creato! Aggiornamento in corso...', 'success');
            
            // Small delay to let user see the toast
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }
    }
    
    /**
     * Handle EGI updated event
     */
    function handleEgiUpdated(data) {
        // Could implement partial DOM update here instead of full refresh
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('/egis/' + data.egiId)) {
            console.log('[EgiChannel] Current EGI updated, refreshing...');
            window.location.reload();
        }
    }
    
    /**
     * Handle EGI deleted event
     */
    function handleEgiDeleted(data) {
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('/egis/' + data.egiId)) {
            console.log('[EgiChannel] Current EGI deleted, redirecting...');
            window.location.href = '/egis';
        } else if (currentPath.includes('/egis') || currentPath.includes('/dashboard')) {
            window.location.reload();
        }
    }
    
    /**
     * Show a toast notification to inform user of the refresh
     */
    function showNotificationToast(message, type) {
        // Check if toast system exists
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }
        
        // Fallback: Create inline toast
        const toast = document.createElement('div');
        toast.className = 'egi-channel-toast';
        toast.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10B981' : '#3B82F6'};
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 99999;
                font-family: system-ui, sans-serif;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            ">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }
    
    /**
     * Send notification that an EGI was created
     */
    function notifyCreated(egiId, egiTitle) {
        if (!channel) {
            init();
        }
        
        if (channel) {
            channel.postMessage({
                type: EVENTS.CREATED,
                egiId: egiId,
                egiTitle: egiTitle || '',
                timestamp: Date.now()
            });
            console.log('[EgiChannel] Notified: EGI created', egiId);
        }
    }
    
    /**
     * Send notification that an EGI was updated
     */
    function notifyUpdated(egiId) {
        if (!channel) {
            init();
        }
        
        if (channel) {
            channel.postMessage({
                type: EVENTS.UPDATED,
                egiId: egiId,
                timestamp: Date.now()
            });
        }
    }
    
    /**
     * Send notification that an EGI was deleted
     */
    function notifyDeleted(egiId) {
        if (!channel) {
            init();
        }
        
        if (channel) {
            channel.postMessage({
                type: EVENTS.DELETED,
                egiId: egiId,
                timestamp: Date.now()
            });
        }
    }
    
    /**
     * Add custom listener for channel events
     */
    function addListener(callback) {
        if (typeof callback === 'function') {
            listeners.push(callback);
        }
    }
    
    /**
     * Remove custom listener
     */
    function removeListener(callback) {
        const index = listeners.indexOf(callback);
        if (index > -1) {
            listeners.splice(index, 1);
        }
    }
    
    /**
     * Close the channel
     */
    function close() {
        if (channel) {
            channel.close();
            channel = null;
            listeners = [];
            console.log('[EgiChannel] Closed');
        }
    }
    
    // Public API
    return {
        init: init,
        notifyCreated: notifyCreated,
        notifyUpdated: notifyUpdated,
        notifyDeleted: notifyDeleted,
        addListener: addListener,
        removeListener: removeListener,
        close: close,
        EVENTS: EVENTS
    };
})();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    EgiChannel.init();
});

// Export for module systems if available
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EgiChannel;
}
