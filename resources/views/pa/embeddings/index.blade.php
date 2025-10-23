<x-pa-layout title="Ricerca Semantica - Vector Embeddings">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Ricerca Semantica</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>🧠 Ricerca Semantica AI</x-slot:pageTitle>

    {{-- Subtitle --}}
    <div class="mb-8">
        <p class="text-gray-600">
            Genera vector embeddings per abilitare la ricerca semantica intelligente su tutti gli atti amministrativi.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Acts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Atti Totali</p>
                    <p class="mt-2 text-3xl font-bold text-[#1B365D]">{{ $stats['total_acts'] }}</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3">
                    <svg class="h-8 w-8 text-[#1B365D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- With Embeddings --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Con Embeddings</p>
                    <p class="mt-2 text-3xl font-bold text-[#2D5016]">{{ $stats['with_embeddings'] }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-8 w-8 text-[#2D5016]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Coverage --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Copertura</p>
                    <p class="mt-2 text-3xl font-bold text-[#D4A574]">{{ $stats['coverage_percentage'] }}%</p>
                </div>
                <div class="rounded-full bg-yellow-100 p-3">
                    <svg class="h-8 w-8 text-[#D4A574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Missing Embeddings --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Da Processare</p>
                    <p class="mt-2 text-3xl font-bold text-[#E67E22]">{{ $stats['without_embeddings'] }}</p>
                </div>
                <div class="rounded-full bg-orange-100 p-3">
                    <svg class="h-8 w-8 text-[#E67E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Card --}}
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        <h3 class="mb-4 text-xl font-bold text-[#1B365D]">Genera Embeddings</h3>
        <p class="mb-6 text-gray-600">
            Genera vector embeddings per abilitare la ricerca semantica su N.A.T.A.N. Chat. Gli embeddings permettono
            all'AI di trovare atti rilevanti in base al <strong>significato</strong>, non solo alle parole chiave.
        </p>

        {{-- Cost Info --}}
        <div class="mb-6 rounded-lg bg-blue-50 p-4">
            <div class="flex items-start">
                <svg class="mr-3 mt-0.5 h-5 w-5 text-[#1B365D]" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-[#1B365D]">Costo stimato</p>
                    <p class="mt-1 text-sm text-gray-600">
                        ~${{ $stats['estimated_cost'] }} per processare {{ $stats['without_embeddings'] }} atti
                        rimanenti
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <button id="generate-btn" onclick="generateEmbeddings(100)"
                class="inline-flex items-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white shadow-md transition-all hover:bg-[#C39563] disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span id="btn-text">Genera 100 Embeddings</span>
            </button>

            <button onclick="generateEmbeddings({{ $stats['without_embeddings'] }})"
                class="inline-flex items-center rounded-lg border-2 border-[#1B365D] px-6 py-3 font-semibold text-[#1B365D] transition-all hover:bg-[#1B365D] hover:text-white">
                Genera Tutti ({{ $stats['without_embeddings'] }})
            </button>
        </div>

        {{-- Progress --}}
        <div id="progress-section" class="mt-6 hidden">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Generazione in corso...</span>
                <span id="progress-text" class="text-sm font-medium text-[#D4A574]">0%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                <div id="progress-bar" class="h-full rounded-full bg-[#D4A574] transition-all duration-300"
                    style="width: 0%"></div>
            </div>
            <p id="status-text" class="mt-2 text-sm text-gray-600"></p>
        </div>

        {{-- Result --}}
        <div id="result-section" class="mt-6 hidden rounded-lg p-4"></div>
    </div>

    <script>
        function generateEmbeddings(limit) {
            const btn = document.getElementById('generate-btn');
            const btnText = document.getElementById('btn-text');
            const progressSection = document.getElementById('progress-section');
            const resultSection = document.getElementById('result-section');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const statusText = document.getElementById('status-text');

            // Disable button
            btn.disabled = true;
            btnText.textContent = 'Generazione...';

            // Show progress
            progressSection.classList.remove('hidden');
            resultSection.classList.add('hidden');

            // Simulate progress (fake for now, real progress needs WebSocket)
            let progress = 0;
            const interval = setInterval(() => {
                progress += 5;
                if (progress >= 90) {
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '%';
                statusText.textContent = `Processati ~${Math.floor((progress / 100) * limit)} atti...`;
            }, 500);

            // Call API
            fetch('{{ route('pa.embeddings.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        limit: limit
                    })
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(interval);
                    progressBar.style.width = '100%';
                    progressText.textContent = '100%';

                    if (data.success) {
                        resultSection.className = 'mt-6 rounded-lg bg-green-50 p-4 border border-green-200';
                        resultSection.innerHTML = `
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-green-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-green-800">✅ Embeddings generati con successo!</p>
                                <p class="mt-1 text-sm text-green-700">
                                    ${data.data.success} atti processati, ${data.data.failed} errori
                                </p>
                                <button onclick="location.reload()" class="mt-3 text-sm font-medium text-green-600 hover:text-green-800">
                                    🔄 Ricarica pagina
                                </button>
                            </div>
                        </div>
                    `;
                    } else {
                        resultSection.className = 'mt-6 rounded-lg bg-red-50 p-4 border border-red-200';
                        resultSection.innerHTML = `
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-red-800">❌ Errore durante la generazione</p>
                                <p class="mt-1 text-sm text-red-700">${data.error}</p>
                            </div>
                        </div>
                    `;
                    }

                    resultSection.classList.remove('hidden');
                    btn.disabled = false;
                    btnText.textContent = 'Genera 100 Embeddings';
                })
                .catch(error => {
                    clearInterval(interval);
                    resultSection.className = 'mt-6 rounded-lg bg-red-50 p-4 border border-red-200';
                    resultSection.innerHTML = `
                    <p class="font-semibold text-red-800">❌ Errore di rete</p>
                    <p class="mt-1 text-sm text-red-700">${error.message}</p>
                `;
                    resultSection.classList.remove('hidden');
                    btn.disabled = false;
                    btnText.textContent = 'Genera 100 Embeddings';
                });
        }
    </script>
</x-pa-layout>
