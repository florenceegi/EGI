/**
 * AI Features Helper - Unified Flow for AI Feature Execution
 * 
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-11-06
 * @purpose Unified flow with cost confirmation for AI features
 * 
 * WORKFLOW:
 * 1. Get pricing from API
 * 2. Show confirmation dialog with cost
 * 3. If confirmed → execute feature
 * 4. Handle success/error
 */

/**
 * Execute AI Feature with cost confirmation
 * 
 * @param {string} featureCode Feature code (e.g. 'ai_trait_generation')
 * @param {number} egiId EGI ID
 * @param {object} params Feature-specific parameters
 * @param {object} callbacks Optional callbacks {onSuccess, onError, onCancel}
 * @returns {Promise<object>} Result object
 */
async function executeAiFeatureWithConfirmation(featureCode, egiId, params = {}, callbacks = {}) {
    try {
        console.log('[AI Features] Requesting pricing', { featureCode, egiId });

        // STEP 1: Get pricing info
        const pricingUrl = `/api/ai/features/pricing?feature_code=${encodeURIComponent(featureCode)}`;
        const pricingResponse = await fetch(pricingUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        if (!pricingResponse.ok) {
            throw new Error('Failed to fetch pricing');
        }

        const pricingData = await pricingResponse.json();
        console.log('[AI Features] Pricing received', pricingData);

        if (!pricingData.success) {
            throw new Error(pricingData.message || 'Pricing not available');
        }

        const pricing = pricingData.data;

        // STEP 2: Show confirmation dialog
        const confirmationMessage = pricing.is_free 
            ? `<p class="text-sm text-gray-600 mb-3">Questa operazione è <strong class="text-green-600">gratuita</strong>.</p>`
            : `
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-400 p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-bold text-gray-800">Costo Operazione</p>
                    </div>
                    <p class="text-2xl font-bold text-orange-600 mb-2">${pricing.cost_egili} Egili</p>
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>Il tuo saldo: <strong>${pricing.user_balance} Egili</strong></span>
                        <span>Dopo: <strong>${pricing.user_balance - pricing.cost_egili} Egili</strong></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Gli Egili verranno scalati solo se l'operazione ha successo.</p>
            `;

        // Check if user has sufficient credits
        if (!pricing.is_free && !pricing.has_sufficient_credits) {
            await Swal.fire({
                icon: 'error',
                title: 'Crediti Insufficienti',
                html: `
                    <p class="mb-3">Non hai abbastanza Egili per questa operazione.</p>
                    <div class="bg-red-50 border border-red-200 rounded p-3 text-left">
                        <p class="text-sm"><strong>Richiesti:</strong> ${pricing.cost_egili} Egili</p>
                        <p class="text-sm"><strong>Disponibili:</strong> ${pricing.user_balance} Egili</p>
                        <p class="text-sm text-red-600"><strong>Mancanti:</strong> ${pricing.cost_egili - pricing.user_balance} Egili</p>
                    </div>
                    <p class="mt-3 text-xs text-gray-600">Acquista Egili per continuare.</p>
                `,
                confirmButtonText: 'Acquista Egili',
                showCancelButton: true,
                cancelButtonText: 'Chiudi',
                confirmButtonColor: '#f97316',
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Redirect to Egili purchase page
                    window.location.href = '/egili/purchase';
                }
            });

            if (callbacks.onCancel) callbacks.onCancel();
            return { success: false, reason: 'insufficient_credits' };
        }

        const confirmResult = await Swal.fire({
            title: pricing.feature_name,
            html: confirmationMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: pricing.is_free ? '#10b981' : '#f97316',
            cancelButtonColor: '#6b7280',
            confirmButtonText: pricing.is_free ? 'Procedi' : `Conferma e Scala ${pricing.cost_egili} Egili`,
            cancelButtonText: 'Annulla'
        });

        if (!confirmResult.isConfirmed) {
            console.log('[AI Features] User cancelled');
            if (callbacks.onCancel) callbacks.onCancel();
            return { success: false, reason: 'cancelled' };
        }

        // STEP 3: Show loading
        Swal.fire({
            title: 'Elaborazione in corso...',
            html: '<p class="text-sm text-gray-600">N.A.T.A.N sta lavorando alla tua richiesta...</p>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // STEP 4: Execute feature
        const executeUrl = '/api/ai/features/execute';
        const executeResponse = await fetch(executeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                feature_code: featureCode,
                egi_id: egiId,
                params: params
            })
        });

        const executeData = await executeResponse.json();
        console.log('[AI Features] Execution result', executeData);

        if (executeData.success) {
            // Success!
            await Swal.fire({
                icon: 'success',
                title: 'Operazione Completata!',
                text: executeData.message,
                confirmButtonColor: '#10b981'
            });

            if (callbacks.onSuccess) {
                callbacks.onSuccess(executeData);
            } else {
                // Default: reload page
                window.location.reload();
            }

            return executeData;
        } else {
            // Execution failed
            throw new Error(executeData.message || 'Execution failed');
        }

    } catch (error) {
        console.error('[AI Features] Error:', error);
        
        await Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: error.message || 'Si è verificato un errore. Riprova.',
            confirmButtonColor: '#ef4444'
        });

        if (callbacks.onError) callbacks.onError(error);
        
        return { success: false, error: error.message };
    }
}

// Export for global use
window.executeAiFeatureWithConfirmation = executeAiFeatureWithConfirmation;

