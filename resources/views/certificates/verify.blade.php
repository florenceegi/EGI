{{-- resources/views/certificates/verify.blade.php --}}
<x-guest-layout :title="__('certificate.verify_page_title', ['uuid' => $certificate->certificate_uuid])" :metaDescription="__('certificate.verify_meta_description', ['uuid' => $certificate->certificate_uuid])">

    {{-- Schema.org nel head --}}
    <x-slot name="schemaMarkup">
        @php
            $certificateUrl = route('egi-certificates.show', $certificate->certificate_uuid);
            $verificationUrl = route('egi-certificates.verify', $certificate->certificate_uuid);
        @endphp
        <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CheckAction",
        "name": "{{ __('certificate.verify_page_title', ['uuid' => $certificate->certificate_uuid]) }}",
        "description": "{{ __('certificate.verify_meta_description', ['uuid' => $certificate->certificate_uuid]) }}",
        "url": "{{ $verificationUrl }}",
        "object": {
            "@type": "DigitalDocument",
            "name": "{{ __('certificate.page_title', ['uuid' => $certificate->certificate_uuid]) }}",
            "url": "{{ $certificateUrl }}"
        },
        "actionStatus": "{{ $isValid ? 'CompletedActionStatus' : 'FailedActionStatus' }}",
        "startTime": "{{ now()->toIso8601String() }}"
    }
    </script>
    </x-slot>

    {{-- Slot personalizzato per disabilitare la hero section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Contenuto principale --}}
    <x-slot name="slot">
        <div class="relative z-20 py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                            <h1 class="text-2xl font-bold text-white">
                                {{ __('certificate.verify_page_title', ['uuid' => $certificate->certificate_uuid]) }}
                            </h1>
                            <p class="mt-2 text-indigo-100">
                                {{ __('certificate.verification.title') }}
                            </p>
                        </div>

                        {{-- Risultati della verifica --}}
                        <div class="p-6">
                            <div class="space-y-6">
                                {{-- Validità firma --}}
                                <div
                                    class="@if ($isValid) bg-green-100 @else bg-red-100 @endif rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            @if ($isValid)
                                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <h3
                                                class="@if ($isValid) text-green-800 @else text-red-800 @endif text-sm font-medium">
                                                @if ($isValid)
                                                    {{ __('certificate.verification.valid') }}
                                                @else
                                                    {{ __('certificate.verification.invalid') }}
                                                @endif
                                            </h3>
                                            <div
                                                class="@if ($isValid) text-green-700 @else text-red-700 @endif mt-2 text-sm">
                                                <p>
                                                    @if ($isValid)
                                                        {{ __('certificate.verification.explanation_valid') }}
                                                    @else
                                                        {{ __('certificate.verification.explanation_invalid') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($isBlockchainCertificate)
                                    {{-- Blockchain Certificate: Show ASA ID and TX ID --}}
                                    @php
                                        $network = config('algorand.algorand.network', 'testnet');
                                        $explorerUrl = config(
                                            "algorand.algorand.{$network}.explorer_url",
                                            'https://testnet.explorer.perawallet.app',
                                        );
                                        $blockchain = $certificate->egiBlockchain;
                                    @endphp

                                    @if ($blockchain)
                                        {{-- ASA ID Verification --}}
                                        <div class="rounded-md bg-blue-100 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-blue-800">
                                                        {{ __('certificate.verification.blockchain_confirmed') }}
                                                    </h3>
                                                    <div class="mt-2 text-sm text-blue-700">
                                                        <p>ASA ID:
                                                            <a href="{{ $explorerUrl }}/asset/{{ $blockchain->asa_id }}"
                                                                target="_blank"
                                                                class="font-mono font-bold underline hover:text-blue-900">
                                                                {{ $blockchain->asa_id }}
                                                            </a>
                                                        </p>
                                                        <p class="mt-1 break-all text-xs">TX ID:
                                                            <a href="{{ $explorerUrl }}/tx/{{ $blockchain->blockchain_tx_id }}"
                                                                target="_blank"
                                                                class="font-mono underline hover:text-blue-900">
                                                                {{ $blockchain->blockchain_tx_id }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Minting Date --}}
                                        <div class="rounded-md bg-green-100 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-green-800">
                                                        {{ __('certificate.verification.minted_on') }}
                                                    </h3>
                                                    <div class="mt-2 text-sm text-green-700">
                                                        <p>{{ $blockchain->minted_at->format('d/m/Y H:i:s') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    {{-- Reservation Certificate: Show Priority and Availability --}}
                                    {{-- Priorità della prenotazione --}}
                                    <div
                                        class="@if ($isHighestPriority) bg-green-100 @else bg-yellow-100 @endif rounded-md p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                @if ($isHighestPriority)
                                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <h3
                                                    class="@if ($isHighestPriority) text-green-800 @else text-yellow-800 @endif text-sm font-medium">
                                                    @if ($isHighestPriority)
                                                        {{ __('certificate.verification.highest_priority') }}
                                                    @else
                                                        {{ __('certificate.verification.not_highest_priority') }}
                                                    @endif
                                                </h3>
                                                <div
                                                    class="@if ($isHighestPriority) text-green-700 @else text-yellow-700 @endif mt-2 text-sm">
                                                    <p>
                                                        @if (!$isHighestPriority)
                                                            {{ __('certificate.verification.explanation_priority') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Disponibilità dell'EGI --}}
                                    <div
                                        class="@if ($isEgiAvailable) bg-green-100 @else bg-yellow-100 @endif rounded-md p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                @if ($isEgiAvailable)
                                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <h3
                                                    class="@if ($isEgiAvailable) text-green-800 @else text-yellow-800 @endif text-sm font-medium">
                                                    @if ($isEgiAvailable)
                                                        {{ __('certificate.verification.egi_available') }}
                                                    @else
                                                        {{ __('certificate.verification.egi_not_available') }}
                                                    @endif
                                                </h3>
                                                <div
                                                    class="@if ($isEgiAvailable) text-green-700 @else text-yellow-700 @endif mt-2 text-sm">
                                                    <p>
                                                        @if (!$isEgiAvailable)
                                                            {{ __('certificate.verification.explanation_not_available') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Dettagli del certificato --}}
                            <div class="mt-8 border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('certificate.details.title') }}
                                </h3>

                                <dl class="mt-4 space-y-4">
                                    <div class="grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">
                                            {{ __('certificate.details.egi_title') }}</dt>
                                        <dd class="col-span-2 text-sm text-gray-900">
                                            {{ $certificate->egi->title ?? __('certificate.unknown_egi') }}</dd>
                                    </div>

                                    @if ($isBlockchainCertificate && $blockchain)
                                        {{-- Blockchain Certificate Details --}}
                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.certificate_type') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ __('certificate.types.blockchain') }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.buyer') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ $blockchain->buyer->name ?? __('certificate.unknown_buyer') }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.amount_paid') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ $blockchain->paid_currency }}
                                                {{ number_format($blockchain->paid_amount, 2) }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.minted_at') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ $blockchain->minted_at->format('d M Y H:i:s') }}</dd>
                                        </div>
                                    @else
                                        {{-- Reservation Certificate Details --}}
                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.certificate_type') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ __('certificate.types.reservation') }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.reservation_type') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ __('reservation.type.' . $certificate->reservation_type) }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.wallet_address') }}</dt>
                                            <dd class="col-span-2 break-all font-mono text-xs text-gray-900">
                                                {{ $certificate->wallet_address }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.offer_amount_fiat') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                €{{ number_format($certificate->offer_amount_fiat, 2) }}</dd>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">
                                                {{ __('certificate.details.created_at') }}</dt>
                                            <dd class="col-span-2 text-sm text-gray-900">
                                                {{ $certificate->created_at->format('d M Y H:i:s') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- Azioni --}}
                            <div class="mt-8 flex justify-between">
                                <a href="{{ route('egi-certificates.show', $certificate->certificate_uuid) }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    {{ __('certificate.actions.back_to_list') }}
                                </a>

                                <a href="{{ route('egis.show', $certificate->egi_id) }}"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('certificate.actions.view_egi') }}
                                </a>
                            </div>

                            {{-- Firma e ID --}}
                            <div class="mt-8 border-t border-gray-200 pt-4 text-center">
                                <p class="text-xs text-gray-500">{{ __('certificate.details.certificate_uuid') }}:
                                    {{ $certificate->certificate_uuid }}</p>
                                <p class="mt-1 break-all font-mono text-xs text-gray-500">
                                    {{ __('certificate.details.signature_hash') }}: {{ $certificate->signature_hash }}
                                </p>
                            </div>

                            {{-- QR Code di condivisione --}}
                            <div class="mt-8 border-t border-gray-200 pt-4">
                                <h3 class="mb-3 text-center text-sm font-medium text-gray-700">
                                    {{ __('certificate.actions.share') }}</h3>
                                <div class="flex justify-center">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verificationUrl) }}"
                                        alt="{{ __('certificate.qr_code_alt') }}" class="mx-auto h-32 w-32">
                                </div>
                                <p class="mt-2 text-center text-xs text-gray-500">
                                    {{ __('certificate.verification.share_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

</x-guest-layout>
