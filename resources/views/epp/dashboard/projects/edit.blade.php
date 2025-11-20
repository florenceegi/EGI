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
                    <form action="{{ route('epp.dashboard.projects.update', $project) }}" method="POST" class="space-y-6">
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
                            <label for="image" class="block text-sm font-medium text-gray-700">
                                {{ __('epp_dashboard.projects.form.image') }}
                            </label>
                            
                            @if ($project->hasMedia('project_images'))
                                <div class="mt-2 mb-4">
                                    <img src="{{ $project->getFirstMediaUrl('project_images', 'thumb') }}" 
                                         alt="{{ $project->name }}" 
                                         class="h-32 w-32 rounded-lg object-cover">
                                </div>
                            @endif

                            <input type="file" 
                                   name="image" 
                                   id="image" 
                                   accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('epp_dashboard.projects.form.image_help') }}
                            </p>
                        </div>

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

