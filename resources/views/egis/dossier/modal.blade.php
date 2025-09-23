{{-- resources/views/egis/dossier/modal.blade.php --}}
{{-- Dossier Modal - Gallery with Images --}}
<div id="dossier-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/90 backdrop-blur-sm" role="dialog"
    aria-modal="true" aria-labelledby="dossier-title">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-6xl rounded-xl bg-gray-900 shadow-2xl">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-gray-800 p-6">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-orange-500/20 p-3">
                        <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                    aria-label="{{ __('egi.dossier.close') }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-orange-400"></div>
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
        <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-orange-500/20">
            <svg class="h-10 w-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-500/20">
            <svg class="h-10 w-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
<div id="image-viewer-modal" class="fixed inset-0 hidden bg-black/95 backdrop-blur-sm" role="dialog" aria-modal="true"
    style="z-index: 10000;">
    <div class="flex min-h-screen items-center justify-center">
        <div class="relative h-full w-full">
            {{-- Close Button --}}
            <button onclick="closeImageViewer()"
                class="absolute right-4 top-4 z-30 rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Zoom Controls Bar --}}
            <div
                class="absolute left-1/2 top-4 z-30 flex -translate-x-1/2 transform items-center space-x-4 rounded-lg bg-black/70 px-4 py-2">
                <button id="zoom-out" class="p-1 text-white transition-colors hover:text-blue-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-white">Zoom:</span>
                    <input type="range" id="zoom-slider" min="25" max="400" value="100"
                        class="h-2 w-32 cursor-pointer appearance-none rounded-lg bg-gray-700">
                    <span id="zoom-level" class="min-w-[3rem] text-sm text-white">100%</span>
                </div>

                <button id="zoom-in" class="p-1 text-white transition-colors hover:text-blue-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>

                <button id="zoom-reset"
                    class="rounded border border-gray-600 p-1 px-2 py-1 text-xs text-white transition-colors hover:text-blue-300">
                    Reset
                </button>

                <button id="zoom-fit"
                    class="rounded border border-gray-600 p-1 px-2 py-1 text-xs text-white transition-colors hover:text-blue-300">
                    Fit
                </button>
            </div>

            {{-- Image Container with Pan and Zoom --}}
            <div id="image-container"
                class="flex h-full w-full select-none items-center justify-center overflow-hidden">
                <img id="viewer-image" src="" alt=""
                    class="max-h-full max-w-full cursor-move object-contain transition-transform duration-200 ease-out"
                    style="transform-origin: center center; user-select: none; -webkit-user-drag: none;">
            </div>

            {{-- Image Info --}}
            <div class="absolute bottom-4 left-4 right-4 z-30 rounded-lg bg-black/70 p-4 text-white">
                <h3 id="viewer-title" class="font-medium"></h3>
                <p id="viewer-description" class="mt-1 text-sm text-gray-300"></p>
            </div>

            {{-- Navigation (if multiple images) --}}
            <button id="prev-image" onclick="prevImage()"
                class="absolute left-4 top-1/2 z-30 hidden -translate-y-1/2 transform rounded-full bg-black/50 p-3 text-white transition-colors hover:bg-black/70">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="next-image" onclick="nextImage()"
                class="absolute right-4 top-1/2 z-30 hidden -translate-y-1/2 transform rounded-full bg-black/50 p-3 text-white transition-colors hover:bg-black/70">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Zoom Help Text --}}
            <div class="absolute bottom-4 left-4 z-30 rounded bg-black/70 px-3 py-1 text-sm text-white">
                <span>{{ __('egi.dossier.zoom_help') }}</span>
            </div>
        </div>
    </div>
</div>
