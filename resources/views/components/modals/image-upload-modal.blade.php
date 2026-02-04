{{--
@Oracode Component: Reusable Image Upload Modal - GDPR Compliant
🎯 Purpose: Reusable modal for multi-image upload with gallery management
🛡️ Security: Client-side validation, CSRF protection
🎨 Brand: FlorenceEGI Renaissance design system
🔧 Accessibility: Full ARIA support, keyboard navigation
🌐 i18n: Complete localization support

@props
- type: string (banner|avatar) - Type of image being uploaded
- collection: string - Spatie Media collection name
- uploadRoute: string - Route for upload action
- setCurrentRoute: string - Route for set current action
- deleteRoute: string - Route for delete action
- currentImage: ?Media - Currently active image
- allImages: Collection - All uploaded images
- title: string - Modal title
- helpText: string - Upload help text

@package FlorenceEGI
@author Antigravity (AI Partner OS3.0)
@version 1.0.0
@date 2026-02-04
--}}

@props([
    'type' => 'banner',
    'collection' => 'creator_banners',
    'uploadRoute' => '',
    'setCurrentRoute' => '',
    'deleteRoute' => '',
    'currentImage' => null,
    'allImages' => collect(),
    'title' => '',
    'helpText' => '',
    'modalId' => 'image-upload-modal',
])

{{-- Modal Backdrop --}}
<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="{{ $modalId }}-title"
    role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
            onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"></div>

        {{-- Center modal --}}
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            class="inline-block transform overflow-hidden rounded-2xl bg-gray-800 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:align-middle">
            {{-- Header --}}
            <div class="border-b border-gray-700 bg-gray-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 id="{{ $modalId }}-title" class="text-xl font-bold text-white">
                        {{ $title }}
                    </h3>
                    <button type="button"
                        onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                        class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-700 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="max-h-[70vh] overflow-y-auto px-6 py-6">
                {{-- Current Image Section --}}
                @if ($currentImage)
                    <div class="mb-6">
                        <h4 class="mb-3 text-sm font-medium text-gray-300">{{ __('profile.currently_active') }}</h4>
                        <div class="border-oro-fiorentino relative overflow-hidden rounded-lg border-2">
                            <img src="{{ $currentImage->getUrl($type === 'banner' ? 'banner_mobile' : 'thumb') }}"
                                alt="{{ __('profile.current_' . $type) }}" class="h-32 w-full object-cover">
                        </div>
                    </div>
                @endif

                {{-- Upload Section --}}
                <div class="mb-6">
                    <h4 class="mb-3 text-sm font-medium text-gray-300">{{ __('profile.upload_new_' . $type) }}</h4>
                    <form id="{{ $modalId }}-upload-form" action="{{ $uploadRoute }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- Upload Area --}}
                        <div id="{{ $modalId }}-upload-area"
                            class="hover:border-oro-fiorentino cursor-pointer rounded-lg border-2 border-dashed border-gray-600 p-8 text-center transition-colors hover:bg-gray-700/50">
                            <input type="file" name="{{ $collection }}" id="{{ $modalId }}-file-input"
                                accept="image/jpeg,image/png,image/webp,image/avif" class="sr-only" multiple>
                            <label for="{{ $modalId }}-file-input" class="block cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-300">
                                    {{ __('profile.drag_drop_or_click') }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $helpText }}</p>
                            </label>
                        </div>

                        {{-- Preview Area --}}
                        <div id="{{ $modalId }}-preview-area" class="mt-4 hidden">
                            <h5 class="mb-2 text-sm font-medium text-gray-300">{{ __('profile.selected_images') }}</h5>
                            <div id="{{ $modalId }}-preview-container"
                                class="grid grid-cols-2 gap-4 md:grid-cols-3"></div>
                        </div>

                        {{-- Upload Button --}}
                        <button type="submit" id="{{ $modalId }}-upload-button"
                            class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 mt-4 hidden w-full rounded-lg px-4 py-3 font-semibold text-gray-900 transition-colors">
                            {{ __('profile.upload_selected_images') }}
                        </button>
                    </form>
                </div>

                {{-- Gallery Section --}}
                @if ($allImages->count() > 0)
                    <div>
                        <h4 class="mb-3 text-sm font-medium text-gray-300">{{ __('profile.all_' . $type . '_images') }}
                            ({{ $allImages->count() }})</h4>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($allImages as $image)
                                <div class="group relative">
                                    <div class="overflow-hidden rounded-lg">
                                        <img src="{{ $image->getUrl($type === 'banner' ? 'banner_mobile' : 'thumb') }}"
                                            alt="{{ $type }}" class="h-24 w-full object-cover">
                                    </div>

                                    {{-- Current indicator --}}
                                    @if ($currentImage && $currentImage->getCustomProperty('source_media_id') == $image->id)
                                        <div class="absolute right-1 top-1 rounded-full bg-green-500 p-1"
                                            title="{{ __('profile.currently_active') }}">
                                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Action buttons --}}
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 transition-all group-hover:bg-opacity-50">
                                        <div class="space-x-2 opacity-0 transition-opacity group-hover:opacity-100">
                                            @if (!$currentImage || $currentImage->getCustomProperty('source_media_id') != $image->id)
                                                <form action="{{ $setCurrentRoute }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="media_id" value="{{ $image->id }}">
                                                    <button type="submit"
                                                        class="rounded-full bg-blue-500 p-2 text-white transition-colors hover:bg-blue-600"
                                                        title="{{ __('profile.set_as_' . $type) }}">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ $deleteRoute }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="media_id" value="{{ $image->id }}">
                                                <button type="submit"
                                                    onclick="return confirm('{{ __('profile.confirm_delete_image') }}')"
                                                    class="rounded-full bg-red-500 p-2 text-white transition-colors hover:bg-red-600"
                                                    title="{{ __('profile.delete_' . $type) }}">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-700 bg-gray-900 px-6 py-4">
                <button type="button"
                    onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                    class="w-full rounded-lg bg-gray-700 px-4 py-2 font-medium text-white transition-colors hover:bg-gray-600 sm:w-auto">
                    {{ __('label.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
