<x-pa-layout noHero="true">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Header - Ottimizzato per mobile --}}
            <div class="mb-4 sm:mb-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <a href="{{ route('pa.acts.index') }}"
                            class="rounded-lg bg-white p-2 shadow-sm transition-all hover:shadow-md">
                            <span class="material-icons text-[#1B365D]">arrow_back</span>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-[#1B365D] sm:text-3xl">N.A.T.A.N. Chat AI</h1>
                            <p class="text-xs text-gray-600 sm:text-sm">Assistente intelligente per i tuoi atti</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 rounded-lg bg-green-100 px-3 py-1.5 sm:px-4 sm:py-2">
                        <span class="material-icons text-xs text-green-600 sm:text-sm">check_circle</span>
                        <span class="text-xs font-medium text-green-800 sm:text-sm">AI Attiva</span>
                    </div>
                </div>
            </div>

            {{-- Two Column Layout: Stack on mobile, side-by-side on desktop --}}
            <div class="grid gap-6 lg:grid-cols-12">

                {{-- Left Column: Chat (8/12 width on desktop, full width on mobile) --}}
                <div class="lg:col-span-8">
                    {{-- Chat Window --}}
                    <div class="rounded-2xl bg-white shadow-xl">

                        {{-- Chat Header - Ottimizzato per mobile --}}
                        <div
                            class="rounded-t-2xl border-b border-gray-200 bg-gradient-to-r from-[#1B365D] to-[#2D5016] p-3 sm:p-6">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm sm:h-12 sm:w-12">
                                    <span class="material-icons text-xl text-white sm:text-2xl">smart_toy</span>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-white sm:text-lg">N.A.T.A.N.</h2>
                                    <p class="text-[10px] text-white/80 sm:text-xs">Nodo di Analisi e Tracciamento Atti
                                        Notarizzati</p>
                                </div>
                            </div>
                        </div>

                        {{-- Messages Container - Ottimizzato per mobile --}}
                        <div id="chatMessages"
                            class="h-[400px] space-y-3 overflow-y-auto p-3 sm:h-[600px] sm:space-y-4 sm:p-6">

                            {{-- Welcome Message (will be hidden after first message) - Ottimizzato mobile --}}
                            <div id="welcomeMessage"
                                class="flex h-full flex-col items-center justify-center px-2 text-center">
                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-[#1B365D] to-[#2D5016] sm:mb-6 sm:h-20 sm:w-20">
                                    <span class="material-icons text-3xl text-white sm:text-4xl">smart_toy</span>
                                </div>
                                <h3 class="mb-2 text-lg font-bold text-[#1B365D] sm:text-2xl">Ciao! Sono N.A.T.A.N.</h3>
                                <p class="mb-4 max-w-md text-sm text-gray-600 sm:mb-8 sm:text-base">
                                    Posso aiutarti ad analizzare i tuoi atti amministrativi. Prova a chiedermi qualcosa!
                                </p>

                                {{-- Suggested Questions - Collassabile su mobile --}}
                                <div class="w-full max-w-2xl">
                                    <button id="toggleSuggestedQuestions"
                                        class="mb-2 flex w-full items-center justify-between rounded-lg bg-gray-100 p-2 transition-colors hover:bg-gray-200 sm:hidden">
                                        <div class="flex items-center gap-2">
                                            <span class="material-icons text-sm text-[#2D5016]">auto_awesome</span>
                                            <span class="text-xs font-medium text-gray-700">Domande suggerite</span>
                                            <span
                                                class="flex items-center gap-1 rounded-full bg-yellow-100 px-1.5 py-0.5 text-[10px] font-medium text-yellow-800">
                                                <span class="material-icons text-[10px]">shuffle</span>
                                                Random
                                            </span>
                                        </div>
                                        <span id="toggleIcon"
                                            class="material-icons text-sm text-gray-500">expand_more</span>
                                    </button>

                                    <div id="suggestedQuestionsContent" class="hidden sm:block">
                                        <div
                                            class="mb-2 hidden flex-col gap-1 sm:mb-4 sm:flex sm:flex-row sm:items-center sm:gap-2">
                                            <p class="text-xs font-medium text-gray-700 sm:text-sm">Domande suggerite:
                                            </p>
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                                                    <span class="material-icons text-xs">shuffle</span>
                                                    Random
                                                </span>
                                                <span class="text-[10px] text-gray-400 sm:text-xs">(cambiano ad ogni
                                                    ricarico)</span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 gap-2 sm:gap-3 md:grid-cols-2">
                                            @foreach ($suggested_questions as $question)
                                                <button
                                                    onclick="NatanChat.sendSuggestedMessage('{{ addslashes($question) }}')"
                                                    class="group rounded-lg border-2 border-gray-200 bg-white p-2 text-left text-xs transition-all hover:border-[#2D5016] hover:bg-[#2D5016] hover:text-white hover:shadow-md sm:p-3">
                                                    <span
                                                        class="material-icons mr-1 inline-block text-xs text-[#2D5016] group-hover:text-white sm:mr-2 sm:text-sm">auto_awesome</span>
                                                    <span
                                                        class="line-clamp-3 text-[11px] sm:text-xs">{{ Str::limit($question, 100) }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Persona Selector - Ottimizzato mobile --}}
                        <div class="border-t border-gray-200 p-2 sm:p-4">
                            <x-natan-persona-selector />
                        </div>

                        {{-- Input Area - Ottimizzato mobile --}}
                        <div class="rounded-b-2xl border-t border-gray-200 bg-gray-50 p-2 sm:p-4">
                            {{-- Web Search Toggle ✨ NEW v3.0 --}}
                            <div class="mb-2 flex items-center justify-between rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-2 sm:p-3">
                                <div class="flex items-center gap-2">
                                    <label for="webSearchToggle" class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" id="webSearchToggle"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0" />
                                        <span class="text-xs font-medium text-gray-700 sm:text-sm">{{ __('natan.web_search.toggle_label') }}</span>
                                    </label>
                                    <span class="material-icons cursor-help text-sm text-gray-400 sm:text-base"
                                        title="{{ __('natan.web_search.toggle_hint') }}">info</span>
                                </div>
                                <span id="webSearchStatus"
                                    class="hidden items-center gap-1 text-xs font-medium text-blue-600 sm:flex">
                                    <span class="material-icons text-xs">public</span>
                                    <span>{{ __('natan.web_search.enabled') }}</span>
                                </span>
                            </div>

                            <form id="chatForm" class="flex gap-1.5 sm:gap-2">
                                <textarea id="userInput" rows="1" placeholder="{{ __('natan.chat.input_placeholder') }}"
                                    class="flex-1 resize-none rounded-xl border-2 border-gray-200 px-3 py-2 text-sm focus:border-[#2D5016] focus:outline-none focus:ring-2 focus:ring-[#2D5016]/20 disabled:cursor-not-allowed disabled:bg-gray-100 sm:px-4 sm:py-3 sm:text-base"
                                    style="max-height: 150px; overflow-y: auto;"></textarea>
                                <button type="submit" id="sendBtn"
                                    class="flex items-center gap-1 self-end rounded-xl bg-[#2D5016] px-3 py-2 text-sm font-medium text-white transition-all hover:bg-[#3D6026] disabled:cursor-not-allowed disabled:opacity-50 sm:gap-2 sm:px-6 sm:py-3 sm:text-base">
                                    <span id="sendBtnText" class="hidden sm:inline">{{ __('natan.chat.send_button') }}</span>
                                    <span id="sendBtnLoader" class="hidden items-center gap-1 sm:gap-2">
                                        <svg class="h-3 w-3 animate-spin sm:h-4 sm:w-4" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span class="hidden sm:inline">Invio...</span>
                                    </span>
                                    <span class="material-icons text-lg sm:text-2xl">send</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Info Footer - Ottimizzato mobile --}}
                    <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 p-2 sm:mt-4 sm:p-3">
                        <div class="flex items-start gap-1.5 sm:gap-2">
                            <span class="material-icons text-xs text-blue-600 sm:text-sm">info</span>
                            <div class="text-[10px] text-blue-800 sm:text-xs">
                                <p class="font-medium">N.A.T.A.N. con Claude 3.5 Sonnet</p>
                                <p class="text-blue-600">Sistema multi-persona · GDPR compliant</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- End Left Column --}}

                {{-- Right Column: Strategic Questions (4/12 width) --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-6">

                        {{-- Questions Header --}}
                        <div
                            class="mb-4 rounded-xl border-2 border-[#2D5016] bg-gradient-to-br from-[#1B365D] to-[#2D5016] p-4 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-icons text-3xl">auto_awesome</span>
                                    <div>
                                        <h3 class="text-lg font-bold">Domande Strategiche</h3>
                                        <p class="text-xs text-white/80">Clicca per testare</p>
                                    </div>
                                </div>
                                <div class="rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold text-yellow-900">
                                    28
                                </div>
                            </div>
                        </div>

                        {{-- Questions Scrollable Container --}}
                        <div class="max-h-[calc(100vh-200px)] space-y-3 overflow-y-auto rounded-xl bg-gray-50 p-3">
                            {{-- Strategic Questions --}}
                            <div class="rounded-lg border border-blue-100 bg-blue-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">🎯</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Strategia</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza le principali aree di investimento del Comune negli ultimi 12 mesi e suggerisci una strategia di ottimizzazione basata su ROI e priorità strategiche"
                                        class="question-btn">
                                        <span class="material-icons">trending_up</span>
                                        <span>Ottimizzazione Investimenti & ROI</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica i ritardi nei progetti PNRR e proponi un piano di recovery con milestone specifiche e azioni correttive immediate"
                                        class="question-btn">
                                        <span class="material-icons">schedule</span>
                                        <span>Recovery Plan PNRR</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Crea una matrice decisionale per prioritizzare i progetti in base a impatto, urgenza, costo e fattibilità tecnica"
                                        class="question-btn">
                                        <span class="material-icons">grid_on</span>
                                        <span>Decision Matrix Progetti</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Confronta le performance del Comune con best practices nazionali e internazionali, identificando gap e opportunità di miglioramento"
                                        class="question-btn">
                                        <span class="material-icons">compare</span>
                                        <span>Benchmarking Best Practices</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Technical Questions --}}
                            <div class="rounded-lg border border-red-100 bg-red-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">⚙️</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Tecnico</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Valuta la fattibilità tecnica dei progetti infrastrutturali in corso e identifica rischi critici con strategie di mitigazione"
                                        class="question-btn">
                                        <span class="material-icons">engineering</span>
                                        <span>Feasibility & Risk Assessment</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza lo stato manutentivo delle infrastrutture pubbliche e proponi un piano di manutenzione predittiva basato su priorità"
                                        class="question-btn">
                                        <span class="material-icons">build</span>
                                        <span>Piano Manutenzione Predittiva</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica progetti con problemi di compliance normativa tecnica e proponi azioni correttive con timeline"
                                        class="question-btn">
                                        <span class="material-icons">verified</span>
                                        <span>Compliance Normativa Tecnica</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Valuta le specifiche tecniche degli appalti recenti e suggerisci miglioramenti per future gare"
                                        class="question-btn">
                                        <span class="material-icons">description</span>
                                        <span>Ottimizzazione Specifiche Appalti</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Financial Questions --}}
                            <div class="rounded-lg border border-green-100 bg-green-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">💰</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Finanziario</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza l'efficienza della spesa pubblica per settore, calcola costo per cittadino servito e identifica aree di ottimizzazione"
                                        class="question-btn">
                                        <span class="material-icons">analytics</span>
                                        <span>Efficienza Spesa Pubblica</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica tutte le opportunità di funding EU disponibili (PNRR, PON, FSE) e valuta quali progetti possono candidarsi"
                                        class="question-btn">
                                        <span class="material-icons">euro</span>
                                        <span>Strategia Funding EU</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Crea un modello finanziario NPV/IRR per i progetti a lungo termine e calcola il break-even point"
                                        class="question-btn">
                                        <span class="material-icons">calculate</span>
                                        <span>Financial Modeling NPV/IRR</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza il budget variance degli ultimi 3 anni e proponi strategie per migliorare la previsione finanziaria"
                                        class="question-btn">
                                        <span class="material-icons">account_balance</span>
                                        <span>Budget Variance Analysis</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Legal Questions --}}
                            <div class="rounded-lg border border-purple-100 bg-purple-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">⚖️</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Legale</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Verifica la compliance GDPR di tutti gli atti che trattano dati personali e identifica eventuali violazioni con azioni correttive"
                                        class="question-btn">
                                        <span class="material-icons">privacy_tip</span>
                                        <span>Audit GDPR Compliance</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza i procedimenti amministrativi con rischio contenzioso e proponi strategie di de-risking legale"
                                        class="question-btn">
                                        <span class="material-icons">gavel</span>
                                        <span>Risk Assessment Contenzioso</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica tutti gli atti con problemi di trasparenza o anticorruzione secondo la normativa vigente"
                                        class="question-btn">
                                        <span class="material-icons">policy</span>
                                        <span>Compliance Trasparenza</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Valuta la regolarità delle procedure di gara recenti e suggerisci best practices per future procedure"
                                        class="question-btn">
                                        <span class="material-icons">assignment</span>
                                        <span>Audit Procedure Gara</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Urban & Social Questions --}}
                            <div class="rounded-lg border border-orange-100 bg-orange-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">🏙️</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Urban & Social</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza l'impatto sociale dei progetti di rigenerazione urbana e calcola il SROI (Social Return on Investment)"
                                        class="question-btn">
                                        <span class="material-icons">people</span>
                                        <span>Social Impact & SROI</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica le aree sottoutilizzate della città e proponi strategie di riqualificazione con focus su accessibilità e inclusione"
                                        class="question-btn">
                                        <span class="material-icons">location_city</span>
                                        <span>Strategia Riqualificazione Aree</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Valuta l'equità territoriale nella distribuzione dei servizi pubblici e proponi azioni per ridurre i gap"
                                        class="question-btn">
                                        <span class="material-icons">balance</span>
                                        <span>Equity Analysis Servizi</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Crea un piano di partecipazione cittadina per i prossimi progetti urbani con metodologie innovative"
                                        class="question-btn">
                                        <span class="material-icons">forum</span>
                                        <span>Piano Partecipazione Cittadina</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Communication Questions --}}
                            <div class="rounded-lg border border-pink-100 bg-pink-50 p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xl">📢</span>
                                    <h4 class="text-sm font-bold text-[#1B365D]">Comunicazione</h4>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Crea una strategia di comunicazione per annunciare i risultati dei progetti PNRR con key messages e piano media"
                                        class="question-btn">
                                        <span class="material-icons">campaign</span>
                                        <span>Strategia Comunicazione PNRR</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica i progetti con maggior potenziale mediatico e sviluppa storytelling efficace per massimizzare l'impatto"
                                        class="question-btn">
                                        <span class="material-icons">auto_stories</span>
                                        <span>Storytelling Progetti High-Impact</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza il sentiment pubblico sui progetti in corso e proponi strategie di engagement per aumentare il supporto"
                                        class="question-btn">
                                        <span class="material-icons">sentiment_satisfied</span>
                                        <span>Sentiment Analysis & Engagement</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Sviluppa un piano di crisis communication per gestire eventuali controversie su progetti sensibili"
                                        class="question-btn">
                                        <span class="material-icons">crisis_alert</span>
                                        <span>Crisis Communication Plan</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Power Questions (Wow Factor) --}}
                            <div
                                class="rounded-lg border-2 border-[#2D5016] bg-gradient-to-r from-[#2D5016]/5 to-[#1B365D]/5 p-3">
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">⚡</span>
                                        <h4 class="text-sm font-bold text-[#1B365D]">Power Questions</h4>
                                    </div>
                                    <span
                                        class="rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900">WOW</span>
                                </div>
                                <div class="space-y-2">
                                    <button onclick="askQuestion(this)"
                                        data-question="Crea una dashboard strategica per il Sindaco con i 10 KPI più critici della città, analisi trend e early warning systems"
                                        class="question-btn border-2 border-[#2D5016] bg-white font-semibold">
                                        <span class="material-icons">dashboard</span>
                                        <span>Executive Dashboard Sindaco</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Identifica le 3 azioni quick-win con massimo impatto politico e minimo costo, con timeline 60 giorni e piano esecutivo dettagliato"
                                        class="question-btn border-2 border-[#2D5016] bg-white font-semibold">
                                        <span class="material-icons">bolt</span>
                                        <span>Top 3 Quick Wins Strategiche</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Analizza tutti i progetti e crea una roadmap strategica 2024-2026 con prioritization matrix, dependencies e critical path"
                                        class="question-btn border-2 border-[#2D5016] bg-white font-semibold">
                                        <span class="material-icons">map</span>
                                        <span>Strategic Roadmap 2024-2026</span>
                                    </button>
                                    <button onclick="askQuestion(this)"
                                        data-question="Simula 3 scenari futuri (ottimistico, realistico, pessimistico) per il portfolio progetti e proponi strategie di adattamento per ciascuno"
                                        class="question-btn border-2 border-[#2D5016] bg-white font-semibold">
                                        <span class="material-icons">query_stats</span>
                                        <span>Scenario Planning Avanzato</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- End Questions Scrollable Container --}}
                    </div>
                </div>
                {{-- End Right Column --}}

            </div>
            {{-- End Two Column Grid --}}

            {{-- Free Chat with AI Section --}}
            <div class="mt-8">
                <div class="rounded-2xl bg-white shadow-xl">
                    {{-- Free Chat Header --}}
                    <div
                        class="rounded-t-2xl border-b border-gray-200 bg-gradient-to-r from-purple-600 to-pink-500 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                                    <span class="material-icons text-2xl text-white">chat</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-white">💬 Chat Libera con AI</h2>
                                    <p class="text-xs text-white/80">Parla liberamente con Claude · Senza limiti sugli
                                        atti
                                    </p>
                                </div>
                            </div>
                            <div class="rounded-lg bg-white/20 px-3 py-2 backdrop-blur-sm">
                                <p class="text-xs font-medium text-white">Nessun RAG · Consulenza generale</p>
                            </div>
                        </div>
                    </div>

                    {{-- Free Chat Messages Container --}}
                    <div id="freeChatMessages" class="h-[400px] space-y-4 overflow-y-auto p-6">
                        {{-- Welcome Message --}}
                        <div id="freeChatWelcome"
                            class="flex h-full flex-col items-center justify-center text-center">
                            <div
                                class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-500">
                                <span class="material-icons text-3xl text-white">forum</span>
                            </div>
                            <h3 class="mb-2 text-xl font-bold text-purple-900">Chiacchiera liberamente con Claude</h3>
                            <p class="mb-4 max-w-md text-sm text-gray-600">
                                Qui puoi fare <strong>qualsiasi domanda</strong> senza limitarti agli atti.
                                Brainstorming,
                                spiegazioni di concetti, consigli strategici, revisione testi...
                            </p>
                            <div class="flex flex-wrap justify-center gap-2">
                                <span
                                    class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800">Brainstorming</span>
                                <span
                                    class="rounded-full bg-pink-100 px-3 py-1 text-xs font-medium text-pink-800">Spiegazioni</span>
                                <span
                                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">Revisioni</span>
                                <span
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Consigli</span>
                            </div>
                        </div>
                    </div>

                    {{-- Free Chat Input --}}
                    <div class="border-t border-gray-200 bg-gray-50 p-4">
                        <form id="freeChatForm" class="flex gap-3">
                            <textarea id="freeChatInput" rows="1" placeholder="Chiedi qualsiasi cosa a Claude... (Shift+Enter per inviare)"
                                class="flex-1 resize-none rounded-xl border-2 border-gray-300 px-4 py-3 text-sm focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200"
                                style="max-height: 200px; overflow-y: auto;"></textarea>
                            <button type="submit" id="freeChatSendBtn"
                                class="flex items-center gap-2 self-end rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 px-6 py-3 font-medium text-white transition-all hover:shadow-lg disabled:opacity-50">
                                <span id="freeChatSendBtnText">Invia</span>
                                <span id="freeChatSendBtnLoader" class="hidden">
                                    <svg class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                                <span class="material-icons">send</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- End Free Chat Section --}}

        </div>
    </div>

    <style>
        .question-btn {
            @apply flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-left text-xs leading-tight text-gray-700 transition-all hover:border-[#2D5016] hover:bg-[#2D5016] hover:text-white hover:shadow-md;
        }

        .question-btn .material-icons {
            @apply text-sm;
            flex-shrink: 0;
        }

        .question-btn:hover .material-icons {
            @apply text-white;
        }

        /* Custom scrollbar for questions panel */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #2D5016;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #1B365D;
        }

        /* Quick Action Buttons */
        .quick-action-btn {
            @apply rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition-all hover:border-[#2D5016] hover:bg-[#2D5016] hover:text-white hover:shadow-md;
        }

        .quick-action-btn:active {
            @apply scale-95;
        }

        /* Elaboration badge */
        .elaboration-badge {
            @apply inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800;
        }
    </style>

    @push('scripts')
        <script>
            /**
             * Ask Question - Insert question into input and submit
             */
            function askQuestion(button) {
                const question = button.getAttribute('data-question');
                const input = document.getElementById('userInput');

                // Scroll to top smoothly
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // Wait for scroll, then set input and submit
                setTimeout(() => {
                    input.value = question;
                    input.focus();

                    // Visual feedback
                    button.classList.add('ring-2', 'ring-[#2D5016]', 'scale-95');
                    setTimeout(() => {
                        button.classList.remove('ring-2', 'ring-[#2D5016]', 'scale-95');
                    }, 200);

                    // Auto-submit after a brief moment
                    setTimeout(() => {
                        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
                    }, 300);
                }, 400);
            }

            /**
             * N.A.T.A.N. Chat - Vanilla JavaScript Implementation
             *
             * NO Alpine.js, NO Livewire, NO jQuery - Pure ES6+ JavaScript
             * Enterprise-grade, readable, debuggable
             */
            const NatanChat = {
                // State
                messages: [],
                isLoading: false,
                conversationHistory: [],

                // DOM Elements (cached)
                elements: {
                    chatMessages: null,
                    welcomeMessage: null,
                    userInput: null,
                    sendBtn: null,
                    sendBtnText: null,
                    sendBtnLoader: null,
                    chatForm: null
                },

                // Configuration
                config: {
                    apiUrl: '{{ route('pa.natan.chat.message') }}',
                    csrfToken: '{{ csrf_token() }}',
                    maxHistoryLength: 10,
                    sessionId: null // Will be set after first message
                },

                /**
                 * Initialize chat
                 */
                init() {
                    console.log('[N.A.T.A.N.] Initializing chat...');

                    // Cache DOM elements
                    this.elements.chatMessages = document.getElementById('chatMessages');
                    this.elements.welcomeMessage = document.getElementById('welcomeMessage');
                    this.elements.userInput = document.getElementById('userInput');
                    this.elements.sendBtn = document.getElementById('sendBtn');
                    this.elements.sendBtnText = document.getElementById('sendBtnText');
                    this.elements.sendBtnLoader = document.getElementById('sendBtnLoader');
                    this.elements.chatForm = document.getElementById('chatForm');

                    // Bind events
                    this.elements.chatForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.handleSubmit();
                    });

                    // Handle Enter key: Shift+Enter to submit, Enter for new line
                    this.elements.userInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && e.shiftKey) {
                            e.preventDefault();
                            this.elements.chatForm.dispatchEvent(new Event('submit'));
                        }
                        // Let normal Enter create a new line (default textarea behavior)
                    });

                    // Auto-resize textarea as user types
                    this.elements.userInput.addEventListener('input', () => {
                        this.autoResizeTextarea(this.elements.userInput);
                    });

                    // Web Search Toggle listener ✨ NEW v3.0
                    const webSearchToggle = document.getElementById('webSearchToggle');
                    const webSearchStatus = document.getElementById('webSearchStatus');
                    if (webSearchToggle && webSearchStatus) {
                        webSearchToggle.addEventListener('change', () => {
                            if (webSearchToggle.checked) {
                                webSearchStatus.classList.remove('hidden');
                                webSearchStatus.classList.add('flex');
                            } else {
                                webSearchStatus.classList.add('hidden');
                                webSearchStatus.classList.remove('flex');
                            }
                        });
                    }

                    // Event delegation for quick action buttons (dynamically created)
                    this.elements.chatMessages.addEventListener('click', (e) => {
                        // Quick actions
                        if (e.target.classList.contains('quick-action-btn')) {
                            const action = e.target.dataset.action;
                            const messageId = parseInt(e.target.dataset.messageId);
                            this.handleElaboration(action, messageId);
                        }

                        // Copy message button
                        const copyBtn = e.target.closest('.copy-message-btn');
                        if (copyBtn) {
                            const content = copyBtn.dataset.content;
                            this.copyToClipboard(content, copyBtn);
                        }

                        // Toggle sources/reference collapse
                        const toggleBtn = e.target.closest('.sources-toggle-btn, .reference-toggle-btn');
                        if (toggleBtn) {
                            const targetId = toggleBtn.dataset.target;
                            const targetDiv = document.getElementById(targetId);
                            const icon = document.querySelector(`[data-icon="${targetId}"]`);

                            if (targetDiv) {
                                targetDiv.classList.toggle('hidden');
                                if (icon) {
                                    icon.style.transform = targetDiv.classList.contains('hidden') ? 'rotate(0deg)' :
                                        'rotate(180deg)';
                                }
                            }
                        }
                    });

                    console.log('[N.A.T.A.N.] Chat initialized successfully');
                },

                /**
                 * Handle form submit
                 */
                async handleSubmit() {
                    const message = this.elements.userInput.value.trim();

                    if (!message || this.isLoading) {
                        return;
                    }

                    // Clear input
                    this.elements.userInput.value = '';

                    // Hide welcome message
                    if (this.elements.welcomeMessage) {
                        this.elements.welcomeMessage.style.display = 'none';
                    }

                    // Add user message
                    this.addMessage('user', message);

                    // Send to API
                    await this.sendToApi(message);
                },

                /**
                 * Send suggested message
                 */
                sendSuggestedMessage(message) {
                    this.elements.userInput.value = message;
                    this.handleSubmit();
                },

                /**
                 * Add message to chat
                 */
                addMessage(role, content, sources = null, persona = null, message_id = null, is_elaboration = false,
                    reference_content = null, web_sources = null) {
                    const timestamp = new Date().toLocaleTimeString('it-IT', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const message = {
                        role,
                        content,
                        sources,
                        web_sources, // NEW v3.0 - External web sources
                        persona, // Persona info for assistant messages
                        message_id, // Database ID for elaborations
                        is_elaboration, // Is this an elaboration of a previous message?
                        reference_content, // Original message that was elaborated (if any)
                        timestamp
                    };

                    this.messages.push(message);
                    this.renderMessage(message);
                    this.scrollToBottom();
                },

                /**
                 * Render single message
                 */
                renderMessage(message) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = message.role === 'user' ? 'flex justify-end' : 'flex justify-start';

                    const bubbleDiv = document.createElement('div');
                    bubbleDiv.className = message.role === 'user' ?
                        'bg-[#2D5016] text-white rounded-2xl rounded-tr-sm max-w-md px-4 py-3 shadow-sm' :
                        'bg-white rounded-2xl rounded-tl-sm max-w-2xl px-4 py-3 shadow-md border border-gray-200';

                    // Persona Badge (for assistant messages)
                    if (message.role === 'assistant' && message.persona) {
                        const personaBadgeDiv = document.createElement('div');
                        personaBadgeDiv.className = 'mb-2 flex flex-wrap items-center gap-2 text-xs';

                        // Get persona data from global window.personasData
                        const personaData = window.personasData ? window.personasData[message.persona.id] : null;
                        const personaIcon = personaData ? personaData.icon : '🎯';
                        const personaColor = personaData ? personaData.color : '#2563eb';

                        personaBadgeDiv.innerHTML = `
                            ${message.is_elaboration ? `
                                                                <span class="elaboration-badge">
                                                                    🔄 Elaborazione
                                                                </span>
                                                            ` : ''}
                            <span style="background-color: ${personaColor};"
                                  class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-medium text-white">
                                <span>${personaIcon}</span>
                                <span>${message.persona.name}</span>
                            </span>
                            ${message.persona.confidence ? `
                                                                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-gray-600" title="Confidenza nella scelta automatica">
                                                                                            ${Math.round(message.persona.confidence * 100)}%
                                                                                        </span>
                                                                                    ` : ''}
                            ${message.persona.method === 'manual' ? `
                                                                                        <span class="rounded bg-blue-100 px-2 py-0.5 text-blue-700" title="Selezione manuale">
                                                                                            ✓ Manuale
                                                                                        </span>
                                                                                    ` : message.persona.method === 'default' ? `
                                                                                        <span class="rounded bg-yellow-100 px-2 py-0.5 text-yellow-700" title="Modalità predefinita">
                                                                                            Auto (Default)
                                                                                        </span>
                                                                                    ` : ''}
                        `;
                        bubbleDiv.appendChild(personaBadgeDiv);

                        // Collapsible Reference Content (for elaborations)
                        if (message.is_elaboration && message.reference_content) {
                            const referenceDiv = document.createElement('div');
                            referenceDiv.className = 'mb-3 rounded-lg border border-purple-200 bg-purple-50';
                            const collapseId = `reference-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                            referenceDiv.innerHTML = `
                                <button class="reference-toggle-btn flex w-full items-center justify-between px-3 py-2 text-left text-xs font-medium text-purple-800 hover:bg-purple-100"
                                        data-target="${collapseId}">
                                    <span>📄 Analisi originale elaborata</span>
                                    <span class="material-icons text-sm transition-transform" data-icon="${collapseId}">expand_more</span>
                                </button>
                                <div id="${collapseId}" class="hidden border-t border-purple-200 px-3 py-2 text-xs text-purple-900">
                                    ${message.reference_content.replace(/\n/g, '<br>').substring(0, 1000)}${message.reference_content.length > 1000 ? '...' : ''}
                                </div>
                            `;
                            bubbleDiv.appendChild(referenceDiv);
                        }
                    }

                    // Message content
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'prose prose-sm max-w-none';
                    contentDiv.style.color = '#1B365D'; // Blu Algoritmo - massimo contrasto
                    contentDiv.innerHTML = this.formatMessage(message.content);
                    bubbleDiv.appendChild(contentDiv);

                    // Sources (for AI messages) - Collapsible
                    if (message.sources && message.sources.length > 0) {
                        const sourcesDiv = document.createElement('div');
                        sourcesDiv.className = 'mt-3 border-t border-gray-200 pt-3';

                        const collapseId = `sources-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                        const sourcesCount = message.sources.length;

                        sourcesDiv.innerHTML = `
                            <button class="sources-toggle-btn mb-2 flex w-full items-center justify-between text-left text-xs font-semibold text-gray-600 hover:text-[#2D5016]"
                                    data-target="${collapseId}">
                                <span>📚 Fonti (${sourcesCount})</span>
                                <span class="material-icons text-sm transition-transform" data-icon="${collapseId}">expand_more</span>
                            </button>
                            <div id="${collapseId}" class="hidden space-y-1">
                                ${message.sources.map(source => `
                                                            <a href="${source.url}" target="_blank"
                                                               class="block rounded border border-gray-200 bg-white p-2 text-xs hover:bg-gray-50">
                                                                <span class="font-medium">${source.protocol_number}</span>
                                                                <span class="text-gray-600"> - </span>
                                                                <span>${source.title}</span>
                                                            </a>
                                                        `).join('')}
                            </div>
                        `;
                        bubbleDiv.appendChild(sourcesDiv);
                    }

                    // Web Sources (for AI messages with web search enabled) ✨ NEW v3.0
                    if (message.web_sources && message.web_sources.length > 0) {
                        const webSourcesDiv = document.createElement('div');
                        webSourcesDiv.className = 'mt-3 border-t border-blue-200 pt-3 bg-blue-50/30 rounded-lg p-2';

                        const collapseId = `web-sources-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                        const webSourcesCount = message.web_sources.length;

                        webSourcesDiv.innerHTML = `
                            <button class="sources-toggle-btn mb-2 flex w-full items-center justify-between text-left text-xs font-semibold text-blue-700 hover:text-blue-900"
                                    data-target="${collapseId}">
                                <span class="flex items-center gap-1">
                                    <span class="material-icons text-sm">public</span>
                                    <span>🌐 {{ __('natan.web_sources.title') }} (${webSourcesCount})</span>
                                </span>
                                <span class="material-icons text-sm transition-transform" data-icon="${collapseId}">expand_more</span>
                            </button>
                            <div id="${collapseId}" class="hidden space-y-2">
                                ${message.web_sources.map((source, idx) => `
                                    <div class="rounded-lg border border-blue-200 bg-white p-3 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <h4 class="font-semibold text-sm text-blue-900">${source.title || 'Source ' + (idx + 1)}</h4>
                                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                ${Math.round((source.relevance_score || 1) * 100)}%
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-700 mb-2 line-clamp-3">${source.snippet || ''}</p>
                                        <a href="${source.url}" target="_blank" rel="noopener noreferrer"
                                           class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                            <span class="material-icons text-xs">open_in_new</span>
                                            <span class="truncate">${source.url}</span>
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                        bubbleDiv.appendChild(webSourcesDiv);
                    }

                    // Timestamp + Copy Button
                    const timestampDiv = document.createElement('div');
                    timestampDiv.className = message.role === 'user' ?
                        'mt-2 flex items-center justify-between text-xs text-white/60' :
                        'mt-2 flex items-center justify-between text-xs text-gray-600';

                    const timeSpan = document.createElement('span');
                    timeSpan.textContent = message.timestamp;
                    timestampDiv.appendChild(timeSpan);

                    // Copy button (only for assistant messages)
                    if (message.role === 'assistant') {
                        const copyBtn = document.createElement('button');
                        copyBtn.className =
                            'copy-message-btn flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 hover:text-[#2D5016] transition-colors';
                        copyBtn.innerHTML = '<span class="material-icons text-sm">content_copy</span><span>Copia</span>';
                        copyBtn.dataset.content = message.content;
                        timestampDiv.appendChild(copyBtn);
                    }

                    bubbleDiv.appendChild(timestampDiv);

                    // Quick Actions (for assistant messages only)
                    if (message.role === 'assistant' && message.message_id) {
                        const quickActionsDiv = document.createElement('div');
                        quickActionsDiv.className =
                            'mt-3 flex flex-wrap gap-2 border-t border-gray-100 pt-3';
                        quickActionsDiv.innerHTML = `
                            <span class="text-xs font-medium text-gray-500">💬 Elabora questa risposta:</span>
                            <button class="quick-action-btn" data-action="simplify" data-message-id="${message.message_id}"
                                    title="Semplifica per cittadini o non esperti">
                                💡 Semplifica
                            </button>
                            <button class="quick-action-btn" data-action="deepen" data-message-id="${message.message_id}"
                                    title="Approfondisci con analisi strategiche">
                                🔍 Approfondisci
                            </button>
                            <button class="quick-action-btn" data-action="actionable" data-message-id="${message.message_id}"
                                    title="Trasforma in azioni concrete">
                                ✅ Azioni concrete
                            </button>
                            <button class="quick-action-btn" data-action="presentation" data-message-id="${message.message_id}"
                                    title="Formato presentazione (slide, executive summary)">
                                📊 Per presentazione
                            </button>
                            <button class="quick-action-btn" data-action="citizen" data-message-id="${message.message_id}"
                                    title="Riscrivi per comunicato cittadini">
                                👥 Per cittadini
                            </button>
                        `;
                        bubbleDiv.appendChild(quickActionsDiv);
                    }

                    messageDiv.appendChild(bubbleDiv);
                    this.elements.chatMessages.appendChild(messageDiv);
                },

                /**
                 * Format message (simple markdown-like)
                 */
                formatMessage(text) {
                    return text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>')
                        .replace(/• /g, '<br>• ');
                },

                /**
                 * Send message to API
                 */
                async sendToApi(message) {
                    this.setLoading(true);

                    // Show loading indicator
                    this.showLoadingIndicator();

                    try {
                        // Get web search toggle state ✨ NEW v3.0
                        const webSearchEnabled = document.getElementById('webSearchToggle')?.checked || false;

                        const response = await fetch(this.config.apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.config.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message,
                                conversation_history: this.getConversationHistory(),
                                persona_id: window.selectedPersona || null, // From persona selector
                                session_id: this.config.sessionId || null,
                                use_web_search: webSearchEnabled // NEW v3.0
                            })
                        });

                        const data = await response.json();

                        // Remove loading indicator
                        this.hideLoadingIndicator();

                        if (data.success) {
                            // Pass persona info, message_id, elaboration flag, reference content, and web_sources ✨ v3.0
                            this.addMessage('assistant', data.response, data.sources, data.persona, data.message_ids
                                ?.assistant, data.is_elaboration, data.reference_content, data.web_sources);

                            // Update session ID if provided
                            if (data.session_id) {
                                this.config.sessionId = data.session_id;
                            }

                            // Show persona suggestion if available
                            if (data.persona && data.persona.suggestion) {
                                this.showPersonaSuggestion(data.persona.suggestion);
                            }
                        } else {
                            this.addMessage('assistant', 'Mi dispiace, si è verificato un errore: ' + (data.message ||
                                'Errore sconosciuto'));
                        }

                    } catch (error) {
                        console.error('[N.A.T.A.N.] API Error:', error);
                        this.hideLoadingIndicator();
                        this.addMessage('assistant',
                            'Mi dispiace, non riesco a connettermi al servizio AI. Riprova tra poco.');
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Show loading indicator
                 */
                showLoadingIndicator() {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'loadingIndicator';
                    loadingDiv.className = 'flex justify-start';
                    loadingDiv.innerHTML = `
                        <div class="max-w-2xl rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="flex space-x-1">
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 0ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 150ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 300ms"></div>
                                </div>
                                <span class="text-xs text-gray-500">N.A.T.A.N. sta pensando...</span>
                            </div>
                        </div>
                    `;
                    this.elements.chatMessages.appendChild(loadingDiv);
                    this.scrollToBottom();
                },

                /**
                 * Hide loading indicator
                 */
                hideLoadingIndicator() {
                    const loadingIndicator = document.getElementById('loadingIndicator');
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                },

                /**
                 * Handle elaboration quick action
                 */
                handleElaboration(action, messageId) {
                    console.log('[N.A.T.A.N.] Elaboration requested:', action, messageId);

                    // Define elaboration prompts
                    const prompts = {
                        simplify: 'Semplifica questa analisi rendendola comprensibile anche a chi non è esperto del settore. Usa un linguaggio accessibile, evita termini tecnici dove possibile, e fornisci esempi concreti.',
                        deepen: 'Approfondisci ulteriormente questa analisi, aggiungendo considerazioni strategiche, implicazioni a lungo termine, e possibili scenari alternativi. Mantieni un approccio analitico da consulente senior.',
                        actionable: 'Trasforma questa analisi in 3-5 azioni concrete e prioritizzate che possiamo eseguire nei prossimi 3-6 mesi. Per ogni azione fornisci: obiettivo, timeline, risorse necessarie, e KPI di successo.',
                        presentation: 'Ristruttura questa analisi in formato presentation-ready con: executive summary (3 bullet points), key findings, dati numerici evidenziati, raccomandazioni prioritizzate. Usa un formato adatto a slide.',
                        citizen: 'Riscrivi questa analisi come comunicato per i cittadini, usando linguaggio semplice, tono rassicurante e trasparente. Spiega cosa significa per loro e quali benefici concreti porterà.'
                    };

                    const prompt = prompts[action];
                    if (!prompt) {
                        console.error('[N.A.T.A.N.] Unknown elaboration action:', action);
                        return;
                    }

                    // Send elaboration request
                    this.sendToApiWithElaboration(prompt, messageId);
                },

                /**
                 * Send elaboration to API
                 */
                async sendToApiWithElaboration(message, referenceMessageId) {
                    this.setLoading(true);
                    this.showLoadingIndicator();

                    try {
                        const response = await fetch(this.config.apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.config.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message,
                                conversation_history: this.getConversationHistory(),
                                persona_id: window.selectedPersona || null,
                                session_id: this.config.sessionId || null,
                                use_rag: false, // No RAG for elaborations
                                reference_message_id: referenceMessageId // Reference the previous message
                            })
                        });

                        const data = await response.json();
                        this.hideLoadingIndicator();

                        if (data.success) {
                            // Add elaborated message with elaboration flag and reference content
                            this.addMessage('assistant', data.response, data.sources, data.persona, data.message_ids
                                ?.assistant, true, data.reference_content);

                            if (data.session_id) {
                                this.config.sessionId = data.session_id;
                            }
                        } else {
                            this.addMessage('assistant', 'Mi dispiace, si è verificato un errore nell\'elaborazione: ' +
                                (data.message || 'Errore sconosciuto'));
                        }
                    } catch (error) {
                        console.error('[N.A.T.A.N.] Elaboration API Error:', error);
                        this.hideLoadingIndicator();
                        this.addMessage('assistant',
                            'Mi dispiace, non riesco a elaborare la risposta. Riprova tra poco.');
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Set loading state
                 */
                setLoading(loading) {
                    this.isLoading = loading;
                    this.elements.userInput.disabled = loading;
                    this.elements.sendBtn.disabled = loading;

                    if (loading) {
                        this.elements.sendBtnText.classList.add('hidden');
                        this.elements.sendBtnLoader.classList.remove('hidden');
                    } else {
                        this.elements.sendBtnText.classList.remove('hidden');
                        this.elements.sendBtnLoader.classList.add('hidden');
                    }
                },

                /**
                 * Get conversation history (last N messages)
                 */
                getConversationHistory() {
                    return this.messages
                        .slice(-this.config.maxHistoryLength)
                        .map(msg => ({
                            role: msg.role,
                            content: msg.content.replace(/<[^>]*>/g, '') // Strip HTML
                        }));
                },

                /**
                 * Scroll to bottom of chat
                 */
                scrollToBottom() {
                    setTimeout(() => {
                        this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
                    }, 100);
                },

                /**
                 * Auto-resize textarea based on content
                 */
                autoResizeTextarea(textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 200) + 'px';
                },

                /**
                 * Copy message content to clipboard
                 */
                async copyToClipboard(content, button) {
                    try {
                        // Strip HTML tags for plain text copy
                        const plainText = content.replace(/<[^>]*>/g, '').replace(/<br\s*\/?>/gi, '\n');
                        await navigator.clipboard.writeText(plainText);

                        // Visual feedback
                        const originalHTML = button.innerHTML;
                        button.innerHTML = '<span class="material-icons text-sm">check</span><span>Copiato!</span>';
                        button.classList.add('text-green-600');

                        setTimeout(() => {
                            button.innerHTML = originalHTML;
                            button.classList.remove('text-green-600');
                        }, 2000);
                    } catch (err) {
                        console.error('[N.A.T.A.N.] Copy failed:', err);
                        button.innerHTML = '<span class="material-icons text-sm">error</span><span>Errore</span>';
                        setTimeout(() => {
                            button.innerHTML =
                                '<span class="material-icons text-sm">content_copy</span><span>Copia</span>';
                        }, 2000);
                    }
                },

                /**
                 * Show persona suggestion
                 * Displays a suggestion banner when a different persona might be better
                 */
                showPersonaSuggestion(suggestionText) {
                    // Remove any existing suggestion
                    const existing = document.getElementById('personaSuggestionBanner');
                    if (existing) existing.remove();

                    // Create suggestion banner
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.id = 'personaSuggestionBanner';
                    suggestionDiv.className =
                        'mx-4 mb-4 animate-fade-in rounded-lg border border-blue-300 bg-blue-50 p-3 shadow-sm';
                    suggestionDiv.innerHTML = `
                        <div class="flex items-start gap-3">
                            <span class="material-icons text-blue-600">lightbulb</span>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800">${suggestionText}</p>
                            </div>
                            <button onclick="document.getElementById('personaSuggestionBanner').remove()"
                                    class="text-blue-400 hover:text-blue-600">
                                <span class="material-icons text-sm">close</span>
                            </button>
                        </div>
                    `;

                    // Insert before chat messages
                    const chatWindow = document.querySelector('.rounded-2xl.bg-white.shadow-xl');
                    if (chatWindow) {
                        chatWindow.insertAdjacentElement('beforebegin', suggestionDiv);

                        // Auto-remove after 10 seconds
                        setTimeout(() => {
                            const banner = document.getElementById('personaSuggestionBanner');
                            if (banner) {
                                banner.classList.add('animate-fade-out');
                                setTimeout(() => banner.remove(), 300);
                            }
                        }, 10000);
                    }
                }
            };

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => NatanChat.init());
            } else {
                NatanChat.init();
            }

            // Toggle Suggested Questions on Mobile
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('toggleSuggestedQuestions');
                const content = document.getElementById('suggestedQuestionsContent');
                const icon = document.getElementById('toggleIcon');

                if (toggleBtn && content && icon) {
                    toggleBtn.addEventListener('click', function() {
                        const isHidden = content.classList.contains('hidden');

                        if (isHidden) {
                            content.classList.remove('hidden');
                            icon.textContent = 'expand_less';
                        } else {
                            content.classList.add('hidden');
                            icon.textContent = 'expand_more';
                        }
                    });
                }
            });

            /**
             * Free Chat - Pure Claude conversation (no RAG)
             */
            const FreeChat = {
                messages: [],
                isLoading: false,
                elements: {},
                config: {
                    apiUrl: '{{ route('pa.natan.chat.message') }}',
                    csrfToken: '{{ csrf_token() }}',
                    sessionId: null
                },

                init() {
                    console.log('[FreeChat] Initializing...');
                    this.elements.chatMessages = document.getElementById('freeChatMessages');
                    this.elements.welcomeMessage = document.getElementById('freeChatWelcome');
                    this.elements.userInput = document.getElementById('freeChatInput');
                    this.elements.sendBtn = document.getElementById('freeChatSendBtn');
                    this.elements.sendBtnText = document.getElementById('freeChatSendBtnText');
                    this.elements.sendBtnLoader = document.getElementById('freeChatSendBtnLoader');
                    this.elements.chatForm = document.getElementById('freeChatForm');

                    this.elements.chatForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.handleSubmit();
                    });

                    // Handle Enter key: Shift+Enter to submit, Enter for new line
                    this.elements.userInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && e.shiftKey) {
                            e.preventDefault();
                            this.elements.chatForm.dispatchEvent(new Event('submit'));
                        }
                    });

                    // Auto-resize textarea as user types
                    this.elements.userInput.addEventListener('input', () => {
                        this.autoResizeTextarea(this.elements.userInput);
                    });

                    // Event delegation for copy buttons
                    this.elements.chatMessages.addEventListener('click', (e) => {
                        const copyBtn = e.target.closest('.free-copy-btn');
                        if (copyBtn) {
                            const content = copyBtn.dataset.content;
                            this.copyToClipboard(content, copyBtn);
                        }
                    });
                },

                autoResizeTextarea(textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 200) + 'px';
                },

                async copyToClipboard(content, button) {
                    try {
                        const plainText = content.replace(/<[^>]*>/g, '').replace(/<br\s*\/?>/gi, '\n');
                        await navigator.clipboard.writeText(plainText);

                        const originalHTML = button.innerHTML;
                        button.innerHTML = '<span class="material-icons text-sm">check</span><span>Copiato!</span>';
                        button.classList.add('text-green-600');

                        setTimeout(() => {
                            button.innerHTML = originalHTML;
                            button.classList.remove('text-green-600');
                        }, 2000);
                    } catch (err) {
                        console.error('[FreeChat] Copy failed:', err);
                        button.innerHTML = '<span class="material-icons text-sm">error</span><span>Errore</span>';
                        setTimeout(() => {
                            button.innerHTML =
                                '<span class="material-icons text-sm">content_copy</span><span>Copia</span>';
                        }, 2000);
                    }
                },

                async handleSubmit() {
                    const message = this.elements.userInput.value.trim();
                    if (!message || this.isLoading) return;

                    this.elements.userInput.value = '';
                    if (this.elements.welcomeMessage) {
                        this.elements.welcomeMessage.style.display = 'none';
                    }

                    this.addMessage('user', message);
                    await this.sendToApi(message);
                },

                addMessage(role, content) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

                    const bubbleDiv = document.createElement('div');
                    bubbleDiv.className = role === 'user' ?
                        'bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-2xl rounded-tr-sm max-w-md px-4 py-3 shadow-sm' :
                        'bg-white rounded-2xl rounded-tl-sm max-w-2xl px-4 py-3 shadow-md border border-purple-200';

                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'prose prose-sm max-w-none';
                    contentDiv.style.color = role === 'user' ? '#ffffff' : '#7c3aed';
                    contentDiv.innerHTML = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g,
                        '<br>');
                    bubbleDiv.appendChild(contentDiv);

                    const timestampDiv = document.createElement('div');
                    timestampDiv.className = role === 'user' ?
                        'mt-2 flex items-center justify-between text-xs text-white/60' :
                        'mt-2 flex items-center justify-between text-xs text-purple-600';

                    const timeSpan = document.createElement('span');
                    timeSpan.textContent = new Date().toLocaleTimeString('it-IT', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    timestampDiv.appendChild(timeSpan);

                    // Copy button (only for assistant messages)
                    if (role === 'assistant') {
                        const copyBtn = document.createElement('button');
                        copyBtn.className =
                            'free-copy-btn flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-purple-600 hover:bg-purple-100 transition-colors';
                        copyBtn.innerHTML = '<span class="material-icons text-sm">content_copy</span><span>Copia</span>';
                        copyBtn.dataset.content = content;
                        timestampDiv.appendChild(copyBtn);
                    }

                    bubbleDiv.appendChild(timestampDiv);

                    messageDiv.appendChild(bubbleDiv);
                    this.elements.chatMessages.appendChild(messageDiv);
                    this.messages.push({
                        role,
                        content
                    });
                    this.scrollToBottom();
                },

                async sendToApi(message) {
                    this.setLoading(true);
                    this.showLoadingIndicator();

                    try {
                        const response = await fetch(this.config.apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.config.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message,
                                conversation_history: this.getConversationHistory(),
                                use_rag: false, // No RAG for free chat
                                session_id: this.config.sessionId
                            })
                        });

                        const data = await response.json();
                        this.hideLoadingIndicator();

                        if (data.success) {
                            this.addMessage('assistant', data.response);
                            if (data.session_id) {
                                this.config.sessionId = data.session_id;
                            }
                        } else {
                            this.addMessage('assistant', 'Mi dispiace, si è verificato un errore: ' + (data.message ||
                                'Errore sconosciuto'));
                        }
                    } catch (error) {
                        console.error('[FreeChat] API Error:', error);
                        this.hideLoadingIndicator();
                        this.addMessage('assistant', 'Mi dispiace, non riesco a connettermi al servizio AI.');
                    } finally {
                        this.setLoading(false);
                    }
                },

                showLoadingIndicator() {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'freeChatLoadingIndicator';
                    loadingDiv.className = 'flex justify-start';
                    loadingDiv.innerHTML = `
                        <div class="max-w-2xl rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="flex space-x-1">
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-purple-400" style="animation-delay: 0ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-purple-400" style="animation-delay: 150ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-purple-400" style="animation-delay: 300ms"></div>
                                </div>
                                <span class="text-xs text-gray-600">Claude sta pensando...</span>
                            </div>
                        </div>
                    `;
                    this.elements.chatMessages.appendChild(loadingDiv);
                    this.scrollToBottom();
                },

                hideLoadingIndicator() {
                    const indicator = document.getElementById('freeChatLoadingIndicator');
                    if (indicator) indicator.remove();
                },

                setLoading(loading) {
                    this.isLoading = loading;
                    this.elements.userInput.disabled = loading;
                    this.elements.sendBtn.disabled = loading;

                    if (loading) {
                        this.elements.sendBtnText.classList.add('hidden');
                        this.elements.sendBtnLoader.classList.remove('hidden');
                    } else {
                        this.elements.sendBtnText.classList.remove('hidden');
                        this.elements.sendBtnLoader.classList.add('hidden');
                    }
                },

                getConversationHistory() {
                    return this.messages.slice(-10).map(msg => ({
                        role: msg.role,
                        content: msg.content.replace(/<[^>]*>/g, '')
                    }));
                },

                scrollToBottom() {
                    setTimeout(() => {
                        this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
                    }, 100);
                }
            };

            // Initialize Free Chat
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => FreeChat.init());
            } else {
                FreeChat.init();
            }
        </script>
    @endpush
</x-pa-layout>
