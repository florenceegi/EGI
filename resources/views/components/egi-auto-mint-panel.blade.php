{{--
    Component: EGI Auto-Mint Panel OS3.0
    Panel for Creator to self-mint their own Pre-Mint EGI

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-21
    @purpose Allow creators to enable and execute auto-minting without Livewire

    Props:
    - egi: Egi model instance
    - isCreator: Boolean indicating if current user is the creator
--}}

@props(['egi', 'isCreator'])

@if (!$isCreator)
    @php return; @endphp
@endif

<div class="overflow-hidden rounded-2xl border-2 border-green-200 bg-white shadow-lg"
    id="auto-mint-panel-{{ $egi->id }}">
    {{-- Header with Verde Rinascita --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-hammer text-2xl text-white"></i>
            <div>
                <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    Auto-Mint Creator
                </h3>
                <p class="text-sm text-green-100">Minta tu stesso la tua opera</p>
            </div>
        </div>
    </div>

    <div class="space-y-6 p-6">
        @if (!$egi->pre_mint_mode)
            {{-- Enable Pre-Mint Mode (Reserve for Creator) --}}
            <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-5">
                <div class="flex items-start gap-4">
                    <i class="fas fa-lightbulb mt-1 text-2xl text-blue-600"></i>
                    <div>
                        <h4 class="mb-2 text-lg font-bold text-blue-900">
                            Vuoi mintare personalmente questo EGI?
                        </h4>
                        <p class="mb-4 text-sm text-blue-800">
                            Abilita la modalità Pre-Mint per riservare a te stesso il mint di questa opera.
                            Potrai scegliere tra EGI Classico o EGI Vivente e diventerai automaticamente il
                            proprietario.
                        </p>
                        <form method="POST" action="{{ route('egi.dual-arch.auto-mint.enable', $egi) }}"
                            onsubmit="return handleAutoMintEnable(event, {{ $egi->id }});">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-6 py-3 font-bold text-white shadow-md transition-all duration-200 hover:from-green-700 hover:to-green-600 hover:shadow-lg">
                                <i class="fas fa-check-circle"></i>
                                Riserva per Auto-Mint
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Pre-Mint Mode Enabled - Show Minting Options --}}
            <div class="space-y-4">
                {{-- Info --}}
                <div class="rounded-xl border-2 border-green-200 bg-green-50 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                        <div>
                            <p class="font-semibold text-green-900">Riservato per Auto-Mint</p>
                            <p class="text-sm text-green-700">Puoi mintare questa opera quando vuoi</p>
                        </div>
                    </div>
                </div>

                {{-- Mint Options --}}
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="mb-4 text-lg font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">
                        Scegli il tipo di mint:
                    </h4>

                    <div class="space-y-3">
                        {{-- Mint as ASA --}}
                        <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                            onsubmit="return handleMintSubmit(event, {{ $egi->id }}, 'ASA');">
                            @csrf
                            <input type="hidden" name="target_type" value="ASA">
                            <button type="submit"
                                class="w-full rounded-xl border-2 border-blue-700 bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-5 text-left font-bold text-white shadow-lg transition-all duration-200 hover:from-blue-950 hover:to-blue-900 hover:shadow-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-shield-check text-3xl"></i>
                                        <div>
                                            <div class="text-lg">EGI Classico (ASA)</div>
                                            <div class="mt-1 text-sm font-normal text-blue-200">
                                                Asset statico su blockchain Algorand
                                            </div>
                                            <div class="mt-2 flex items-center gap-2 text-xs font-normal text-blue-300">
                                                <i class="fas fa-check-circle"></i>
                                                Gratuito • Permanente • Sicuro
                                            </div>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-2xl"></i>
                                </div>
                            </button>
                        </form>

                        {{-- Mint as SmartContract --}}
                        <form method="POST" action="{{ route('egi.dual-arch.pre-mint.promote', $egi) }}"
                            onsubmit="return handleMintSubmit(event, {{ $egi->id }}, 'SmartContract');">
                            @csrf
                            <input type="hidden" name="target_type" value="SmartContract">
                            <button type="submit"
                                class="w-full rounded-xl border-2 border-purple-400 bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-5 text-left font-bold text-white shadow-lg transition-all duration-200 hover:from-purple-800 hover:to-purple-700 hover:shadow-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-brain text-3xl"></i>
                                        <div>
                                            <div class="flex items-center gap-2 text-lg">
                                                EGI Vivente (SmartContract)
                                                <span class="rounded-full bg-amber-500 px-2 py-1 text-xs">PREMIUM</span>
                                            </div>
                                            <div class="mt-1 text-sm font-normal text-purple-200">
                                                Asset intelligente con AI Curator integrata
                                            </div>
                                            <div class="mt-2 space-y-1 text-xs font-normal text-purple-300">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    Analisi AI automatiche
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    Promozione intelligente
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    Memoria evolutiva
                                                </div>
                                            </div>
                                            <div class="mt-3 text-sm font-semibold text-amber-300">
                                                Da
                                                €{{ config('egi_living.subscription_plans.one_time.price_eur', '9.99') }}
                                            </div>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-2xl"></i>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Disable Auto-Mint --}}
                <div class="border-t border-gray-200 pt-4">
                    <form method="POST" action="{{ route('egi.dual-arch.auto-mint.disable', $egi) }}"
                        onsubmit="return handleAutoMintDisable(event, {{ $egi->id }});">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 text-sm text-gray-600 transition-colors duration-200 hover:text-red-600">
                            <i class="fas fa-times-circle"></i>
                            Disabilita Auto-Mint
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Vanilla JavaScript for AJAX handling --}}
@push('scripts')
    <script>
        /**
         * @Oracode JavaScript: Auto-Mint Panel Handlers
         * 🎯 Purpose: Handle auto-mint actions via AJAX without Livewire
         * 🔒 Security: CSRF token included, server-side validation
         * 📡 Communication: Fetch API for async requests
         *
         * @package FlorenceEGI\Frontend\DualArchitecture
         * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
         * @version 1.0.0
         * @date 2025-10-21
         */

        function handleAutoMintEnable(event, egiId) {
            event.preventDefault();

            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Abilitazione...';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success toast and reload
            Swal.fire({
                icon: 'success',
                title: 'Pre-Mint Riservato',
                text: data.message,
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
                            text: data.message || 'Errore durante l\'abilitazione Auto-Mint',
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
                    console.error('Auto-Mint enable error:', error);
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

        function handleAutoMintDisable(event, egiId) {
            event.preventDefault();

            const form = event.target;

        Swal.fire({
            title: 'Disabilita Pre-Mint?',
            text: 'Sei sicuro di voler disabilitare la modalità Pre-Mint? L\'EGI sarà visibile sul marketplace.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, disabilita',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                processAutoMintDisable(form);
            });

            return false;
        }

        function processAutoMintDisable(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Pre-Mint Disabilitato',
                            text: data.message,
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
                            text: data.message || 'Errore durante la disabilitazione',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Auto-Mint disable error:', error);
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
                });

            return false;
        }

        function handleMintSubmit(event, egiId, mintType) {
            event.preventDefault();

            const form = event.target;

            const confirmMsg = mintType === 'ASA' ?
                'Confermi di voler mintare questo EGI come Asset Classico (ASA)?' :
                'Confermi di voler mintare questo EGI Vivente con AI? (Richiede pagamento)';

            Swal.fire({
                title: 'Conferma Mint',
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

                processMint(form, mintType);
            });

            return false;
        }

        function processMint(form, mintType) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Minting in corso...';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        target_type: mintType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Mint Avviato!',
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
                            title: 'Errore Mint',
                            text: data.message || 'Errore durante il mint',
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
                    console.error('Mint error:', error);
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
