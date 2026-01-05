<x-guest-layout :title="$title" :metaDescription="$metaDescription">
    <x-slot name="noHero">true</x-slot>

    <x-slot name="slot">
        <div class="relative z-20 min-h-screen bg-gray-50 pb-12">

            {{-- HEADER SECTION: GOLD THEME --}}
            <div class="relative border-b-4 border-[#FFC107] bg-gradient-to-b from-[#FFF8E1] to-[#FFECB3] shadow-md">
                <div class="container mx-auto px-4 py-10 sm:px-6 lg:px-8">
                    <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                        {{-- Title & Subtitle --}}
                        <div class="text-center md:text-left">
                            <h1
                                class="text-3xl font-extrabold uppercase tracking-widest text-[#3E2723] drop-shadow-sm md:text-4xl">
                                {{ __('mint.commodity.types.' . $commodityData['type_slug']) }}
                            </h1>
                            <p
                                class="mt-1 font-serif text-sm font-semibold italic tracking-wide text-[#6D4C41] md:text-base">
                                {{ __('mint.coa.title') ?? 'Certificate of Authenticity' }}
                            </p>
                        </div>

                        {{-- Serial Number Badge --}}
                        <div
                            class="rotate-1 transform rounded-lg border-2 border-[#FFC107] bg-white/90 px-6 py-3 shadow-inner backdrop-blur-sm transition-transform duration-300 hover:rotate-0">
                            <span
                                class="mb-1 block text-center text-xs font-bold uppercase tracking-wider text-[#8D6E63]">
                                {{ __('mint.commodity.serial_number') }}
                            </span>
                            <span class="block font-mono text-2xl font-bold text-[#3E2723]">
                                {{ $commodityData['serial_number'] ? $commodityData['serial_number'] : '#' . $certificate->egi_id }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Decorative shine effect --}}
                <div
                    class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white to-transparent opacity-50">
                </div>
            </div>

            <div class="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                {{-- Success Alert --}}
                @if (session('success'))
                    <div class="mb-8 rounded-lg border-l-4 border-green-500 bg-green-50 p-4 shadow-sm" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ __('mint.success.minted') ?? 'Minted Successfully' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">

                    {{-- LEFT COLUMN: PHYSICAL SPECS --}}
                    <div class="space-y-8">
                        <div class="overflow-hidden rounded-xl border border-[#FFD54F] bg-[#FFFDE7] shadow-lg">
                            <div class="flex items-center border-b border-[#FFE082] bg-[#FFECB3]/50 px-6 py-4">
                                <span class="mr-3 h-8 w-2 rounded-full bg-[#FFC107]"></span>
                                <h2 class="text-xl font-bold text-[#4E342E]">
                                    {{ __('mint.commodity.section_details') }}
                                </h2>
                            </div>

                            <div class="p-6 md:p-8">
                                <div class="grid grid-cols-2 gap-6">
                                    {{-- Weight Card --}}
                                    <div
                                        class="transform rounded-xl border border-[#FFE082] bg-white p-5 text-center shadow-sm transition-transform duration-200 hover:scale-105">
                                        <div class="mb-2 text-xs font-bold uppercase tracking-wider text-[#8D6E63]">
                                            {{ __('mint.commodity.weight') }}
                                        </div>
                                        <div class="text-3xl font-extrabold text-[#3E2723]">
                                            {{ $commodityData['weight'] }}
                                            <span
                                                class="ml-1 text-lg font-medium text-[#5D4037]">{{ $commodityData['unit'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Purity Card --}}
                                    <div
                                        class="transform rounded-xl border border-[#FFE082] bg-white p-5 text-center shadow-sm transition-transform duration-200 hover:scale-105">
                                        <div class="mb-2 text-xs font-bold uppercase tracking-wider text-[#8D6E63]">
                                            {{ __('mint.commodity.purity') }}
                                        </div>
                                        <div class="text-3xl font-extrabold text-[#3E2723]">
                                            {{ $commodityData['purity'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 space-y-4">
                                    <div class="flex justify-between border-b border-dashed border-[#FFE082] pb-2">
                                        <span class="font-medium text-[#6D4C41]">{{ __('mint.utility.title') }}</span>
                                        <span class="font-bold text-[#3E2723]">{{ $certificate->egi->title }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-dashed border-[#FFE082] pb-2">
                                        <span
                                            class="font-medium text-[#6D4C41]">{{ __('mint.post_mint.minted_at') }}</span>
                                        <span
                                            class="font-mono font-bold text-[#3E2723]">{{ $certificate->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-dashed border-[#FFE082] pb-2">
                                        <span
                                            class="font-medium text-[#6D4C41]">{{ __('mint.commodity.value_at_mint') }}</span>
                                        <span class="font-bold text-[#3E2723]">€
                                            {{ number_format($certificate->purchase_amount ?? $certificate->offer_amount_fiat, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- QR Code Section --}}
                        <div
                            class="flex flex-col items-center rounded-xl border-2 border-dashed border-gray-300 bg-white p-6 text-center">
                            <h3 class="mb-4 text-sm font-medium uppercase tracking-wide text-gray-500">Blockchain
                                Verification</h3>
                            <div class="mb-2 rounded border bg-white p-2 shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('egi-certificates.verify', $certificate->certificate_uuid)) }}"
                                    alt="Verify Certificate" class="h-32 w-32">
                            </div>
                            <p class="mt-2 max-w-xs text-xs text-gray-400">
                                Scan to verify the authenticity of this digital asset on the Algorand Blockchain.
                            </p>
                            @if ($certificate->hasPdf())
                                <a href="{{ route('egi-certificates.download', $certificate->certificate_uuid) }}"
                                    class="mt-4 inline-flex items-center rounded-full border border-transparent bg-[#FFB300] px-6 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#FFA000] focus:outline-none focus:ring-2 focus:ring-[#FFC107] focus:ring-offset-2">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ __('mint.post_mint.download_certificate') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: BLOCKCHAIN & OWNER --}}
                    <div class="space-y-8">
                        {{-- Owner Badge --}}
                        <div class="flex items-start rounded-r-xl border-l-4 border-indigo-500 bg-white p-6 shadow-md">
                            <div class="flex-shrink-0">
                                <div class="rounded-full bg-indigo-100 p-2">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">
                                    {{ __('mint.post_mint.current_owner') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Officially registered owner</p>
                                <div class="mt-2 text-xl font-bold text-indigo-700">{{ $certificate->buyer_name }}
                                </div>
                            </div>
                        </div>

                        {{-- Blockchain Data --}}
                        <div
                            class="overflow-hidden rounded-xl border border-[#37474F] bg-[#263238] text-[#ECEFF1] shadow-xl">
                            <div class="flex items-center justify-between bg-[#37474F] px-6 py-4">
                                <div class="flex items-center">
                                    <img src="https://styles.redditmedia.com/t5_38l8r/styles/communityIcon_4w946kmo88l61.png"
                                        class="mr-3 h-6 w-6" alt="Algo"
                                        style="filter: grayscale(100%) brightness(200%);">
                                    <h3 class="text-lg font-bold text-[#FFC107]">Algorand Blockchain</h3>
                                </div>
                                <span
                                    class="rounded border border-[#546E7A] bg-[#263238] px-2 py-1 text-xs text-[#B0BEC5]">{{ config('algorand.algorand.network', 'testnet') }}</span>
                            </div>

                            <div class="space-y-6 p-6">
                                <div>
                                    <div class="mb-1 text-xs font-bold uppercase tracking-wider text-[#90A4AE]">
                                        {{ __('mint.post_mint.asa_id') }}</div>
                                    <div class="font-mono text-2xl tracking-tight text-[#FFffff]">
                                        <a href="https://explorer.perawallet.app/asset/{{ $certificate->egiBlockchain->asa_id ?? '' }}"
                                            target="_blank"
                                            class="border-b border-dashed border-[#546E7A] pb-1 transition-colors hover:text-[#FFC107]">
                                            {{ $certificate->egiBlockchain->asa_id ?? 'N/A' }}
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-1 text-xs font-bold uppercase tracking-wider text-[#90A4AE]">
                                        {{ __('mint.post_mint.tx_id') }}</div>
                                    <div class="rounded border border-[#37474F] bg-[#1C2529] p-3">
                                        <a href="https://explorer.perawallet.app/tx/{{ $certificate->egiBlockchain->blockchain_tx_id ?? '' }}"
                                            target="_blank"
                                            class="break-all font-mono text-xs text-[#80CBC4] transition-colors hover:text-white">
                                            {{ $certificate->egiBlockchain->blockchain_tx_id ?? 'Pending...' }}
                                            <svg class="ml-1 inline h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 border-t border-[#37474F] pt-4">
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase text-[#90A4AE]">UUID</div>
                                        <div class="break-all font-mono text-xs text-[#B0BEC5]">
                                            {{ $certificate->certificate_uuid }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('egis.show', $certificate->egi_id) }}"
                                class="text-sm text-gray-500 underline hover:text-gray-900">
                                {{ __('mint.post_mint.view_egi') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-guest-layout>
