{{-- CoA Vocabulary Search Results Component for Traits Modal --}}
<div class="vocabulary-search-results-container" data-component="vocabulary-search-results">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <h3 class="text-lg font-medium text-gray-900">{{ __('coa_traits.search_results') }}</h3>
            @if($category)
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                    {{ ucfirst($category) }}
                </span>
            @endif
        </div>

        <div class="text-sm text-gray-500">
            {{ __('coa_traits.results_for') }}: "{{ $query }}"
        </div>
    </div>

    @if($terms->isNotEmpty())
        <div class="grid gap-3">
            @foreach($terms as $term)
                @php
                    // Usa le traduzioni dalle chiavi o dagli attributi del modello
                    $translatedName = $term->translated_name ?? __('coa_vocabulary.' . $term->slug, [], app()->getLocale());
                    $translatedDescription = $term->translated_description ?? __('coa_vocabulary.' . $term->slug . '_description', [], app()->getLocale());
                @endphp
                <div class="p-3 transition-all border border-gray-200 rounded-lg cursor-pointer term-item hover:border-blue-300 hover:shadow-sm"
                     data-term-id="{{ $term->id }}"
                     data-term-slug="{{ $term->slug }}"
                     onclick="VocabularyModalController.selectTerm('{{ $term->slug }}', '{{ addslashes($translatedName) }}')">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900">
                                    @php
                                        $highlightedName = $translatedName;
                                        if ($query && strlen($query) >= 2) {
                                            $highlightedName = preg_replace_callback(
                                                '/(' . preg_quote($query, '/') . ')/i',
                                                function($matches) {
                                                    return '<mark class="px-1 bg-yellow-200">' . $matches[0] . '</mark>';
                                                },
                                                $translatedName
                                            );
                                        }
                                    @endphp
                                    {!! $highlightedName !!}
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                    {{ $term->category }}
                                </span>
                            </div>

                            @if($translatedDescription)
                                <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                    @php
                                        $highlightedDescription = $translatedDescription;
                                        if ($query && strlen($query) >= 2) {
                                            $highlightedDescription = preg_replace_callback(
                                                '/(' . preg_quote($query, '/') . ')/i',
                                                function($matches) {
                                                    return '<mark class="px-1 bg-yellow-200">' . $matches[0] . '</mark>';
                                                },
                                                $translatedDescription
                                            );
                                        }
                                    @endphp
                                    {!! $highlightedDescription !!}
                                </p>
                            @endif

                            <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
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
                            <button class="inline-flex items-center justify-center w-8 h-8 text-gray-400 transition-colors rounded-full hover:text-blue-500 hover:bg-blue-50"
                                    onclick="event.stopPropagation(); selectSearchTerm('{{ $term->slug }}', '{{ addslashes($translatedName) }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-xs text-center text-gray-500">
            {{ $terms->count() }} {{ __('coa_traits.results_found') }}
            @if($category)
                {{ __('coa_traits.in_category') }} {{ $category }}
            @endif
        </div>
    @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('coa_traits.no_results_found') }}</h3>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('coa_traits.no_terms_match_search') }} "{{ $query }}"
                @if($category)
                    {{ __('coa_traits.in_category') }} {{ $category }}
                @endif
                .
            </p>
            <div class="mt-4">
                <button onclick="VocabularyModalController.showCategories()" class="text-sm text-blue-600 hover:text-blue-500">
                    {{ __('coa_traits.clear_search') }}
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function selectSearchTerm(termSlug, termName) {
    // Use VocabularyModalController instead of vocabularyModal
    if (typeof window.VocabularyModalController !== 'undefined' && window.VocabularyModalController.selectTerm) {
        window.VocabularyModalController.selectTerm(termSlug, termName);
    } else {
        console.error('VocabularyModalController not available for selectSearchTerm');
    }
}

function highlightSearchTerm(text, query) {
    if (!query || query.length < 2) return text;

    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="px-1 bg-yellow-200">$1</mark>');
}
</script>
