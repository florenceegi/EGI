{{-- resources/views/epp/dashboard/projects/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('epp_dashboard.projects.page_title') }}
            </h2>
            <a href="{{ route('epp.dashboard.index') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900">
                ← {{ __('epp_dashboard.projects.back_to_dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ __('epp_dashboard.projects.your_projects') }}
                    </h3>
                    <p class="text-gray-600">
                        {{ __('epp_dashboard.projects.subtitle', ['count' => $projects->total()]) }}
                    </p>
                </div>
                <a href="{{ route('epp.dashboard.projects.create') }}" 
                   class="inline-flex items-center rounded-lg bg-green-700 px-4 py-2 text-white hover:bg-green-800">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('epp_dashboard.projects.create_project') }}
                </a>
            </div>

            <!-- Projects Grid -->
            @if($projects->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($projects as $project)
                        <div class="overflow-hidden rounded-lg bg-white shadow transition-all hover:shadow-xl">
                            <!-- Project Header -->
                            <div class="border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50 p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <span class="mr-3 text-4xl">
                                            @if($project->project_type === 'ARF')
                                                🌳
                                            @elseif($project->project_type === 'APR')
                                                🌊
                                            @elseif($project->project_type === 'BPE')
                                                🐝
                                            @endif
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $project->name }}</h4>
                                            <p class="text-xs text-gray-600">
                                                {{ __('epp_projects.types.' . strtolower($project->project_type)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium
                                        {{ $project->status === 'in_progress' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $project->status === 'planned' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $project->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ __('epp_projects.show.status_' . $project->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Project Body -->
                            <div class="p-6">
                                <!-- Description -->
                                <p class="mb-4 text-sm text-gray-700 line-clamp-3">
                                    {{ $project->description }}
                                </p>

                                <!-- Progress -->
                                @if($project->target_value && $project->target_value > 0)
                                <div class="mb-4">
                                    <div class="mb-1 flex justify-between text-xs">
                                        <span class="text-gray-600">{{ __('epp_dashboard.projects.progress') }}</span>
                                        <span class="font-medium text-green-700">{{ round($project->completion_percentage) }}%</span>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full bg-green-600 transition-all" 
                                             style="width: {{ $project->completion_percentage }}%"></div>
                                    </div>
                                    <div class="mt-1 flex justify-between text-xs text-gray-600">
                                        <span>{{ number_format($project->current_value, 0, ',', '.') }}</span>
                                        <span>{{ number_format($project->target_value, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                @else
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">{{ __('epp_dashboard.projects.progress') }}</span>
                                        <span class="font-medium text-blue-700">{{ number_format($project->current_value ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="mt-1 text-xs italic text-gray-500">{{ __('epp_dashboard.projects.no_target_set') }}</p>
                                </div>
                                @endif

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-purple-700">{{ $project->collections_count }}</div>
                                        <div class="text-xs text-gray-600">{{ __('epp_dashboard.projects.collections') }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-amber-700">{{ $project->egis_count }}</div>
                                        <div class="text-xs text-gray-600">EGI</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-green-700">€{{ number_format($project->equilibrium ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-600">💎</div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('epp-projects.show', $project) }}" 
                                       class="flex-1 rounded-lg border border-gray-300 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ __('epp_dashboard.projects.view_public') }}
                                    </a>
                                    <a href="{{ route('epp.dashboard.projects.edit', $project) }}"
                                       class="flex-1 rounded-lg bg-green-700 py-2 text-center text-sm font-medium text-white hover:bg-green-800">
                                        {{ __('epp_dashboard.projects.edit') }}
                                    </a>
                                </div>
                            </div>

                            <!-- Project Footer -->
                            <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>{{ __('epp_dashboard.projects.created') }}: {{ $project->created_at->format('d/m/Y') }}</span>
                                    @if($project->target_date)
                                        <span>{{ __('epp_dashboard.projects.target') }}: {{ $project->target_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($projects->hasPages())
                    <div class="mt-8">
                        {{ $projects->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="rounded-lg bg-white p-12 text-center shadow">
                    <div class="mb-4 text-6xl">🌱</div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">
                        {{ __('epp_dashboard.projects.no_projects_title') }}
                    </h3>
                    <p class="mb-6 text-gray-600">
                        {{ __('epp_dashboard.projects.no_projects_desc') }}
                    </p>
                    <a href="{{ route('epp.dashboard.projects.create') }}"
                       class="inline-flex items-center rounded-lg bg-green-700 px-6 py-3 text-white hover:bg-green-800">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('epp_dashboard.projects.create_first_project') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

