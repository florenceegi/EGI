{{-- CoA Vocabulary Error Component for Traits Modal --}}
<div class="vocabulary-error-container" data-component="vocabulary-error">
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Errore</h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ $error ?? 'Si è verificato un errore imprevisto.' }}
        </p>
        <div class="mt-6 flex justify-center space-x-3">
            <button onclick="retryOperation()"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Riprova
            </button>
            <button onclick="goBackToStart()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Torna all'inizio
            </button>
        </div>
    </div>
</div>

<script>
function retryOperation() {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.retryLastOperation) {
        window.vocabularyModal.retryLastOperation();
    } else {
        // Fallback: reload the page
        location.reload();
    }
}

function goBackToStart() {
    // This function will be implemented by the parent modal component
    if (typeof window.vocabularyModal !== 'undefined' && window.vocabularyModal.showCategories) {
        window.vocabularyModal.showCategories();
    }
}
</script>
