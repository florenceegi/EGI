{{-- resources/views/components/egi-mint-history.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Mint/Rebind History --}}
{{-- Displays mint/rebind blockchain purchase history for an EGI in NFT marketplace style. --}}
{{-- @accessibility-trait Uses semantic markup and clear visual indicators for blockchain transactions --}}
{{-- @schema-type ItemList - Represents a collection of blockchain purchase events --}}

@props(['egi', 'certificates' => collect()])

<div class="mt-10 overflow-hidden rounded-lg bg-white shadow-md" x-data="{ expanded: false }" id="egi-mint-history">

    {{-- Header --}}
    <div class="flex cursor-pointer items-center justify-between bg-gradient-to-r from-green-600 to-emerald-600 p-4"
        @click="expanded = !expanded" aria-expanded="false" :aria-expanded="expanded.toString()"
        aria-controls="mint-history-content">
        <div class="flex items-center">
            <svg class="mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-white">{{ __('certificate.mint_history.title') }}</h3>
        </div>
        <div class="flex items-center text-white">
            <span class="mr-2 text-sm">{{ $certificates->count() }}
                {{ trans_choice('certificate.mint_history.entries', $certificates->count()) }}</span>
            <svg x-show="!expanded" class="h-5 w-5 transition-transform" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <svg x-show="expanded" class="h-5 w-5 transition-transform" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </div>
    </div>

    {{-- Content (collapsible) --}}
    <div id="mint-history-content" class="overflow-hidden border-t border-gray-200 transition-all duration-300"
        x-show="expanded" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[1000px]"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-[1000px]"
        x-transition:leave-end="opacity-0 max-h-0">

        @if ($certificates->isEmpty())
            {{-- Empty state --}}
            <div class="p-8 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <h4 class="mb-1 text-lg font-medium text-gray-900">{{ __('certificate.mint_history.no_entries') }}</h4>
                <p class="text-gray-500">{{ __('certificate.mint_history.no_purchases_yet') }}</p>
            </div>
        @else
            {{-- Timeline view --}}
            <div class="p-6">
                <div class="flow-root">
                    <ul role="list" class="mint-timeline">
                        @foreach ($certificates as $certificate)
                            <li class="mint-timeline-item">
                                <div class="mint-timeline-connector">
                                    <div class="mint-timeline-icon bg-green-500">
                                        <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mint-timeline-content">
                                    {{-- Card Header --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="mr-3 flex h-10 w-10 items-center justify-center rounded-md bg-green-50">
                                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    @if ($certificate->user)
                                                        {{ $certificate->user->name }}
                                                    @else
                                                        <span
                                                            class="font-mono text-xs">{{ Str::limit($certificate->wallet_address, 15) }}</span>
                                                    @endif
                                                </h4>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('certificate.type.blockchain_purchase') }}
                                                    <span
                                                        class="ml-1 inline-flex rounded-full bg-green-100 px-1.5 py-0.5 text-xs font-medium text-green-800">
                                                        <svg class="mr-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        {{ __('certificate.badge.minted') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">
                                                @if ($certificate->egiBlockchain && $certificate->egiBlockchain->paid_currency === 'EGL')
                                                    {{-- Pagamento in EGILI --}}
                                                    {{ number_format($certificate->egiBlockchain->paid_amount, 0, ',', '.') }} Egili
                                                @else
                                                    {{-- Pagamento in EUR --}}
                                                    €{{ number_format($certificate->offer_amount_fiat, 2, ',', '.') }}
                                                @endif
                                            </p>
                                            @if ($certificate->egiBlockchain)
                                                <p class="text-xs text-gray-500">
                                                    ASA ID: {{ $certificate->egiBlockchain->asa_id }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Blockchain Info --}}
                                    @if ($certificate->egiBlockchain)
                                        <div class="mt-3 rounded-md border border-green-100 bg-green-50 px-3 py-2">
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center text-gray-600">
                                                    <svg class="mr-1 h-4 w-4 text-green-600"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    <span
                                                        class="font-medium">{{ __('certificate.ownership_type') }}:</span>
                                                    <span class="ml-1">
                                                        @if ($certificate->egiBlockchain->ownership_type === 'user_wallet')
                                                            {{ __('certificate.ownership.user_wallet') }}
                                                        @else
                                                            {{ __('certificate.ownership.treasury') }}
                                                        @endif
                                                    </span>
                                                </div>
                                                @if ($certificate->egiBlockchain->tx_id)
                                                    <a href="https://app.dappflow.org/explorer/transaction/{{ $certificate->egiBlockchain->tx_id }}"
                                                        target="_blank"
                                                        class="font-medium text-green-600 hover:text-green-800">
                                                        {{ __('certificate.view_on_explorer') }} ↗
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Card Footer --}}
                                    <div class="mt-3 flex items-center justify-between text-xs">
                                        <div class="flex items-center text-gray-500">
                                            <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <time datetime="{{ $certificate->created_at->toIso8601String() }}">
                                                {{ $certificate->created_at->diffForHumans() }}
                                            </time>
                                        </div>
                                        <a href="{{ route('egi-certificates.show', $certificate->certificate_uuid) }}"
                                            class="font-medium text-green-600 hover:text-green-800">
                                            {{ __('certificate.view_certificate') }} →
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Timeline styling */
    .mint-timeline {
        margin-top: 1.25rem;
        position: relative;
    }

    .mint-timeline-item {
        display: flex;
        position: relative;
        padding-bottom: 1.5rem;
    }

    .mint-timeline-item:last-child {
        padding-bottom: 0;
    }

    .mint-timeline-connector {
        position: relative;
        flex: 0 0 auto;
        width: 40px;
    }

    .mint-timeline-icon {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 1rem;
    }

    .mint-timeline-content {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
        border: 1px solid #d1fae5;
        border-radius: 0.375rem;
        transition: all 0.3s;
        background: linear-gradient(to bottom right, #ffffff, #f0fdf4);
    }

    .mint-timeline-content:hover {
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
        border-color: #10b981;
    }

    /* Timeline connector line */
    .mint-timeline-item:not(:last-child) .mint-timeline-connector::before {
        content: '';
        position: absolute;
        top: 28px;
        left: 12px;
        bottom: 0;
        width: 1px;
        background-color: #d1fae5;
    }
</style>

@push('scripts')
    <script>
        // This script is only needed if you're not using Alpine.js globally
        document.addEventListener('DOMContentLoaded', function() {
            // Makes sure to initialize the component even if Alpine.js is loaded after this code
            if (typeof Alpine === 'undefined') {
                // If using a module bundler like webpack, you might need a different approach
                console.warn('Alpine.js is required for the EGI Mint History component.');
            }
        });
    </script>
@endpush
