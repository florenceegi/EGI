{{-- CoA Vocabulary Terms Component for Traits Modal --}}
<style>
    .vocabulary-terms-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .vocabulary-terms-container::-webkit-scrollbar {
        width: 8px;
    }

    .vocabulary-terms-container::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }

    .vocabulary-terms-container::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .vocabulary-terms-container::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    .group-header {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background-color: rgba(249, 250, 251, 0.95);
    }
</style>

<div class="vocabulary-terms-container" data-component="vocabulary-terms" data-category="{{ $category }}">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <button onclick="VocabularyModalController.showCategories()"
                    class="inline-flex items-center px-2 py-1 text-sm text-gray-600 transition-colors hover:text-gray-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coa_traits.categories') }}
            </button>
            <span class="text-gray-400">›</span>
            <h3 class="text-lg font-medium text-gray-900">{{ __('coa_vocabulary.category_' . $category) }}</h3>
        </div>

        @if($search)
            <div class="text-sm text-gray-500">
                {{ __('coa_traits.results_for') }}: "{{ $search }}"
            </div>
        @endif
    </div>

    @if($terms->isNotEmpty())
        {{-- Raggruppa i termini per ui_group --}}
        @php
            $groupedTerms = $terms->groupBy('ui_group');
        @endphp

        <div class="overflow-y-auto vocabulary-terms-container max-h-96">
            @foreach($groupedTerms as $groupName => $groupTerms)
                {{-- Header del gruppo --}}
                @if($groupName)
                    <div class="sticky top-0 z-10 px-3 py-2 mb-3 border-b border-gray-200 group-header bg-gray-50">
                        <h5 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">
                            {{ $groupName }}
                            <span class="text-xs text-gray-500 normal-case">
                                ({{ $groupTerms->count() }} {{ $groupTerms->count() === 1 ? __('coa_traits.term') : __('coa_traits.terms') }})
                            </span>
                        </h5>
                    </div>
                @endif

                {{-- Termini del gruppo --}}
                <div class="grid gap-3 mb-6">
                    @foreach($groupTerms as $term)
                        <div class="p-3 transition-all border border-gray-200 rounded-lg cursor-pointer term-item hover:border-blue-300 hover:shadow-sm"
                             data-term-id="{{ $term->id }}"
                             data-term-slug="{{ $term->slug }}"
                             onclick="VocabularyModalController.selectTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        {{ $term->name }}
                                    </h4>

                                    @if($term->description)
                                        <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                            {{ $term->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ __('coa_vocabulary.category_' . $term->category) }}
                                        </span>

                                        @if($term->ui_group)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                                {{ $term->ui_group }}
                                            </span>
                                        @endif

                                        @if($term->locale)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                                </svg>
                                                {{ strtoupper($term->locale) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-shrink-0 ml-2">
                                    <button class="inline-flex items-center justify-center w-8 h-8 text-gray-400 transition-colors rounded-full hover:text-blue-500 hover:bg-blue-50"
                                            onclick="event.stopPropagation(); VocabularyModalController.selectTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-xs text-center text-gray-500">
            {{ $terms->count() }} {{ __('coa_traits.terms_found') }}
        </div>
    @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if($search)
                    {{ __('coa_traits.no_results_found') }}
                @else
                    {{ __('coa_traits.no_terms_available') }}
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($search)
                    {{ __('coa_traits.no_terms_match_search') }} "{{ $search }}" {{ __('coa_traits.in_category') }} {{ $category }}.
                @else
                    {{ __('coa_traits.no_terms_found_category') }} {{ $category }}.
                @endif
            </p>
        </div>
    @endif
</div>

<script>
function selectTerm(termSlug, termName) {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.selectTerm) {
        window.vocabularyModal.selectTerm(termSlug, termName);
    }
}

function goBackToCategories() {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.showCategories) {
        window.vocabularyModal.showCategories();
    }
}
</script>
