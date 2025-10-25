{{--
    Component: EGI Unified Mint Panel - Semplificato OS3.0
    Pannello unificato per preparare e mintare EGI
    Linguaggio semplice e chiaro per tutti

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 2.0.0 (FlorenceEGI - Unified Flow)
    @date 2025-10-25
    @purpose Simplified unified panel for EGI preparation and minting

    Props:
    - egi: Egi model instance
    - isCreator: Boolean indicating if current user is the creator
--}}

@props(['egi', 'isCreator'])

@if (!$isCreator)
    @php return; @endphp
@endif

<div class="overflow-hidden rounded-2xl border-2 border-blue-200 bg-white shadow-lg" id="unified-mint-panel-{{ $egi->id }}">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-magic text-2xl text-white"></i>
            <div>
                <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    Prepara e Minta il tuo EGI
                </h3>
                <p class="text-sm text-blue-100">Segui i passi per mettere la tua opera su blockchain</p>
            </div>
        </div>
    </div>

    <div class="space-y-6 p-6">
        {{-- Step 1: Descrizione AI --}}
        <div class="rounded-xl border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-indigo-50 p-5">
            <div class="mb-3 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-600 text-sm font-bold text-white">1</span>
                <h4 class="text-lg font-bold text-purple-900">Scrivi la descrizione</h4>
            </div>
            <p class="mb-4 text-sm text-purple-700">
                Puoi scriverla tu oppure farla creare dall'intelligenza artificiale N.A.T.A.N
            </p>
            <div class="flex gap-2">
                <button type="button" onclick="handleGenerateDescription({{ $egi->id }})"
                    class="flex-1 rounded-lg bg-purple-600 px-4 py-3 text-sm font-semibold text-white shadow transition-all hover:bg-purple-700 hover:shadow-lg">
                    <i class="fas fa-sparkles mr-2"></i>Genera con AI
                </button>
                <button type="button" onclick="handleImproveDescription({{ $egi->id }})"
                    class="flex-1 rounded-lg border-2 border-purple-600 bg-white px-4 py-3 text-sm font-semibold text-purple-600 shadow transition-all hover:bg-purple-50">
                    <i class="fas fa-wand-magic mr-2"></i>Migliora
                </button>
            </div>
        </div>

        {{-- Step 2: Traits (collassabile) --}}
        <details class="group rounded-xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-yellow-50">
            <summary class="cursor-pointer p-5 transition-colors hover:bg-amber-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-600 text-sm font-bold text-white">2</span>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-amber-900">Aggiungi le caratteristiche</h4>
                        <p class="text-sm text-amber-700">Colori, stile, tecnica, emozioni... (clicca per aprire)</p>
                    </div>
                    <svg class="h-5 w-5 text-amber-600 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </summary>
            
            <div class="border-t-2 border-amber-200 p-5">
                <x-egi-ai-traits-panel :egi="$egi" :isCreator="$isCreator" />
            </div>
        </details>

        {{-- Step 3: Mint su Blockchain --}}
        <div class="border-t-2 border-gray-200 pt-6">
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">3</span>
                <h4 class="text-lg font-bold text-gray-900">Scegli come mintare</h4>
            </div>
            <p class="mb-6 text-sm text-gray-600">
                Mettere "su blockchain" significa renderla unica e certificata per sempre
            </p>

            <div class="space-y-4">
                {{-- Opzione 1: EGI Classico (Semplice) --}}
                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                    onsubmit="return handleMintSubmit(event, {{ $egi->id }}, 'ASA');">
                    @csrf
                    <input type="hidden" name="target_type" value="ASA">
                    <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-left text-white shadow-lg transition-all hover:from-blue-700 hover:to-blue-600 hover:shadow-xl">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <i class="fas fa-certificate mt-1 text-3xl"></i>
                                <div>
                                    <div class="text-xl font-bold">EGI Semplice</div>
                                    <div class="mt-1 text-sm text-blue-100">
                                        Come un certificato digitale unico e sicuro
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ Gratis</span>
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ Veloce</span>
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ Per sempre</span>
                                    </div>
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-2xl"></i>
                        </div>
                    </button>
                </form>

                {{-- Opzione 2: EGI Vivente (Intelligente) --}}
                <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                    onsubmit="return handleMintSubmit(event, {{ $egi->id }}, 'SmartContract');">
                    @csrf
                    <input type="hidden" name="target_type" value="SmartContract">
                    <button type="submit"
                        class="w-full rounded-xl border-2 border-purple-400 bg-gradient-to-r from-purple-600 to-purple-500 p-6 text-left text-white shadow-lg transition-all hover:from-purple-700 hover:to-purple-600 hover:shadow-xl">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <i class="fas fa-brain mt-1 text-3xl"></i>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl font-bold">EGI Intelligente</span>
                                        <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-amber-900">PREMIUM</span>
                                    </div>
                                    <div class="mt-1 text-sm text-purple-100">
                                        Con intelligenza artificiale che promuove e valorizza la tua opera
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ AI inclusa</span>
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ Auto-promozione</span>
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">✓ Storico completo</span>
                                    </div>
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-2xl"></i>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex gap-3">
                <i class="fas fa-info-circle mt-0.5 text-blue-600"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold">Cosa significa "mintare"?</p>
                    <p class="mt-1">Significa creare una copia unica della tua opera su blockchain, come un certificato digitale che nessuno può copiare o falsificare.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Vanilla JavaScript for AJAX handling --}}
@push('scripts')
    <script>
        /**
         * Handle Mint Submit (ASA or SmartContract)
         */
        function handleMintSubmit(event, egiId, targetType) {
            event.preventDefault();

            const form = event.target;
            const typeName = targetType === 'ASA' ? 'EGI Semplice' : 'EGI Intelligente';
            const typeDesc = targetType === 'ASA' ? 
                'Verrà creato un certificato digitale unico sulla blockchain' : 
                'Verrà creato un EGI intelligente con AI che promuoverà la tua opera';

            Swal.fire({
                title: `Confermi di mintare come ${typeName}?`,
                html: `<p class="text-sm text-gray-600">${typeDesc}</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: targetType === 'ASA' ? '#2563eb' : '#9333ea',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '✓ Sì, minta!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                processMint(form, targetType);
            });

            return false;
        }

        function processMint(form, targetType) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Minting in corso...';

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
                            title: '🎉 EGI Mintato!',
                            html: '<p>La tua opera è ora sulla blockchain!</p>',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: 'Vai all\'EGI'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: data.message || 'Errore durante il mint',
                            confirmButtonColor: '#ef4444'
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Mint error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore di Connessione',
                        text: 'Impossibile comunicare con il server. Riprova.',
                        confirmButtonColor: '#ef4444'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                });
        }

        /**
         * Handle Generate AI Description
         */
        function handleGenerateDescription(egiId) {
            console.log('[AI Description] Generate button clicked', {
                egiId
            });

            Swal.fire({
                title: 'Generare descrizione con AI?',
                html: '<p class="text-sm text-gray-600">N.A.T.A.N analizzerà la tua opera e creerà una descrizione professionale</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sì, genera!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'N.A.T.A.N al lavoro...',
                        html: 'Sto analizzando la tua opera...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const apiUrl = `/egi/${egiId}/dual-arch/ai/generate-description`;

                    fetch(apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Descrizione Creata!',
                                    html: `<div class="text-left"><p class="mb-3 text-sm text-gray-700">Ecco un'anteprima:</p><div class="rounded-lg bg-gray-100 p-3 text-xs text-gray-800">${data.data.description.substring(0, 200)}...</div></div>`,
                                    confirmButtonColor: '#9333ea',
                                    confirmButtonText: 'Perfetto!'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Errore',
                                    text: data.message || 'Errore durante la generazione',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('AI Description error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Errore',
                                text: 'Impossibile generare la descrizione. Riprova.',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                }
            });
        }

        /**
         * Handle Improve Description
         */
        function handleImproveDescription(egiId) {
            Swal.fire({
                title: 'Migliorare descrizione con AI?',
                html: '<p class="text-sm text-gray-600">N.A.T.A.N analizzerà la descrizione attuale e la migliorerà</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sì, migliora!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'N.A.T.A.N al lavoro...',
                        html: 'Sto migliorando la descrizione...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const apiUrl = `/egi/${egiId}/dual-arch/ai/improve-description`;

                    fetch(apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Descrizione Migliorata!',
                                    html: `<div class="text-left"><p class="mb-3 text-sm text-gray-700">Ecco un'anteprima:</p><div class="rounded-lg bg-gray-100 p-3 text-xs text-gray-800">${data.data.description.substring(0, 200)}...</div></div>`,
                                    confirmButtonColor: '#6366f1',
                                    confirmButtonText: 'Perfetto!'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Errore',
                                    text: data.message || 'Errore durante il miglioramento',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('AI Improve error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Errore',
                                text: 'Impossibile migliorare la descrizione. Riprova.',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                }
            });
        }
    </script>
@endpush

