<x-app-layout>
    <x-slot name="title">{{ __('biography.manage.title') }}</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Header Section -->
        <div class="border-b border-gray-700 bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                    <div>
                        <h1 class="font-serif text-2xl font-bold text-white sm:text-3xl">
                            {{ __('biography.manage.title') }}
                        </h1>
                        <p class="mt-2 text-sm text-[#D4A574] sm:text-base">
                            {{ __('biography.manage.subtitle') }}
                        </p>
                    </div>
                    <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
                        @if ($user->biographies()->exists())
                            <a href="{{ route('biography.view') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm text-white transition-colors hover:bg-gray-600 sm:text-base">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                {{ __('biography.manage.view_biography') }}
                            </a>
                        @endif
                        <button onclick="createNewBiography()"
                            class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-2 text-sm font-semibold text-gray-900 transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574] sm:text-base">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('biography.manage.create_new') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            @if ($biographies->count() > 0)
                <!-- Existing Biographies -->
                <div class="mb-8 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($biographies as $biography)
                        <div
                            class="rounded-xl border border-gray-700 bg-gray-800/50 p-4 sm:p-6 backdrop-blur-sm transition-all duration-200 hover:border-[#D4A574]/50">
                            <!-- Biography Header -->
                            <div class="mb-4 flex flex-col space-y-3 sm:flex-row sm:items-start sm:justify-between sm:space-y-0">
                                <div class="flex-1">
                                    <h3 class="mb-2 text-lg font-semibold text-white sm:text-xl">
                                        {{ $biography->title }}
                                    </h3>
                                    <div class="flex flex-col space-y-2 text-sm text-gray-400 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4">
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            {{ __('biography.manage.chapters') }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            {{ $biography->updated_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Status Badge -->
                                    @if ($biography->is_public)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-900/50 px-2 py-1 text-xs font-medium text-green-300">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.manage.public') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-900/50 px-2 py-1 text-xs font-medium text-gray-300">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.manage.private') }}
                                        </span>
                                    @endif
                                    @if ($biography->is_completed)
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-900/50 px-2 py-1 text-xs font-medium text-blue-300">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.manage.completed') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Biography Preview -->
                            @if ($biography->excerpt)
                                <p class="mb-4 line-clamp-3 text-sm text-gray-300">
                                    {{ $biography->excerpt }}
                                </p>
                            @endif

                            <!-- Biography Stats -->
                            <div class="mb-4 flex flex-col space-y-2 text-sm text-gray-400 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                                <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4">
                                    @if ($biography->type === 'chapters')
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            {{ $biography->chapters->count() }} {{ __('biography.manage.chapters') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $biography->getEstimatedReadingTime() }}
                                        {{ __('biography.manage.min_read') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                                <a href="{{ route('biography.edit', $biography->id) }}"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#1B365D] px-4 py-2 text-sm text-white transition-colors hover:bg-[#2D5016] sm:text-base">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    {{ __('biography.manage.edit') }}
                                </a>
                                <button onclick="deleteBiography({{ $biography->id }})"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-900/50 px-3 py-2 text-sm text-red-300 transition-colors hover:bg-red-900 sm:text-base">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="py-12 text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-800">
                        <svg class="h-12 w-12 text-[#D4A574]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mb-4 text-xl font-semibold text-white">
                        {{ __('biography.manage.no_biographies_title') }}
                    </h3>
                    <p class="mx-auto mb-8 max-w-md text-gray-400">
                        {{ __('biography.manage.no_biographies_description') }}
                    </p>
                    <button onclick="createNewBiography()"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        {{ __('biography.manage.create_first') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function createNewBiography() {
                window.location.href = '{{ route('biography.create') }}';
            }

            function deleteBiography(biographyId) {
                if (!confirm('{{ __('biography.manage.confirm_delete') }}')) {
                    return;
                }

                // Show loading state
                const deleteButton = document.querySelector(`button[onclick="deleteBiography(${biographyId})"]`);
                const originalText = deleteButton.innerHTML;
                deleteButton.innerHTML = '<div class="w-4 h-4 border-b-2 border-red-400 rounded-full animate-spin"></div>';
                deleteButton.disabled = true;

                fetch(`/biography/${biographyId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin' // Include session cookies
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type');

                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();

                            if (response.ok && data.success) {
                                // Success - show notification and reload
                                const notification = document.createElement('div');
                                notification.className =
                                    'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                                notification.textContent = 'Biografia eliminata con successo';
                                document.body.appendChild(notification);

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // API returned error
                                throw new Error(data.message || 'Errore durante l\'eliminazione della biografia');
                            }
                        } else {
                            // Non-JSON response (probably HTML error page)
                            const text = await response.text();
                            console.error('Server response:', text);
                            throw new Error(`Errore del server (${response.status}). Controlla i log del server.`);
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);

                        // Restore button state
                        deleteButton.innerHTML = originalText;
                        deleteButton.disabled = false;

                        // Show error message
                        alert(error.message || '{{ __('biography.manage.delete_error') }}');
                    });
            }
        </script>
    @endpush
</x-app-layout>
