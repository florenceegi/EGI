{{-- resources/views/epp/dashboard/collections/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('epp_dashboard.collections.page_title') }}
            </h2>
            <a href="{{ route('epp.dashboard.index') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900">
                ← {{ __('epp_dashboard.collections.back_to_dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    {{ __('epp_dashboard.collections.your_collections') }}
                </h3>
                <p class="text-gray-600">
                    {{ __('epp_dashboard.collections.subtitle', ['count' => $collections->total()]) }}
                </p>
            </div>

            <!-- Collections Grid -->
            @if($collections->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($collections as $collection)
                        <div class="overflow-hidden rounded-lg bg-white shadow transition-all hover:shadow-xl">
                            <!-- Collection Image -->
                            @if($collection->url_image_ipfs || $collection->path_image_to_ipfs)
                                <div class="h-48 overflow-hidden bg-gray-200">
                                    <img src="{{ $collection->url_image_ipfs ?? asset('storage/' . $collection->path_image_to_ipfs) }}" 
                                         alt="{{ $collection->collection_name }}"
                                         class="h-full w-full object-cover">
                                </div>
                            @else
                                <div class="flex h-48 items-center justify-center bg-gradient-to-br from-purple-100 to-purple-200">
                                    <span class="text-6xl">📚</span>
                                </div>
                            @endif

                            <!-- Collection Body -->
                            <div class="p-4">
                                <h4 class="mb-2 font-bold text-gray-900 line-clamp-2">
                                    {{ $collection->collection_name }}
                                </h4>

                                <!-- EPP Project -->
                                @if($collection->eppProject)
                                    <div class="mb-3 flex items-center text-xs text-gray-600">
                                        <span class="mr-1">
                                            @if($collection->eppProject->project_type === 'ARF')
                                                🌳
                                            @elseif($collection->eppProject->project_type === 'APR')
                                                🌊
                                            @elseif($collection->eppProject->project_type === 'BPE')
                                                🐝
                                            @endif
                                        </span>
                                        <span class="truncate">{{ $collection->eppProject->name }}</span>
                                    </div>
                                @endif

                                <!-- Stats -->
                                <div class="mb-4 flex items-center justify-between text-sm">
                                    <div>
                                        <span class="text-gray-600">EGI:</span>
                                        <span class="font-bold text-amber-700">{{ $collection->egis_count }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $collection->created_at->format('d/m/Y') }}
                                    </div>
                                </div>

                                <!-- Action -->
                                <a href="{{ route('collections.show', $collection) }}" 
                                   class="block rounded-lg bg-purple-700 py-2 text-center text-sm font-medium text-white hover:bg-purple-800">
                                    {{ __('epp_dashboard.collections.view') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($collections->hasPages())
                    <div class="mt-8">
                        {{ $collections->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="rounded-lg bg-white p-12 text-center shadow">
                    <div class="mb-4 text-6xl">📚</div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">
                        {{ __('epp_dashboard.collections.no_collections_title') }}
                    </h3>
                    <p class="mb-6 text-gray-600">
                        {{ __('epp_dashboard.collections.no_collections_desc') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

