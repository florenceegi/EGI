{{-- Componente: Ripartizione delle Responsabilità --}}
{{-- Grafico e descrizione della divisione delle responsabilità fiscali --}}

<div class="mt-8">
    <h3 class="mb-4 text-center text-2xl font-bold text-emerald-700">Ripartizione delle Responsabilità</h3>

    <p class="mb-4 text-center text-gray-600">
        Il grafico illustra come la responsabilità fiscale sia principalmente in capo all'utente,
        mentre la piattaforma fornisce gli strumenti per la
        <a href="#glossary-compliance" class="glossary-link">compliance</a>.
    </p>

    {{-- Chart Container --}}
    <div class="chart-container mx-auto" style="max-width: 400px; height: 300px;">
        <canvas id="responsibilityChart"></canvas>
    </div>

    {{-- Legenda esplicativa --}}
    <div class="mx-auto mt-6 grid max-w-2xl gap-4 md:grid-cols-2">
        {{-- Responsabilità Utente --}}
        <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
            <h4 class="font-semibold text-blue-800">Responsabilità Utente (70%)</h4>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-700">
                <li>Dichiarazione dei redditi</li>
                <li>Emissione fatture/ricevute</li>
                <li>Versamento imposte dovute</li>
                <li>Conservazione documentazione</li>
            </ul>
        </div>

        {{-- Supporto Piattaforma --}}
        <div class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4">
            <h4 class="font-semibold text-emerald-800">Supporto Piattaforma (30%)</h4>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-700">
                <li>Report automatici</li>
                <li>Tracciabilità transazioni</li>
                <li>Alert soglie fiscali</li>
                <li>Export dati contabili</li>
            </ul>
        </div>
    </div>

    {{-- Nota importante --}}
    <div class="mx-auto mt-6 max-w-2xl rounded-lg border-l-4 border-amber-500 bg-amber-50 p-4">
        <h4 class="font-semibold text-amber-800">⚠️ Nota Importante</h4>
        <p class="mt-1 text-sm text-amber-700">
            FlorenceEGI <strong>non è un sostituto d'imposta</strong> e non fornisce consulenza fiscale.
            Gli strumenti messi a disposizione hanno lo scopo di facilitare la
            <a href="#glossary-compliance" class="glossary-link">compliance</a> dell'utente,
            che rimane l'unico responsabile dei propri adempimenti tributari.
        </p>
    </div>
</div>

{{-- Script per il grafico (Chart.js) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('responsibilityChart');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Responsabilità Utente', 'Supporto Piattaforma'],
                    datasets: [{
                        data: [70, 30],
                        backgroundColor: ['#3B82F6', '#10B981'],
                        borderColor: ['#2563EB', '#059669'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
