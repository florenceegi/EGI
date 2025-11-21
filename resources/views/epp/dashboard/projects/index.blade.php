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
                <div class="grid grid-cols-1 gap-8 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3">
                    @foreach($projects as $project)
                        <div class="overflow-hidden rounded-2xl bg-white shadow-lg transition-all hover:shadow-2xl flex flex-col min-h-[600px]">
                            {{-- 🖼️ PROJECT IMAGE --}}
                            @php
                                $projectImageUrl = $project->getFirstMediaUrl('project_images');
                            @endphp
                            
                            @if($projectImageUrl)
                                <div class="relative h-64 overflow-hidden bg-gradient-to-br from-[#2D5016]/10 to-[#1B365D]/10">
                                    <img src="{{ $projectImageUrl }}" 
                                         alt="{{ $project->name }}"
                                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    
                                    {{-- Project Type Badge on Image --}}
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm
                                            {{ $project->project_type === 'ARF' ? 'bg-green-600/90' : '' }}
                                            {{ $project->project_type === 'APR' ? 'bg-blue-600/90' : '' }}
                                            {{ $project->project_type === 'BPE' ? 'bg-yellow-600/90' : '' }}">
                                            <span class="text-lg">
                                                @if($project->project_type === 'ARF')
                                                    🌳
                                                @elseif($project->project_type === 'APR')
                                                    🌊
                                                @elseif($project->project_type === 'BPE')
                                                    🐝
                                                @endif
                                            </span>
                                            {{ __('epp_projects.types.' . strtolower($project->project_type)) }}
                                        </span>
                                    </div>

                                    {{-- Status Badge on Image --}}
                                    <div class="absolute top-3 right-3">
                                        <span class="rounded-full px-3 py-1 text-xs font-medium backdrop-blur-sm
                                            {{ $project->status === 'in_progress' ? 'bg-green-500/90 text-white' : '' }}
                                            {{ $project->status === 'planned' ? 'bg-blue-500/90 text-white' : '' }}
                                            {{ $project->status === 'completed' ? 'bg-gray-500/90 text-white' : '' }}
                                            {{ $project->status === 'cancelled' ? 'bg-red-500/90 text-white' : '' }}">
                                            {{ __('epp_projects.show.status_' . $project->status) }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                {{-- Project Header (no image) - PLACEHOLDER GRANDE --}}
                                <div class="relative h-64 border-b border-gray-200 bg-gradient-to-br from-green-100 via-blue-50 to-green-50 flex items-center justify-center">
                                    <div class="text-center">
                                        <span class="text-8xl mb-4 block">
                                            @if($project->project_type === 'ARF')
                                                🌳
                                            @elseif($project->project_type === 'APR')
                                                🌊
                                            @elseif($project->project_type === 'BPE')
                                                🐝
                                            @endif
                                        </span>
                                        <p class="text-sm text-gray-600 font-medium">
                                            {{ __('epp_projects.types.' . strtolower($project->project_type)) }}
                                        </p>
                                    </div>
                                    
                                    {{-- Status Badge --}}
                                    <div class="absolute top-4 right-4">
                                        <span class="rounded-full px-3 py-1.5 text-xs font-medium backdrop-blur-sm
                                            {{ $project->status === 'in_progress' ? 'bg-green-500 text-white' : '' }}
                                            {{ $project->status === 'planned' ? 'bg-blue-500 text-white' : '' }}
                                            {{ $project->status === 'completed' ? 'bg-gray-500 text-white' : '' }}
                                            {{ $project->status === 'cancelled' ? 'bg-red-500 text-white' : '' }}">
                                            {{ __('epp_projects.show.status_' . $project->status) }}
                                        </span>
                                    </div>
                                    
                                    {{-- Type Badge --}}
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold backdrop-blur-sm
                                            {{ $project->project_type === 'ARF' ? 'bg-green-600 text-white' : '' }}
                                            {{ $project->project_type === 'APR' ? 'bg-blue-600 text-white' : '' }}
                                            {{ $project->project_type === 'BPE' ? 'bg-yellow-600 text-white' : '' }}">
                                            {{ __('epp_projects.types.' . strtolower($project->project_type)) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Project Body -->
                            <div class="p-8 flex-1 flex flex-col">
                                {{-- Project Name with Avatar --}}
                                <div class="flex items-center gap-4 mb-3">
                                    @if($project->getFirstMediaUrl('project_avatar'))
                                        <img src="{{ $project->getFirstMediaUrl('project_avatar') }}" 
                                             alt="{{ $project->name }}"
                                             class="w-16 h-16 rounded-full object-cover ring-4 ring-green-100">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#2D5016]/20 to-[#1B365D]/20 flex items-center justify-center ring-4 ring-green-100">
                                            <span class="text-2xl">
                                                @if($project->project_type === 'ARF')
                                                    🌳
                                                @elseif($project->project_type === 'APR')
                                                    🌊
                                                @elseif($project->project_type === 'BPE')
                                                    🐝
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <h4 class="font-bold text-2xl text-gray-900 line-clamp-2 leading-tight flex-1">{{ $project->name }}</h4>
                                </div>
                                {{-- Description --}}
                                <p class="mb-6 text-base text-gray-700 line-clamp-3 flex-1 leading-relaxed">
                                    {{ $project->description }}
                                </p>

                                {{-- Progress --}}
                                @if($project->target_value && $project->target_value > 0)
                                <div class="mb-6 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                    <div class="mb-3 flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-700">{{ __('epp_dashboard.projects.progress') }}</span>
                                        <span class="text-2xl font-bold text-[#2D5016]">{{ round($project->completion_percentage) }}%</span>
                                    </div>
                                    <div class="h-4 w-full overflow-hidden rounded-full bg-white shadow-inner">
                                        <div class="h-full bg-gradient-to-r from-[#2D5016] to-[#4CAF50] transition-all shadow-md" 
                                             style="width: {{ $project->completion_percentage }}%"></div>
                                    </div>
                                    <div class="mt-3 flex justify-between text-sm text-gray-700">
                                        <span>{{ __('epp_dashboard.projects.current') }}: <strong class="text-[#2D5016]">{{ number_format($project->current_value, 0, ',', '.') }}</strong></span>
                                        <span>{{ __('epp_dashboard.projects.target_label') }}: <strong class="text-gray-900">{{ number_format($project->target_value, 0, ',', '.') }}</strong></span>
                                    </div>
                                </div>
                                @else
                                <div class="mb-6 p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-base font-semibold text-blue-900">{{ __('epp_dashboard.projects.current') }}</span>
                                        <span class="text-3xl font-bold text-blue-700">{{ number_format($project->current_value ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-blue-600 font-medium">{{ __('epp_dashboard.projects.no_target_set') }}</p>
                                </div>
                                @endif

                                {{-- Stats Grid --}}
                                <div class="grid grid-cols-3 gap-4 mb-6 p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 shadow-sm">
                                    <div class="text-center">
                                        <div class="text-3xl font-black text-purple-600 mb-1">{{ $project->collections_count }}</div>
                                        <div class="text-xs text-gray-600 uppercase font-bold tracking-wide">{{ __('epp_dashboard.projects.collections') }}</div>
                                    </div>
                                    <div class="text-center border-x-2 border-gray-300">
                                        <div class="text-3xl font-black text-amber-600 mb-1">{{ $project->egis_count }}</div>
                                        <div class="text-xs text-gray-600 uppercase font-bold tracking-wide">EGI</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-black text-[#D4A574] mb-1">€{{ number_format($project->equilibrium ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-600 uppercase font-bold tracking-wide">💎 VALUE</div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-3 mt-auto pt-4 border-t border-gray-200">
                                    <a href="{{ route('epp-projects.show', $project) }}" 
                                       class="flex-1 rounded-xl border-2 border-gray-300 py-3 text-center text-sm font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-500 transition-all shadow-sm hover:shadow">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ __('epp_dashboard.projects.view_public') }}
                                        </span>
                                    </a>
                                    <a href="{{ route('epp.dashboard.projects.edit', $project) }}"
                                       class="flex-1 rounded-xl bg-gradient-to-r from-[#2D5016] to-[#3a6b1f] py-3 text-center text-sm font-bold text-white hover:from-[#234012] hover:to-[#2D5016] transition-all shadow-md hover:shadow-lg">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            {{ __('epp_dashboard.projects.edit') }}
                                        </span>
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

