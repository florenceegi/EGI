{{-- resources/views/mint/checkout.blade.php --}}
<x-platform-layout :title="__('mint.page_title', ['title' => $egi->title])">
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-gray-900">
                    {{ __('mint.header_title') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('mint.header_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- EGI Preview --}}
                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.egi_preview.title') }}</h2>

                        {{-- EGI Card Preview --}}
                        <div class="mb-4 aspect-square overflow-hidden rounded-lg">
                            <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                class="h-full w-full object-cover">
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900">{{ $egi->title }}</h3>
                        <p class="mb-2 text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}</p>

                        @if ($egi->description)
                            <p class="text-sm text-gray-700">{{ Str::limit($egi->description, 150) }}</p>
                        @endif
                    </div>

                    {{-- Blockchain Info --}}
                    <div class="rounded-lg bg-blue-50 p-6">
                        <h3 class="mb-3 text-lg font-semibold text-blue-900">{{ __('mint.blockchain_info.title') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">{{ __('mint.blockchain_info.network') }}</span>
                                <span class="font-medium">{{ __('mint.blockchain_info.network_value') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">{{ __('mint.blockchain_info.token_type') }}</span>
                                <span class="font-medium">{{ __('mint.blockchain_info.token_type_value') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">{{ __('mint.blockchain_info.supply') }}</span>
                                <span class="font-medium">{{ __('mint.blockchain_info.supply_value') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Certificate of Authenticity (CoA) --}}
                    @if ($egi->coa && $egi->coa->status === 'valid')
                        <div class="rounded-lg bg-amber-50 p-6">
                            <div class="mb-3 flex items-center">
                                <svg class="mr-2 h-6 w-6 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-amber-900">{{ __('mint.coa.title') }}</h3>
                            </div>
                            <div class="space-y-3">
                                {{-- CoA Status Badge --}}
                                <div
                                    class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ __('mint.coa.certified') }}
                                </div>

                                {{-- CoA Details --}}
                                <div class="space-y-2 text-sm">
                                    @if ($egi->coa->serial)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.certificate_number') }}</span>
                                            <span
                                                class="font-mono font-medium text-amber-900">{{ $egi->coa->serial }}</span>
                                        </div>
                                    @endif
                                    @if ($egi->coa->issuer_name)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.issuer') }}</span>
                                            <span
                                                class="font-medium text-amber-900">{{ Str::limit($egi->coa->issuer_name, 30) }}</span>
                                        </div>
                                    @endif
                                    @if ($egi->coa->issued_at)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.issue_date') }}</span>
                                            <span
                                                class="font-medium text-amber-900">{{ $egi->coa->issued_at->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    {{-- Note: authenticity_level does NOT exist in Coa model --}}
                                </div>

                                {{-- CoA Info Note --}}
                                <div class="mt-3 rounded-md border border-amber-200 bg-amber-100 p-3">
                                    <p class="text-xs text-amber-800">
                                        <svg class="mr-1 inline h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('mint.coa.info_note') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Checkout Form --}}
                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.payment.title') }}</h2>

                        {{-- Mint Status Badges --}}
                        @if ($mintStatus === 'completed')
                            {{-- COMPLETED: Green Badge with ASA ID --}}
                            <div class="mb-6 rounded-lg border-2 border-green-200 bg-green-50 p-6">
                                <div class="mb-3 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-green-900">
                                        {{ __('mint.status.already_minted') }}</h3>
                                </div>
                                <p class="mb-4 text-sm text-green-800">{{ __('mint.status.minted_message') }}</p>
                                <div class="space-y-3 rounded-lg border border-green-300 bg-green-100 p-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-green-700">{{ __('mint.status.asa_id') }}</span>
                                        <span
                                            class="font-mono text-base font-bold text-green-900">{{ $blockchainData['asa_id'] ?? $egi->token_EGI }}</span>
                                    </div>
                                    @if (!empty($blockchainData['tx_id']))
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="font-medium text-green-700">{{ __('mint.status.transaction_id') }}</span>
                                            <span
                                                class="font-mono text-xs font-semibold text-green-900">{{ Str::limit($blockchainData['tx_id'], 20) }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-3 border-t border-green-300 pt-3">
                                        <a href="https://testnet.algoexplorer.io/asset/{{ $blockchainData['asa_id'] ?? $egi->token_EGI }}"
                                            target="_blank"
                                            class="inline-flex items-center text-sm font-medium text-green-700 transition-colors hover:text-green-900">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            {{ __('mint.status.view_on_explorer') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @elseif($mintStatus === 'processing')
                            {{-- PROCESSING: Blue Badge with spinner --}}
                            <div class="mb-6 rounded-lg border-2 border-blue-200 bg-blue-50 p-6">
                                <div class="mb-3 flex items-center">
                                    <svg class="mr-2 h-6 w-6 animate-spin text-blue-600" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-blue-900">
                                        {{ __('mint.status.processing_title') }}</h3>
                                </div>
                                <p class="mb-4 text-sm text-blue-800">{{ __('mint.status.processing_message') }}</p>
                                <div class="rounded-lg border border-blue-300 bg-blue-100 p-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span
                                            class="font-medium text-blue-700">{{ __('mint.status.status_label') }}</span>
                                        <span class="font-semibold text-blue-900">
                                            {{ $blockchainData['status'] === 'minting_queued' ? __('mint.status.queued') : __('mint.status.minting') }}
                                        </span>
                                    </div>
                                    <p class="mt-3 text-xs text-blue-600">
                                        ⏱️ {{ __('mint.status.estimated_time') }}
                                    </p>
                                </div>
                            </div>
                        @elseif($mintStatus === 'failed')
                            {{-- FAILED: Red Badge with error --}}
                            <div class="mb-6 rounded-lg border-2 border-red-200 bg-red-50 p-6">
                                <div class="mb-3 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-red-900">
                                        {{ __('mint.status.failed_title') }}</h3>
                                </div>
                                <p class="mb-4 text-sm text-red-800">{{ __('mint.status.failed_message') }}</p>
                                <div class="rounded-lg border border-red-300 bg-red-100 p-4">
                                    <p class="font-mono text-xs text-red-700">
                                        {{ $blockchainData['error'] ?? 'Errore sconosciuto' }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Reservation Summary (if minting after reservation) --}}
                        @if ($reservation)
                            <div class="mb-6 rounded-lg bg-green-50 p-4">
                                <h3 class="mb-2 font-semibold text-green-900">
                                    {{ __('mint.payment.winning_reservation') }}
                                </h3>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-green-700">{{ __('mint.payment.your_offer') }}</span>
                                        <span
                                            class="font-bold text-green-900">€{{ number_format($reservation->amount_eur ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700">{{ __('mint.payment.reservation_date') }}</span>
                                        <span
                                            class="text-green-900">{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Direct Mint Price --}}
                            <div class="mb-6 rounded-lg bg-blue-50 p-4">
                                <h3 class="mb-2 font-semibold text-blue-900">
                                    {{ __('mint.payment.direct_mint_price') }}
                                </h3>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">{{ __('mint.payment.base_price') }}</span>
                                        <span
                                            class="font-bold text-blue-900">€{{ number_format($egi->price ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Payment Form --}}
                        <form id="mint-form"
                            action="{{ $reservation ? route('mint.process') : route('egi.mint-direct.process', $egi->id) }}"
                            method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                            @if ($reservation)
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                            @endif

                            {{-- Payment Method --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.payment_method_label') }}
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="stripe" checked
                                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ __('mint.payment.credit_card') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="paypal"
                                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ __('mint.payment.paypal') }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Optional Wallet Address --}}
                            <div>
                                <label for="buyer_wallet" class="mb-2 block text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.wallet_label') }}
                                </label>
                                <input type="text" id="buyer_wallet" name="buyer_wallet"
                                    placeholder="{{ __('mint.payment.wallet_placeholder') }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('mint.payment.wallet_help') }}
                                </p>
                            </div>

                            {{-- AREA 5.5.1: Co-Creator Display Name (IMMUTABLE AFTER MINT) --}}
                            <div>
                                <label for="co_creator_display_name"
                                    class="mb-2 block text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.co_creator_name_label') }}
                                    <span class="text-xs text-gray-500">({{ __('mint.payment.optional') }})</span>
                                </label>
                                @php
                                    // Use nick_name if exists, otherwise full wallet (NOT abbreviated)
                                    $defaultCoCreatorName = Auth::user()->nick_name ?? (Auth::user()->wallet ?? '');
                                @endphp
                                <input type="text" id="co_creator_display_name" name="co_creator_display_name"
                                    value="{{ old('co_creator_display_name', $defaultCoCreatorName) }}"
                                    placeholder="{{ $defaultCoCreatorName ?: __('mint.payment.co_creator_name_placeholder') }}"
                                    maxlength="100"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    title="{{ __('mint.payment.co_creator_name_pattern') }}">
                                <div class="mt-1 flex items-start justify-between">
                                    <p class="text-xs text-gray-500">
                                        {{ __('mint.payment.co_creator_name_help') }}
                                    </p>
                                    <span id="char-counter" class="text-xs text-gray-400">
                                        <span id="char-count">{{ strlen($defaultCoCreatorName) }}</span>/100
                                    </span>
                                </div>
                                <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-xs font-medium text-amber-800">
                                                {{ __('mint.payment.co_creator_name_warning') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @error('co_creator_display_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Total --}}
                            <div class="border-t pt-4">
                                <div class="flex items-center justify-between text-lg font-semibold">
                                    <span>{{ __('mint.payment.total_label') }}</span>
                                    <span class="text-green-600">
                                        €{{ number_format($reservation ? $reservation->amount_eur : $egi->price, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Worker Status Progress Bar (hidden by default, shown during system check) --}}
                            <div id="worker-progress-container"
                                class="mb-4 hidden rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <div class="mb-2 flex items-center">
                                    <svg class="mr-3 h-5 w-5 animate-spin text-blue-600" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span id="worker-progress-message" class="text-sm font-medium text-blue-900">
                                        {{ __('mint.worker.checking') }}
                                    </span>
                                </div>

                                {{-- Progress Steps --}}
                                <div class="mb-2 mt-3 flex items-center justify-between">
                                    <div class="flex flex-1 items-center">
                                        <div id="step-1" class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                                1</div>
                                            <span class="ml-2 text-xs text-blue-900">{{ __('mint.worker.step_1') }}</span>
                                        </div>
                                    </div>
                                    <div class="mx-2 h-0.5 flex-1 bg-gray-300" id="progress-line-1"></div>
                                    <div class="flex flex-1 items-center">
                                        <div id="step-2" class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-gray-600">
                                                2</div>
                                            <span class="ml-2 text-xs text-gray-600">{{ __('mint.worker.step_2') }}</span>
                                        </div>
                                    </div>
                                    <div class="mx-2 h-0.5 flex-1 bg-gray-300" id="progress-line-2"></div>
                                    <div class="flex flex-1 items-center">
                                        <div id="step-3" class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-gray-600">
                                                3</div>
                                            <span class="ml-2 text-xs text-gray-600">{{ __('mint.worker.step_3') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mt-3 h-2 w-full rounded-full bg-gray-200">
                                    <div id="worker-progress-bar"
                                        class="h-2 rounded-full bg-blue-600 transition-all duration-500"
                                        style="width: 33%"></div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" @if (in_array($mintStatus, ['processing', 'completed'])) disabled @endif
                                class="@if (in_array($mintStatus, ['processing', 'completed'])) bg-gray-400 cursor-not-allowed opacity-60
                                @else
                                    bg-gradient-to-r from-green-500 to-emerald-600 hover:scale-[1.02] hover:from-green-600 hover:to-emerald-700 focus:ring-green-500 @endif w-full transform rounded-lg px-4 py-3 font-medium text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2">
                                <span class="flex items-center justify-center">
                                    @if ($mintStatus === 'completed')
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('mint.status.already_minted_button') }}
                                    @elseif($mintStatus === 'processing')
                                        <svg class="mr-2 h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        {{ __('mint.status.processing_button') }}
                                    @else
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('mint.payment.submit_button') }}
                                    @endif
                                </span>
                            </button>
                        </form>
                    </div>

                    {{-- Info MiCA Compliance --}}
                    <div class="rounded-lg bg-yellow-50 p-4">
                        <h3 class="mb-2 font-semibold text-yellow-900">{{ __('mint.compliance.mica_title') }}</h3>
                        <p class="text-sm text-yellow-800">
                            {{ __('mint.compliance.mica_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Modal --}}
    {{-- Loading Modal --}}
    <div id="loading-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="relative w-full max-w-md transform rounded-lg bg-white p-6 shadow-xl transition-all">
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-6 w-6 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('mint.modal.processing_title') }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">{{ __('mint.modal.processing_message') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const form = document.getElementById('mint-form');

            // AREA 5.5.1: Character counter for Co-Creator Display Name
            const coCreatorNameInput = document.getElementById('co_creator_display_name');
            const charCountSpan = document.getElementById('char-count');

            if (coCreatorNameInput && charCountSpan) {
                coCreatorNameInput.addEventListener('input', function() {
                    const length = this.value.length;
                    charCountSpan.textContent = length;

                    // Visual feedback quando si avvicina al limite
                    const charCounter = document.getElementById('char-counter');
                    if (length > 90) {
                        charCounter.classList.add('text-red-600', 'font-semibold');
                        charCounter.classList.remove('text-gray-400');
                    } else if (length > 75) {
                        charCounter.classList.add('text-amber-600', 'font-medium');
                        charCounter.classList.remove('text-gray-400', 'text-red-600');
                    } else {
                        charCounter.classList.remove('text-red-600', 'text-amber-600', 'font-semibold', 'font-medium');
                        charCounter.classList.add('text-gray-400');
                    }
                });

                // Validazione pattern in tempo reale
                coCreatorNameInput.addEventListener('blur', function() {
                    const pattern = /^[a-zA-Z0-9\s.\'\-]+$/;
                    if (this.value && !pattern.test(this.value)) {
                        this.classList.add('border-red-500');
                        this.setCustomValidity('{{ __('mint.payment.co_creator_name_invalid') }}');
                    } else {
                        this.classList.remove('border-red-500');
                        this.setCustomValidity('');
                    }
                });
            }

            /**
             * Check worker availability with visual progress bar
             * Returns: Promise<boolean> - true if worker ready, false if unavailable
             */
            async function checkWorkerWithProgress() {
                // Translations from PHP
                const workerMessages = {
                    checking: '{{ __('mint.worker.checking') }}',
                    starting: '{{ __('mint.worker.starting') }}',
                    finalizing: '{{ __('mint.worker.finalizing') }}',
                    ready: '{{ __('mint.worker.ready') }}',
                    unavailable: '{{ __('mint.worker.unavailable') }}'
                };
                

                const progressContainer = document.getElementById('worker-progress-container');
                const progressBar = document.getElementById('worker-progress-bar');
                const progressMessage = document.getElementById('worker-progress-message');
                const step1 = document.getElementById('step-1').querySelector('div');
                const step2 = document.getElementById('step-2').querySelector('div');
                const step3 = document.getElementById('step-3').querySelector('div');
                const line1 = document.getElementById('progress-line-1');
                const line2 = document.getElementById('progress-line-2');

                // Show progress container
                progressContainer.classList.remove('hidden');

                const maxAttempts = 3;
                const delayMs = 2000;

                for (let attempt = 1; attempt <= maxAttempts; attempt++) {
                    // Update progress based on attempt
                    const progress = (attempt / maxAttempts) * 100;
                    progressBar.style.width = `${progress}%`;

                    // Update step visual feedback
                    if (attempt === 1) {
                        progressMessage.textContent = workerMessages.checking;
                        step1.classList.add('bg-blue-600', 'text-white');
                        step1.classList.remove('bg-gray-300', 'text-gray-600');
                    } else if (attempt === 2) {
                        progressMessage.textContent = workerMessages.starting;
                        line1.classList.add('bg-blue-600');
                        step2.classList.add('bg-blue-600', 'text-white');
                        step2.classList.remove('bg-gray-300', 'text-gray-600');
                    } else if (attempt === 3) {
                        progressMessage.textContent = workerMessages.finalizing;
                        line2.classList.add('bg-blue-600');
                        step3.classList.add('bg-blue-600', 'text-white');
                        step3.classList.remove('bg-gray-300', 'text-gray-600');
                    }

                    try {
                        const response = await fetch('/worker/status', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.can_proceed) {
                            // Worker ready! ✅
                            progressMessage.textContent = workerMessages.ready;
                            progressBar.style.width = '100%';
                            progressBar.classList.add('bg-green-600');

                            // Mark all steps complete
                            [step1, step2, step3].forEach(step => {
                                step.classList.add('bg-green-600', 'text-white');
                                step.classList.remove('bg-blue-600', 'bg-gray-300', 'text-gray-600');
                            });
                            [line1, line2].forEach(line => {
                                line.classList.add('bg-green-600');
                            });

                            // Wait 500ms to show success, then hide
                            await new Promise(resolve => setTimeout(resolve, 500));
                            progressContainer.classList.add('hidden');
                            return true;
                        }

                        // Worker not ready, wait before retry
                        if (attempt < maxAttempts) {
                            await new Promise(resolve => setTimeout(resolve, delayMs));
                        }

                    } catch (error) {
                        console.error('Worker status check failed:', error);

                        if (attempt < maxAttempts) {
                            await new Promise(resolve => setTimeout(resolve, delayMs));
                        }
                    }
                }

                // All attempts failed ❌
                progressMessage.textContent = workerMessages.unavailable;
                progressBar.classList.add('bg-red-600');
                progressBar.style.width = '100%';

                setTimeout(() => {
                    progressContainer.classList.add('hidden');
                }, 2000);

                return false;
            }

            // Check for success parameter in URL on page load
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const success = urlParams.get('success');

                if (success === '1') {
                    // Remove success param from URL without reload
                    const newUrl = window.location.pathname + window.location.hash;
                    window.history.replaceState({}, document.title, newUrl);

                    // Check mint status from backend
                    const mintStatus = '{{ $mintStatus }}';

                    if (mintStatus === 'completed') {
                        // EGI minted - show success notification with ASA
                        @if (!empty($blockchainData['asa_id']))
                            showMintSuccessNotification({
                                asaId: '{{ $blockchainData['asa_id'] }}',
                                egiTitle: '{{ $egi->title }}'
                            });
                        @endif
                    } else if (mintStatus === 'processing') {
                        // Mint still processing - show processing notification
                        showMintProcessingNotification();

                        // Start polling for mint completion
                        startMintStatusPolling();
                    }
                }

                /**
                 * Enterprise-grade mint status polling
                 * - AJAX polling without page reload
                 * - Exponential backoff (5s → 10s → 15s)
                 * - Automatic UI update on status change
                 * - Error handling with UEM integration
                 * - Max 10 minutes timeout
                 */
                function startMintStatusPolling() {
                    const egiId = {{ $egi->id }};
                    const statusUrl = `/mint/status/${egiId}`;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    let pollCount = 0;
                    let pollInterval = 5000; // Start with 5 seconds
                    const maxPolls = 60; // 10 minutes max (adaptive interval)
                    let currentPollTimeout = null;

                    const pollStatus = async () => {
                        pollCount++;

                        // Timeout after max polls
                        if (pollCount > maxPolls) {
                            console.warn('[MINT POLL] Max polling time reached (10 minutes)');
                            showPollingTimeoutNotification();
                            return;
                        }

                        try {
                            const response = await fetch(statusUrl, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin'
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }

                            const data = await response.json();

                            console.log(`[MINT POLL ${pollCount}] Status:`, data.status);

                            // Handle status changes
                            if (data.status === 'minted') {
                                // ✅ MINT COMPLETED
                                console.log('[MINT POLL] ✅ Mint completed!', data);
                                updateUIToMinted(data);
                                return; // Stop polling

                            } else if (data.status === 'failed') {
                                // ❌ MINT FAILED
                                console.error('[MINT POLL] ❌ Mint failed:', data.error);
                                updateUIToFailed(data);
                                return; // Stop polling

                            } else if (data.status === 'minting_queued' || data.status === 'minting') {
                                // ⏳ STILL PROCESSING - Continue polling with adaptive interval

                                // Exponential backoff: 5s → 8s → 10s → 15s
                                if (pollCount > 20) {
                                    pollInterval = 15000; // 15s after 20 polls (~3 minutes)
                                } else if (pollCount > 10) {
                                    pollInterval = 10000; // 10s after 10 polls (~1.5 minutes)
                                } else if (pollCount > 5) {
                                    pollInterval = 8000; // 8s after 5 polls (~40 seconds)
                                }

                                currentPollTimeout = setTimeout(pollStatus, pollInterval);
                            }

                        } catch (error) {
                            console.error('[MINT POLL] Error:', error);

                            // Retry with longer interval on error
                            if (pollCount < maxPolls) {
                                currentPollTimeout = setTimeout(pollStatus, pollInterval * 2);
                            } else {
                                showPollingErrorNotification();
                            }
                        }
                    };

                    // Start polling
                    pollStatus();
                }

                /**
                 * Update UI to show minted status (green badge + ASA)
                 */
                function updateUIToMinted(data) {
                    // Remove processing badge
                    const processingBadge = document.querySelector('.border-blue-200.bg-blue-50');
                    if (processingBadge) {
                        processingBadge.remove();
                    }

                    // Show success notification
                    showMintSuccessNotification({
                        asaId: data.asa_id,
                        txId: data.tx_id,
                        egiTitle: '{{ $egi->title }}'
                    });

                    // Reload page to show complete minted state
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000); // 2 seconds after notification
                }

                /**
                 * Update UI to show failed status (red badge)
                 */
                function updateUIToFailed(data) {
                    // Remove processing badge
                    const processingBadge = document.querySelector('.border-blue-200.bg-blue-50');
                    if (processingBadge) {
                        processingBadge.remove();
                    }

                    // Show error notification with SweetAlert2
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('mint.errors.mint_failed') }}',
                            text: data.error || 'Si è verificato un errore durante il mint',
                            confirmButtonText: 'Ricarica Pagina',
                            confirmButtonColor: '#DC2626',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        alert(`{{ __('mint.errors.mint_failed') }}\n\n${data.error || 'Unknown error'}`);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }

                /**
                 * Show timeout notification
                 */
                function showPollingTimeoutNotification() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __('mint.errors.polling_timeout') }}',
                            html: 'Il mint potrebbe richiedere più tempo del previsto.<br><br>' +
                                  '<strong>Cosa fare:</strong><br>' +
                                  '• Ricarica la pagina tra 2-3 minuti<br>' +
                                  '• Controlla lo stato del mint nella sezione "I Tuoi EGI"<br>' +
                                  '• Se il problema persiste, contatta l\'assistenza',
                            confirmButtonText: 'Ricarica Ora',
                            showCancelButton: true,
                            cancelButtonText: 'Chiudi',
                            confirmButtonColor: '#3B82F6',
                            cancelButtonColor: '#6B7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        alert('{{ __('mint.errors.polling_timeout') }}\n\nIl mint potrebbe richiedere più tempo del previsto. Ricarica la pagina tra qualche minuto per verificare lo stato.');
                    }
                }

                /**
                 * Show polling error notification
                 */
                function showPollingErrorNotification() {
                    console.error('[MINT POLL] Too many errors, stopping');
                }
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // STEP 1: Check worker availability with visual progress
                const workerReady = await checkWorkerWithProgress();

                if (!workerReady) {
                    // Show error with SweetAlert2
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('mint.worker.error_title') }}',
                            html: '{{ __('mint.worker.error_message') }}',
                            confirmButtonText: '{{ __('mint.worker.error_button') }}',
                            confirmButtonColor: '#DC2626',
                            showClass: {
                                popup: 'animate__animated animate__fadeIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOut'
                            }
                        });
                    } else {
                        // Fallback toast if SweetAlert2 not loaded
                        console.error('Worker unavailable and SweetAlert2 not loaded');
                    }
                    return;
                }

                // Show loading modal (EXISTING - kept as is)
                document.getElementById('loading-modal').classList.remove('hidden');

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server response:', errorText);
                        throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Redirect with success parameter
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('success', '1');
                        window.location.href = result.redirect || currentUrl.toString();
                    } else {
                        throw new Error(result.message || result.error || '{{ __('mint.js.default_error') }}');
                    }
                } catch (error) {
                    console.error('Mint error:', error);
                    document.getElementById('loading-modal').classList.add('hidden');
                    
                    // Show error with SweetAlert2
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore Durante il Mint',
                            text: error.message || '{{ __('mint.js.default_error') }}',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#DC2626'
                        });
                    } else {
                        alert('{{ __('mint.js.error_prefix') }}' + error.message);
                    }
                }
            });

            // Function to show mint processing notification
            function showMintProcessingNotification() {
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 z-50 max-w-md p-6 transition-all transform translate-x-0 bg-white border-2 border-blue-500 rounded-lg shadow-2xl';
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-blue-900">⏳ Mint in Elaborazione</h3>
                            <p class="mt-1 text-sm text-blue-800">Il tuo EGI è in fase di creazione sulla blockchain Algorand. Riceverai una notifica quando sarà completato (5-10 minuti).</p>
                            <p class="mt-2 text-xs text-blue-600">Puoi chiudere questa pagina e tornare più tardi. Lo stato sarà aggiornato automaticamente.</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                                class="ml-4 text-blue-600 transition-colors hover:text-blue-900">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Auto-remove after 15 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(150%)';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 15000);
            }

            // Function to show mint success notification
            function showMintSuccessNotification(data) {
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 z-50 max-w-md p-6 transition-all transform translate-x-0 bg-white border-2 border-green-500 rounded-lg shadow-2xl';
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-green-900">{{ __('mint.notification.success_title') }}</h3>
                            <p class="mt-1 text-sm text-green-800">{{ __('mint.notification.success_message') }}</p>
                            ${data.asaId ? `
                                                                        <div class="p-3 mt-3 border rounded-lg border-green-300 bg-green-50">
                                                                            <div class="flex items-center justify-between mb-2 text-sm">
                                                                                <span class="font-medium text-green-700">{{ __('mint.notification.asa_label') }}:</span>
                                                                                <span class="font-mono font-bold text-green-900">${data.asaId}</span>
                                                                            </div>
                                                                            <a href="https://testnet.algoexplorer.io/asset/${data.asaId}" target="_blank"
                                                                               class="inline-flex items-center text-sm font-medium text-green-700 transition-colors hover:text-green-900">
                                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                                </svg>
                                                                                {{ __('mint.notification.view_blockchain') }}
                                                                            </a>
                                                                        </div>
                                                                    ` : ''}
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                                class="ml-4 text-green-600 transition-colors hover:text-green-900">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Auto-remove after 10 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(150%)';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 10000);
            }
        </script>
    @endpush
</x-platform-layout>
