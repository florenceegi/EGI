{{-- resources/views/egis/show-epp.blade.php --}}
{{-- 🌿 EPP EGI View - Simplified Layout for Environmental Projects --}}
<x-guest-layout :title="$egi->title . ' | ' . $collection->collection_name" :metaDescription="Str::limit($egi->description, 155) ?? __('egi.meta_description_default', ['title' => $egi->title])">

    @php
        // Auth Context
        $isCreator = App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $egi->user_id;
        $isAuthenticated = App\Helpers\FegiAuth::check();
        $currentUserId = $isAuthenticated ? App\Helpers\FegiAuth::id() : null;
        
        // Permissions (Simplified for EPP view)
        $canManage = $canManage ?? false; // Passed from controller
        $canDeleteEgi = $canManage && $isCreator; // Basic logic
        $canUpdateEgi = $canManage;

        // Business Logic
        $reservationService = app('App\Services\ReservationService');
        $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);
        
        $displayPrice = $egi->price;
        $priceLabel = __('egi.current_price');
        $displayUser = null;

        if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
            $displayPrice = $highestPriorityReservation->offer_amount_fiat;
            if ($highestPriorityReservation->fiat_currency !== 'EUR') {
                $displayPrice = $highestPriorityReservation->amount_eur ?? $displayPrice;
            }
            $displayUser = $highestPriorityReservation->user;
            $priceLabel = $highestPriorityReservation->type === 'weak' ? __('egi.reservation.fegi_reservation') : __('egi.reservation.highest_bid');
        }

        $displayPrice = is_numeric($displayPrice) ? (float) $displayPrice : 0;
        $isForSale = $displayPrice && $displayPrice > 0 && !$egi->mint;
        $canBeReserved = !$egi->mint && ($egi->is_published) && $displayPrice && $displayPrice > 0 && !$isCreator;
        
        // Owner / Creator Info
        $ownerName = $egi->owner ? $egi->owner->name : ($collection->creator->name ?? 'EPP Project');
        $ownerAvatar = $egi->owner ? $egi->owner->profile_photo_url : ($collection->creator->profile_photo_url ?? null);
         if (empty($ownerAvatar)) {
            $ownerAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($ownerName) . '&color=FFFFFF&background=1B365D';
        }
        
        // Price Locking logic (kept for compatibility with included partials)
        $isPriceLocked = ($isCreator) && $highestPriorityReservation;
    @endphp

    <x-slot name="schemaMarkup">
        @include('egis.partials.schema-markup', compact('egi', 'collection', 'isCreator'))
    </x-slot>

    <x-slot name="noHero">true</x-slot>

    <x-slot name="slot">
        
        {{-- Header EPP --}}
        @if($collection->eppProject)
        <div class="bg-green-900/20 border-b border-green-500/20 py-2 text-center">
            <a href="{{ route('epp-projects.show', $collection->eppProject->id) }}" class="inline-flex items-center text-sm text-green-400 hover:text-green-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('epp_projects.back_to_project') }}: <span class="font-bold ml-1">{{ $collection->eppProject->name }}</span>
            </a>
        </div>
        @endif

        {{-- Gallery Layout --}}
        <div class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen">
            <div class="container px-0 py-0 mx-auto md:max-w-full lg:max-w-full xl:max-w-full 2xl:max-w-full">
                <div class="relative w-full">
                    <div id="egi-main-grid" class="grid grid-cols-1 lg:grid-cols-3" style="width: 100%; box-sizing: border-box;">
                        
                        {{-- Col 1: Artwork Area (Main content - Wider) --}}
                        <div class="relative p-2 lg:p-4 lg:col-span-2">
                             <div class="relative w-full max-w-full mx-auto">
                                <x-egi-collection-navigator :collectionEgis="$collectionEgis" :currentEgi="$egi" />
                                
                                {{-- Media Display (Image or PDF) --}}
                                @php
                                    // Determine media type and URL
                                    // Use original_image_url if available (highest quality), otherwise main_image_url
                                    $mediaUrl = $egi->original_image_url ?? $egi->main_image_url;
                                    $isPdf = false;
                                    
                                    // Logic to detect PDF:
                                    // 1. Check DB extension field if available
                                    // 2. Check URL extension
                                    if (!empty($egi->extension) && strtolower($egi->extension) === 'pdf') {
                                        $isPdf = true;
                                    } 
                                    elseif ($mediaUrl && str_ends_with(strtolower(parse_url($mediaUrl, PHP_URL_PATH)), '.pdf')) {
                                        $isPdf = true;
                                    }
                                @endphp

                                @if($isPdf)
                                    <div class="relative mx-auto w-full mb-6">
                                        {{-- PDF Viewer Container --}}
                                        <div class="aspect-[3/4] w-full bg-gray-800 rounded-lg overflow-hidden shadow-2xl border border-gray-700 relative">
                                            <object data="{{ $mediaUrl }}" type="application/pdf" class="w-full h-full absolute inset-0">
                                                <div class="flex flex-col items-center justify-center h-full p-6 text-center bg-gray-900">
                                                    <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" /></svg>
                                                    <p class="text-gray-300 mb-4">{{ __('egi.pdf_viewer_fallback') }}</p>
                                                    <a href="{{ $mediaUrl }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-md transition-colors font-medium">
                                                        {{ __('egi.download_pdf') }}
                                                    </a>
                                                </div>
                                            </object>
                                        </div>
                                        
                                        {{-- PDF Actions Bar --}}
                                        <div class="flex justify-end mt-2">
                                            <a href="{{ $mediaUrl }}" target="_blank" class="text-sm text-emerald-400 hover:text-emerald-300 flex items-center transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                {{ __('egi.open_full_pdf') }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    {{-- Standard Image Display --}}
                                    @include('egis.partials.artwork.main-image-display', compact('egi'))
                                @endif
                                
                                {{-- Floating Title Card --}}
                                @include('egis.partials.artwork.floating-title-card', compact('egi', 'collection', 'isCreator'))
                             </div>
                             
                             {{-- Description --}}
                             <div class="mt-6 max-w-4xl mx-auto px-4">
                                <h3 class="text-xl font-semibold text-white mb-2">{{ __('egi.fields.description') }}</h3>
                                <div class="prose prose-invert max-w-none">
                                    @include('egis.partials.sidebar.description-section', compact('egi'))
                                </div>
                             </div>
                        </div>

                        {{-- Col 2: Action Panel (Sidebar - Sticky) --}}
                        <div class="bg-gray-900/95 backdrop-blur-xl lg:block lg:min-h-screen border-l border-gray-800">
                            <div class="p-4 space-y-6 lg:sticky lg:top-0">
                                
                                {{-- Owner Info Compact --}}
                                <div class="flex items-center gap-3 pb-4 border-b border-gray-800">
                                    <img src="{{ $ownerAvatar }}" alt="{{ $ownerName }}" class="w-10 h-10 rounded-full border border-gray-600">
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('egi.ownership.current_owner') }}</p>
                                        <p class="text-sm font-bold text-white">{{ $ownerName }}</p>
                                    </div>
                                </div>

                                {{-- Price & Purchase Section - ESSENTIAL --}}
                                @include('egis.partials.sidebar.price-purchase-section', compact('egi', 'isForSale', 'displayPrice', 'priceLabel', 'displayUser', 'highestPriorityReservation', 'isCreator', 'canBeReserved'))
                                
                                {{-- Reservation History --}}
                                @include('egis.partials.sidebar.reservation-history-section', compact('egi'))
                                
                                {{-- CRUD Panel (Only for creator/manager) --}}
                                @if ($canUpdateEgi)
                                    <div class="pt-4 border-t border-gray-800">
                                        <h4 class="text-xs text-gray-500 uppercase mb-2">Management</h4>
                                        @include('egis.partials.sidebar.crud-panel', compact('egi', 'canUpdateEgi', 'canDeleteEgi', 'isPriceLocked', 'displayPrice', 'displayUser', 'highestPriorityReservation'))
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        {{-- Modals minimali --}}
        @include('egis.partials.modals.delete-confirmation-modal', compact('canDeleteEgi', 'egi'))
        
        {{-- Custom Styles for Enhanced Interactivity --}}
        @include('egis.partials.styles.egi-show-styles')
        
        {{-- JavaScript Zoom Implementation - Inlined from show.blade.php --}}
        <script>
            /**
             * @Oracode ImageZoom: EPP Version
             */
            class ImageZoom {
                constructor(triggerId) {
                    this.triggerId = triggerId;
                    this.maxRetries = 50;
                    this.retryCount = 0;
                    this.scale = 1; this.panX = 0; this.panY = 0;
                    this.isZoomOpen = false;
                    this.waitForElements();
                }

                waitForElements() {
                    this.trigger = document.getElementById(this.triggerId);
                    this.overlay = document.getElementById('zoom-overlay');
                    this.overlayImage = document.getElementById('zoom-overlay-image');
                    this.closeButton = document.getElementById('zoom-close');

                    if (this.trigger && this.overlay && this.overlayImage && this.closeButton) {
                        this.bindEvents();
                    } else if (this.retryCount++ < this.maxRetries) {
                        setTimeout(() => this.waitForElements(), 100);
                    }
                }

                bindEvents() {
                    this.trigger.addEventListener('click', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        this.open();
                    });
                    this.closeButton.addEventListener('click', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        this.close();
                    });
                    this.overlay.addEventListener('click', (e) => {
                        if (e.target === this.overlay) this.close();
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.isZoomOpen) this.close();
                    });
                }

                open() {
                    const src = this.trigger.dataset.zoomSrc || this.trigger.src;
                    if (!src) return;
                    this.overlayImage.src = src;
                    this.overlay.classList.remove('hidden');
                    this.overlay.style.display = 'flex';
                    this.isZoomOpen = true;
                    document.body.style.overflow = 'hidden';
                }

                close() {
                    this.overlay.classList.add('hidden');
                    this.overlay.style.display = 'none';
                    this.isZoomOpen = false;
                    document.body.style.overflow = '';
                }
            }

            function initializeZoom() {
                try { new ImageZoom('zoom-image-trigger'); } catch (e) { console.error(e); }
            }

            if (document.readyState !== 'loading') initializeZoom();
            else document.addEventListener('DOMContentLoaded', initializeZoom);
        </script>
        
        {{-- Zoom Overlay Structure --}}
        <div id="zoom-overlay" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black/90 backdrop-blur-sm sm:p-6 md:p-8">
            <div id="zoom-content" class="relative h-full max-h-[85vh] w-full max-w-[90vw]">
                <img id="zoom-overlay-image" src="" alt="" class="object-contain w-full h-full user-select-none touch-none" />
                <button id="zoom-close" aria-label="Close" class="absolute z-10 flex items-center justify-center text-2xl font-bold text-white rounded-full shadow-xl right-2 top-2 h-10 w-10 bg-black/70 hover:bg-black/90">×</button>
            </div>
        </div>

    </x-slot>
</x-guest-layout>

