{{--
/**
 * AI Credits Cost Preview Modal
 *
 * @package Resources\Views\Pa\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-28
 * @purpose Cost preview modal before starting AI analysis - shows estimated credits, balance check, purchase flow
 *
 * FEATURES:
 * - Estimated cost calculation with API pricing
 * - Balance verification (sufficient/insufficient)
 * - Exchange rate display (ECB)
 * - Cost breakdown (input/output tokens)
 * - Purchase credits CTA if insufficient
 * - GDPR-compliant financial preview
 */
--}}

{{-- Cost Preview Modal (hidden by default) --}}
<div id="aiCostPreviewModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm"
    role="dialog"
    aria-labelledby="costPreviewTitle"
    aria-modal="true">
    <div class="mx-4 w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-[#E67E22] to-[#D4A574] px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-3xl text-white">account_balance_wallet</span>
                </div>
                <div class="flex-1">
                    <h3 id="costPreviewTitle" class="text-2xl font-bold text-white">
                        {{ __('ai_credits.preview.modal_title') }}
                    </h3>
                    <p class="mt-1 text-sm text-white/90" id="costPreviewSubtitle">
                        {{ __('ai_credits.preview.loading') }}
                    </p>
                </div>
                <button type="button"
                    onclick="AICostPreview.close()"
                    class="rounded-lg p-2 text-white/80 transition-colors hover:bg-white/10 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="space-y-6 px-8 py-6">

            {{-- Loading State --}}
            <div id="costPreviewLoading" class="text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-[#E67E22]"></div>
                </div>
                <p class="text-sm text-gray-600">{{ __('ai_credits.preview.loading') }}</p>
            </div>

            {{-- Content (hidden until loaded) --}}
            <div id="costPreviewContent" class="hidden space-y-6">

                {{-- Balance Status Card --}}
                <div id="balanceStatusCard" class="rounded-xl border-2 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-sm text-gray-600">{{ __('ai_credits.preview.your_balance') }}</p>
                            <p class="text-3xl font-bold" id="userCurrentBalance">0</p>
                        </div>
                        <div class="text-right">
                            <p class="mb-1 text-sm text-gray-600">{{ __('ai_credits.preview.balance_after') }}</p>
                            <p class="text-3xl font-bold" id="userBalanceAfter">0</p>
                        </div>
                    </div>
                </div>

                {{-- Analysis Details --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-blue-50 p-4 text-center">
                        <span class="material-symbols-outlined mb-2 text-3xl text-blue-600">description</span>
                        <p class="text-xs text-gray-600">{{ __('ai_credits.preview.acts_to_analyze') }}</p>
                        <p class="text-2xl font-bold text-blue-600" id="estimateActsCount">0</p>
                    </div>
                    <div class="rounded-lg bg-purple-50 p-4 text-center">
                        <span class="material-symbols-outlined mb-2 text-3xl text-purple-600">splitscreen</span>
                        <p class="text-xs text-gray-600">{{ __('ai_credits.preview.chunks_required') }}</p>
                        <p class="text-2xl font-bold text-purple-600" id="estimateChunksCount">1</p>
                    </div>
                    <div class="col-span-2 rounded-lg bg-orange-50 p-4 text-center sm:col-span-1">
                        <span class="material-symbols-outlined mb-2 text-3xl text-orange-600">token</span>
                        <p class="text-xs text-gray-600">{{ __('ai_credits.preview.tokens_estimated') }}</p>
                        <p class="text-2xl font-bold text-orange-600" id="estimateTotalTokens">0</p>
                    </div>
                </div>

                {{-- Cost Breakdown --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-6">
                    <h4 class="mb-4 font-bold text-gray-900">{{ __('ai_credits.preview.cost_breakdown') }}</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ __('ai_credits.preview.input_tokens') }}</span>
                            <span class="font-semibold text-gray-900">
                                <span id="estimateInputTokens">0</span> tokens
                                × <span id="inputTokenPrice">$0.003</span>/1M
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ __('ai_credits.preview.output_tokens') }}</span>
                            <span class="font-semibold text-gray-900">
                                <span id="estimateOutputTokens">0</span> tokens
                                × <span id="outputTokenPrice">$0.015</span>/1M
                            </span>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">{{ __('ai_credits.preview.total_cost') }}</span>
                            <span class="text-2xl font-bold text-[#E67E22]">
                                <span id="estimatedCostCredits">0.00</span>
                                <span class="text-sm text-gray-600">{{ __('ai_credits.balance.credits') }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Exchange Rate Info --}}
                <div class="rounded-lg bg-blue-50 p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined mt-0.5 text-blue-600">info</span>
                        <div class="flex-1 text-xs">
                            <p class="mb-1 font-medium text-gray-900">{{ __('ai_credits.preview.rate_info') }}: <span id="exchangeRateDisplay">0.92</span> USD/EUR</p>
                            <p class="text-gray-600">
                                {{ __('ai_credits.preview.rate_source') }} •
                                {{ __('ai_credits.preview.rate_updated') }}: <span id="exchangeRateUpdated">-</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Insufficient Credits Warning (hidden by default) --}}
                <div id="insufficientCreditsWarning" class="hidden rounded-xl border-2 border-red-300 bg-red-50 p-6">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-3xl text-red-600">error</span>
                        <div class="flex-1">
                            <h4 class="mb-2 font-bold text-red-900">{{ __('ai_credits.preview.insufficient_title') }}</h4>
                            <p class="mb-4 text-sm text-red-800" id="insufficientMessage">
                                {{ __('ai_credits.preview.insufficient_message', ['required' => '<span id="creditsRequired">0</span>', 'balance' => '<span id="creditsBalance">0</span>']) }}
                            </p>
                            <a href="/pa/ai-credits/purchase"
                                class="inline-flex items-center gap-2 rounded-lg bg-[#E67E22] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#D35400]">
                                <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                                {{ __('ai_credits.preview.purchase_credits') }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Modal Footer --}}
        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-8 py-4">
            <button type="button"
                onclick="AICostPreview.close()"
                class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                {{ __('ai_credits.preview.cancel') }}
            </button>
            <button type="button"
                id="costPreviewProceedBtn"
                onclick="AICostPreview.proceed()"
                disabled
                class="rounded-lg bg-[#E67E22] px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-[#D35400] disabled:cursor-not-allowed disabled:opacity-50">
                <span class="material-symbols-outlined mr-2 align-middle text-sm">check_circle</span>
                {{ __('ai_credits.preview.proceed') }}
            </button>
        </div>

    </div>
</div>
