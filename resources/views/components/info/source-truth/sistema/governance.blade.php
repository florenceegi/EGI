{{--
    Componente: Governance Duale
    Sezione: Sistema
    Descrizione: Equilibrio tra impresa e missione attraverso due entità complementari
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-scale class="w-5 h-5 text-amber-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Governance Duale
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Equilibrio tra impresa e missione attraverso due entità complementari.
    </p>

    <!-- Titolo con visualizzazione -->
    <div class="mb-8 text-center">
        <h4 class="text-2xl font-bold text-gray-800 mb-4">
            Struttura di <a href="#glossario" class="text-emerald-600 hover:underline">Governance Duale</a>
        </h4>
        <div class="flex justify-center items-center gap-4">
            <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center">
                <span class="text-3xl">🏢</span>
            </div>
            <div class="text-4xl text-gray-400">⚖️</div>
            <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center">
                <span class="text-3xl">🏛️</span>
            </div>
        </div>
    </div>

    <!-- Due entità -->
    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <!-- FlorenceEGI SRL -->
        <div class="p-6 border-2 rounded-lg border-emerald-500 bg-emerald-50">
            <h4 class="mb-3 text-xl font-bold text-emerald-800 flex items-center gap-2">
                <x-heroicon-o-building-office-2 class="w-6 h-6 text-emerald-600" />
                FlorenceEGI SRL
            </h4>
            <p class="mb-4 text-gray-700 font-semibold">Motore Operativo e Commerciale</p>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>Sviluppo tecnologico</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>Partnership strategiche</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>Marketing e revenue</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>Crescita scalabile</span>
                </li>
            </ul>
            <div class="mt-4 p-3 bg-emerald-100 rounded-lg text-center">
                <span class="text-sm font-medium text-emerald-800">Focus: Sostenibilità economica</span>
            </div>
        </div>

        <!-- Associazione Frangette -->
        <div class="p-6 border-2 border-blue-500 rounded-lg bg-blue-50">
            <h4 class="mb-3 text-xl font-bold text-blue-800 flex items-center gap-2">
                <x-heroicon-o-heart class="w-6 h-6 text-blue-600" />
                Associazione Frangette APS
            </h4>
            <p class="mb-4 text-gray-700 font-semibold">Custode dei Valori e dell'Etica</p>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Vigila sui principi fondativi</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Tutela destinazione 20% <a href="#glossario" class="text-blue-600 hover:underline font-medium">EPP</a></span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Garantisce coerenza artistico-sociale</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Protegge la missione</span>
                </li>
            </ul>
            <div class="mt-4 p-3 bg-blue-100 rounded-lg text-center">
                <span class="text-sm font-medium text-blue-800">Focus: Sostenibilità valoriale</span>
            </div>
        </div>
    </div>

    <!-- Come funziona -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-purple-500" />
            Come Funziona
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">1</div>
                    <div>
                        <div class="font-medium text-purple-900">Decisioni Operative</div>
                        <p class="text-sm text-gray-600">FlorenceEGI SRL gestisce autonomamente tecnologia, marketing, partnership</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">2</div>
                    <div>
                        <div class="font-medium text-purple-900">Decisioni Valoriali</div>
                        <p class="text-sm text-gray-600">Frangette APS ha diritto di veto su scelte che impattano la missione</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">3</div>
                    <div>
                        <div class="font-medium text-purple-900">Verifica EPP</div>
                        <p class="text-sm text-gray-600">L'Associazione certifica che il 20% vada effettivamente a progetti ambientali</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Perché funziona -->
    <div class="p-6 rounded-lg bg-gray-50 border-l-4 border-gray-400">
        <h5 class="font-medium text-gray-900 mb-3">Perché Questo Modello Funziona</h5>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="p-3 bg-white rounded-lg">
                <div class="text-2xl mb-2">⚖️</div>
                <div class="font-medium text-gray-800">Equilibrio</div>
                <p class="text-xs text-gray-500">Profitto e scopo coesistono</p>
            </div>
            <div class="p-3 bg-white rounded-lg">
                <div class="text-2xl mb-2">🔒</div>
                <div class="font-medium text-gray-800">Protezione</div>
                <p class="text-xs text-gray-500">Missione non sacrificabile</p>
            </div>
            <div class="p-3 bg-white rounded-lg">
                <div class="text-2xl mb-2">🚀</div>
                <div class="font-medium text-gray-800">Crescita</div>
                <p class="text-xs text-gray-500">Impresa può scalare liberamente</p>
            </div>
        </div>
    </div>

    <!-- Citazione -->
    <blockquote class="mt-6 p-4 border-l-4 border-amber-500 rounded-r-lg bg-amber-50">
        <p class="text-lg font-semibold text-gray-800">
            "Questo modello assicura equilibrio tra impresa e missione, tra profitto e scopo."
        </p>
    </blockquote>
</div>
