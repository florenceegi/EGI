{{-- CoA Vocabulary Terms Component for Traits Modal --}}
<div class="vocabulary-terms-container" data-component="vocabulary-terms" data-category="{{ $category }}">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <button onclick="goBackToCategories()"
                    class="inline-flex items-center px-2 py-1 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coa_traits.categories') }}
            </button>
            <span class="text-gray-400">›</span>
            <h3 class="text-lg font-medium text-gray-900">{{ ucfirst($category) }}</h3>
        </div>

        @if($search)
            <div class="text-sm text-gray-500">
                                {{ __('coa_traits.results_for') }}: "{{ $search }}"
            </div>
        @endif
    </div>

    @if($terms->isNotEmpty())
        <div class="grid gap-3">
            @foreach($terms as $term)
                <div class="term-item p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all cursor-pointer"
                     data-term-id="{{ $term->id }}"
                     data-term-slug="{{ $term->slug }}"
                     onclick="selectTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
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

                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                <span class="inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $term->category }}
                                </span>

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
                            <button class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-full transition-colors"
                                    onclick="event.stopPropagation(); selectTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-xs text-gray-500 text-center">
            {{ $terms->count() }} {{ __('coa_traits.terms_found') }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
