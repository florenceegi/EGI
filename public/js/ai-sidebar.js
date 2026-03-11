/**
 * AI Sidebar - Onboarding Assistant
 * Shopify-style: Chat AI + Stripe-style checklist
 *
 * P0-0: Vanilla JS ONLY (NO Alpine/Livewire)
 *
 * @author EGI Team
 */

(function () {
    "use strict";

    // Sidebar state
    const state = {
        isOpen: false,
        sidebar: null,
        toggleBtn: null,
        closeBtn: null,
        chatForm: null,
        chatInput: null,
        chatContainer: null,
        checklistEl: null,
        userId: null,
        userType: null,
        checklist: [],
        isLoading: false,
        storageKey: "ai-sidebar-open:" + window.location.pathname, // per-path state
        conversationHistory: [], // storia conversazione per contesto AI
        unpublishedCount: 0,
        unpublishedEgis: [],
    };

    /**
     * Initialize sidebar when DOM ready
     */
    function init() {
        // Get DOM elements
        state.sidebar = document.getElementById("ai-sidebar");
        state.toggleBtn = document.getElementById("ai-sidebar-toggle");
        state.closeBtn = document.getElementById("ai-sidebar-close");
        state.chatForm = document.getElementById("ai-sidebar-form");
        state.chatInput = document.getElementById("ai-sidebar-input");
        state.chatContainer = document.getElementById("ai-sidebar-chat");
        state.checklistEl = document.getElementById("ai-sidebar-checklist");

        if (!state.sidebar || !state.toggleBtn) {
            return; // Component not on this page
        }

        // Move elements to <body> to escape any stacking context created by ancestors
        // (transform, will-change, filter on ancestors break position:fixed z-index)
        document.body.appendChild(state.toggleBtn);
        document.body.appendChild(state.sidebar);

        // Get data attributes
        state.userId = state.sidebar.dataset.userId;
        state.userType = state.sidebar.dataset.userType;

        try {
            state.checklist = JSON.parse(
                state.sidebar.dataset.checklist || "[]",
            );
        } catch (e) {
            console.error("AI Sidebar: Failed to parse checklist data", e);
            state.checklist = [];
        }

        // Unpublished EGIs
        state.unpublishedCount = parseInt(
            state.sidebar.dataset.unpublishedCount || "0",
            10,
        );
        try {
            state.unpublishedEgis = JSON.parse(
                state.sidebar.dataset.unpublishedEgis || "[]",
            );
        } catch (e) {
            state.unpublishedEgis = [];
        }

        // Bind events
        bindEvents();

        // Restore state only for this exact page path
        const savedState = localStorage.getItem(state.storageKey);
        if (savedState === "true") {
            openSidebar();
        }
    }

    /**
     * Bind all event listeners
     */
    function bindEvents() {
        // Toggle button
        state.toggleBtn.addEventListener("click", toggleSidebar);

        // Close button
        if (state.closeBtn) {
            state.closeBtn.addEventListener("click", closeSidebar);
        }

        // Chat form
        if (state.chatForm) {
            state.chatForm.addEventListener("submit", handleChatSubmit);
        }

        // Checklist items
        if (state.checklistEl) {
            state.checklistEl.addEventListener("click", handleChecklistClick);
        }

        // Close on escape
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && state.isOpen) {
                closeSidebar();
            }
        });

        // Close on outside click (mobile)
        document.addEventListener("click", function (e) {
            if (
                state.isOpen &&
                window.innerWidth < 768 &&
                !state.sidebar.contains(e.target) &&
                !state.toggleBtn.contains(e.target)
            ) {
                closeSidebar();
            }
        });
    }

    /**
     * Toggle sidebar open/close
     */
    function toggleSidebar() {
        if (state.isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    /**
     * Open sidebar
     */
    function openSidebar() {
        state.isOpen = true;
        state.sidebar.classList.remove("collapsed");
        state.sidebar.setAttribute("aria-hidden", "false");
        state.toggleBtn.dataset.sidebarOpen = "true";
        // Hide FAB so it doesn't overlap the chat send button
        state.toggleBtn.style.opacity = "0";
        state.toggleBtn.style.pointerEvents = "none";
        localStorage.setItem(state.storageKey, "true");

        // Focus input
        setTimeout(() => {
            if (state.chatInput) {
                state.chatInput.focus();
            }
        }, 300);
    }

    /**
     * Close sidebar
     */
    function closeSidebar() {
        state.isOpen = false;
        state.sidebar.classList.add("collapsed");
        state.sidebar.setAttribute("aria-hidden", "true");
        state.toggleBtn.dataset.sidebarOpen = "false";
        // Restore FAB visibility
        state.toggleBtn.style.opacity = "";
        state.toggleBtn.style.pointerEvents = "";
        localStorage.setItem(state.storageKey, "false");
    }

    /**
     * Handle checklist item click
     */
    function handleChecklistClick(e) {
        const item = e.target.closest(".checklist-item");
        if (!item) return;

        // Don't do anything for completed items
        if (item.classList.contains("completed")) return;

        handleStepAction(item.dataset);
    }

    /**
     * Handle step action (open modal, navigate, etc.)
     */
    function handleStepAction(dataset) {
        const stepId = dataset.stepId;
        const action = dataset.action;
        const modal = dataset.modal;

        // Close sidebar on mobile
        if (window.innerWidth < 768) {
            closeSidebar();
        }

        // Handle different actions
        if (modal) {
            // Open specific modal
            openActionModal(modal, stepId);
        } else if (action) {
            // Execute action (URL navigation, function call, etc.)
            executeAction(action, stepId);
        }
    }

    /**
     * Open action modal
     */
    function openActionModal(modalId, stepId) {
        // Check for common modal systems
        const modalEl = document.getElementById(modalId);

        if (modalEl) {
            // Standard modal - add 'open' class or remove 'hidden'
            if (modalEl.classList.contains("hidden")) {
                modalEl.classList.remove("hidden");
                modalEl.classList.add("flex");
            }

            // Dispatch custom event for modals that listen
            modalEl.dispatchEvent(
                new CustomEvent("modal:open", {
                    detail: { stepId: stepId },
                }),
            );
        }

        // Check for window-level modal handlers
        if (window[modalId] && typeof window[modalId].open === "function") {
            window[modalId].open();
        }

        // Specific modal handlers
        switch (modalId) {
            case "avatar-upload-modal":
                // Check for creator/company/collector avatar modals
                if (window.openImageModal) {
                    const userType =
                        document.getElementById("ai-sidebar")?.dataset
                            .userType || "creator";
                    window.openImageModal(`${userType}-avatar-modal`);
                }
                break;
            case "banner-upload-modal":
                // Check for creator/company/collector banner modals
                if (window.openImageModal) {
                    const userType =
                        document.getElementById("ai-sidebar")?.dataset
                            .userType || "creator";
                    window.openImageModal(`${userType}-banner-modal`);
                }
                break;
            case "payment-modal":
                if (window.paymentModal) window.paymentModal.open();
                break;
            case "collection-modal":
                if (window.collectionModal) window.collectionModal.open();
                break;
            // Add more modal handlers as needed
        }
    }

    /**
     * Execute action (URL, function)
     */
    function executeAction(action, stepId) {
        // Check if it's a URL
        if (action.startsWith("/") || action.startsWith("http")) {
            window.location.href = action;
            return;
        }

        // Check if it's a window function
        if (window[action] && typeof window[action] === "function") {
            window[action](stepId);
            return;
        }

        console.warn("AI Sidebar: Unknown action", action);
    }

    /**
     * Get current page context for AI
     * Extracts view identifier and language from page
     *
     * @returns {Object} Page context {view, lang, route, archetype}
     */
    function getPageContext() {
        // Get language from HTML lang attribute
        const lang = document.documentElement.lang || "it";

        // Get archetype from sidebar data attribute
        const archetype = state.userType || "guest";

        // Get current route from pathname
        const pathname = window.location.pathname;

        // Detect view from window.currentView (if set by blade template)
        // or infer from pathname
        let view = window.currentView || inferViewFromPath(pathname, archetype);

        return {
            view: view,
            lang: lang,
            route: pathname,
            archetype: archetype,
        };
    }

    /**
     * Infer view identifier from URL pathname and archetype
     *
     * @param {string} pathname Current URL pathname
     * @param {string} archetype User archetype (company, creator, collector, etc.)
     * @returns {string|null} View identifier or null
     */
    function inferViewFromPath(pathname, archetype) {
        // Company routes - CHECK SPECIFIC ROUTES FIRST (longest match first)
        if (
            pathname.match(/^\/company\/\d+\/portfolio/) ||
            pathname.match(/^\/company\/[\w-]+\/portfolio/)
        ) {
            return "company.portfolio";
        }
        if (
            pathname.match(/^\/company\/\d+\/collections/) ||
            pathname.match(/^\/company\/[\w-]+\/collections/)
        ) {
            return "company.collections";
        }
        if (
            pathname.match(/^\/company\/\d+\/about/) ||
            pathname.match(/^\/company\/[\w-]+\/about/)
        ) {
            return "company.about";
        }
        if (
            pathname.match(/^\/company\/\d+\/impact/) ||
            pathname.match(/^\/company\/[\w-]+\/impact/)
        ) {
            return "company.impact";
        }
        // Company home (redirects to portfolio) - FALLBACK
        if (
            pathname.match(/^\/company\/\d+$/) ||
            pathname.match(/^\/company\/[\w-]+$/)
        ) {
            return "company.portfolio";
        }

        // Creator routes
        if (pathname === "/creator/dashboard" || pathname === "/dashboard") {
            if (archetype === "creator") return "creator.dashboard";
            if (archetype === "company") return "company.portfolio";
            if (archetype === "collector") return "collector.dashboard";
        }

        // Collector routes
        if (pathname === "/collector/marketplace") {
            return "collector.marketplace";
        }

        // EPP routes
        if (pathname === "/epp/dashboard") {
            return "epp.dashboard";
        }

        // PA routes
        if (pathname === "/pa/dashboard") {
            return "pa.dashboard";
        }

        // Collections routes
        if (pathname.match(/^\/collections\/\d+$/)) {
            return "collection.show";
        }
        if (pathname === "/collections/create") {
            return "collection.create";
        }

        // Default: no specific view detected
        return null;
    }

    /**
     * Handle chat form submit (real AI question)
     */
    async function handleChatSubmit(e) {
        e.preventDefault();

        const message = state.chatInput.value.trim();
        if (!message || state.isLoading) return;

        state.isLoading = true;
        state.chatInput.disabled = true;

        // Add user message to chat
        addChatMessage(message, "user");
        state.chatInput.value = "";

        // Show typing indicator
        const typingId = showTypingIndicator();

        try {
            // Call Natan AI API
            const response = await fetch("/art-advisor/chat", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "text/event-stream",
                },
                body: JSON.stringify({
                    message: message,
                    conversation_history: state.conversationHistory,
                    context: {
                        user_type: state.userType,
                        user_id: state.userId,
                        mode: "onboarding_help",
                        page_context: getPageContext(), // NEW: inject page context
                    },
                    expert: "platform",
                }),
            });

            // Remove typing indicator
            removeTypingIndicator(typingId);

            if (response.ok) {
                // Handle SSE stream
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let fullMessage = "";
                const messageId = addChatMessage("", "assistant");

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value);
                    const lines = chunk.split("\n");

                    let currentEvent = "";
                    for (const line of lines) {
                        if (line.startsWith("event: ")) {
                            currentEvent = line.slice(7).trim();
                        } else if (line.startsWith("data: ")) {
                            if (currentEvent === "action") {
                                try {
                                    const action = JSON.parse(line.slice(6));
                                    handleNatanAction(action, messageId);
                                } catch (e) {
                                    /* ignore */
                                }
                            } else {
                                try {
                                    const data = JSON.parse(line.slice(6));
                                    if (data.content) {
                                        fullMessage += data.content;
                                        updateChatMessage(
                                            messageId,
                                            fullMessage,
                                        );
                                    }
                                } catch (e) {
                                    fullMessage += line.slice(6);
                                    updateChatMessage(messageId, fullMessage);
                                }
                            }
                            currentEvent = "";
                        }
                    }
                }
                // Aggiorna storia conversazione
                state.conversationHistory.push({
                    role: "user",
                    content: message,
                });
                if (fullMessage) {
                    state.conversationHistory.push({
                        role: "assistant",
                        content: fullMessage,
                    });
                }
                // Mantieni max ultimi 20 messaggi (10 scambi)
                if (state.conversationHistory.length > 20) {
                    state.conversationHistory =
                        state.conversationHistory.slice(-20);
                }
            } else {
                addChatMessage(
                    getTranslation("ai_sidebar.errors.request_failed"),
                    "error",
                );
            }
        } catch (error) {
            removeTypingIndicator(typingId);
            console.error("AI Sidebar: Chat error", error);
            addChatMessage(
                getTranslation("ai_sidebar.errors.connection_error"),
                "error",
            );
        } finally {
            state.isLoading = false;
            state.chatInput.disabled = false;
            state.chatInput.focus();
        }
    }

    /**
     * Handle Natan action event from SSE
     * Called when AI outputs [[NATAN_ACTION:create_egi:{...}]]
     * Opens inline multi-file uploader directly in the chat — NO redirect.
     */
    function handleNatanAction(action, messageId) {
        if (action.type !== "create_egi" || !action.data) return;

        const data = action.data;
        const uid = "natan-up-" + Date.now();

        const uploaderHtml =
            '<div class="mt-3 rounded-lg border border-violet-500/40 bg-gray-800/60 p-3" id="' +
            uid +
            '-wrap">' +
            '<p class="mb-2 text-xs text-violet-300">📁 Seleziona i tuoi file — li carico io:</p>' +
            '<label class="block cursor-pointer rounded-lg border-2 border-dashed border-violet-500/40 p-4 text-center hover:border-violet-400 transition-colors">' +
            '<span class="text-2xl">🖼️</span>' +
            '<p class="text-xs text-gray-300 mt-1">Clicca o trascina i file qui</p>' +
            '<p class="text-xs text-gray-500 mt-0.5">PNG, JPG, GIF, MP4, PDF, ecc.</p>' +
            '<input type="file" multiple class="hidden" id="' +
            uid +
            '-input">' +
            "</label>" +
            '<div id="' +
            uid +
            '-list" class="mt-2 space-y-1"></div>' +
            '<button id="' +
            uid +
            '-btn" class="hidden mt-2 w-full rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-500 transition-colors">' +
            '⬆️ Carica <span id="' +
            uid +
            '-count">0</span> file' +
            "</button>" +
            "</div>";

        const uploaderEl = document.createElement("div");
        uploaderEl.innerHTML = uploaderHtml;

        const msgEl = state.chatContainer.querySelector("#" + messageId);
        if (msgEl) msgEl.appendChild(uploaderEl.firstElementChild);
        else state.chatContainer.appendChild(uploaderEl.firstElementChild);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;

        // Wire up file input
        const fileInput = document.getElementById(uid + "-input");
        const fileList = document.getElementById(uid + "-list");
        const uploadBtn = document.getElementById(uid + "-btn");
        const countEl = document.getElementById(uid + "-count");

        fileInput.addEventListener("change", function () {
            fileList.innerHTML = "";
            const files = Array.from(fileInput.files);
            files.forEach(function (f, i) {
                const num = files.length > 1 ? " #" + (i + 1) : "";
                const title = data.title + num;
                const row = document.createElement("div");
                row.id = uid + "-file-" + i;
                row.className =
                    "flex items-center gap-2 rounded bg-gray-700/60 px-2 py-1 text-xs text-gray-200";
                row.innerHTML =
                    '<span class="shrink-0 text-base">🖼️</span>' +
                    '<span class="flex-1 truncate">' +
                    f.name +
                    "</span>" +
                    '<span class="shrink-0 text-gray-400 italic">' +
                    title +
                    " · €" +
                    data.price_eur +
                    "</span>" +
                    '<span class="shrink-0 status-dot text-gray-500">⏳</span>';
                fileList.appendChild(row);
            });
            countEl.textContent = files.length;
            uploadBtn.classList.remove("hidden");
            state.chatContainer.scrollTop = state.chatContainer.scrollHeight;
        });

        uploadBtn.addEventListener("click", function () {
            uploadBtn.disabled = true;
            uploadBtn.textContent = "⏳ Caricamento in corso...";
            const files = Array.from(fileInput.files);
            uploadEgiFiles(
                files,
                data.title,
                data.price_eur,
                uid,
                msgEl || state.chatContainer,
            );
        });
    }

    /**
     * Upload multiple files to POST /upload/egi one by one.
     * Titles: "Cosmonauta" (1 file) or "Cosmonauta #1", "Cosmonauta #2" (many files).
     */
    async function uploadEgiFiles(
        files,
        titleBase,
        priceEur,
        uid,
        appendTarget,
    ) {
        const csrf =
            document.querySelector('meta[name="csrf-token"]')?.content || "";
        let successCount = 0;
        let errorCount = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const title =
                files.length > 1 ? titleBase + " #" + (i + 1) : titleBase;
            const rowEl = document.getElementById(uid + "-file-" + i);
            const dot = rowEl ? rowEl.querySelector(".status-dot") : null;
            if (dot) dot.textContent = "⏫";

            try {
                const fd = new FormData();
                fd.append("file", file);
                fd.append("egi-title", title);
                fd.append("egi-description", "");
                fd.append("egi-floor-price", priceEur);
                fd.append("_token", csrf);

                const res = await fetch("/upload/egi", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: fd,
                });

                if (res.ok) {
                    successCount++;
                    if (dot) dot.textContent = "✅";
                } else {
                    errorCount++;
                    const errBody = await res.text().catch(() => "");
                    console.error(
                        "[EGI Upload] errore file " +
                            i +
                            " (HTTP " +
                            res.status +
                            "):",
                        errBody.substring(0, 300),
                    );
                    if (dot) dot.textContent = "❌";
                }
            } catch (e) {
                errorCount++;
                if (dot) dot.textContent = "❌";
            }
        }

        const wrap = document.getElementById(uid + "-wrap");
        const summaryEl = document.createElement("div");
        summaryEl.className =
            "mt-2 rounded-lg p-2 text-xs font-medium " +
            (errorCount === 0
                ? "bg-green-900/40 text-green-300"
                : "bg-yellow-900/40 text-yellow-300");
        summaryEl.textContent =
            errorCount === 0
                ? "✅ " +
                  successCount +
                  " EGI " +
                  (successCount === 1 ? "caricato" : "caricati") +
                  " con successo!"
                : "⚠️ " +
                  successCount +
                  " ok, " +
                  errorCount +
                  " errori. Riprova quelli con ❌.";
        if (wrap) wrap.appendChild(summaryEl);

        // Disable input so it can't be resubmitted
        const inputEl = document.getElementById(uid + "-input");
        if (inputEl) inputEl.disabled = true;

        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;

        // Auto-reload page after successful upload so sidebar reflects new EGIs
        if (errorCount === 0 && successCount > 0) {
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        }
    }

    /**
     * Handle the "publish EGIs" flow from the sidebar chip.
     * Shows an inline question bubble with two choices.
     */
    function handlePublishFlow() {
        if (!state.chatContainer) return;
        openSidebar();

        const n = state.unpublishedCount;
        const bubble = document.createElement("div");
        bubble.className =
            "ai-message rounded-xl bg-gradient-to-r from-indigo-900/30 to-purple-900/30 p-4 mt-4";
        bubble.innerHTML =
            '<div class="mb-3 flex items-center gap-2">' +
            '<div class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs">\u2728</div>' +
            '<span class="text-xs font-medium text-indigo-300">EGI Assistant</span></div>' +
            '<p class="text-sm text-gray-200 mb-3">Ho trovato <strong>' +
            n +
            " EGI</strong> non ancora visibili al pubblico. Vuoi renderli tutti visibili adesso, oppure preferisci scegliere quali?</p>" +
            '<div class="flex gap-2 flex-wrap" id="ai-publish-choice-btns">' +
            '<button id="ai-publish-all-btn" class="rounded-lg bg-green-700 hover:bg-green-600 px-3 py-1.5 text-xs text-white font-medium transition-colors">\u2705 Pubblica tutti</button>' +
            '<button id="ai-publish-select-btn" class="rounded-lg bg-indigo-700 hover:bg-indigo-600 px-3 py-1.5 text-xs text-white font-medium transition-colors">\ud83d\udcdd Scegli quali</button>' +
            "</div>";

        state.chatContainer.appendChild(bubble);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;

        document
            .getElementById("ai-publish-all-btn")
            .addEventListener("click", function () {
                document.getElementById("ai-publish-choice-btns").innerHTML =
                    '<span class="text-xs text-gray-400">\u23f3 Pubblicazione in corso\u2026</span>';
                doBulkPublish([], true);
            });
        document
            .getElementById("ai-publish-select-btn")
            .addEventListener("click", function () {
                showPublishModal();
            });
    }

    /**
     * Show a modal overlay to let the user pick which EGIs to publish.
     */
    function showPublishModal() {
        const existing = document.getElementById("ai-publish-modal");
        if (existing) existing.remove();

        const egis = state.unpublishedEgis;
        const rows = egis
            .map(function (e) {
                const thumb = e.thumb
                    ? '<img src="' +
                      e.thumb +
                      '" alt="" style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;">'
                    : '<div style="width:40px;height:40px;border-radius:6px;background:#374151;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;">\ud83d\uddbc\ufe0f</div>';
                return (
                    '<label style="display:flex;align-items:center;gap:12px;padding:8px;border-radius:8px;cursor:pointer;margin-bottom:4px;" onmouseover="this.style.background=\'#1f2937\'" onmouseout="this.style.background=\'\'">' +
                    '<input type="checkbox" class="ai-publish-cb" value="' +
                    e.id +
                    '" checked style="width:16px;height:16px;accent-color:#6366f1;flex-shrink:0;">' +
                    thumb +
                    '<span style="font-size:14px;color:#e5e7eb;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    (e.title || "EGI #" + e.id) +
                    "</span>" +
                    "</label>"
                );
            })
            .join("");

        const modal = document.createElement("div");
        modal.id = "ai-publish-modal";
        modal.style.cssText =
            "position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.75);padding:16px;";
        modal.innerHTML =
            '<div style="width:100%;max-width:380px;border-radius:16px;background:#111827;border:1px solid #374151;box-shadow:0 25px 50px rgba(0,0,0,0.5);display:flex;flex-direction:column;max-height:80vh;">' +
            '<div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #374151;">' +
            '<h3 style="font-size:14px;font-weight:600;color:#fff;margin:0;">\ud83d\uddd2\ufe0f Seleziona EGI da pubblicare</h3>' +
            '<button id="ai-publish-modal-close" style="background:none;border:none;color:#9ca3af;font-size:20px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>' +
            "</div>" +
            '<div style="overflow-y:auto;padding:12px;flex:1;">' +
            rows +
            "</div>" +
            '<div style="display:flex;gap:8px;padding:12px;border-top:1px solid #374151;">' +
            '<button id="ai-publish-modal-confirm" style="flex:1;border-radius:8px;background:#15803d;border:none;padding:8px;font-size:14px;color:#fff;font-weight:600;cursor:pointer;">\u2705 Pubblica selezionati</button>' +
            '<button id="ai-publish-modal-cancel" style="border-radius:8px;background:#374151;border:none;padding:8px 16px;font-size:14px;color:#d1d5db;cursor:pointer;">Annulla</button>' +
            "</div>" +
            "</div>";

        document.body.appendChild(modal);

        document
            .getElementById("ai-publish-modal-close")
            .addEventListener("click", function () {
                modal.remove();
            });
        document
            .getElementById("ai-publish-modal-cancel")
            .addEventListener("click", function () {
                modal.remove();
            });
        document
            .getElementById("ai-publish-modal-confirm")
            .addEventListener("click", function () {
                const selected = Array.from(
                    modal.querySelectorAll(".ai-publish-cb:checked"),
                ).map(function (cb) {
                    return cb.value;
                });
                if (!selected.length) return;
                modal.remove();
                doBulkPublish(selected, false);
            });
    }

    /**
     * POST /egis/bulk-publish then reload the page.
     */
    async function doBulkPublish(ids, publishAll) {
        const csrf =
            document.querySelector('meta[name="csrf-token"]')?.content || "";
        try {
            const body = publishAll ? { all: true } : { ids: ids };
            const res = await fetch("/egis/bulk-publish", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(body),
            });
            const data = await res.json().catch(function () {
                return {};
            });
            if (res.ok && data.success) {
                addChatMessage(
                    "\u2705 " +
                        (data.count || "") +
                        " EGI resi visibili! Aggiorno la pagina\u2026",
                    "assistant",
                );
                setTimeout(function () {
                    window.location.reload();
                }, 1500);
            } else {
                addChatMessage(
                    "\u274c Errore durante la pubblicazione. Riprova.",
                    "error",
                );
            }
        } catch (e) {
            addChatMessage("\u274c Errore di rete. Riprova.", "error");
        }
    }

    /**
     * Add message to chat container
     */
    function addChatMessage(content, type) {
        const messageId = "msg-" + Date.now();
        const div = document.createElement("div");
        div.id = messageId;
        div.className = `chat-message ${type} mb-3 rounded-lg p-3 text-sm`;

        if (type === "user") {
            div.className += " bg-indigo-600/30 ml-8 text-white";
            div.innerHTML = `<p>${escapeHtml(content)}</p>`;
        } else if (type === "assistant") {
            div.className += " bg-gray-800 mr-4 text-gray-200";
            div.innerHTML = `
                <div class="mb-1 flex items-center gap-1 text-xs text-indigo-400">
                    <span>✨</span>
                    <span>${getTranslation("ai_sidebar.assistant_name")}</span>
                </div>
                <div class="message-content">${content}</div>
            `;
        } else if (type === "error") {
            div.className += " bg-red-900/30 text-red-300";
            div.innerHTML = `<p>⚠️ ${escapeHtml(content)}</p>`;
        }

        state.chatContainer.appendChild(div);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;

        return messageId;
    }

    /**
     * Update existing chat message
     */
    function updateChatMessage(messageId, content) {
        const messageEl = document.getElementById(messageId);
        if (messageEl) {
            const contentEl = messageEl.querySelector(".message-content");
            if (contentEl) {
                contentEl.innerHTML = formatMarkdown(content);
            }
        }
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;
    }

    /**
     * Show typing indicator
     */
    function showTypingIndicator() {
        const id = "typing-" + Date.now();
        const div = document.createElement("div");
        div.id = id;
        div.className = "typing-indicator mb-3 rounded-lg bg-gray-800 p-3 mr-4";
        div.innerHTML = `
            <div class="flex items-center gap-1 text-xs text-indigo-400">
                <span>✨</span>
                <span>${getTranslation("ai_sidebar.assistant_name")}</span>
            </div>
            <div class="flex gap-1 mt-2">
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 0ms"></span>
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 150ms"></span>
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        `;
        state.chatContainer.appendChild(div);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;
        return id;
    }

    /**
     * Remove typing indicator
     */
    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) {
            el.remove();
        }
    }

    /**
     * Simple markdown formatter
     */
    function formatMarkdown(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
            .replace(/\*(.*?)\*/g, "<em>$1</em>")
            .replace(
                /`(.*?)`/g,
                '<code class="bg-gray-700 px-1 rounded">$1</code>',
            )
            .replace(/\n/g, "<br>");
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Get translation (from window or fallback)
     */
    function getTranslation(key, params = {}) {
        // Check if Laravel translations are available
        if (window.translations && window.translations[key]) {
            let text = window.translations[key];
            // Replace placeholders
            Object.keys(params).forEach((param) => {
                text = text.replace(`:${param}`, params[param]);
            });
            return text;
        }

        // Fallback translations
        const fallbacks = {
            "ai_sidebar.messages.welcome":
                "Welcome! Let me help you complete your profile setup.",
            "ai_sidebar.messages.progress_low": `Great start! You've completed ${params.completed || 0} of ${params.total || 0} steps. Next: ${params.nextStep || "continue setup"}`,
            "ai_sidebar.messages.progress_high": `Almost there! Just ${params.remaining || 0} more steps to go.`,
            "ai_sidebar.messages.complete":
                "Congratulations! Your profile is fully set up. 🎉",
            "ai_sidebar.messages.all_done": "All setup steps completed!",
            "ai_sidebar.quick_actions_label": "Suggested next steps:",
            "ai_sidebar.assistant_name": "EGI Assistant",
            "ai_sidebar.errors.request_failed":
                "Request failed. Please try again.",
            "ai_sidebar.errors.connection_error":
                "Connection error. Please check your internet.",
        };

        return fallbacks[key] || key;
    }

    /**
     * Refresh checklist data from server
     */
    async function refreshChecklist() {
        try {
            const response = await fetch(
                `/api/onboarding/checklist/${state.userType}/${state.userId}`,
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                    },
                },
            );

            if (response.ok) {
                const data = await response.json();
                state.checklist = data.checklist || [];
                state.sidebar.dataset.checklist = JSON.stringify(
                    state.checklist,
                );

                // Re-render
                generateAIMessage();
                generateQuickActions();
                renderChecklist();
            }
        } catch (error) {
            console.error("AI Sidebar: Failed to refresh checklist", error);
        }
    }

    /**
     * Re-render checklist (after refresh)
     */
    function renderChecklist() {
        if (!state.checklistEl) return;

        const completed = state.checklist.filter(
            (item) => item.completed,
        ).length;
        const total = state.checklist.length;
        const percent = Math.round((completed / total) * 100);

        // Update progress
        const progressFill = state.sidebar.querySelector(".progress-fill");
        if (progressFill) {
            progressFill.style.width = percent + "%";
        }

        const progressText = state.sidebar.querySelector(
            "[data-progress-text]",
        );
        if (progressText) {
            progressText.textContent = `${completed}/${total}`;
        }

        // Update badge on toggle button
        const badge = state.toggleBtn.querySelector("span");
        if (badge) {
            if (percent === 100) {
                badge.innerHTML = `<svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>`;
                badge.className =
                    "absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-white";
            } else {
                badge.textContent = total - completed;
                badge.className =
                    "absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white";
            }
        }
    }

    // Public API
    window.aiSidebar = {
        open: openSidebar,
        close: closeSidebar,
        toggle: toggleSidebar,
        refresh: refreshChecklist,
        getState: () => ({ isOpen: state.isOpen, checklist: state.checklist }),
        handlePublishFlow: handlePublishFlow,
    };

    // Expose handlePublishFlow directly for inline Blade script
    window.handlePublishFlow = handlePublishFlow;

    // Initialize when DOM ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    // Listen for checklist updates
    window.addEventListener("checklist:updated", refreshChecklist);
    window.addEventListener("modal:closed", refreshChecklist);
})();
