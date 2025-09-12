// resources/js/creator-home.js

/**
 * @Oracode Script: Creator Home Page Interactions
 * 🎯 Purpose: Handle creator profile page functionality
 * 🛡️ Security: CSRF protection, rate limiting on actions
 *
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-06-29
 */

document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // ==========================================================================
    // Stats Counter Animation
    // ==========================================================================

    /**
     * Animates number counting from 0 to target value
     */
    function animateStats() {
        const stats = document.querySelectorAll("[data-stat-value]");

        stats.forEach((stat) => {
            const target = parseInt(
                stat.getAttribute("data-stat-value") || stat.textContent
            );
            const duration = 1500; // 1.5 seconds
            const start = 0;
            const increment = target / (duration / 16); // 60fps
            let current = start;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    stat.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    stat.textContent = target.toLocaleString();
                }
            };

            // Start animation when element is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            });

            observer.observe(stat);
        });
    }

    // ==========================================================================
    // Follow/Unfollow Functionality
    // ==========================================================================

    const followButton = document.querySelector('[aria-label*="Segui"]');
    if (followButton) {
        followButton.addEventListener("click", async function (e) {
            e.preventDefault();

            // Prevent double clicks
            if (this.disabled) return;
            this.disabled = true;

            try {
                const response = await fetch(
                    `/api/creator/${creatorId}/follow`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    }
                );

                if (response.ok) {
                    const data = await response.json();

                    // Update button state
                    if (data.following) {
                        this.innerHTML = `
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ${window.__("creator.home.following")}
                            </span>
                        `;
                        this.classList.add("bg-gray-600");
                        this.classList.remove("bg-oro-fiorentino");
                    } else {
                        this.innerHTML = `
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                ${window.__("creator.home.follow_button")}
                            </span>
                        `;
                        this.classList.remove("bg-gray-600");
                        this.classList.add("bg-oro-fiorentino");
                    }

                    // Update follower count
                    const followerStat = document.querySelector(
                        '[data-stat-type="supporters"]'
                    );
                    if (followerStat) {
                        const currentCount = parseInt(
                            followerStat.textContent.replace(/,/g, "")
                        );
                        followerStat.textContent = (
                            currentCount + (data.following ? 1 : -1)
                        ).toLocaleString();
                    }
                }
            } catch (error) {
                console.error("Follow action failed:", error);
                // Show error toast/notification
            } finally {
                this.disabled = false;
            }
        });
    }

    // ==========================================================================
    // Become Patron Modal
    // ==========================================================================

    const patronButton = document.querySelector(
        '[aria-label*="Diventa mecenate"]'
    );
    if (patronButton) {
        patronButton.addEventListener("click", function (e) {
            e.preventDefault();

            // Check if user is authenticated
            if (!window.FegiAuth || !window.FegiAuth.check()) {
                window.location.href =
                    "/login?redirect=" +
                    encodeURIComponent(window.location.pathname);
                return;
            }

            // Open patron modal (to be implemented)
            console.log("Opening patron modal...");
            // TODO: Implement patron modal
        });
    }

    // ==========================================================================
    // Tab Navigation Enhancement
    // ==========================================================================

    const tabContainer = document.querySelector("nav .scrollbar-hide");
    if (tabContainer) {
        // Add keyboard navigation
        const tabs = tabContainer.querySelectorAll("a");
        tabs.forEach((tab, index) => {
            tab.addEventListener("keydown", function (e) {
                if (e.key === "ArrowRight" && index < tabs.length - 1) {
                    e.preventDefault();
                    tabs[index + 1].focus();
                } else if (e.key === "ArrowLeft" && index > 0) {
                    e.preventDefault();
                    tabs[index - 1].focus();
                }
            });
        });

        // Tab scrolling will be handled by user interaction only
        // No automatic scroll on page load to prevent unwanted scrolling
    }

    // ==========================================================================
    // Lazy Load Collections
    // ==========================================================================

    const collectionCards = document.querySelectorAll(
        ".collection-preview-card"
    );
    if (collectionCards.length > 0) {
        const imageObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const img = entry.target.querySelector("img[data-src]");
                        if (img) {
                            img.src = img.dataset.src;
                            img.removeAttribute("data-src");
                            img.classList.add("fade-in");
                        }
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                rootMargin: "50px",
            }
        );

        collectionCards.forEach((card) => imageObserver.observe(card));
    }

    // ==========================================================================
    // Share Profile Functionality
    // ==========================================================================

    function initShareButtons() {
        const shareButton = document.querySelector("[data-share-profile]");
        if (shareButton && navigator.share) {
            shareButton.addEventListener("click", async function () {
                try {
                    await navigator.share({
                        title: document.title,
                        text: this.dataset.shareText || "",
                        url: window.location.href,
                    });
                } catch (err) {
                    if (err.name !== "AbortError") {
                        console.error("Share failed:", err);
                    }
                }
            });
        }
    }

    // ==========================================================================
    // Initialize
    // ==========================================================================

    // Get creator ID from URL
    const creatorId = window.location.pathname.split("/")[2];

    // Initialize all features
    animateStats();
    initShareButtons();

    // Add page transition class
    document.body.classList.add("creator-page-loaded");

    // Log page view for analytics
    if (window.gtag) {
        window.gtag("event", "page_view", {
            page_title: "Creator Profile",
            creator_id: creatorId,
        });
    }
});

// ==========================================================================
// Utility Functions
// ==========================================================================

/**
 * Simple translation helper
 */
window.__ =
    window.__ ||
    function (key, replacements = {}) {
        // This would connect to your Laravel translations
        // For now, return the key
        return key;
    };
