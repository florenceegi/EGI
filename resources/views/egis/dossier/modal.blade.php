{{-- resources/views/egis/dossier/modal.blade.php --}}
{{-- Dossier Modal - Gallery with Images --}}
<div id="dossier-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/90 backdrop-blur-sm" role="dialog"
    aria-modal="true" aria-labelledby="dossier-title">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-6xl bg-gray-900 shadow-2xl rounded-xl">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-800">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-orange-500/20">
                        <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 id="dossier-title" class="text-2xl font-bold text-white">{{ __('egi.dossier.title') }}</h1>
                        <p class="text-gray-400" id="egi-title">{{ __('egi.dossier.loading') }}</p>
                    </div>
                </div>
                <button onclick="closeDossierModal()"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:bg-gray-800 hover:text-white"
                    aria-label="{{ __('egi.dossier.close') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="max-h-[70vh] overflow-y-auto p-6">
                {{-- Content will be loaded dynamically --}}
                <div id="dossier-content">
                    <div class="flex items-center justify-center py-8">
                        <div class="w-8 h-8 border-b-2 border-orange-400 rounded-full animate-spin"></div>
                        <span class="ml-3 text-gray-400">{{ __('egi.dossier.loading') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Template for Gallery Content (hidden, used by JS) --}}
<template id="dossier-gallery-template">
    <div class="space-y-6">
        {{-- Artwork Info --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <h3 class="mb-4 text-lg font-semibold text-white">{{ __('egi.dossier.artwork_info') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('egi.dossier.author') }}:</dt>
                        <dd class="font-medium text-white" data-field="author">-</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('egi.dossier.year') }}:</dt>
                        <dd class="font-medium text-white" data-field="year">-</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('egi.dossier.internal_id') }}:</dt>
                        <dd class="font-mono text-white" data-field="internal_id">-</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h3 class="mb-4 text-lg font-semibold text-white">{{ __('egi.dossier.dossier_info') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('egi.dossier.images_count') }}:</dt>
                        <dd class="font-medium text-white" data-field="images_count">-</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">{{ __('egi.dossier.type') }}:</dt>
                        <dd class="font-medium text-white">{{ __('egi.dossier.utility_gallery') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Gallery Grid --}}
        <div>
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('egi.dossier.gallery_title') }}</h3>
            <div id="images-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Images will be populated by JavaScript --}}
            </div>
        </div>
    </div>
</template>

{{-- Template for No Utility Content --}}
<template id="dossier-no-utility-template">
    <div class="py-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-orange-500/20">
            <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 class="mb-4 text-xl font-semibold text-white">{{ __('egi.dossier.no_utility_title') }}</h2>
        <p class="mb-6 text-gray-400">{{ __('egi.dossier.no_utility_message') }}</p>
        <p class="text-sm text-gray-500">{{ __('egi.dossier.no_utility_description') }}</p>
    </div>
</template>

{{-- Template for No Images Content --}}
<template id="dossier-no-images-template">
    <div class="py-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-blue-500/20">
            <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="mb-4 text-xl font-semibold text-white">{{ __('egi.dossier.no_images_title') }}</h2>
        <p class="mb-6 text-gray-400">{{ __('egi.dossier.no_images_message') }}</p>
        <p class="text-sm text-gray-500">{{ __('egi.dossier.no_images_description') }}</p>
    </div>
</template>

{{-- Image Viewer Modal (for full-size view) --}}
<div id="image-viewer-modal" class="fixed inset-0 hidden bg-black/95 backdrop-blur-sm" role="dialog"
    aria-modal="true" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen">
        <div class="relative w-full h-full">
            {{-- Close Button --}}
            <button onclick="closeImageViewer()"
                class="absolute z-30 p-2 text-white transition-colors rounded-full right-4 top-4 bg-black/50 hover:bg-black/70">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Zoom Controls Bar --}}
            <div class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-black/70 rounded-lg px-4 py-2 flex items-center space-x-4 z-30">
                <button id="zoom-out" class="text-white hover:text-blue-300 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                
                <div class="flex items-center space-x-2">
                    <span class="text-white text-sm">Zoom:</span>
                    <input type="range" id="zoom-slider" min="25" max="400" value="100" 
                           class="w-32 h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
                    <span id="zoom-level" class="text-white text-sm min-w-[3rem]">100%</span>
                </div>
                
                <button id="zoom-in" class="text-white hover:text-blue-300 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>
                
                <button id="zoom-reset" class="text-white hover:text-blue-300 p-1 border border-gray-600 rounded px-2 py-1 text-xs transition-colors">
                    Reset
                </button>
                
                <button id="zoom-fit" class="text-white hover:text-blue-300 p-1 border border-gray-600 rounded px-2 py-1 text-xs transition-colors">
                    Fit
                </button>
            </div>

            {{-- Image Container with Pan and Zoom --}}
            <div id="image-container" class="w-full h-full overflow-hidden select-none flex items-center justify-center">
                <img id="viewer-image" src="" alt=""
                    class="object-contain max-w-full max-h-full transition-transform duration-200 ease-out cursor-move"
                    style="transform-origin: center center; user-select: none; -webkit-user-drag: none;">
            </div>

            {{-- Image Info --}}
            <div class="absolute p-4 text-white rounded-lg bottom-4 left-4 right-4 bg-black/70 z-30">
                <h3 id="viewer-title" class="font-medium"></h3>
                <p id="viewer-description" class="mt-1 text-sm text-gray-300"></p>
            </div>

            {{-- Navigation (if multiple images) --}}
            <button id="prev-image" onclick="prevImage()"
                class="absolute hidden p-3 text-white transition-colors transform -translate-y-1/2 rounded-full left-4 top-1/2 bg-black/50 hover:bg-black/70 z-30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="next-image" onclick="nextImage()"
                class="absolute hidden p-3 text-white transition-colors transform -translate-y-1/2 rounded-full right-4 top-1/2 bg-black/50 hover:bg-black/70 z-30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Zoom Help Text --}}
            <div class="absolute bottom-4 left-4 bg-black/70 rounded px-3 py-1 text-white text-sm z-30">
                <span>{{ __('egi.dossier.zoom_help') }}</span>
            </div>
        </div>
    </div>
</div>
