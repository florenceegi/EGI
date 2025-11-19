{{-- resources/views/epp/dashboard/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('epp_dashboard.home.page_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8 overflow-hidden rounded-lg bg-gradient-to-r from-green-700 to-green-600 p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="mb-2 text-3xl font-bold">
                            {{ __('epp_dashboard.home.welcome', ['name' => Auth::user()->name]) }}
                        </h1>
                        <p class="text-green-100">
                            {{ __('epp_dashboard.home.subtitle') }}
                        </p>
                    </div>
                    <div class="text-6xl">🌱</div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Projects -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('epp_dashboard.home.total_projects') }}</p>
                                <p class="mt-2 text-3xl font-bold text-green-700">{{ $stats['total_projects'] }}</p>
                            </div>
                            <div class="rounded-full bg-green-100 p-3 text-3xl">🌳</div>
                        </div>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('epp_dashboard.home.active_projects') }}</p>
                                <p class="mt-2 text-3xl font-bold text-blue-700">{{ $stats['active_projects'] }}</p>
                            </div>
                            <div class="rounded-full bg-blue-100 p-3 text-3xl">⚡</div>
                        </div>
                    </div>
                </div>

                <!-- Total Collections -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('epp_dashboard.home.total_collections') }}</p>
                                <p class="mt-2 text-3xl font-bold text-purple-700">{{ $stats['total_collections'] }}</p>
                            </div>
                            <div class="rounded-full bg-purple-100 p-3 text-3xl">📚</div>
                        </div>
                    </div>
                </div>

                <!-- Total EGIs -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('epp_dashboard.home.total_egis') }}</p>
                                <p class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['total_egis'] }}</p>
                            </div>
                            <div class="rounded-full bg-amber-100 p-3 text-3xl">🎨</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects & Collections Grid -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Your Projects -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('epp_dashboard.home.your_projects') }}
                            </h3>
                            <a href="{{ route('epp.dashboard.projects') }}" 
                               class="text-sm font-medium text-green-700 hover:text-green-800">
                                {{ __('epp_dashboard.home.view_all') }} →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($projects->take(3) as $project)
                            <div class="mb-4 rounded-lg border border-gray-200 p-4 transition-all hover:border-green-300 hover:shadow-md">
                                <div class="mb-2 flex items-start justify-between">
                                    <div class="flex items-center">
                                        <span class="mr-2 text-2xl">
                                            @if($project->project_type === 'ARF')
                                                🌳
                                            @elseif($project->project_type === 'APR')
                                                🌊
                                            @elseif($project->project_type === 'BPE')
                                                🐝
                                            @endif
                                        </span>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $project->name }}</h4>
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
                                @if($project->target_value && $project->target_value > 0)
                                <div class="mt-3">
                                    <div class="mb-1 flex justify-between text-xs">
                                        <span class="text-gray-600">{{ __('epp_dashboard.home.progress') }}</span>
                                        <span class="font-medium text-green-700">{{ round($project->completion_percentage) }}%</span>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full bg-green-600 transition-all" 
                                             style="width: {{ $project->completion_percentage }}%"></div>
                                    </div>
                                </div>
                                @else
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium text-blue-700">{{ number_format($project->current_value ?? 0, 0, ',', '.') }}</span>
                                        <span class="italic">{{ __('epp_dashboard.projects.no_target_set') }}</span>
                                    </p>
                                </div>
                                @endif
                                <div class="mt-3 flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        📚 {{ $project->collections_count }} {{ __('epp_dashboard.home.collections') }}
                                    </span>
                                    <a href="{{ route('epp-projects.show', $project) }}" 
                                       class="font-medium text-green-700 hover:text-green-800">
                                        {{ __('epp_dashboard.home.view') }} →
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-500">
                                <div class="mb-4 text-5xl">🌱</div>
                                <p class="mb-4">{{ __('epp_dashboard.home.no_projects') }}</p>
                                <a href="{{ route('epp.dashboard.projects.create') }}" 
                                   class="inline-block rounded-lg bg-green-700 px-4 py-2 text-white hover:bg-green-800">
                                    {{ __('epp_dashboard.home.create_project') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Collections -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('epp_dashboard.home.recent_collections') }}
                            </h3>
                            <a href="{{ route('epp.dashboard.collections') }}" 
                               class="text-sm font-medium text-green-700 hover:text-green-800">
                                {{ __('epp_dashboard.home.view_all') }} →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($collections as $collection)
                            <div class="mb-4 flex items-center rounded-lg border border-gray-200 p-4 transition-all hover:border-green-300 hover:shadow-md">
                                @if($collection->url_image_ipfs || $collection->path_image_to_ipfs)
                                    <img src="{{ $collection->url_image_ipfs ?? asset('storage/' . $collection->path_image_to_ipfs) }}" 
                                         alt="{{ $collection->collection_name }}"
                                         class="mr-4 h-16 w-16 rounded-lg object-cover">
                                @else
                                    <div class="mr-4 flex h-16 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-green-100 to-green-200 text-2xl">
                                        📚
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $collection->collection_name }}</h4>
                                    <p class="text-xs text-gray-600">
                                        {{ $collection->eppProject->name ?? __('epp_dashboard.home.no_project') }}
                                    </p>
                                    <div class="mt-1 flex items-center text-xs text-gray-600">
                                        <span>🎨 {{ $collection->egis_count }} EGI</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $collection->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('collections.show', $collection) }}" 
                                   class="ml-4 text-green-700 hover:text-green-800">
                                    →
                                </a>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-500">
                                <div class="mb-4 text-5xl">📚</div>
                                <p class="mb-4">{{ __('epp_dashboard.home.no_collections') }}</p>
                                <a href="{{ route('epp.dashboard.collections') }}" 
                                   class="inline-block rounded-lg bg-green-700 px-4 py-2 text-white hover:bg-green-800">
                                    {{ __('epp_dashboard.home.create_collection') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ __('epp_dashboard.home.quick_actions') }}
                    </h3>
                </div>
                <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                    <a href="{{ route('epp.dashboard.projects') }}" 
                       class="flex items-center rounded-lg border-2 border-gray-200 p-4 transition-all hover:border-green-500 hover:bg-green-50">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-2xl">
                            🌳
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('epp_dashboard.home.manage_projects') }}</div>
                            <div class="text-sm text-gray-600">{{ __('epp_dashboard.home.manage_projects_desc') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('epp.dashboard.collections') }}" 
                       class="flex items-center rounded-lg border-2 border-gray-200 p-4 transition-all hover:border-purple-500 hover:bg-purple-50">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-2xl">
                            📚
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('epp_dashboard.home.manage_collections') }}</div>
                            <div class="text-sm text-gray-600">{{ __('epp_dashboard.home.manage_collections_desc') }}</div>
                        </div>
                    </a>

                    <a href="{{ route('epp-projects.index') }}" 
                       class="flex items-center rounded-lg border-2 border-gray-200 p-4 transition-all hover:border-blue-500 hover:bg-blue-50">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">
                            🌍
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('epp_dashboard.home.view_public_page') }}</div>
                            <div class="text-sm text-gray-600">{{ __('epp_dashboard.home.view_public_page_desc') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

