{{-- resources/views/epp/dashboard/projects/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('epp_dashboard.projects.edit_project') }}
            </h2>
            <a href="{{ route('epp.dashboard.projects') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900">
                ← {{ __('epp_dashboard.projects.back_to_projects') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <form action="{{ route('epp.dashboard.projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Project Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $project->name) }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Type -->
                        <div>
                            <label for="project_type" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.project_type') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="project_type" 
                                    id="project_type" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('project_type') border-red-500 @enderror">
                                <option value="ARF" {{ old('project_type', $project->project_type) == 'ARF' ? 'selected' : '' }}>
                                    🌳 {{ __('epp_projects.types.arf') }}
                                </option>
                                <option value="APR" {{ old('project_type', $project->project_type) == 'APR' ? 'selected' : '' }}>
                                    🌊 {{ __('epp_projects.types.apr') }}
                                </option>
                                <option value="BPE" {{ old('project_type', $project->project_type) == 'BPE' ? 'selected' : '' }}>
                                    🐝 {{ __('epp_projects.types.bpe') }}
                                </option>
                            </select>
                            @error('project_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.description') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="5" 
                                      required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('description') border-red-500 @enderror">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Value & Current Value -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="target_value" class="block text-sm font-medium text-gray-700">
                                    {{ __('epp_dashboard.projects.form.target_value') }}
                                </label>
                                <input type="number" 
                                       name="target_value" 
                                       id="target_value" 
                                       value="{{ old('target_value', $project->target_value) }}"
                                       min="0"
                                       step="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('target_value') border-red-500 @enderror">
                                @error('target_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="current_value" class="block text-sm font-medium text-gray-700">
                                    {{ __('epp_dashboard.projects.form.current_value') }}
                                </label>
                                <input type="number" 
                                       name="current_value" 
                                       id="current_value" 
                                       value="{{ old('current_value', $project->current_value) }}"
                                       min="0"
                                       step="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('current_value') border-red-500 @enderror">
                                @error('current_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('epp_dashboard.projects.form.current_value_help') }}
                                </p>
                            </div>
                        </div>

                        <!-- Target Date -->
                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.target_date') }}
                            </label>
                            <input type="date" 
                                   name="target_date" 
                                   id="target_date" 
                                   value="{{ old('target_date', $project->target_date ? $project->target_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('target_date') border-red-500 @enderror">
                            @error('target_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.status') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('status') border-red-500 @enderror">
                                <option value="planned" {{ old('status', $project->status) == 'planned' ? 'selected' : '' }}>
                                    {{ __('epp_projects.show.status_planned') }}
                                </option>
                                <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>
                                    {{ __('epp_projects.show.status_in_progress') }}
                                </option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>
                                    {{ __('epp_projects.show.status_completed') }}
                                </option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('epp_projects.show.status_cancelled') }}
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Evidence URL -->
                        <div>
                            <label for="evidence_url" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.evidence_url') }}
                            </label>
                            <input type="url" 
                                   name="evidence_url" 
                                   id="evidence_url" 
                                   value="{{ old('evidence_url', $project->evidence_url) }}"
                                   placeholder="https://..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('evidence_url') border-red-500 @enderror">
                            @error('evidence_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('epp_dashboard.projects.form.image') }}
                            </label>
                            
                            <div class="relative">
                                <!-- Drop Zone -->
                                <div id="dropZone" 
                                     class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-[#2D5016] transition-colors cursor-pointer bg-gray-50 hover:bg-green-50/30">
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           accept="image/jpeg,image/png,image/jpg,image/webp"
                                           class="hidden">
                                    
                                    <div id="uploadPrompt" class="{{ $project->hasMedia('project_images') ? 'hidden' : '' }}">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="mt-4">
                                            <span class="text-base font-semibold text-[#2D5016]">{{ __('epp_dashboard.projects.form.upload_click') }}</span>
                                            <span class="text-gray-600"> {{ __('epp_dashboard.projects.form.upload_or_drag') }}</span>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">
                                            JPG, PNG, WEBP {{ __('epp_dashboard.projects.form.upload_max_size') }}
                                        </p>
                                    </div>
                                    
                                    <!-- Preview (existing or new) -->
                                    <div id="imagePreview" class="{{ $project->hasMedia('project_images') ? '' : 'hidden' }}">
                                        <img id="previewImg" 
                                             src="{{ $project->hasMedia('project_images') ? $project->getFirstMediaUrl('project_images') : '' }}" 
                                             alt="{{ $project->name }}" 
                                             class="mx-auto max-h-64 rounded-lg shadow-lg">
                                        <div class="mt-4 space-x-2">
                                            <button type="button" 
                                                    id="changeImage"
                                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#2D5016] bg-white border border-[#2D5016] rounded-lg hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-[#2D5016]">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ __('epp_dashboard.projects.form.change_image') }}
                                            </button>
                                            <button type="button" 
                                                    id="removeImage"
                                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('epp_dashboard.projects.form.remove_image') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">
                                            {{ __('epp_dashboard.projects.form.image_help') }}
                                        </p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            {{ __('epp_dashboard.projects.form.image_save_info') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const dropZone = document.getElementById('dropZone');
                            const fileInput = document.getElementById('image');
                            const uploadPrompt = document.getElementById('uploadPrompt');
                            const imagePreview = document.getElementById('imagePreview');
                            const previewImg = document.getElementById('previewImg');
                            const removeBtn = document.getElementById('removeImage');
                            const changeBtn = document.getElementById('changeImage');

                            // Click to upload
                            dropZone.addEventListener('click', function(e) {
                                if (e.target !== removeBtn && !removeBtn.contains(e.target) && 
                                    e.target !== changeBtn && !changeBtn.contains(e.target)) {
                                    fileInput.click();
                                }
                            });

                            // Change image button
                            changeBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                fileInput.click();
                            });

                            // Drag & Drop
                            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                                dropZone.addEventListener(eventName, preventDefaults, false);
                            });

                            function preventDefaults(e) {
                                e.preventDefault();
                                e.stopPropagation();
                            }

                            ['dragenter', 'dragover'].forEach(eventName => {
                                dropZone.addEventListener(eventName, highlight, false);
                            });

                            ['dragleave', 'drop'].forEach(eventName => {
                                dropZone.addEventListener(eventName, unhighlight, false);
                            });

                            function highlight() {
                                dropZone.classList.add('border-[#2D5016]', 'bg-green-50/50');
                            }

                            function unhighlight() {
                                dropZone.classList.remove('border-[#2D5016]', 'bg-green-50/50');
                            }

                            dropZone.addEventListener('drop', handleDrop, false);

                            function handleDrop(e) {
                                const dt = e.dataTransfer;
                                const files = dt.files;
                                if (files.length > 0) {
                                    fileInput.files = files;
                                    handleFiles(files);
                                }
                            }

                            // File selection
                            fileInput.addEventListener('change', function() {
                                handleFiles(this.files);
                            });

                            function handleFiles(files) {
                                if (files.length === 0) return;
                                
                                const file = files[0];
                                
                                // Validate file type
                                if (!file.type.match('image.*')) {
                                    alert('{{ __("epp_dashboard.projects.form.error_file_type") }}');
                                    return;
                                }
                                
                                // Validate file size (2MB)
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('{{ __("epp_dashboard.projects.form.error_file_size") }}');
                                    return;
                                }
                                
                                // Show preview
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    previewImg.src = e.target.result;
                                    uploadPrompt.classList.add('hidden');
                                    imagePreview.classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);
                            }

                            // Remove image
                            removeBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                fileInput.value = '';
                                previewImg.src = '';
                                uploadPrompt.classList.remove('hidden');
                                imagePreview.classList.add('hidden');
                            });
                        });
                        </script>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between border-t pt-6">
                            <button type="button"
                                    onclick="if(confirm('{{ __('epp_dashboard.projects.form.delete_confirm') }}')) { document.getElementById('delete-form').submit(); }"
                                    class="rounded-lg border border-red-500 px-6 py-2 text-red-600 hover:bg-red-50">
                                {{ __('epp_dashboard.projects.form.delete_button') }}
                            </button>
                            
                            <div class="flex space-x-4">
                                <a href="{{ route('epp.dashboard.projects') }}" 
                                   class="rounded-lg border border-gray-300 px-6 py-2 text-gray-700 hover:bg-gray-50">
                                    {{ __('epp_dashboard.projects.form.cancel') }}
                                </a>
                                <button type="submit" 
                                        class="rounded-lg bg-green-700 px-6 py-2 text-white hover:bg-green-800">
                                    {{ __('epp_dashboard.projects.form.update_button') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (hidden) -->
                    <form id="delete-form" 
                          action="{{ route('epp.dashboard.projects.destroy', $project) }}" 
                          method="POST" 
                          class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

