<x-app-layout>
    {{--
    @Oracode View: GDPR Profile Images Management - FlorenceEGI Brand Compliant
    🎯 Purpose: Dedicated interface for profile image management
    🛡️ Privacy: GDPR compliant image handling with user control
    🎨 Brand: FlorenceEGI Renaissance design system
    🔧 Accessibility: Full ARIA support, semantic structure, keyboard navigation
    🌐 i18n: Complete localization support with profile.* translations

    @package FlorenceEGI
    @author Padmin D. Curtis (for Fabio Cherici)
    @version 2.0.0 - Separated from main profile view
    @date 2025-08-25
    @seo-purpose Provide dedicated profile image management interface
    @accessibility-trait Full ARIA landmark structure
    --}}

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl gdpr-title">
                    {{ __('profile.profile_images_management') }}
                </h1>
                <p class="mt-1 gdpr-subtitle">
                    {{ __('profile.profile_images_subtitle') }}
                </p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-8 h-8 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </x-slot>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 gdpr-alert gdpr-alert-success" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 gdpr-alert gdpr-alert-error" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Profile Banner Management --}}
    <div class="mb-8 space-y-8">
        {{-- Current Banner Card --}}
        <div class="p-6 gdpr-card rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg gdpr-title">
                    {{ __('profile.current_banner') }}
                </h3>
                <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            @php
                $currentBanner = auth()->user()->getFirstMedia('banner_images');
            @endphp

            @if ($currentBanner)
                <div class="mb-4">
                    <div class="relative w-full h-32 overflow-hidden border-2 rounded-lg border-oro-fiorentino">
                        <img src="{{ $currentBanner->getUrl('banner_mobile') }}"
                            alt="{{ __('profile.current_banner') }}"
                            class="object-cover w-full h-full">
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-sm gdpr-subtitle">{{ __('profile.currently_active') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $currentBanner->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <form action="{{ route('profile.delete-banner') }}" method="POST"
                            onsubmit="return confirm('{{ __('profile.confirm_delete_image') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1 text-xs text-white transition-colors bg-red-600 rounded hover:bg-red-700">
                                {{ __('profile.delete_banner') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <div class="flex items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">{{ __('profile.no_banner_set') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Banner Upload Form --}}
            <form action="{{ route('profile.upload-banner') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4" id="banner-upload-form">
                @csrf
                <div>
                    <label for="banner_image" class="block text-sm font-medium gdpr-label">
                        {{ __('profile.upload_banner') }}
                    </label>
                    <input type="file" name="banner_image" id="banner_image" accept="image/*" required
                        class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-oro-fiorentino file:text-black hover:file:bg-oro-fiorentino/80 transition-colors">
                    <p class="mt-1 text-xs text-gray-500">{{ __('profile.banner_help') }}</p>
                </div>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-black transition-colors border border-transparent rounded-md bg-oro-fiorentino hover:bg-oro-fiorentino/80 focus:outline-none focus:ring-2 focus:ring-oro-fiorentino focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    {{ __('profile.upload_banner') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Profile Images Content --}}
    <div class="space-y-8">
        {{-- Current Profile Image Card --}}
        <div class="p-6 gdpr-card rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg gdpr-title">
                    {{ __('profile.current_profile_image') }}
                </h3>
                <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>

            @if (auth()->user()->getCurrentProfileImage())
                <div class="flex items-center mb-4 space-x-4">
                    <img src="{{ auth()->user()->getCurrentProfileImage()->getUrl('thumb') }}"
                        alt="{{ __('profile.current_profile_image') }}"
                        class="object-cover w-20 h-20 border-2 rounded-full border-oro-fiorentino aspect-square">
                    <div>
                        <p class="text-sm gdpr-subtitle">{{ __('profile.currently_active') }}</p>
                        <p class="text-xs text-gray-500">
                            {{ auth()->user()->getCurrentProfileImage()->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center mb-4 space-x-4">
                    <img src="{{ auth()->user()->defaultProfilePhotoUrl() }}"
                        alt="{{ __('profile.default_profile_image') }}"
                        class="object-cover w-20 h-20 border-2 rounded-full border-oro-fiorentino aspect-square">
                    <div>
                        <p class="text-sm gdpr-subtitle">{{ __('profile.using_default_image') }}</p>
                        <p class="text-xs text-gray-500">{{ __('profile.no_custom_image_set') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Upload New Image Card --}}
        <div class="p-6 gdpr-card rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg gdpr-title">
                    {{ __('profile.upload_new_image') }}
                </h3>
                <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <form action="{{ route('profile.upload-image') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4" id="upload-form">
                @csrf

                {{-- Upload Area --}}
                <div class="p-8 text-center transition-colors border-2 border-gray-300 border-dashed rounded-lg hover:border-oro-fiorentino hover:bg-gray-50/50"
                    id="upload-area">
                    <input type="file" name="profile_image" id="profile_image"
                        accept="image/jpeg,image/png,image/webp" class="sr-only" multiple>
                    <label for="profile_image" class="block cursor-pointer">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-lg font-medium gdpr-subtitle">{{ __('profile.drag_drop_or_click') }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">{{ __('profile.supported_formats_with_size') }}
                        </p>
                    </label>
                </div>

                {{-- Preview Area (Hidden by default) --}}
                <div id="preview-area" class="hidden space-y-4">
                    <h4 class="text-sm font-medium gdpr-subtitle">{{ __('profile.selected_images') }}</h4>
                    <div id="preview-container" class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                        <!-- Previews will be inserted here -->
                    </div>
                </div>

                {{-- Upload Button (Hidden by default) --}}
                <button type="submit" id="upload-button"
                    class="items-center justify-center hidden w-full px-4 py-3 text-sm font-medium rounded-lg gdpr-btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    {{ __('profile.upload_selected_images') }}
                </button>
            </form>
        </div>

        {{-- All Profile Images Card --}}
        @if (auth()->user()->getAllProfileImages()->count() > 0)
            <div class="p-6 gdpr-card rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg gdpr-title">
                        {{ __('profile.all_profile_images') }}
                    </h3>
                    <span class="text-sm text-gray-500">{{ auth()->user()->getAllProfileImages()->count() }}
                        {{ __('profile.images') }}</span>
                </div>

                <div class="p-3 mb-4 text-sm text-blue-700 rounded-lg bg-blue-50">
                    <div class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-4 w-4 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">{{ __('profile.how_to_use_images') }}</p>
                            <p class="mt-1 text-blue-600">{{ __('profile.image_management_help') }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach (auth()->user()->getAllProfileImages() as $image)
                        <div class="relative w-24 group aspect-square">
                            <img src="{{ $image->getUrl('thumb') }}" alt="{{ __('profile.profile_image') }}"
                                class="object-cover w-full h-full rounded-lg">

                            @if (auth()->user()->getCurrentProfileImage() && auth()->user()->getCurrentProfileImage()->id === $image->id)
                                <div class="absolute p-1 text-white bg-green-500 rounded-full right-1 top-1"
                                    title="{{ __('profile.currently_active') }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif

                            <div
                                class="absolute inset-0 flex items-center justify-center transition-all duration-200 bg-black bg-opacity-0 rounded-lg group-hover:bg-opacity-50">
                                <div
                                    class="space-x-2 transition-opacity duration-200 opacity-0 group-hover:opacity-100">
                                    @if (!auth()->user()->getCurrentProfileImage() || auth()->user()->getCurrentProfileImage()->id !== $image->id)
                                        <form action="{{ route('profile.set-current-image') }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="media_id"
                                                value="{{ $image->id }}">
                                            <button type="submit"
                                                class="p-2 text-white transition-colors bg-blue-500 rounded-full hover:bg-blue-600"
                                                title="{{ __('profile.set_as_profile') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('profile.delete-image') }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="media_id" value="{{ $image->id }}">
                                        <button type="submit"
                                            class="p-2 text-white transition-colors bg-red-500 rounded-full hover:bg-red-600"
                                            onclick="return confirm('{{ __('profile.confirm_delete_image') }}')"
                                            title="{{ __('profile.delete_image') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
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

    {{-- JavaScript for Profile Image Management --}}
    @push('scripts')
        <script>
            window.appConfig = @json(config('app'));

            // GDPR specific configuration
            window.gdprConfig = {
                locale: '{{ app()->getLocale() }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    uploadImage: '{{ route('profile.upload-image') }}',
                    setCurrentImage: '{{ route('profile.set-current-image') }}',
                    deleteImage: '{{ route('profile.delete-image') }}'
                }
            };
        </script>

        {{-- Profile Image Upload with Preview --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('profile_image');
                const uploadArea = document.getElementById('upload-area');
                const previewArea = document.getElementById('preview-area');
                const previewContainer = document.getElementById('preview-container');
                const uploadButton = document.getElementById('upload-button');
                const uploadForm = document.getElementById('upload-form');

                let selectedFiles = [];

                // Handle file selection
                fileInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    selectedFiles = files;

                    if (files.length > 0) {
                        showPreviews(files);
                        showUploadButton();
                    } else {
                        hidePreviews();
                        hideUploadButton();
                    }
                });

                // Drag and drop functionality
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('border-oro-fiorentino', 'bg-gray-50/50');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('border-oro-fiorentino', 'bg-gray-50/50');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('border-oro-fiorentino', 'bg-gray-50/50');

                    const files = Array.from(e.dataTransfer.files).filter(file =>
                        file.type.startsWith('image/') && ['image/jpeg', 'image/png', 'image/webp']
                        .includes(file.type)
                    );

                    if (files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        selectedFiles = files;
                        showPreviews(files);
                        showUploadButton();
                    }
                });

                // Show previews
                function showPreviews(files) {
                    previewContainer.innerHTML = '';

                    files.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'relative group';
                            previewDiv.innerHTML = `
                                <img src="${e.target.result}" alt="Preview" class="object-cover w-24 h-24 rounded-lg aspect-square">
                                <div class="absolute inset-0 flex items-center justify-center transition-all duration-200 bg-black bg-opacity-0 rounded-lg group-hover:bg-opacity-50">
                                    <button type="button" class="p-2 text-white transition-opacity duration-200 bg-red-500 rounded-full opacity-0 remove-preview group-hover:opacity-100 hover:bg-red-600" data-index="${index}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2 text-xs text-gray-500 truncate">${file.name}</div>
                            `;
                            previewContainer.appendChild(previewDiv);
                        };
                        reader.readAsDataURL(file);
                    });

                    previewArea.classList.remove('hidden');
                }

                // Hide previews
                function hidePreviews() {
                    previewArea.classList.add('hidden');
                    previewContainer.innerHTML = '';
                }

                // Show upload button
                function showUploadButton() {
                    uploadButton.classList.remove('hidden');
                }

                // Hide upload button
                function hideUploadButton() {
                    uploadButton.classList.add('hidden');
                }

                // Remove preview
                previewContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-preview')) {
                        const index = parseInt(e.target.closest('.remove-preview').dataset.index);
                        selectedFiles.splice(index, 1);

                        if (selectedFiles.length > 0) {
                            showPreviews(selectedFiles);
                        } else {
                            hidePreviews();
                            hideUploadButton();
                            fileInput.value = '';
                        }
                    }
                });

                // Form submission
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (selectedFiles.length === 0) {
                        return;
                    }

                    // Show loading state
                    uploadButton.disabled = true;
                    uploadButton.innerHTML = `
                        <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('profile.uploading') }}...
                    `;

                    // Create new FormData and append files
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');

                    selectedFiles.forEach(file => {
                        formData.append('profile_image[]', file);
                    });

                    // Submit form with new data
                    fetch('{{ route('profile.upload-image') }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Upload failed');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Reload page to show new images
                                window.location.reload();
                            } else {
                                alert(data.message || '{{ __('profile.image_upload_failed') }}');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(error.message || '{{ __('profile.image_upload_failed') }}');
                        })
                        .finally(() => {
                            // Reset button state
                            uploadButton.disabled = false;
                            uploadButton.innerHTML = `
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            {{ __('profile.upload_selected_images') }}
                        `;
                        });
                });
            });
        </script>
    @endpush
</x-app-layout>
