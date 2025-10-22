{{--
    Component: EGI AI Traits Proposals Review
    Panel for reviewing and approving AI-generated trait proposals

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - AI Traits Generation)
    @date 2025-10-21

    Props:
    - generation: AiTraitGeneration model instance with proposals
--}}

@props(['generation'])

<div class="overflow-hidden rounded-2xl border-2 border-indigo-200 bg-white shadow-lg"
    id="ai-traits-proposals-{{ $generation->id }}">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-700 to-purple-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-stars text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        Proposte Traits AI
                    </h3>
                    <p class="text-sm text-indigo-100">N.A.T.A.N ha identificato {{ $generation->proposals->count() }}
                        traits
                        (Confidence: {{ $generation->total_confidence }}%)</p>
                </div>
            </div>
            <span class="rounded-full bg-white/20 px-4 py-1 text-sm font-semibold text-white">
                #{{ $generation->id }}
            </span>
        </div>
    </div>

    <div class="space-y-4 p-6">
        {{-- Analysis Notes --}}
        @if ($generation->analysis_notes)
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb mt-1 text-indigo-600"></i>
                    <div>
                        <h4 class="mb-1 font-semibold text-indigo-900">Analisi AI</h4>
                        <p class="text-sm text-indigo-800">{{ $generation->analysis_notes }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Proposals List --}}
        <div class="space-y-3">
            @foreach ($generation->proposals as $proposal)
                <div class="trait-proposal rounded-xl border-2 border-gray-200 bg-gray-50 p-4 transition-all duration-200 hover:border-purple-300 hover:shadow-md"
                    data-proposal-id="{{ $proposal->id }}">
                    <div class="flex items-start justify-between gap-4">
                        {{-- Trait Info --}}
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                {{-- Match Type Badge --}}
                                @php
                                    $badgeColors = [
                                        'exact' => 'bg-green-100 text-green-800 border-green-300',
                                        'fuzzy' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'new_value' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'new_type' => 'bg-orange-100 text-orange-800 border-orange-300',
                                        'new_category' => 'bg-red-100 text-red-800 border-red-300',
                                    ];
                                    $badgeClass =
                                        $badgeColors[$proposal->match_type] ??
                                        'bg-gray-100 text-gray-800 border-gray-300';
                                @endphp
                                <span class="{{ $badgeClass }} rounded-full border px-3 py-1 text-xs font-semibold">
                                    @switch($proposal->match_type)
                                        @case('exact')
                                            ✓ Match Esatto
                                        @break

                                        @case('fuzzy')
                                            ≈ Match Fuzzy
                                        @break

                                        @case('new_value')
                                            + Valore Nuovo
                                        @break

                                        @case('new_type')
                                            + Tipo Nuovo
                                        @break

                                        @case('new_category')
                                            + Categoria Nuova
                                        @break
                                    @endswitch
                                </span>

                                {{-- Confidence Badge --}}
                                <span
                                    class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800">
                                    {{ $proposal->confidence }}% confident
                                </span>
                            </div>

                            {{-- Trait Display --}}
                            <div class="mb-2">
                                <span class="font-semibold text-gray-700">{{ $proposal->category_suggestion }}</span>
                                <i class="fas fa-arrow-right mx-2 text-xs text-gray-400"></i>
                                <span class="font-semibold text-gray-700">{{ $proposal->type_suggestion }}</span>
                                <i class="fas fa-arrow-right mx-2 text-xs text-gray-400"></i>
                                <span class="text-lg font-bold text-purple-600">
                                    {{ $proposal->display_value_suggestion ?? $proposal->value_suggestion }}
                                </span>
                            </div>

                            {{-- Reasoning --}}
                            @if ($proposal->reasoning)
                                <p class="text-sm italic text-gray-600">
                                    <i class="fas fa-quote-left mr-1 text-xs text-gray-400"></i>
                                    {{ $proposal->reasoning }}
                                </p>
                            @endif
                        </div>

                        {{-- Approval Checkbox --}}
                        <div class="flex flex-col items-center gap-2">
                            <label class="flex cursor-pointer items-center">
                                <input type="checkbox"
                                    class="proposal-checkbox h-6 w-6 rounded border-gray-300 text-purple-600 focus:ring-2 focus:ring-purple-500"
                                    data-proposal-id="{{ $proposal->id }}" checked>
                            </label>
                            <span class="text-xs text-gray-500">Approva</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 border-t border-gray-200 pt-4">
            <button onclick="handleApproveAll({{ $generation->id }})"
                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-6 py-3 font-bold text-white shadow-md transition-all duration-200 hover:from-green-700 hover:to-green-600 hover:shadow-lg">
                <i class="fas fa-check-double"></i>
                Approva Tutti
            </button>

            <button onclick="handleRejectAll({{ $generation->id }})"
                class="flex items-center justify-center gap-2 rounded-lg border-2 border-red-300 bg-white px-6 py-3 font-bold text-red-600 transition-all duration-200 hover:bg-red-50">
                <i class="fas fa-times-circle"></i>
                Rifiuta Tutti
            </button>

            <button onclick="handleApplySelected({{ $generation->id }})"
                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-3 font-bold text-white shadow-md transition-all duration-200 hover:from-purple-700 hover:to-indigo-700 hover:shadow-lg">
                <i class="fas fa-magic"></i>
                Applica Selezionati
            </button>
        </div>
    </div>
</div>

{{-- JavaScript for Trait Proposals --}}
@push('scripts')
    <script>
        /**
         * Approve all proposals
         */
        function handleApproveAll(generationId) {
            document.querySelectorAll(`#ai-traits-proposals-${generationId} .proposal-checkbox`).forEach(checkbox => {
                checkbox.checked = true;
            });

            Swal.fire({
                title: 'Tutti Approvati!',
                text: 'Tutti i traits sono stati selezionati. Clicca "Applica Selezionati" per confermare.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#9333ea',
                timer: 2000,
            });
        }

        /**
         * Reject all proposals
         */
        function handleRejectAll(generationId) {
            document.querySelectorAll(`#ai-traits-proposals-${generationId} .proposal-checkbox`).forEach(checkbox => {
                checkbox.checked = false;
            });

            Swal.fire({
                title: 'Tutti Deselezionati',
                text: 'Nessun trait sarà applicato.',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6b7280',
                timer: 2000,
            });
        }

        /**
         * Apply selected proposals
         */
        async function handleApplySelected(generationId) {
            const checkboxes = document.querySelectorAll(`#ai-traits-proposals-${generationId} .proposal-checkbox`);
            const decisions = [];

            checkboxes.forEach(checkbox => {
                const proposalId = checkbox.getAttribute('data-proposal-id');
                const action = checkbox.checked ? 'approved' : 'rejected';
                decisions.push({
                    proposal_id: parseInt(proposalId),
                    action: action
                });
            });

            const approvedCount = decisions.filter(d => d.action === 'approved').length;
            const rejectedCount = decisions.filter(d => d.action === 'rejected').length;

            if (approvedCount === 0) {
                Swal.fire({
                    title: 'Nessun Trait Selezionato',
                    text: 'Devi approvare almeno un trait per procedere.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b',
                });
                return;
            }

            // Confirm
            const result = await Swal.fire({
                title: 'Applica Traits?',
                html: `<p>Stai per applicare <strong>${approvedCount} traits</strong> al tuo EGI.</p>
                       ${rejectedCount > 0 ? `<p class="text-sm text-gray-600 mt-2">(${rejectedCount} traits rifiutati)</p>` : ''}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Applica',
                cancelButtonText: 'Annulla',
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#6b7280',
            });

            if (!result.isConfirmed) return;

            // Show loading
            Swal.fire({
                title: 'Applicando Traits...',
                html: '<p>Creazione traits in corso...</p>',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Step 1: Review proposals
                const reviewResponse = await fetch(`/traits/generations/${generationId}/review`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        decisions: decisions
                    }),
                });

                const reviewData = await reviewResponse.json();

                if (!reviewData.success) {
                    throw new Error(reviewData.message || 'Errore durante la review');
                }

                // Step 2: Apply traits
                const applyResponse = await fetch(`/traits/generations/${generationId}/apply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const applyData = await applyResponse.json();

                if (applyData.success) {
                    await Swal.fire({
                        title: 'Traits Applicati!',
                        html: `<p>${applyData.message}</p><p class="text-sm text-gray-600 mt-2">${applyData.data.created_traits_count} traits creati con successo!</p>`,
                        icon: 'success',
                        confirmButtonText: 'Ricarica Pagina',
                        confirmButtonColor: '#10b981',
                    });

                    // Reload to show new traits
                    window.location.reload();
                } else {
                    throw new Error(applyData.message || 'Errore durante l\'applicazione');
                }
            } catch (error) {
                console.error('Apply Traits Error:', error);

                Swal.fire({
                    title: 'Errore',
                    text: error.message || 'Si è verificato un errore durante l\'applicazione dei traits.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                });
            }
        }
    </script>
@endpush



