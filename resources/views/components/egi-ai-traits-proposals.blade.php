{{--
    Component: EGI AI Traits Proposals Review - MODAL VERSION
    Modal for reviewing and approving AI-generated trait proposals

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 2.0.0 (FlorenceEGI - AI Traits Generation Modal)
    @date 2025-11-04

    Props:
    - generation: AiTraitGeneration model instance with proposals
    - isOpen: boolean (default false) - controlla apertura modale
--}}

@props(['generation', 'isOpen' => false])

{{-- Trigger Button per aprire la modale --}}
<button type="button"
    onclick="openAiTraitsModal{{ $generation->id }}()"
    class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
    <i class="fas fa-stars text-lg"></i>
    <span>{{ __('egi_dual_arch.ai.review_proposals_modal') }} ({{ $generation->proposals->count() }} traits)</span>
</button>

{{-- Modal Overlay --}}
<div id="ai-traits-modal-{{ $generation->id }}" 
    class="fixed inset-0 z-[10000] {{ $isOpen ? 'flex' : 'hidden' }} items-center justify-center bg-black/70 backdrop-blur-sm p-3 md:p-4"
    style="display: {{ $isOpen ? 'flex' : 'none' }};"
    onclick="if(event.target === this) closeAiTraitsModal{{ $generation->id }}()">
    
    {{-- Modal Content --}}
    <div class="relative w-full max-w-4xl max-h-[95vh] bg-white rounded-2xl shadow-2xl flex flex-col"
        onclick="event.stopPropagation()"
        id="ai-traits-proposals-{{ $generation->id }}">
        {{-- Header - Fixed --}}
        <div class="flex-shrink-0 bg-gradient-to-r from-indigo-700 to-purple-600 px-4 py-3 md:px-6 md:py-4 rounded-t-2xl">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 md:gap-3 min-w-0">
                    <i class="fas fa-stars text-xl md:text-2xl text-white flex-shrink-0"></i>
                    <div class="min-w-0">
                        <h3 class="text-base md:text-xl font-bold text-white truncate" style="font-family: 'Playfair Display', serif;">
                            {{ __('egi_dual_arch.ai.proposals_modal_title') }}
                        </h3>
                        <p class="text-xs md:text-sm text-indigo-100">
                            N.A.T.A.N: {{ $generation->proposals->count() }} traits ({{ $generation->total_confidence }}%)
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="hidden md:inline-block rounded-full bg-white/20 px-3 py-1 text-sm font-semibold text-white">
                        #{{ $generation->id }}
                    </span>
                    <button type="button"
                        onclick="closeAiTraitsModal{{ $generation->id }}()"
                        class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors duration-200"
                        title="{{ __('egi_dual_arch.ai.close_modal') }}">
                        <i class="fas fa-times text-lg md:text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Body - Scrollable --}}
        <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
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

        </div>{{-- Fine body scrollable --}}

        {{-- Footer - Sticky con tutti i bottoni visibili --}}
        <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50 p-3 md:p-4 rounded-b-2xl sticky bottom-0">
            <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                <button onclick="handleApproveAll{{ $generation->id }}()"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-3 py-2.5 md:px-4 md:py-3 font-bold text-sm md:text-base text-white shadow-md transition-all duration-200 hover:from-green-700 hover:to-green-600 hover:shadow-lg">
                    <i class="fas fa-check-double"></i>
                    <span class="hidden sm:inline">{{ __('egi_dual_arch.ai.approve_all') }}</span>
                    <span class="sm:hidden">{{ __('egi_dual_arch.ai.approve') }}</span>
                </button>

                <button onclick="handleRejectAll{{ $generation->id }}()"
                    class="flex items-center justify-center gap-2 rounded-lg border-2 border-red-300 bg-white px-3 py-2.5 md:px-4 md:py-3 font-bold text-sm md:text-base text-red-600 transition-all duration-200 hover:bg-red-50">
                    <i class="fas fa-times-circle"></i>
                    <span class="hidden sm:inline">{{ __('egi_dual_arch.ai.reject_all') }}</span>
                    <span class="sm:hidden">{{ __('egi_dual_arch.ai.reject') }}</span>
                </button>

                <button onclick="handleApplySelected{{ $generation->id }}()"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 px-3 py-2.5 md:px-4 md:py-3 font-bold text-sm md:text-base text-white shadow-md transition-all duration-200 hover:from-purple-700 hover:to-indigo-700 hover:shadow-lg">
                    <i class="fas fa-magic"></i>
                    <span>{{ __('egi_dual_arch.ai.apply_selected') }}</span>
                </button>
            </div>
        </div>
    </div>{{-- Fine modal content --}}
</div>{{-- Fine modal overlay --}}

{{-- JavaScript for Trait Proposals Modal - Inline per garantire caricamento immediato --}}
<script>
        /**
         * Open AI Traits Modal
         */
        function openAiTraitsModal{{ $generation->id }}() {
            const modal = document.getElementById('ai-traits-modal-{{ $generation->id }}');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent body scroll
            }
        }

        /**
         * Close AI Traits Modal
         */
        function closeAiTraitsModal{{ $generation->id }}() {
            const modal = document.getElementById('ai-traits-modal-{{ $generation->id }}');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Restore body scroll
            }
        }

        /**
         * Close modal on ESC key
         */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('ai-traits-modal-{{ $generation->id }}');
                if (modal && modal.style.display === 'flex') {
                    closeAiTraitsModal{{ $generation->id }}();
                }
            }
        });

        /**
         * Approve all proposals
         * Definita come funzione globale per accessibilità dagli onclick
         */
        window.handleApproveAll{{ $generation->id }} = function() {
            const generationId = {{ $generation->id }};
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
        };

        /**
         * Reject all proposals
         * Definita come funzione globale per accessibilità dagli onclick
         */
        window.handleRejectAll{{ $generation->id }} = function() {
            const generationId = {{ $generation->id }};
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
        };

        /**
         * Apply selected proposals
         * Definita come funzione globale asincrona per accessibilità dagli onclick
         */
        window.handleApplySelected{{ $generation->id }} = async function() {
            const generationId = {{ $generation->id }};
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
                    // Close modal first
                    closeAiTraitsModal{{ $generation->id }}();
                    
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
        };
</script>











