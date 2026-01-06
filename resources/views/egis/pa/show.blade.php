{{--
/**
 * PA Heritage Detail View (Universal EGI Architecture)
 *
 * @package Resources\Views\Egis\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - PA Enterprise Architecture STEP 2.2)
 * @date 2025-10-04
 * @purpose Dettaglio bene culturale PA con CoA completo, firme, blockchain verification
 *
 * Architecture:
 * - Migrated from: resources/views/pa/heritage/show.blade.php
 * - Routed by: ViewService (egis.pa.show)
 * - Controller: EgiController@show (universal) or PAHeritageController@show (deprecated)
 * - Service: EgiService->show() with PA authorization
 *
 * Features:
 * - Header entity with PA info
 * - EGI card details (image, title, artist, dates)
 * - CoA display completo (ID, status, hash, emission date)
 * - CoA traits display (technique, materials, support)
 * - Digital signatures section with signers
 * - Blockchain verification badge
 * - Files download list (PDF CoA, images)
 * - Public QR code (placeholder FASE 3)
 *
 * Changes from v1.0:
 * - Moved to egis/pa/ structure for universal architecture
 * - Backward compatibility maintained via PAHeritageController routes
 * - Uses ViewService for role-based routing
 */
--}}

<x-pa-layout :title="$egi->title">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.heritage.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Patrimonio</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">{{ Str::limit($egi->title, 30) }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ $egi->title }}</x-slot:pageTitle>

    {{-- PA Entity Header --}}
    @if ($egi->collection && $egi->collection->owner)
        <x-pa.pa-entity-header :entity="$egi->collection->owner" :collection="$egi->collection" :showContact="false" :showStats="false" :compact="true"
            class="mb-8" />
    @endif

    {{-- Main Grid Layout: Image + Details --}}
    <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Left Column: Image Gallery --}}
        <div class="space-y-4">
            {{-- Main Image --}}
            <div class="aspect-square overflow-hidden rounded-xl bg-gray-100 shadow-lg">
                @php
                    $imageUrl =
                        $egi->main_image_url ?: $egi->original_image_url ?: asset('images/placeholder-heritage.jpg');
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $egi->title }}" class="h-full w-full object-cover"
                    loading="lazy" />
            </div>

            {{-- Image Info --}}
            <div class="rounded-lg bg-white p-4 text-sm text-gray-600 shadow">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">image</span>
                        {{ __('pa_heritage.certified_image') }}
                    </span>
                    @if ($egi->main_image_url)
                        <a href="{{ $egi->original_image_url }}" target="_blank"
                            class="flex items-center gap-1 text-[#D4A574] hover:underline">
                            <span class="material-symbols-outlined text-base">download</span>
                            {{ __('pa_heritage.high_resolution') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: EGI Details --}}
        <div class="space-y-6">
            {{-- Title + CoA Badge --}}
            <div>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h1 class="text-3xl font-bold text-[#1B365D]">{{ $egi->title }}</h1>
                    @if ($egi->coa)
                        <x-pa.pa-coa-badge :status="$egi->coa->status" size="lg" />
                    @else
                        <x-pa.pa-coa-badge status="none" size="lg" />
                    @endif
                </div>

                @if ($egi->artist)
                    <p class="flex items-center gap-2 text-xl text-gray-700">
                        <span class="material-symbols-outlined text-base">palette</span>
                        {{ $egi->artist }}
                    </p>
                @endif
            </div>

            {{-- Category Badge --}}
            @php
                $categoryColors = [
                    'artwork' => ['bg' => 'bg-[#8E44AD]/10', 'text' => 'text-[#8E44AD]', 'icon' => 'palette'],
                    'monument' => ['bg' => 'bg-[#1B365D]/10', 'text' => 'text-[#1B365D]', 'icon' => 'account_balance'],
                    'artifact' => ['bg' => 'bg-[#E67E22]/10', 'text' => 'text-[#E67E22]', 'icon' => 'inventory_2'],
                    'document' => ['bg' => 'bg-[#2D5016]/10', 'text' => 'text-[#2D5016]', 'icon' => 'description'],
                ];
                $category = $categoryColors[$egi->category] ?? $categoryColors['artwork'];
            @endphp
            <div
                class="{{ $category['bg'] }} {{ $category['text'] }} inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold">
                <span class="material-symbols-outlined text-base">{{ $category['icon'] }}</span>
                {{ ucfirst($egi->category) }}
            </div>

            {{-- Description --}}
            @if ($egi->description)
                <div class="rounded-lg bg-gray-50 p-6">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-600">
                        {{ __('pa_heritage.section_description') }}</h3>
                    <p class="leading-relaxed text-gray-700">{{ $egi->description }}</p>
                </div>
            @endif

            {{-- Quick Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                @if ($egi->created_at)
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                            {{ __('pa_heritage.cataloged_date') }}</p>
                        <p class="text-lg font-semibold text-[#1B365D]">{{ $egi->created_at->format('d/m/Y') }}</p>
                    </div>
                @endif

                @if ($egi->collection)
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                            {{ __('pa_heritage.section_collection') }}</p>
                        <p class="text-lg font-semibold text-[#1B365D]">{{ Str::limit($egi->collection->name, 25) }}
                        </p>
                    </div>
                @endif

                @if ($egi->is_published)
                    <div class="col-span-2 rounded-lg bg-white p-4 shadow">
                        <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                            {{ __('pa_heritage.publication_status') }}</p>
                        <p class="flex items-center gap-2 text-lg font-semibold text-[#2D5016]">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            {{ __('pa_heritage.published') }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                <x-pa.pa-action-button :label="__('pa_heritage.btn_back_to_list')" href="{{ route('pa.heritage.index') }}" icon="arrow_back"
                    variant="outline" size="md" class="flex-1" />
                @if ($egi->coa && $egi->coa->status === 'valid')
                    <x-pa.pa-action-button :label="__('pa_heritage.btn_download_coa')" href="{{ route('coa.pdf.download', $egi->coa->id) }}"
                        icon="download" variant="primary" size="md" target="_blank" class="flex-1" />
                @endif
            </div>
        </div>
    </div>

    {{-- CoA Section (if exists) --}}
    @if ($egi->coa)
        <div class="mb-8 rounded-xl bg-gradient-to-r from-[#1B365D] to-[#0F2342] p-8 text-white shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="flex items-center gap-3 text-2xl font-bold">
                    <span class="material-symbols-outlined text-3xl">verified</span>
                    {{ __('pa_heritage.coa_title') }}
                </h2>
                <x-pa.pa-coa-badge :status="$egi->coa->status" size="lg" />
            </div>

            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-lg bg-white/10 p-4">
                    <p class="mb-1 text-sm text-white/70">{{ __('pa_heritage.coa_id') }}</p>
                    <p class="font-mono text-lg font-semibold">{{ $egi->coa->id }}</p>
                </div>
                <div class="rounded-lg bg-white/10 p-4">
                    <p class="mb-1 text-sm text-white/70">{{ __('pa_heritage.coa_emission_date') }}</p>
                    <p class="text-lg font-semibold">{{ $egi->coa->issued_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="rounded-lg bg-white/10 p-4">
                    <p class="mb-1 text-sm text-white/70">{{ __('pa_heritage.coa_issuer') }}</p>
                    <p class="text-lg font-semibold">{{ ucfirst($egi->coa->issuer_type) }}</p>
                </div>
            </div>

            {{-- CoA Traits (if exists) --}}
            @if ($egi->coaTraits && $egi->coaTraits->isNotEmpty())
                <div class="mb-6 rounded-lg bg-white/5 p-6">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                        <span class="material-symbols-outlined">info</span>
                        {{ __('pa_heritage.coa_technical_features') }}
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        @foreach ($egi->coaTraits as $trait)
                            <div>
                                <p class="mb-1 text-sm text-white/70">{{ $trait->category }}</p>
                                <p class="font-semibold">{{ $trait->term }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Signatures --}}
            @if ($egi->coa->signatures && $egi->coa->signatures->isNotEmpty())
                <div class="mb-6 rounded-lg bg-white/5 p-6">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                        <span class="material-symbols-outlined">draw</span>
                        {{ __('pa_heritage.coa_digital_signatures') }}
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($egi->coa->signatures as $signature)
                            <div class="rounded-lg bg-white/10 p-4">
                                <div class="mb-2 flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold">
                                            {{ $signature->signer->name ?? __('pa_heritage.coa_signer') }}</p>
                                        <p class="text-sm text-white/70">{{ $signature->role }}</p>
                                    </div>
                                    <span class="material-symbols-outlined text-[#2D5016]">verified_user</span>
                                </div>
                                <p class="mt-2 text-xs text-white/60">{{ $signature->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Blockchain Verification --}}
            @if ($egi->coa->verification_hash)
                <div class="rounded-lg bg-white/5 p-6">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                        <span class="material-symbols-outlined">link</span>
                        {{ __('pa_heritage.coa_blockchain_verification') }}
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <p class="mb-1 text-sm text-white/70">{{ __('pa_heritage.coa_transaction_hash') }}</p>
                            <p class="rounded bg-white/10 px-4 py-2 font-mono text-sm">
                                {{ Str::limit($egi->coa->verification_hash, 60) }}</p>
                        </div>
                        <a href="#"
                            class="flex items-center gap-2 rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-[#1B365D] transition-colors hover:bg-[#C39463]">
                            <span class="material-symbols-outlined">open_in_new</span>
                            {{ __('pa_heritage.btn_verify') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- CoA Files --}}
            @if ($egi->coa->files && $egi->coa->files->isNotEmpty())
                <div class="mt-6">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                        <span class="material-symbols-outlined">folder_open</span>
                        {{ __('pa_heritage.coa_attached_files') }}
                    </h3>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach ($egi->coa->files as $file)
                            <a href="{{ Storage::url($file->path) }}" target="_blank"
                                class="group flex items-center justify-between rounded-lg bg-white/10 p-4 transition-colors hover:bg-white/20">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="material-symbols-outlined text-2xl">{{ $file->kind === 'pdf' ? 'picture_as_pdf' : 'image' }}</span>
                                    <div>
                                        <p class="font-semibold">
                                            {{ $file->kind === 'pdf' ? __('pa_heritage.file_pdf') : __('pa_heritage.coa_image_file') }}
                                        </p>
                                        <p class="text-sm text-white/70">{{ number_format($file->size / 1024, 2) }} KB
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="material-symbols-outlined transition-transform group-hover:translate-x-1">download</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- No CoA Section --}}
        <div class="mb-8 rounded-xl bg-gray-50 p-12 text-center shadow">
            <span class="material-symbols-outlined mb-4 block text-8xl text-gray-300">description_off</span>
            <h3 class="mb-3 text-2xl font-bold text-gray-700">{{ __('pa_heritage.no_coa_issued') }}</h3>
            <p class="mb-6 text-gray-600">{{ __('pa_heritage.no_coa_message') }}
            </p>
            <x-pa.pa-action-button :label="__('pa_heritage.btn_request_coa')" href="#" icon="add_circle" variant="primary"
                size="lg" />
        </div>
    @endif

    {{-- Public QR Code Section (Placeholder FASE 3) --}}
    <div class="rounded-xl bg-white p-8 text-center shadow-md">
        <span class="material-symbols-outlined mb-4 block text-6xl text-gray-300">qr_code_2</span>
        <h3 class="mb-2 text-xl font-bold text-[#1B365D]">{{ __('pa_heritage.qr_public_title') }}</h3>
        <p class="mb-4 text-gray-600">{{ __('pa_heritage.qr_public_message') }}</p>
        <x-pa.pa-action-button :label="__('pa_heritage.btn_generate_qr')" href="#" icon="qr_code_scanner" variant="outline"
            size="md" disabled />
    </div>

    {{-- OS3: Notify other tabs when new EGI is created (Vanilla JS, no Alpine/Livewire) --}}
    @if (session('success') && str_contains(session('success'), 'creato'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Notify other tabs that a new EGI was created
                    if (typeof EgiChannel !== 'undefined') {
                        EgiChannel.notifyCreated(
                            {{ $egi->id }},
                            '{{ addslashes($egi->title) }}'
                        );
                        console.log('[EGI Show] Notified creation of EGI #{{ $egi->id }}');
                    }
                });
            </script>
        @endpush
    @endif

</x-pa-layout>
