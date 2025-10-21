{{--
    Component: EGI Pre-Mint Panel OS3.0
    Panel for Pre-Mint EGI with AI analysis and promotion options

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-21
    @purpose Display Pre-Mint status and actions without Livewire

    Props:
    - egi: Egi model instance
--}}

@props(['egi'])

@php
    $daysRemaining = $egi->pre_mint_created_at
        ? config('egi_living.pre_mint.max_duration_days', 30) - now()->diffInDays($egi->pre_mint_created_at)
        : null;
@endphp

<div class="overflow-hidden rounded-2xl border-2 border-amber-200 bg-white shadow-lg">
    {{-- Header with Oro Fiorentino gradient --}}
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-seedling text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        EGI Pre-Mint
                    </h3>
                    <p class="text-sm text-amber-100">Asset virtuale in crescita</p>
                </div>
            </div>

            @if ($daysRemaining !== null && $daysRemaining > 0)
                <span
                    class="flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-sm font-semibold text-white backdrop-blur-sm">
                    <i class="fas fa-clock"></i>
                    {{ $daysRemaining }} giorni
                </span>
            @endif
        </div>
    </div>

    <div class="space-y-6 p-6">
        {{-- Info Box --}}
        <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle mt-1 text-xl text-blue-600"></i>
                <div class="text-sm text-blue-900">
                    <p class="mb-2 font-semibold">Cos'è il Pre-Mint?</p>
                    <p class="text-blue-800">
                        Il tuo EGI è in modalità virtuale. Puoi testarlo, promuoverlo e far analizzare
                        i metadati dall'AI prima di mintarlo sulla blockchain.
                    </p>
                </div>
            </div>
        </div>

        {{-- Status Grid --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Creato il --}}
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Creato il</span>
                    <i class="fas fa-calendar text-gray-500"></i>
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $egi->pre_mint_created_at?->format('d/m/Y H:i') ?? $egi->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- Tempo rimanente --}}
            @if ($daysRemaining !== null)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-amber-800">Scadenza</span>
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                    </div>
                    <div class="{{ $daysRemaining <= 7 ? 'text-red-600' : 'text-amber-900' }} text-lg font-semibold">
                        @if ($daysRemaining <= 0)
                            Scaduto
                        @elseif($daysRemaining <= 7)
                            <span class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $daysRemaining }} giorni
                            </span>
                        @else
                            {{ $daysRemaining }} giorni
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- AI Analysis Section --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                <i class="fas fa-robot text-purple-600"></i>
                Analisi AI N.A.T.A.N
            </h4>

            <div class="space-y-3">
                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.request-analysis', $egi) }}"
                    onsubmit="return handleAIAnalysisRequest(event, {{ $egi->id }}, 'description');">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-lg border border-purple-200 bg-gradient-to-r from-purple-100 to-purple-50 px-4 py-3 font-medium text-purple-900 transition-all duration-200 hover:from-purple-200 hover:to-purple-100">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-file-alt"></i>
                            Genera Descrizione AI
                        </span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.request-analysis', $egi) }}"
                    onsubmit="return handleAIAnalysisRequest(event, {{ $egi->id }}, 'traits');">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-lg border border-blue-200 bg-gradient-to-r from-blue-100 to-blue-50 px-4 py-3 font-medium text-blue-900 transition-all duration-200 hover:from-blue-200 hover:to-blue-100">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-tags"></i>
                            Estrai Traits con AI
                        </span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.request-analysis', $egi) }}"
                    onsubmit="return handleAIAnalysisRequest(event, {{ $egi->id }}, 'promotion');">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-lg border border-green-200 bg-gradient-to-r from-green-100 to-green-50 px-4 py-3 font-medium text-green-900 transition-all duration-200 hover:from-green-200 hover:to-green-100">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-bullhorn"></i>
                            Strategia Promozione AI
                        </span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Promotion Actions --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="mb-4 text-lg font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">
                Pronto per il mint su blockchain?
            </h4>

            <div class="space-y-3">
                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                    onsubmit="return handlePromoteSubmit(event, {{ $egi->id }}, 'ASA');">
                    @csrf
                    <input type="hidden" name="target_type" value="ASA">
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-xl bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-4 font-bold text-white shadow-lg transition-all duration-200 hover:from-blue-950 hover:to-blue-900 hover:shadow-xl">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-shield-check text-xl"></i>
                            <div class="text-left">
                                <div>Promuovi a EGI Classico</div>
                                <div class="text-xs font-normal text-blue-200">Asset statico su blockchain</div>
                            </div>
                        </span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                    onsubmit="return handlePromoteSubmit(event, {{ $egi->id }}, 'SmartContract');">
                    @csrf
                    <input type="hidden" name="target_type" value="SmartContract">
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-xl border-2 border-purple-400 bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 font-bold text-white shadow-lg transition-all duration-200 hover:from-purple-800 hover:to-purple-700 hover:shadow-xl">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-brain text-xl"></i>
                            <div class="text-left">
                                <div class="flex items-center gap-2">
                                    Promuovi a EGI Vivente
                                    <span class="rounded-full bg-amber-500 px-2 py-0.5 text-xs">PREMIUM</span>
                                </div>
                                <div class="text-xs font-normal text-purple-200">Con AI Curator e funzioni intelligenti
                                </div>
                            </div>
                        </span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Vanilla JavaScript for AJAX handling --}}
@push('scripts')
    <script>
        /**
         * @Oracode JavaScript: Pre-Mint Panel Handlers
         * 🎯 Purpose: Handle AI analysis and promotion via AJAX
         * 🔒 Security: CSRF token, server-side validation
         * 📡 Communication: Fetch API for async requests
         *
         * @package FlorenceEGI\Frontend\DualArchitecture
         * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
         * @version 1.0.0
         * @date 2025-10-21
         */

        function handleAIAnalysisRequest(event, egiId, analysisType) {
            event.preventDefault();

            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analisi in corso...';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        analysis_type: analysisType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Analisi AI Richiesta',
                            text: 'N.A.T.A.N elaborerà i tuoi dati a breve.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: data.message || 'Errore durante la richiesta di analisi',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('AI Analysis request error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore di Connessione',
                        text: 'Impossibile comunicare con il server. Riprova.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                });

            return false;
        }

        function handlePromoteSubmit(event, egiId, targetType) {
            event.preventDefault();

            const form = event.target;

            const confirmMsg = targetType === 'ASA' ?
                'Confermi di voler promuovere questo Pre-Mint EGI a Asset Classico (ASA)?' :
                'Confermi di voler promuovere questo Pre-Mint EGI a EGI Vivente con AI? (Richiede pagamento)';

            Swal.fire({
                title: 'Conferma Promozione',
                text: confirmMsg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Conferma',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                processPromotion(form, targetType);
            });

            return false;
        }

        function processPromotion(form, targetType) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Promozione in corso...';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        target_type: targetType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Promozione Avviata!',
                            text: 'La transazione blockchain è in elaborazione.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore Promozione',
                            text: data.message || 'Errore durante la promozione',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Promotion error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore di Connessione',
                        text: 'Impossibile comunicare con il server. Riprova.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                });
        }
    </script>
@endpush
