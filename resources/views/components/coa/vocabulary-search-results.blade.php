{{-- CoA Vocabulary Search Results Component for Traits Modal --}}
<div class="vocabulary-search-results-container" data-component="vocabulary-search-results">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <h3 class="text-lg font-medium text-gray-900">Risultati ricerca</h3>
            @if($category)
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                    {{ ucfirst($category) }}
                </span>
            @endif
        </div>

        <div class="text-sm text-gray-500">
            Per: "{{ $query }}"
        </div>
    </div>

    @if($terms->isNotEmpty())
        <div class="grid gap-3">
            @foreach($terms as $term)
                <div class="term-item p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all cursor-pointer"
                     data-term-id="{{ $term->id }}"
                     data-term-slug="{{ $term->slug }}"
                     onclick="selectSearchTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900">
                                    {!! highlightSearchTerm($term->name, $query) !!}
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                    {{ $term->category }}
                                </span>
                            </div>

                            @if($term->description)
                                <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                    {!! highlightSearchTerm($term->description, $query) !!}
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
                                    onclick="event.stopPropagation(); selectSearchTerm('{{ $term->slug }}', '{{ addslashes($term->name) }}')">
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
            {{ $terms->count() }} risultati trovati
            @if($category)
                in {{ $category }}
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun risultato trovato</h3>
            <p class="mt-1 text-sm text-gray-500">
                Nessun termine corrisponde alla ricerca "{{ $query }}"
                @if($category)
                    nella categoria {{ $category }}
                @endif
                .
            </p>
            <div class="mt-4">
                <button onclick="clearSearch()" class="text-sm text-blue-600 hover:text-blue-500">
                    Cancella ricerca
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function selectSearchTerm(termSlug, termName) {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.selectTerm) {
        window.vocabularyModal.selectTerm(termSlug, termName);
    }
}

function clearSearch() {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.clearSearch) {
        window.vocabularyModal.clearSearch();
    }
}

function highlightSearchTerm(text, query) {
    if (!query || query.length < 2) return text;

    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 px-1">$1</mark>');
}
</script>

@php
if (!function_exists('highlightSearchTerm')) {
    function highlightSearchTerm($text, $query) {
        if (!$query || strlen($query) < 2) return $text;

        return preg_replace_callback(
            '/(' . preg_quote($query, '/') . ')/i',
            function($matches) {
                return '<mark class="bg-yellow-200 px-1">' . $matches[0] . '</mark>';
            },
            $text
        );
    }
}
@endphp
