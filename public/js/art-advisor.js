/**
 * AI Art Advisor - Frontend Logic
 *
 * Handles modal interaction, SSE streaming, expert selection,
 * and action buttons (copy/apply).
 *
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-29
 */

(function () {
    "use strict";

    // ====================================
    // STATE MANAGEMENT
    // ====================================
    const state = {
        modal: null,
        messagesContainer: null,
        input: null,
        form: null,
        sendBtn: null,
        currentExpert: "creative",
        visionEnabled: false,
        context: {},
        mode: "general",
        isStreaming: false,
        currentReader: null, // Track active stream reader
    };

    // ====================================
    // INITIALIZATION
    // ====================================
    document.addEventListener("DOMContentLoaded", function () {
        initializeElements();
        attachEventListeners();
        loadContext();
        checkAutoOpen();
    });

    /**
     * Initialize DOM element references
     */
    function initializeElements() {
        state.modal = document.getElementById("art-advisor-modal");
        state.messagesContainer = document.getElementById(
            "art-advisor-messages"
        );
        state.input = document.getElementById("art-advisor-input");
        state.form = document.getElementById("art-advisor-form");
        state.sendBtn = document.getElementById("art-advisor-send-btn");

        if (!state.modal || !state.messagesContainer) {
            console.warn("[ArtAdvisor] Modal elements not found in DOM");
            return;
        }
    }

    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        // Close button
        const closeBtn = document.getElementById("art-advisor-close");
        if (closeBtn) {
            closeBtn.addEventListener("click", closeModal);
        }

        // Trigger button
        const triggerBtn = document.getElementById("art-advisor-trigger");
        if (triggerBtn) {
            triggerBtn.addEventListener("click", openModal);
        }

        // Form submit
        if (state.form) {
            state.form.addEventListener("submit", handleSubmit);
        }

        // Character counter
        if (state.input) {
            state.input.addEventListener("input", updateCharCounter);
        }

        // Expert selector buttons
        const expertBtns = document.querySelectorAll(".expert-selector-btn");
        expertBtns.forEach((btn) => {
            btn.addEventListener("click", () =>
                selectExpert(btn.dataset.expert)
            );
        });

        // Vision toggle
        const visionToggle = document.getElementById("vision-toggle");
        if (visionToggle) {
            visionToggle.addEventListener("click", toggleVision);
        }

        // Close on overlay click
        if (state.modal) {
            state.modal.addEventListener("click", function (e) {
                if (e.target === state.modal) {
                    closeModal();
                }
            });
        }

        // ESC key to close
        document.addEventListener("keydown", function (e) {
            if (
                e.key === "Escape" &&
                !state.modal.classList.contains("hidden")
            ) {
                closeModal();
            }
        });
    }

    /**
     * Load context from data attributes
     */
    function loadContext() {
        if (!state.modal) return;

        const contextData = state.modal.dataset.context;
        const modeData = state.modal.dataset.mode;

        try {
            state.context = contextData ? JSON.parse(contextData) : {};
            state.mode = modeData || "general";
        } catch (e) {
            console.error("[ArtAdvisor] Failed to parse context", e);
            state.context = {};
        }
    }

    /**
     * Check if modal should auto-open
     */
    function checkAutoOpen() {
        if (!state.modal) return;

        const autoOpen = state.modal.dataset.autoOpen === "true";
        if (autoOpen) {
            setTimeout(openModal, 500); // Small delay for page load
        }
    }

    // ====================================
    // MODAL CONTROLS
    // ====================================

    /**
     * Open modal
     */
    function openModal() {
        if (!state.modal) return;

        state.modal.classList.remove("hidden");
        state.modal.classList.add("flex");

        // ALWAYS clear messages when opening (fresh start)
        if (state.messagesContainer) {
            state.messagesContainer.innerHTML = "";
            showWelcomeMessage();
        }

        // Focus input
        if (state.input) {
            setTimeout(() => state.input.focus(), 100);
        }
    }

    /**
     * Close modal
     */
    function closeModal() {
        if (!state.modal) return;

        // Cancel active stream if any
        if (state.currentReader) {
            try {
                state.currentReader.cancel();
                console.log("[ArtAdvisor] Stream cancelled on modal close");
            } catch (e) {
                console.warn("[ArtAdvisor] Failed to cancel stream", e);
            }
            state.currentReader = null;
        }

        // Reset streaming state
        state.isStreaming = false;
        updateSendButton(false);

        state.modal.classList.add("hidden");
        state.modal.classList.remove("flex");
    }

    /**
     * Global function to open modal (accessible from page scripts)
     */
    window.openArtAdvisor = function (options = {}) {
        if (options.mode) {
            state.mode = options.mode;
            state.modal.dataset.mode = options.mode;
        }
        if (options.context) {
            state.context = { ...state.context, ...options.context };
        }
        openModal();

        // Clear and show mode-specific welcome
        if (state.messagesContainer) {
            state.messagesContainer.innerHTML = "";
            showWelcomeMessage();
        }
    };

    // ====================================
    // EXPERT SELECTION
    // ====================================

    /**
     * Select expert and update UI
     */
    function selectExpert(expertId) {
        state.currentExpert = expertId;

        // Update button styles
        const expertBtns = document.querySelectorAll(".expert-selector-btn");
        expertBtns.forEach((btn) => {
            if (btn.dataset.expert === expertId) {
                btn.classList.add(
                    "active",
                    "bg-gradient-to-r",
                    "from-purple-600",
                    "to-blue-600",
                    "text-white",
                    "shadow-lg"
                );
                btn.classList.remove("bg-gray-700", "text-gray-300");
            } else {
                btn.classList.remove(
                    "active",
                    "bg-gradient-to-r",
                    "from-purple-600",
                    "to-blue-600",
                    "text-white",
                    "shadow-lg"
                );
                btn.classList.add("bg-gray-700", "text-gray-300");
            }
        });
    }

    /**
     * Toggle vision mode
     */
    function toggleVision() {
        state.visionEnabled = !state.visionEnabled;

        const visionToggle = document.getElementById("vision-toggle");
        const visionText = document.getElementById("vision-toggle-text");

        if (state.visionEnabled) {
            visionToggle.classList.add(
                "bg-blue-600",
                "border-blue-500",
                "text-white"
            );
            visionToggle.classList.remove(
                "bg-gray-700/50",
                "border-gray-600",
                "text-gray-300"
            );
            if (visionText) {
                visionText.textContent = "🔍 Vision Attiva";
            }
        } else {
            visionToggle.classList.remove(
                "bg-blue-600",
                "border-blue-500",
                "text-white"
            );
            visionToggle.classList.add(
                "bg-gray-700/50",
                "border-gray-600",
                "text-gray-300"
            );
            if (visionText) {
                visionText.textContent = "Analizza Immagine";
            }
        }
    }

    // ====================================
    // WELCOME MESSAGE
    // ====================================

    /**
     * Show mode-specific welcome message
     */
    function showWelcomeMessage() {
        const welcomeMessages = {
            general:
                "Ciao! Sono il tuo AI Art Advisor. Come posso aiutarti oggi?",
            generate_description:
                "Perfetto! Creiamo una descrizione efficace per la tua opera. Dimmi:\n\n1. **Che emozione** vuoi trasmettere? (calma, energia, mistero, gioia...)\n2. **A chi è rivolta?** (collezionisti luxury, giovani creator, corporate/PA...)\n3. **Preferisci enfatizzare**: tecnica artistica o concept/storytelling?\n4. Vuoi che **analizzi visivamente l'immagine** per dettagli precisi?",
            suggest_traits:
                "Analizziamo la tua opera per suggerire traits ottimali.\n\nVuoi che esamini l'immagine visivamente oppure preferisci descrivermi tu le caratteristiche principali?",
            pricing_advice:
                "Ti aiuto a definire il pricing strategico. Dimmi:\n\n1. Sei **emerging artist** o hai già un portfolio/track record?\n2. Preferisci **prezzo fisso** o vuoi testare il mercato con **asta/offerte**?\n3. Quanto tempo hai impiegato per creare quest'opera?",
        };

        const message =
            welcomeMessages[state.mode] || welcomeMessages["general"];
        addMessage("ai", message, state.currentExpert);
    }

    // ====================================
    // MESSAGE HANDLING
    // ====================================

    /**
     * Add message to chat
     */
    function addMessage(role, content, expert = null) {
        if (!state.messagesContainer) return;

        const messageDiv = document.createElement("div");
        messageDiv.className = `flex ${
            role === "user" ? "justify-end" : "justify-start"
        }`;

        const bubble = document.createElement("div");
        bubble.className = `max-w-[80%] rounded-2xl px-4 py-3 ${
            role === "user"
                ? "bg-gradient-to-br from-blue-600 to-purple-600 text-white"
                : "bg-gray-700/50 text-gray-100 border border-gray-600/50"
        }`;

        // Add expert badge for AI messages
        if (role === "ai" && expert) {
            const expertBadge = document.createElement("div");
            expertBadge.className = "mb-2 text-xs font-medium text-blue-400";
            expertBadge.textContent =
                expert === "creative"
                    ? "🎨 Creative Advisor"
                    : "📖 Platform Assistant";
            bubble.appendChild(expertBadge);
        }

        const textDiv = document.createElement("div");
        textDiv.className = "whitespace-pre-wrap text-sm leading-relaxed";
        
        // Format markdown for better readability
        textDiv.innerHTML = formatMarkdown(content);

        bubble.appendChild(textDiv);

        // Add action buttons for AI responses if applicable
        if (role === "ai" && shouldShowActions(content)) {
            const actionsDiv = createActionButtons(content);
            bubble.appendChild(actionsDiv);
        }

        messageDiv.appendChild(bubble);
        state.messagesContainer.appendChild(messageDiv);

        // Scroll to bottom
        state.messagesContainer.scrollTop =
            state.messagesContainer.scrollHeight;
    }

    /**
     * Check if response should have action buttons
     */
    function shouldShowActions(content) {
        // Simple heuristic: if response contains structured output or suggestions
        return (
            content.length > 100 &&
            (content.includes("DESCRIZIONE SUGGERITA") ||
                content.includes("TRAITS SUGGERITI") ||
                content.includes("ANALISI PRICING") ||
                content.includes("```"))
        );
    }

    /**
     * Create action buttons (Copy / Apply)
     */
    function createActionButtons(content) {
        const div = document.createElement("div");
        div.className = "mt-3 flex gap-2 border-t border-gray-600/30 pt-3";

        // Copy button
        const copyBtn = document.createElement("button");
        copyBtn.className =
            "flex items-center gap-1 rounded-lg bg-gray-600/50 px-3 py-1.5 text-xs font-medium text-gray-200 transition-colors hover:bg-gray-600";
        copyBtn.innerHTML = "<span>📋</span><span>Copia</span>";
        copyBtn.addEventListener("click", () => copyToClipboard(content));

        // Apply button (only for description generation mode)
        const applyBtn = document.createElement("button");
        applyBtn.className =
            "flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-green-700";
        applyBtn.innerHTML = "<span>✅</span><span>Applica al Form</span>";
        applyBtn.addEventListener("click", () => applyToForm(content));

        div.appendChild(copyBtn);

        if (state.mode === "generate_description") {
            div.appendChild(applyBtn);
        }

        return div;
    }

    /**
     * Copy text to clipboard
     */
    function copyToClipboard(text) {
        navigator.clipboard
            .writeText(text)
            .then(() => {
                showToast("Copiato negli appunti!", "success");
            })
            .catch((err) => {
                console.error("[ArtAdvisor] Copy failed", err);
                showToast("Errore copia", "error");
            });
    }

    /**
     * Apply AI suggestion to form field
     */
    function applyToForm(content) {
        // Extract clean text (remove markdown, formatting)
        let cleanText = extractMainContent(content);

        if (state.mode === "generate_description") {
            // Find description textarea in CRUD form
            const descriptionField =
                document.getElementById("description") ||
                document.querySelector('textarea[name="description"]');

            if (descriptionField) {
                descriptionField.value = cleanText;
                descriptionField.dispatchEvent(
                    new Event("input", { bubbles: true })
                );
                closeModal();
                showToast("Descrizione applicata al form!", "success");
            } else {
                console.error("[ArtAdvisor] Description field not found");
                showToast("Campo descrizione non trovato", "error");
            }
        }
    }

    /**
     * Extract main content from formatted AI response
     */
    function extractMainContent(text) {
        // Remove markdown formatting
        let clean = text.replace(/#{1,6}\s/g, ""); // Remove headers
        clean = clean.replace(/\*\*\*/g, ""); // Remove bold+italic
        clean = clean.replace(/\*\*/g, ""); // Remove bold
        clean = clean.replace(/\*/g, ""); // Remove italic
        clean = clean.replace(/```[\s\S]*?```/g, ""); // Remove code blocks

        // Extract between markers if present
        if (text.includes("DESCRIZIONE SUGGERITA:")) {
            const parts = text.split("---");
            if (parts.length > 0) {
                clean = parts[0].replace("DESCRIZIONE SUGGERITA:", "").trim();
            }
        }

        return clean.trim();
    }

    // ====================================
    // FORM SUBMISSION & SSE
    // ====================================

    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        e.preventDefault();

        if (state.isStreaming) {
            console.warn("[ArtAdvisor] Already streaming, ignoring submit");
            return;
        }

        const message = state.input.value.trim();
        if (!message) return;

        // Add user message to chat
        addMessage("user", message);

        // Clear input
        state.input.value = "";
        updateCharCounter();

        // Send request
        sendChatRequest(message);
    }

    /**
     * Send chat request with SSE streaming
     */
    function sendChatRequest(message) {
        state.isStreaming = true;
        updateSendButton(true);

        const url = "/art-advisor/chat";
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;

        // Prepare payload
        const payload = {
            expert: state.currentExpert,
            message: message,
            context: state.context,
            use_vision: state.visionEnabled,
        };

        // Create AI message bubble
        const aiMessageDiv = createStreamingMessage();

        // Start SSE
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "text/event-stream",
            },
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.body;
            })
            .then((body) => {
                const reader = body.getReader();
                state.currentReader = reader; // Track reader for cancellation
                const decoder = new TextDecoder();
                let buffer = "";

                return readStream(reader, decoder, aiMessageDiv);
            })
            .catch((error) => {
                console.error("[ArtAdvisor] Request failed", error);
                updateStreamingMessage(
                    aiMessageDiv,
                    "Si è verificato un errore. Riprova tra poco.",
                    true
                );
            })
            .finally(() => {
                state.isStreaming = false;
                state.currentReader = null; // Clear reader reference
                updateSendButton(false);
            });
    }

    /**
     * Read SSE stream
     */
    function readStream(reader, decoder, messageDiv) {
        let buffer = "";
        let accumulatedText = "";

        return reader.read().then(function processText({ done, value }) {
            if (done) {
                console.log("[ArtAdvisor] Stream complete");
                return;
            }

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split("\n");
            buffer = lines.pop() || "";

            lines.forEach((line) => {
                if (line.startsWith("event:")) {
                    // Event line, next line is data
                } else if (line.startsWith("data:")) {
                    try {
                        const data = JSON.parse(line.substring(5));
                        handleSSEEvent(data, messageDiv, accumulatedText);

                        if (data.text) {
                            accumulatedText += data.text;
                        }
                    } catch (e) {
                        console.error(
                            "[ArtAdvisor] Failed to parse SSE data",
                            e
                        );
                    }
                }
            });

            return reader.read().then(processText);
        });
    }

    /**
     * Handle SSE event
     */
    function handleSSEEvent(data, messageDiv, accumulatedText) {
        // Check event type from data context
        if (data.expert) {
            // Start event
            console.log("[ArtAdvisor] Stream started", data);
        } else if (data.text) {
            // Chunk event
            updateStreamingMessage(messageDiv, accumulatedText + data.text);
        } else if (data.model) {
            // Complete event
            console.log("[ArtAdvisor] Stream complete", data);
            finalizeMessage(messageDiv);
        } else if (data.message && data.code) {
            // Error event
            updateStreamingMessage(messageDiv, data.message, true);
        }
    }

    /**
     * Create streaming message bubble
     */
    function createStreamingMessage() {
        const messageDiv = document.createElement("div");
        messageDiv.className = "flex justify-start";

        const bubble = document.createElement("div");
        bubble.className =
            "max-w-[80%] rounded-2xl border border-gray-600/50 bg-gray-700/50 px-4 py-3";

        // Expert badge
        const badge = document.createElement("div");
        badge.className = "mb-2 text-xs font-medium text-blue-400";
        badge.textContent =
            state.currentExpert === "creative"
                ? "🎨 Creative Advisor"
                : "📖 Platform Assistant";
        bubble.appendChild(badge);

        // Text content
        const textDiv = document.createElement("div");
        textDiv.className =
            "whitespace-pre-wrap text-sm leading-relaxed text-gray-100";
        textDiv.textContent = "...";
        bubble.appendChild(textDiv);

        // Streaming indicator
        const indicator = document.createElement("div");
        indicator.className =
            "streaming-indicator mt-2 flex items-center gap-1 text-xs text-gray-400";
        indicator.innerHTML =
            '<span class="animate-pulse">●</span><span>Streaming...</span>';
        bubble.appendChild(indicator);

        messageDiv.appendChild(bubble);
        state.messagesContainer.appendChild(messageDiv);
        state.messagesContainer.scrollTop =
            state.messagesContainer.scrollHeight;

        return bubble;
    }

    /**
     * Format markdown to HTML for better readability
     */
    function formatMarkdown(text) {
        let html = text;

        // Escape HTML first
        html = html.replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;');

        // Code blocks FIRST (to protect them from other replacements)
        html = html.replace(/```([\s\S]*?)```/g, 
            '<pre class="bg-gray-800/50 border border-gray-600/30 rounded-lg p-3 my-3 overflow-x-auto"><code class="text-xs text-green-300 font-mono">$1</code></pre>');

        // Horizontal rules
        html = html.replace(/^---$/gm, '<hr class="my-4 border-gray-600/50">');

        // Bold BEFORE headers (to avoid conflicts)
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong class="font-semibold text-white">$1</strong>');

        // Headers
        html = html.replace(/^### (.+)$/gm, '<h3 class="text-base font-bold text-blue-300 mt-5 mb-2">$1</h3>');
        html = html.replace(/^## (.+)$/gm, '<h2 class="text-lg font-bold text-blue-400 mt-5 mb-3">$1</h2>');
        html = html.replace(/^# (.+)$/gm, '<h1 class="text-xl font-bold text-white mt-5 mb-3">$1</h1>');

        // Emoji sections (🎨 TITLE or 📊 TITLE:) - Enhanced visual separation
        html = html.replace(/^([🎨🎯📊💡✨📝💰🔍⚡🌟📋💼🎭🖼️])\s*\*?\*?(.+?)\*?\*?:?\s*$/gm, 
            '<div class="mt-4 mb-2 flex items-baseline gap-2 border-l-2 border-blue-500 pl-3"><span class="text-2xl flex-shrink-0">$1</span><strong class="text-lg font-bold text-blue-300">$2</strong></div>');

        // Numbered lists with emoji (1. 💡 Title)
        html = html.replace(/^(\d+)\.\s*([🎨🎯📊💡✨📝💰🔍⚡🌟📋💼🎭🖼️])\s*(.+)$/gm,
            '<div class="mb-3 mt-2"><div class="flex items-start gap-3 mb-1"><span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-600/30 text-sm font-bold text-blue-300">$1</span><span class="text-xl flex-shrink-0">$2</span><strong class="flex-1 font-semibold text-white">$3</strong></div>');

        // Regular numbered lists (1. item)
        html = html.replace(/^(\d+)\.\s*(.+)$/gm, 
            '<div class="mb-2 flex gap-3"><span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-600/20 text-sm font-bold text-blue-400">$1</span><span class="flex-1">$2</span></div>');

        // Bullet lists (- item)
        html = html.replace(/^-\s+(.+)$/gm, 
            '<div class="mb-1.5 ml-6 flex gap-2"><span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-400"></span><span class="flex-1">$1</span></div>');

        // Paragraphs - Better spacing
        html = html.split('\n\n').map(para => {
            if (para.trim() && !para.includes('<div') && !para.includes('<h') && !para.includes('<hr') && !para.includes('<pre')) {
                return `<p class="mb-3 leading-relaxed text-gray-200">${para}</p>`;
            }
            return para;
        }).join('\n');

        return html;
    }

    /**
     * Update streaming message content
     */
    function updateStreamingMessage(bubble, text, isError = false) {
        const textDiv = bubble.querySelector("div.whitespace-pre-wrap");
        if (textDiv) {
            // Use formatted markdown for better readability
            textDiv.innerHTML = formatMarkdown(text);

            if (isError) {
                textDiv.classList.add("text-red-400");
            }
        }

        // Scroll to bottom
        state.messagesContainer.scrollTop =
            state.messagesContainer.scrollHeight;
    }

    /**
     * Finalize message (remove streaming indicator, add actions)
     */
    function finalizeMessage(bubble) {
        // Remove streaming indicator
        const indicator = bubble.querySelector(".streaming-indicator");
        if (indicator) {
            indicator.remove();
        }

        // Add action buttons if applicable
        const textDiv = bubble.querySelector("div.whitespace-pre-wrap");
        if (textDiv && shouldShowActions(textDiv.textContent)) {
            const actions = createActionButtons(textDiv.textContent);
            bubble.appendChild(actions);
        }
    }

    // ====================================
    // UTILITIES
    // ====================================

    /**
     * Update character counter
     */
    function updateCharCounter() {
        const charCount = document.getElementById("char-count");
        if (charCount && state.input) {
            charCount.textContent = state.input.value.length;
        }
    }

    /**
     * Update send button state
     */
    function updateSendButton(disabled) {
        if (!state.sendBtn) return;

        state.sendBtn.disabled = disabled;

        if (disabled) {
            state.sendBtn.textContent = "...";
        } else {
            state.sendBtn.textContent = "Invia";
        }
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = "info") {
        // Simple toast implementation
        const toast = document.createElement("div");
        toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 transform rounded-lg px-6 py-3 text-sm font-medium text-white shadow-xl ${
            type === "success"
                ? "bg-green-600"
                : type === "error"
                ? "bg-red-600"
                : "bg-blue-600"
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = "opacity 0.3s";
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
})();
