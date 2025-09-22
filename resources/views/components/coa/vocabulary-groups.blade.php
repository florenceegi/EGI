{{-- CoA Vocabulary Groups Component for Traits Modal --}}
<style>
    .vocabulary-groups-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .vocabulary-groups-container::-webkit-scrollbar {
        width: 8px;
    }

    .vocabulary-groups-container::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }

    .vocabulary-groups-container::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .vocabulary-groups-container::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    .group-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .group-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .group-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3B82F6, #8B5CF6, #06B6D4);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .group-card:hover::before {
        opacity: 1;
    }

    /* Category-specific styling */
    .group-card[data-category="technique"] {
        border-left: 4px solid #3B82F6;
    }

    .group-card[data-category="materials"] {
        border-left: 4px solid #10B981;
    }

    .group-card[data-category="support"] {
        border-left: 4px solid #F59E0B;
    }

    /* Responsive grid - Desktop 2 columns, Mobile 1 column */
    @media (max-width: 1023px) {
        .groups-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }

        .group-card {
            padding: 1.25rem;
        }

        .group-card h4 {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .vocabulary-groups-container {
            max-height: 70vh;
        }
    }

    @media (min-width: 1024px) {
        .groups-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1.25rem !important;
        }
    }
</style>

<div class="vocabulary-groups-container" data-component="vocabulary-groups" data-category="{{ $category }}">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-2">
            <button onclick="VocabularyModalController.showCategories()"
                    class="inline-flex items-center px-2 py-1 text-sm text-gray-600 transition-colors hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coa_traits.categories') }}
            </button>
            <span class="text-gray-400">›</span>
            <h3 class="text-lg font-semibold text-gray-900">
                {{ __('coa_vocabulary.category_' . $category) }}
            </h3>
        </div>

        <div class="text-sm text-gray-500">
            {{ $groups->count() }} {{ $groups->count() === 1 ? __('coa_traits.group') : __('coa_traits.groups') }}
        </div>
    </div>

    @if($groups->isNotEmpty())
        <div class="groups-grid grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4 max-h-96 overflow-y-auto vocabulary-groups-container">
            @foreach($groups as $group)
                <div class="group-card p-4 bg-white border border-gray-200 rounded-xl cursor-pointer transition-all hover:border-blue-300 hover:shadow-lg"
                     data-category="{{ $category }}"
                     data-group="{{ $group['group'] }}"
                     onclick="VocabularyModalController.loadGroup('{{ $category }}', '{{ addslashes($group['group']) }}')">

                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            {{-- Group Icon based on category --}}
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex-shrink-0">
                                    @switch($category)
                                        @case('technique')
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('materials')
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.781 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('support')
                                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                            </div>
                                    @endswitch
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $group['name'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $group['count'] }} {{ $group['count'] === 1 ? __('coa_traits.term') : __('coa_traits.terms') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Progress indicator --}}
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                                @php
                                    // Calculate relative size for visual indication (max 50 terms = 100%)
                                    $percentage = min(100, ($group['count'] / 50) * 100);
                                @endphp
                                <div class="h-1.5 rounded-full transition-all
                                    @switch($category)
                                        @case('technique')
                                            bg-blue-500
                                            @break
                                        @case('materials')
                                            bg-green-500
                                            @break
                                        @case('support')
                                            bg-amber-500
                                            @break
                                        @default
                                            bg-gray-500
                                    @endswitch"
                                     style="width: {{ $percentage }}%"></div>
                            </div>

                            {{-- Group description or sample terms --}}
                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                Esplora i termini del gruppo {{ $group['group'] }}
                            </p>
                        </div>

                        {{-- Arrow indicator --}}
                        <div class="flex-shrink-0 ml-3 mt-1">
                            <svg class="w-5 h-5 text-gray-400 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('coa_traits.no_groups_available') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('coa_traits.no_groups_found_for_category') }}</p>
        </div>
    @endif
</div>
