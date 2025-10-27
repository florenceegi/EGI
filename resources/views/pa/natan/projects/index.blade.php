<x-pa-layout :title="__('projects.page_title_index')">
    {{-- Breadcrumb --}}
    <x-slot:breadcrumb>
        <li><a href="{{ route('pa.dashboard') }}">{{ __('menu.pa_dashboard') }}</a></li>
        <li>{{ __('projects.projects') }}</li>
    </x-slot:breadcrumb>

    {{-- Page Title --}}
    <x-slot:pageTitle>
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-[#1B365D]">{{ __('projects.projects') }}</h1>
            <a href="{{ route('pa.projects.create') }}" class="btn btn-primary bg-[#D4A574] hover:bg-[#B89968] border-0 text-white">
                <i class="fas fa-plus mr-2"></i>
                {{ __('projects.create_new') }}
            </a>
        </div>
    </x-slot:pageTitle>

    {{-- Main Content --}}
    <div class="space-y-6">
        {{-- Search and Filters --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('pa.projects.index') }}" class="flex flex-col md:flex-row gap-4">
                {{-- Search Input --}}
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('projects.search_placeholder') }}
                    </label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('projects.search_by_name_desc') }}"
                        class="input input-bordered w-full"
                    >
                </div>

                {{-- Status Filter --}}
                <div class="md:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('projects.filter_status') }}
                    </label>
                    <select name="status" id="status" class="select select-bordered w-full">
                        <option value="">{{ __('projects.all_status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            {{ __('projects.status_active') }}
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            {{ __('projects.status_inactive') }}
                        </option>
                    </select>
                </div>

                {{-- Filter Buttons --}}
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary bg-[#1B365D] hover:bg-[#0F2342] border-0 text-white">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('projects.filter_apply') }}
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('pa.projects.index') }}" class="btn btn-outline">
                            <i class="fas fa-times mr-2"></i>
                            {{ __('projects.filter_clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Active Filters Display --}}
        @if(request('search') || request('status'))
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-600">{{ __('projects.active_filters') }}:</span>
                
                @if(request('search'))
                    <span class="badge badge-lg gap-2 bg-[#D4A574] text-white">
                        {{ __('projects.search') }}: {{ request('search') }}
                        <a href="{{ route('pa.projects.index', array_filter(['status' => request('status')])) }}" 
                           class="btn btn-ghost btn-xs text-white hover:bg-white/20">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('status'))
                    <span class="badge badge-lg gap-2 bg-[#1B365D] text-white">
                        {{ __('projects.status') }}: {{ __('projects.status_' . request('status')) }}
                        <a href="{{ route('pa.projects.index', array_filter(['search' => request('search')])) }}" 
                           class="btn btn-ghost btn-xs text-white hover:bg-white/20">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            </div>
        @endif

        {{-- Projects Grid --}}
        @if($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projects as $project)
                    <div class="card bg-white shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                        <div class="card-body">
                            {{-- Project Icon and Status Badge --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="avatar placeholder">
                                    <div class="bg-[{{ $project->color ?? '#1B365D' }}] text-white rounded-full w-16 h-16 flex items-center justify-center text-2xl">
                                        <i class="{{ $project->icon ?? 'fas fa-folder' }}"></i>
                                    </div>
                                </div>
                                
                                @if($project->is_active)
                                    <span class="badge badge-success gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('projects.status_active') }}
                                    </span>
                                @else
                                    <span class="badge badge-ghost gap-2">
                                        <i class="fas fa-pause-circle"></i>
                                        {{ __('projects.status_inactive') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Project Title --}}
                            <h3 class="card-title text-lg text-[#1B365D] mb-2">
                                <a href="{{ route('pa.projects.show', $project) }}" class="hover:text-[#D4A574] transition-colors">
                                    {{ $project->name }}
                                </a>
                            </h3>

                            {{-- Project Description --}}
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $project->description ?? __('projects.no_description') }}
                            </p>

                            {{-- Project Stats --}}
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-[#1B365D]">
                                        {{ $project->documents()->count() }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ __('projects.documents_count') }}
                                    </div>
                                </div>
                                <div class="text-center bg-gray-50 rounded-lg p-3">
                                    <div class="text-2xl font-bold text-[#2D5016]">
                                        {{ $project->chatMessages()->count() }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ __('projects.chats_count') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Project Actions --}}
                            <div class="card-actions justify-end gap-2">
                                <a href="{{ route('pa.projects.show', $project) }}" 
                                   class="btn btn-sm btn-primary bg-[#1B365D] hover:bg-[#0F2342] border-0 text-white">
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ __('projects.view_details') }}
                                </a>
                                <a href="{{ route('pa.projects.edit', $project) }}" 
                                   class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit mr-1"></i>
                                    {{ __('projects.edit') }}
                                </a>
                            </div>

                            {{-- Last Updated --}}
                            <div class="text-xs text-gray-500 mt-3 border-t pt-3">
                                <i class="far fa-clock mr-1"></i>
                                {{ __('projects.updated_at') }}: {{ $project->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $projects->links() }}
            </div>

        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="mb-6">
                    <i class="fas fa-folder-open text-6xl text-gray-300"></i>
                </div>
                
                @if(request('search') || request('status'))
                    {{-- No Results from Filters --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">
                        {{ __('projects.no_results_title') }}
                    </h3>
                    <p class="text-gray-600 mb-6">
                        {{ __('projects.no_results_message') }}
                    </p>
                    <a href="{{ route('pa.projects.index') }}" class="btn btn-primary bg-[#1B365D] hover:bg-[#0F2342] border-0 text-white">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('projects.clear_filters') }}
                    </a>
                @else
                    {{-- No Projects at All --}}
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">
                        {{ __('projects.no_projects_title') }}
                    </h3>
                    <p class="text-gray-600 mb-6">
                        {{ __('projects.no_projects_message') }}
                    </p>
                    <a href="{{ route('pa.projects.create') }}" class="btn btn-primary bg-[#D4A574] hover:bg-[#B89968] border-0 text-white btn-lg">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('projects.create_first_project') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Project Limits Info --}}
        <div class="alert alert-info bg-blue-50 border-blue-200">
            <i class="fas fa-info-circle text-blue-600"></i>
            <div>
                <div class="font-medium text-blue-900">{{ __('projects.limits_title') }}</div>
                <div class="text-sm text-blue-700">
                    {{ __('projects.limits_message', [
                        'current' => $projects->total(),
                        'max' => 20,
                        'remaining' => max(0, 20 - $projects->total())
                    ]) }}
                </div>
            </div>
        </div>
    </div>
</x-pa-layout>
