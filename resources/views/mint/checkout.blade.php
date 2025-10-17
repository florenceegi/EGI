{{-- resources/views/mint/checkout.blade.php --}}
<x-platform-layout :title="__('mint.page_title', ['title' => $egi->title])">
    <div class="container px-4 py-8 mx-auto bg-white">
        <div class="mx-auto max-w-7xl">
            {{-- Header --}}
            <div class="p-6 mb-8 bg-white rounded-lg shadow-sm">
                <h1 class="mb-3 text-4xl font-bold text-gray-900">
                    @if ($mintStatus === 'completed')
                        {{ __('mint.header_title_completed') }}
                    @else
                        {{ __('mint.header_title') }}
                    @endif
                </h1>
                <p class="text-xl font-medium text-gray-900">
                    @if ($mintStatus === 'completed')
                        {{ __('mint.header_description_completed') }}
                    @else
                        {{ __('mint.header_description') }}
                    @endif
                </p>

                {{-- DEBUG PANEL - Mostra log salvati in localStorage --}}
                <div id="debug-panel" class="p-4 mt-4 border border-yellow-200 rounded-lg bg-yellow-50"
                    style="display: none;">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold text-yellow-900">🐛 DEBUG LOG (ultimo mint)</h3>
                        <button onclick="document.getElementById('debug-panel').style.display='none'"
                            class="text-yellow-600 hover:text-yellow-900">✖</button>
                    </div>
                    <pre id="debug-content" class="p-2 overflow-auto text-xs bg-white rounded max-h-40"></pre>
                </div>
            </div>

            <script>
                // Mostra debug panel se ci sono log salvati
                const debugStep = localStorage.getItem('mint_debug_step');
                if (debugStep) {
                    const debugPanel = document.getElementById('debug-panel');
                    const debugContent = document.getElementById('debug-content');

                    const debugInfo = {
                        step: localStorage.getItem('mint_debug_step'),
                        data: localStorage.getItem('mint_debug_data'),
                        response_status: localStorage.getItem('mint_debug_response_status'),
                        result: localStorage.getItem('mint_debug_result'),
                        error: localStorage.getItem('mint_debug_error'),
                        exception: localStorage.getItem('mint_debug_exception')
                    };

                    debugContent.textContent = JSON.stringify(debugInfo, null, 2);
                    debugPanel.style.display = 'block';
                }
            </script>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- COLUMN 1: EGI Preview + Blockchain Info --}}
                <div class="space-y-6">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.egi_preview.title') }}</h2>

                        {{-- EGI Card Preview with clickable image --}}
                        <a href="{{ route('egis.show', ['egi' => $egi->id]) }}" target="_blank"
                            class="block mb-4 overflow-hidden transition-all rounded-lg aspect-square hover:opacity-90 hover:shadow-xl"
                            title="{{ __('mint.egi_preview.click_to_view') }}">
                            <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                class="object-cover w-full h-full">
                        </a>

                        <div class="flex items-start justify-between mb-2">
                            <h3 class="flex-1 text-lg font-semibold text-gray-900">{{ $egi->title }}</h3>
                            <div class="ml-4 text-right">
                                <div class="text-2xl font-bold text-blue-600">
                                    €
                                    {{ number_format($reservation ? $reservation->amount_eur : $egi->price, 2, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ __('mint.egi_preview.price_label') }}</div>
                            </div>
                        </div>
                        <p class="mb-2 text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}</p>

                        @if ($egi->description)
                            <p class="text-sm text-gray-700">{{ Str::limit($egi->description, 150) }}</p>
                        @endif
                    </div>

                    {{-- Blockchain Info --}}
                    <div class="p-6 rounded-lg bg-blue-50">
                        <h3 class="mb-3 text-lg font-semibold text-blue-900">{{ __('mint.blockchain_info.title') }}
                        </h3>
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
                </div>

                {{-- COLUMN 2: CoA + Utility + Traits --}}
                <div class="space-y-6">
                    {{-- Certificate of Authenticity (CoA) --}}
                    @if ($egi->coa && $egi->coa->status === 'valid')
                        <div class="p-6 rounded-lg bg-amber-50">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-amber-900">{{ __('mint.coa.title') }}</h3>
                            </div>
                            <div class="space-y-3">
                                {{-- CoA Status Badge --}}
                                <div
                                    class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                                <div class="p-3 mt-3 border rounded-md border-amber-200 bg-amber-100">
                                    <p class="text-xs text-amber-800">
                                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
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

                    {{-- Utility Section --}}
                    @if ($egi->utility)
                        <div class="p-6 rounded-lg shadow-md bg-gradient-to-br from-violet-50 to-purple-50">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="text-lg font-semibold text-purple-900">{{ __('mint.utility.title') }}</h3>
                            </div>

                            <div class="mb-4 space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-purple-700">{{ __('mint.utility.type') }}</span>
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">
                                        {{ __('utility.types.' . $egi->utility->type . '.label') }}
                                    </span>
                                </div>
                                <div class="text-sm">
                                    <span
                                        class="font-medium text-purple-700">{{ __('mint.utility.description') }}</span>
                                    <p class="mt-1 text-purple-900">{{ Str::limit($egi->utility->description, 200) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Utility Images Gallery --}}
                            @if ($egi->utility->getMedia('utility_gallery')->count() > 0)
                                <div class="mt-4">
                                    <h4 class="mb-2 text-sm font-semibold text-purple-800">
                                        {{ __('mint.utility.gallery') }}</h4>
                                    <div class="flex gap-2 pb-2 overflow-x-auto" style="scrollbar-width: thin;">
                                        @foreach ($egi->utility->getMedia('utility_gallery') as $index => $media)
                                            <div
                                                class="flex-shrink-0 w-20 h-20 overflow-hidden transition-all border-2 border-purple-200 rounded-lg cursor-pointer hover:scale-105 hover:border-purple-400">
                                                <img src="{{ $media->getUrl('thumb') }}"
                                                    alt="{{ $egi->utility->title }} - Image {{ $index + 1 }}"
                                                    class="object-cover w-full h-full"
                                                    onclick="window.open('{{ $media->getUrl('large') }}', '_blank')"
                                                    loading="lazy">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Traits Section --}}
                    @if ($egi->traits && $egi->traits->count() > 0)
                        <div class="p-6 rounded-lg shadow-md bg-gradient-to-br from-blue-50 to-indigo-50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-indigo-900">{{ __('mint.traits.title') }}
                                    </h3>
                                </div>
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded-full">
                                    {{ $egi->traits->count() }} {{ __('mint.traits.attributes') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($egi->traits as $trait)
                                    @php
                                        $category = $trait->category;
                                        $categoryColor = $category ? $category->color : '#6366f1';
                                        $categoryIcon = $category ? $category->icon : '🏷️';
                                    @endphp
                                    <div
                                        class="relative p-3 transition-all bg-white border border-indigo-200 rounded-lg shadow-sm hover:shadow-md">
                                        {{-- Category Badge --}}
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 text-xs rounded-full"
                                                style="background-color: {{ $categoryColor }}20; color: {{ $categoryColor }};">
                                                {{ $categoryIcon }}
                                            </span>
                                            @if ($trait->getFirstMedia('trait_images'))
                                                <span
                                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-[10px]"
                                                    title="{{ __('mint.traits.has_image') }}">
                                                    📷
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Trait Type --}}
                                        <div
                                            class="mb-1 text-[10px] font-medium uppercase tracking-wide text-indigo-600">
                                            @if ($trait->traitType)
                                                {{ __('trait_elements.types.' . $trait->traitType->name, [], null) ?: $trait->traitType->name }}
                                            @else
                                                {{ __('mint.traits.unknown') }}
                                            @endif
                                        </div>

                                        {{-- Trait Value --}}
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $trait->display_value ?? $trait->value }}
                                            @if ($trait->traitType && $trait->traitType->unit)
                                                <span
                                                    class="text-xs text-gray-500">{{ $trait->traitType->unit }}</span>
                                            @endif
                                        </div>

                                        {{-- Rarity Bar --}}
                                        @if (isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                            @php
                                                // Rarity calculation (inverted: lower % = more rare)
                                                if ($trait->rarity_percentage <= 5) {
                                                    $rarityClass = 'mythic';
                                                    $rarityColor = '#ff00ff';
                                                    $barWidth = 90;
                                                } elseif ($trait->rarity_percentage <= 10) {
                                                    $rarityClass = 'legendary';
                                                    $rarityColor = '#ffd700';
                                                    $barWidth = 75;
                                                } elseif ($trait->rarity_percentage <= 20) {
                                                    $rarityClass = 'epic';
                                                    $rarityColor = '#9333ea';
                                                    $barWidth = 60;
                                                } elseif ($trait->rarity_percentage <= 40) {
                                                    $rarityClass = 'rare';
                                                    $rarityColor = '#3b82f6';
                                                    $barWidth = 45;
                                                } elseif ($trait->rarity_percentage <= 70) {
                                                    $rarityClass = 'uncommon';
                                                    $rarityColor = '#10b981';
                                                    $barWidth = 30;
                                                } else {
                                                    $rarityClass = 'common';
                                                    $rarityColor = '#6b7280';
                                                    $barWidth = 15;
                                                }
                                            @endphp
                                            <div class="mt-2">
                                                <div class="w-full h-1 overflow-hidden bg-gray-200 rounded-full">
                                                    <div class="h-full transition-all rounded-full"
                                                        style="width: {{ $barWidth }}%; background-color: {{ $rarityColor }};">
                                                    </div>
                                                </div>
                                                <div class="mt-1 text-[9px] text-gray-500">
                                                    {{ $trait->rarity_percentage }}%
                                                    {{ __('mint.traits.collection') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- COLUMN 3: Checkout Form + MiCA Compliance --}}
                <div class="space-y-6">
                    {{-- Post-mint success UI will be injected here by JavaScript --}}
                    <div id="post-mint-container"></div>

                    <div id="mint-form-container" class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.payment.title') }}</h2>

                        {{-- Mint Status Badges --}}
                        @if ($mintStatus === 'completed')
                            {{-- COMPLETED: Green Badge with ASA ID --}}
                            <div class="p-6 mb-6 border-2 border-green-200 rounded-lg bg-green-50">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-green-900">
                                        {{ __('mint.status.already_minted') }}</h3>
                                </div>
                                <p class="mb-4 text-sm text-green-800">{{ __('mint.status.minted_message') }}</p>
                                <div class="p-4 space-y-3 bg-green-100 border border-green-300 rounded-lg">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-green-700">{{ __('mint.status.asa_id') }}</span>
                                        <a href="https://testnet.explorer.perawallet.app/asset/{{ $blockchainData['asa_id'] ?? $egi->token_EGI }}"
                                            target="_blank"
                                            class="font-mono text-base font-bold text-green-900 transition-colors hover:text-green-700 hover:underline">
                                            {{ $blockchainData['asa_id'] ?? $egi->token_EGI }}
                                        </a>
                                    </div>
                                    @if (!empty($blockchainData['tx_id']))
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="font-medium text-green-700">{{ __('mint.status.transaction_id') }}</span>
                                            <a href="https://testnet.explorer.perawallet.app/tx/{{ $blockchainData['tx_id'] }}"
                                                target="_blank"
                                                class="font-mono text-xs font-semibold text-green-900 transition-colors hover:text-green-700 hover:underline">
                                                {{ Str::limit($blockchainData['tx_id'], 20) }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="pt-3 mt-3 border-t border-green-300">
                                        <a href="https://testnet.explorer.perawallet.app/asset/{{ $blockchainData['asa_id'] ?? $egi->token_EGI }}"
                                            target="_blank"
                                            class="inline-flex items-center text-sm font-medium text-green-700 transition-colors hover:text-green-900">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
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
                            <div class="p-6 mb-6 border-2 border-blue-200 rounded-lg bg-blue-50">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 mr-2 text-blue-600 animate-spin" fill="none"
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
                                <div class="p-4 bg-blue-100 border border-blue-300 rounded-lg">
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
                            <div class="p-6 mb-6 border-2 border-red-200 rounded-lg bg-red-50">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-red-900">
                                        {{ __('mint.status.failed_title') }}</h3>
                                </div>
                                <p class="mb-4 text-sm text-red-800">{{ __('mint.status.failed_message') }}</p>
                                <div class="p-4 bg-red-100 border border-red-300 rounded-lg">
                                    <p class="font-mono text-xs text-red-700">
                                        {{ $blockchainData['error'] ?? 'Errore sconosciuto' }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Reservation Summary (if minting after reservation) --}}
                        @if ($reservation)
                            <div class="p-4 mb-6 rounded-lg bg-green-50">
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
                            <div class="p-4 mb-6 rounded-lg bg-blue-50">
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
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.payment_method_label') }}
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="stripe" checked
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ __('mint.payment.credit_card') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="paypal"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ __('mint.payment.paypal') }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Optional Wallet Address --}}
                            <div>
                                <label for="buyer_wallet" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.wallet_label') }}
                                </label>
                                <input type="text" id="buyer_wallet" name="buyer_wallet"
                                    placeholder="{{ __('mint.payment.wallet_placeholder') }}"
                                    class="w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-md focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('mint.payment.wallet_help') }}
                                </p>
                            </div>

                            {{-- AREA 5.5.1: Co-Creator Display Name (IMMUTABLE AFTER MINT) --}}
                            <div>
                                <label for="co_creator_display_name"
                                    class="block mb-2 text-sm font-medium text-gray-700">
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
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    title="{{ __('mint.payment.co_creator_name_pattern') }}">
                                <div class="flex items-start justify-between mt-1">
                                    <p class="text-xs text-gray-500">
                                        {{ __('mint.payment.co_creator_name_help') }}
                                    </p>
                                    <span id="char-counter" class="text-xs text-gray-400">
                                        <span id="char-count">{{ strlen($defaultCoCreatorName) }}</span>/100
                                    </span>
                                </div>
                                <div class="p-3 mt-2 border rounded-md border-amber-200 bg-amber-50">
                                    <div class="flex">
                                        <svg class="flex-shrink-0 w-5 h-5 text-amber-500" fill="currentColor"
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
                            <div class="pt-4 border-t">
                                <div class="flex items-center justify-between text-lg font-semibold">
                                    <span>{{ __('mint.payment.total_label') }}</span>
                                    <span class="text-green-600">
                                        €{{ number_format($reservation ? $reservation->amount_eur : $egi->price, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Worker Status Progress Bar (hidden by default, shown during system check) --}}
                            <div id="worker-progress-container"
                                class="hidden p-4 mb-4 border border-blue-200 rounded-lg bg-blue-50">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-3 text-blue-600 animate-spin" fill="none"
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
                                <div class="flex items-center justify-between mt-3 mb-2">
                                    <div class="flex items-center flex-1">
                                        <div id="step-1" class="flex items-center">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-blue-600 rounded-full">
                                                1</div>
                                            <span
                                                class="ml-2 text-xs text-blue-900">{{ __('mint.worker.step_1') }}</span>
                                        </div>
                                    </div>
                                    <div class="mx-2 h-0.5 flex-1 bg-gray-300" id="progress-line-1"></div>
                                    <div class="flex items-center flex-1">
                                        <div id="step-2" class="flex items-center">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-300 rounded-full">
                                                2</div>
                                            <span
                                                class="ml-2 text-xs text-gray-600">{{ __('mint.worker.step_2') }}</span>
                                        </div>
                                    </div>
                                    <div class="mx-2 h-0.5 flex-1 bg-gray-300" id="progress-line-2"></div>
                                    <div class="flex items-center flex-1">
                                        <div id="step-3" class="flex items-center">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-300 rounded-full">
                                                3</div>
                                            <span
                                                class="ml-2 text-xs text-gray-600">{{ __('mint.worker.step_3') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                    <div id="worker-progress-bar"
                                        class="h-2 transition-all duration-500 bg-blue-600 rounded-full"
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
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('mint.status.already_minted_button') }}
                                    @elseif($mintStatus === 'processing')
                                        <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        {{ __('mint.status.processing_button') }}
                                    @else
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
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
                    <div class="p-4 rounded-lg bg-yellow-50">
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
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50"></div>
            <div class="relative w-full max-w-md p-6 transition-all transform bg-white rounded-lg shadow-xl">
                <div class="text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
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
                console.log('[MINT DEBUG] 🚀 DOMContentLoaded event fired');
                localStorage.setItem('mint_debug_step', 'dom_loaded');

                const urlParams = new URLSearchParams(window.location.search);
                const success = urlParams.get('success');

                // Remove success param from URL if present
                if (success === '1') {
                    const newUrl = window.location.pathname + window.location.hash;
                    window.history.replaceState({}, document.title, newUrl);
                    console.log('[MINT DEBUG] ✂️ Removed ?success=1 from URL');
                }

                // Check mint status from backend (ALWAYS, not only with ?success=1)
                const mintStatus = '{{ $mintStatus }}';
                console.log('[MINT DEBUG] 📊 Mint status from backend: ' + mintStatus);
                localStorage.setItem('mint_debug_status', mintStatus);

                if (mintStatus === 'completed') {
                    // EGI already minted - show post-mint UI directly
                    console.log('[MINT DEBUG] ✅ EGI already minted, showing post-mint UI directly');

                    @if (!empty($blockchainData['asa_id']))
                        console.log('[MINT DEBUG] 🔗 Blockchain data available, calling updateUIToMinted');
                        console.log('[MINT DEBUG] 📦 Blockchain data:');
                        console.log({
                            asa_id: '{{ $blockchainData['asa_id'] }}',
                            tx_id: '{{ $blockchainData['tx_id'] ?? '' }}',
                            minted_at: '{{ $blockchainData['minted_at'] ?? '' }}'
                        });

                        // Call updateUIToMinted with blockchain data
                        updateUIToMinted({
                            status: 'minted',
                            asa_id: '{{ $blockchainData['asa_id'] }}',
                            tx_id: '{{ $blockchainData['tx_id'] ?? '' }}',
                            minted_at: '{{ $blockchainData['minted_at'] ?? '' }}'
                        });
                    @else
                        console.error('[MINT DEBUG] ❌ No blockchain data available despite completed status!');
                        localStorage.setItem('mint_debug_error', 'no_blockchain_data');
                    @endif
                } else if (mintStatus === 'processing') {
                    // Mint still processing - show processing notification
                    console.log('[MINT DEBUG] ⏳ Mint still processing, showing notification');
                    showMintProcessingNotification();

                    // Start polling for mint completion
                    startMintStatusPolling();
                }
            });

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
             * Update UI to show minted status (generate certificate + show success state)
             */
            async function updateUIToMinted(data) {
                console.log('[MINT] 🎉 updateUIToMinted called with data:');
                console.log(data);
                localStorage.setItem('mint_debug_step', 'updateUIToMinted_called');
                localStorage.setItem('mint_debug_data', JSON.stringify(data));

                // Remove processing badge
                const processingBadge = document.querySelector('.border-blue-200.bg-blue-50');
                if (processingBadge) {
                    processingBadge.remove();
                }

                // Show initial success notification
                showMintSuccessNotification({
                    asaId: data.asa_id,
                    txId: data.tx_id,
                    egiTitle: '{{ $egi->title }}'
                });

                console.log('[MINT] 📋 Showing post-mint loading state');
                localStorage.setItem('mint_debug_step', 'showing_loading');

                // Show loading state
                showPostMintLoading();

                try {
                    console.log('[MINT DEBUG] 📞 Calling certificate generation endpoint...');
                    console.log('[MINT DEBUG] 🌐 Endpoint URL: /mint/{{ $egi->id }}/certificate/generate');
                    localStorage.setItem('mint_debug_step', 'calling_certificate_endpoint');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    console.log('[MINT DEBUG] 🔑 CSRF Token: ' + (csrfToken ? 'Found' : 'MISSING!'));

                    if (!csrfToken) {
                        console.error('[MINT DEBUG] ❌ CSRF token missing! Fetch will fail!');
                        localStorage.setItem('mint_debug_error', 'csrf_token_missing');
                    }

                    // Call endpoint to generate certificate + payment breakdown
                    const response = await fetch(`/mint/{{ $egi->id }}/certificate/generate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    console.log('[MINT DEBUG] 📨 Certificate endpoint response status: ' + response.status);
                    console.log('[MINT DEBUG] 📨 Response headers:');
                    console.log({
                        'Content-Type': response.headers.get('Content-Type'),
                        'Status': response.status,
                        'StatusText': response.statusText
                    });
                    localStorage.setItem('mint_debug_step', 'certificate_response_received');
                    localStorage.setItem('mint_debug_response_status', response.status);

                    console.log('[MINT DEBUG] 📦 Parsing JSON response...');
                    const result = await response.json();
                    console.log('[MINT DEBUG] 📦 Certificate endpoint result:');
                    console.log(result);
                    console.log('[MINT DEBUG] 📦 Result.success: ' + result.success);
                    console.log('[MINT DEBUG] 📦 Result.data:');
                    console.log(result.data);
                    localStorage.setItem('mint_debug_result', JSON.stringify(result));

                    if (result.success) {
                        console.log(
                            '[MINT DEBUG] ✅ Certificate generated successfully, calling showPostMintSuccess()');
                        console.log('[MINT DEBUG] 📄 Certificate URL: ' + (result.data?.certificate_url || 'N/A'));
                        console.log('[MINT DEBUG] 💰 Payment breakdown count: ' + (result.data?.payment_breakdown
                            ?.length || 0));
                        localStorage.setItem('mint_debug_step', 'showing_success_ui');

                        // Show post-mint success UI with certificate + payment breakdown
                        showPostMintSuccess(result.data);

                        console.log('[MINT DEBUG] ✅ showPostMintSuccess() called successfully');
                    } else {
                        console.error('[MINT DEBUG] ⚠️ Certificate generation failed:', result.message);
                        localStorage.setItem('mint_debug_step', 'certificate_failed');
                        localStorage.setItem('mint_debug_error', result.message);
                        showPostMintPartialSuccess(data);
                    }

                } catch (error) {
                    console.error('[MINT] ❌ Failed to generate certificate:', error);
                    localStorage.setItem('mint_debug_step', 'exception_caught');
                    localStorage.setItem('mint_debug_exception', error.message);
                    // Certificate generation failed, but mint succeeded - show partial success
                    showPostMintPartialSuccess(data);
                }
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
                        text: data.error || '{{ __('mint.errors.mint_error_generic') }}',
                        confirmButtonText: '{{ __('mint.errors.reload_page') }}',
                        confirmButtonColor: '#DC2626',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    console.error('Mint failed:', data.error || '{{ __('mint.errors.unknown_error') }}');
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
                        html: '{{ __('mint.errors.polling_timeout_message') }}<br><br>' +
                            '<strong>Cosa fare:</strong><br>' +
                            '• {{ __('mint.errors.polling_timeout_instructions') }}',
                        confirmButtonText: '{{ __('mint.errors.polling_reload_now') }}',
                        showCancelButton: true,
                        cancelButtonText: '{{ __('mint.errors.polling_close') }}',
                        confirmButtonColor: '#3B82F6',
                        cancelButtonColor: '#6B7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    console.error('{{ __('mint.errors.polling_timeout') }}:',
                        '{{ __('mint.errors.polling_timeout_message') }}');
                }
            }

            /**
             * Show polling error notification
             */
            function showPollingErrorNotification() {
                console.error('[MINT POLL] Too many errors, stopping');
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // STEP 1: Show progress bar (INFORMATIVE ONLY - backend handles auto-start)
                // Fire and forget - don't block submission
                checkWorkerWithProgress();

                // Show loading modal immediately
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
                        // Extract error message from response
                        let errorMessage = `{{ __('mint.errors.mint_failed') }}`;

                        try {
                            const errorData = await response.json();
                            console.log('Error response JSON:', errorData);

                            // Extract user-friendly message from server
                            if (errorData.message) {
                                errorMessage = errorData.message;
                            } else if (errorData.error) {
                                errorMessage = errorData.error;
                            }
                        } catch (jsonError) {
                            // JSON parsing failed, try text
                            console.error('Failed to parse JSON, trying text:', jsonError);

                            try {
                                const errorText = await response.clone().text();
                                console.error('Server response text:', errorText);

                                // If text is short and readable, use it
                                if (errorText && errorText.length > 0 && errorText.length < 200) {
                                    errorMessage = errorText;
                                }
                            } catch (textError) {
                                console.error('Failed to parse text:', textError);
                                // Keep fallback message
                            }
                        }

                        // Throw error with extracted message
                        throw new Error(errorMessage);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Redirect with success parameter to trigger polling on page load
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('success', '1');
                        window.location.href = currentUrl.toString();
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
                            title: '{{ __('mint.errors.submit_error_title') }}',
                            text: error.message || '{{ __('mint.js.default_error') }}',
                            confirmButtonText: '{{ __('mint.js.ok_button') }}',
                            confirmButtonColor: '#DC2626'
                        });
                    } else {
                        console.error('{{ __('mint.js.error_prefix') }}', error.message);
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
                        <div class="flex-1 ml-4">
                            <h3 class="text-lg font-semibold text-blue-900">{{ __('mint.notification.processing_title') }}</h3>
                            <p class="mt-1 text-sm text-blue-800">{{ __('mint.notification.processing_message') }}</p>
                            <p class="mt-2 text-xs text-blue-600">{{ __('mint.notification.processing_note') }}</p>
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
                        <div class="flex-1 ml-4">
                            <h3 class="text-lg font-semibold text-green-900">{{ __('mint.notification.success_title') }}</h3>
                            <p class="mt-1 text-sm text-green-800">{{ __('mint.notification.success_message') }}</p>
                            ${data.asaId ? `
                                                                                                                    <div class="p-3 mt-3 border border-green-300 rounded-lg bg-green-50">
                                                                                                                        <div class="flex items-center justify-between mb-2 text-sm">
                                                                                                                            <span class="font-medium text-green-700">{{ __('mint.notification.asa_label') }}:</span>
                                                                                                                            <a href="https://testnet.explorer.perawallet.app/asset/${data.asaId}" target="_blank"
                                                                                                                                class="font-mono font-bold text-green-900 transition-colors hover:text-green-700 hover:underline">
                                                                                                                                ${data.asaId}
                                                                                                                            </a>
                                                                                                                        </div>
                                                                                                                        <a href="https://testnet.explorer.perawallet.app/asset/${data.asaId}" target="_blank"
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

            /**
             * Show loading state while generating certificate
             */
            function showPostMintLoading() {
                // Hide checkout form container (find by ID for specificity)
                const checkoutFormContainer = document.getElementById('mint-form-container');
                if (checkoutFormContainer) {
                    checkoutFormContainer.style.display = 'none';
                }

                // Show loading UI in the post-mint container
                const postMintContainer = document.getElementById('post-mint-container');
                if (postMintContainer) {
                    const loadingHtml = `
                        <div id="post-mint-loading" class="p-6 bg-white rounded-lg shadow-sm">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <svg class="w-16 h-16 mb-4 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <h3 class="mb-2 text-xl font-semibold text-gray-900">{{ __('mint.post_mint.generating_certificate') }}</h3>
                                <p class="text-gray-600">{{ __('mint.post_mint.please_wait') }}</p>
                            </div>
                        </div>
                    `;
                    postMintContainer.innerHTML = loadingHtml;
                }
            }

            /**
             * Show post-mint success with certificate + payment breakdown
             */
            function showPostMintSuccess(data) {
                console.log('[MINT DEBUG] 🎨 showPostMintSuccess() called with data:');
                console.log(data);
                localStorage.setItem('mint_debug_showPostMintSuccess', JSON.stringify(data));

                // Remove loading
                const loadingElement = document.getElementById('post-mint-loading');
                console.log('[MINT DEBUG] 🔍 Looking for #post-mint-loading element: ' + (loadingElement ? 'Found' :
                    'Not found'));
                loadingElement?.remove();

                // Build payment breakdown table HTML
                let paymentBreakdownHtml = '';
                if (data.payment_breakdown && data.payment_breakdown.length > 0) {
                    console.log('[MINT DEBUG] 💰 Building payment breakdown table with ' + data.payment_breakdown.length +
                        ' entries');
                    paymentBreakdownHtml = `
                        <div class="p-4 mb-6 border border-gray-200 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-sm font-semibold text-gray-700">{{ __('mint.post_mint.payment_breakdown') }}</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-300">
                                        <th class="py-2 text-left text-gray-600">{{ __('mint.post_mint.recipient') }}</th>
                                        <th class="py-2 text-left text-gray-600">{{ __('mint.post_mint.role') }}</th>
                                        <th class="py-2 text-right text-gray-600">{{ __('mint.post_mint.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.payment_breakdown.map(dist => `
                                                                                        <tr class="border-b border-gray-200">
                                                                                            <td class="py-2 font-medium text-gray-900">${dist.recipient}</td>
                                                                                            <td class="py-2 text-gray-700">${dist.role}</td>
                                                                                            <td class="py-2 font-semibold text-right text-gray-900">&euro; ${dist.amount_eur}</td>
                                                                                        </tr>
                                                                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                // Build blockchain data HTML
                const blockchainHtml = data.blockchain_data ? `
                    <div class="p-4 mb-6 border border-green-200 rounded-lg bg-green-50">
                        <h4 class="mb-3 text-sm font-semibold text-green-800">{{ __('mint.post_mint.blockchain_info') }}</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="font-medium text-green-700">{{ __('mint.post_mint.asa_id') }}:</dt>
                                <dd class="font-mono text-green-900">${data.blockchain_data.asa_id}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-green-700">{{ __('mint.post_mint.tx_id') }}:</dt>
                                <dd class="font-mono text-xs text-green-900 truncate" title="${data.blockchain_data.tx_id}">${data.blockchain_data.tx_id.substring(0, 16)}...</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-green-700">{{ __('mint.post_mint.minted_at') }}:</dt>
                                <dd class="text-green-900">${data.blockchain_data.minted_at}</dd>
                            </div>
                        </dl>
                        <a href="${data.blockchain_data.pera_explorer_url}" target="_blank" class="inline-flex items-center mt-3 text-sm font-medium text-green-700 transition-colors hover:text-green-900">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            {{ __('mint.post_mint.view_pera_explorer') }}
                        </a>
                    </div>
                ` : '';

                // Show success UI
                const successHtml = `
                    <div id="post-mint-success" class="p-6 bg-white rounded-lg shadow-sm">
                        <div class="mb-6 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 text-white bg-green-500 rounded-full">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="mb-2 text-2xl font-bold text-gray-900">{{ __('mint.post_mint.congratulations') }}</h2>
                            <p class="text-gray-600">{{ __('mint.post_mint.success_message') }}</p>
                        </div>

                        ${blockchainHtml}
                        ${paymentBreakdownHtml}

                        <div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50">
                            <h4 class="mb-3 text-sm font-semibold text-blue-800">{{ __('mint.post_mint.certificate_title') }}</h4>
                            <p class="mb-4 text-sm text-blue-700">{{ __('mint.post_mint.certificate_description') }}</p>

                            <!-- PDF Thumbnail -->
                            <div class="mb-4 overflow-hidden border-2 border-blue-300 rounded-lg">
                                <a href="${data.public_url}" target="_blank" class="block transition-opacity hover:opacity-80">
                                    <div class="flex items-center justify-center h-48 bg-gradient-to-br from-blue-50 to-blue-100">
                                        <div class="text-center">
                                            <svg class="w-20 h-20 mx-auto mb-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-sm font-medium text-blue-700">{{ __('mint.post_mint.certificate_blockchain') }}</p>
                                            <p class="text-xs text-blue-600">UUID: ${data.certificate_uuid.substring(0, 8)}...</p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="${data.certificate_url}" download class="flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ __('mint.post_mint.download_certificate') }}
                                </a>
                                <a href="${data.public_url}" target="_blank" class="flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-blue-700 transition-colors bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('mint.post_mint.view_certificate') }}
                                </a>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('egis.show', $egi->id) }}" class="flex-1 px-6 py-3 text-center text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                {{ __('mint.post_mint.view_egi') }}
                            </a>
                            <a href="${data.public_url}" class="flex-1 px-6 py-3 text-center text-blue-600 transition-colors border-2 border-blue-600 rounded-lg hover:bg-blue-50">
                                {{ __('mint.post_mint.view_certificate') }}
                            </a>
                        </div>
                    </div>
                `;

                console.log('[MINT DEBUG] 🔍 Looking for post-mint container...');

                // Insert in the dedicated post-mint container
                const postMintContainer = document.getElementById('post-mint-container');
                console.log('[MINT DEBUG] 📦 Post-mint container: ' + (postMintContainer ? 'FOUND ✅' : 'NOT FOUND ❌'));

                if (postMintContainer) {
                    console.log('[MINT DEBUG] ➕ Inserting success HTML into container...');
                    console.log('[MINT DEBUG] 📏 Success HTML length: ' + successHtml.length + ' chars');
                    postMintContainer.innerHTML = successHtml;
                    console.log('[MINT DEBUG] ✅ Success HTML inserted!');
                    localStorage.setItem('mint_debug_step', 'success_ui_inserted');
                } else {
                    console.error('[MINT DEBUG] ❌ Post-mint container #post-mint-container not found!');
                    localStorage.setItem('mint_debug_error', 'post_mint_container_not_found');
                }
            }

            /**
             * Show partial success (mint OK, certificate generation failed)
             */
            function showPostMintPartialSuccess(data) {
                // Remove loading
                document.getElementById('post-mint-loading')?.remove();

                // Show fallback UI
                const successHtml = `
                    <div id="post-mint-success" class="p-6 bg-white rounded-lg shadow-sm">
                        <div class="mb-6 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 text-white bg-yellow-500 rounded-full">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="mb-2 text-2xl font-bold text-gray-900">{{ __('mint.post_mint.partial_success_title') }}</h2>
                            <p class="text-gray-600">{{ __('mint.post_mint.partial_success_message') }}</p>
                        </div>

                        <div class="p-4 mb-6 border border-yellow-200 rounded-lg bg-yellow-50">
                            <p class="text-sm text-yellow-800">{{ __('mint.post_mint.certificate_generation_failed') }}</p>
                            <p class="mt-2 text-sm text-yellow-700">{{ __('mint.post_mint.contact_support') }}</p>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('egis.show', $egi->id) }}" class="flex-1 px-6 py-3 text-center text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                {{ __('mint.post_mint.view_egi') }}
                            </a>
                            <a href="{{ route('my-certificates') }}" class="flex-1 px-6 py-3 text-center text-blue-600 transition-colors border-2 border-blue-600 rounded-lg hover:bg-blue-50">
                                {{ __('mint.post_mint.my_certificates') }}
                            </a>
                        </div>
                    </div>
                `;

                // Insert in the third column container
                const thirdColumn = document.querySelector('.grid.grid-cols-1.gap-6.lg\\:grid-cols-3 > div:last-child');
                if (thirdColumn) {
                    thirdColumn.insertAdjacentHTML('beforeend', successHtml);
                }
            }
        </script>
    @endpush
</x-platform-layout>
