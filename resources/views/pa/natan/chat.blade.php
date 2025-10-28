<x-pa-layout noHero="true">
    {{-- AI Processing Panel Component --}}
    @include('pa.natan._ai-processing-panel')

    {{-- AI Cost Preview Modal Component --}}
    @include('pa.natan._ai-cost-preview-modal')

    {{-- Chat History is now integrated in enterprise sidebar --}}
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
                            <div class="flex items-center justify-between gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm sm:h-12 sm:w-12">
                                        <span class="material-icons text-xl text-white sm:text-2xl">smart_toy</span>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-white sm:text-lg">N.A.T.A.N.</h2>
                                        <p class="text-[10px] text-white/80 sm:text-xs">Nodo di Analisi e Tracciamento
                                            Atti
                                            Notarizzati</p>
                                    </div>
                                </div>

                                {{-- ✨ NEW v4.0 - Active Project Badge --}}
                                @if ($activeProject)
                                    <div
                                        class="flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-1.5 backdrop-blur-sm">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg"
                                            style="background-color: {{ $activeProject->color ?? '#D4A574' }}">
                                            <span
                                                class="material-icons text-sm text-white">{{ $activeProject->icon ?? 'folder' }}</span>
                                        </div>
                                        <div class="hidden sm:block">
                                            <p class="text-xs font-medium text-white">{{ $activeProject->name }}</p>
                                            <p class="text-[10px] text-white/60">{{ __('projects.active_context') }}</p>
                                        </div>
                                    </div>
                                @endif
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
                            <div
                                class="mb-2 flex items-center justify-between rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-2 sm:p-3">
                                <div class="flex items-center gap-2">
                                    <label for="webSearchToggle" class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" id="webSearchToggle"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0" />
                                        <span
                                            class="text-xs font-medium text-gray-700 sm:text-sm">{{ __('natan.web_search.toggle_label') }}</span>
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

                            {{-- ✨ NEW v4.0 - Upload Documents (only when project active) --}}
                            @if ($activeProject)
                                <div
                                    class="mb-3 flex items-center gap-3 rounded-lg border-2 border-[#D4A574] bg-gradient-to-r from-[#D4A574]/10 to-white p-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg"
                                        style="background-color: {{ $activeProject->color ?? '#D4A574' }}20">
                                        <span class="material-icons"
                                            style="color: {{ $activeProject->color ?? '#D4A574' }}">{{ $activeProject->icon ?? 'folder' }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-[#1B365D]">{{ $activeProject->name }}</p>
                                        <p class="text-xs text-gray-600">{{ __('projects.upload_documents_hint') }}</p>
                                    </div>
                                    <button type="button" onclick="triggerDocumentUpload()"
                                        class="flex items-center gap-2 rounded-lg border-0 bg-[#D4A574] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#B89968]">
                                        <span class="material-icons text-sm">upload_file</span>
                                        <span class="hidden sm:inline">{{ __('projects.upload_document') }}</span>
                                    </button>
                                    <input type="file" id="documentUploadInput" accept=".pdf,.docx,.txt,.md"
                                        class="hidden" onchange="handleDocumentUpload(event)">
                                </div>
                            @endif

                            <form id="chatForm" class="flex gap-1.5 sm:gap-2">
                                <textarea id="userInput" rows="1" placeholder="{{ __('natan.chat.input_placeholder') }}"
                                    class="flex-1 resize-none rounded-xl border-2 border-gray-200 px-3 py-2 text-sm focus:border-[#2D5016] focus:outline-none focus:ring-2 focus:ring-[#2D5016]/20 disabled:cursor-not-allowed disabled:bg-gray-100 sm:px-4 sm:py-3 sm:text-base"
                                    style="max-height: 150px; overflow-y: auto;"></textarea>
                                <button type="submit" id="sendBtn"
                                    class="flex items-center gap-1 self-end rounded-xl bg-[#2D5016] px-3 py-2 text-sm font-medium text-white transition-all hover:bg-[#3D6026] disabled:cursor-not-allowed disabled:opacity-50 sm:gap-2 sm:px-6 sm:py-3 sm:text-base">
                                    <span id="sendBtnText"
                                        class="hidden sm:inline">{{ __('natan.chat.send_button') }}</span>
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
        {{-- AI Cost Tracking System --}}
        <script src="{{ asset('js/ai-cost-tracking.js') }}"></script>

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

                    // Send to API with SSE streaming (NEW v6.0)
                    // Feature flag: use SSE for real-time progress tracking
                    const useSSE = true; // TODO: make configurable in user settings

                    // 🚨 DIAGNOSTIC LOG - REMOVE AFTER TESTING
                    console.log('🔥 [DIAGNOSTIC] handleSubmit called');
                    console.log('🔥 [DIAGNOSTIC] useSSE flag:', useSSE);
                    console.log('🔥 [DIAGNOSTIC] Will call:', useSSE ? 'sendToApiWithSSE' : 'sendToApi');

                    if (useSSE) {
                        console.log('🚀 [DIAGNOSTIC] Calling sendToApiWithSSE NOW');
                        await this.sendToApiWithSSE(message);
                    } else {
                        console.log('⚠️ [DIAGNOSTIC] Calling OLD sendToApi');
                        await this.sendToApi(message);
                    }
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
                 * Clear chat and start new conversation
                 * NEW v3.1 - Used by History Sidebar
                 */
                clearChat() {
                    console.log('[N.A.T.A.N.] Clearing chat');

                    // Reset state
                    this.messages = [];
                    this.conversationHistory = [];
                    this.config.sessionId = null;

                    // Clear UI
                    if (this.elements.chatMessages) {
                        this.elements.chatMessages.innerHTML = '';
                    }

                    // Show welcome message
                    if (this.elements.welcomeMessage) {
                        this.elements.welcomeMessage.style.display = 'flex';
                    }

                    // Clear input
                    if (this.elements.userInput) {
                        this.elements.userInput.value = '';
                    }
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

                    // Show AI Processing Panel (enterprise UI for complex queries)
                    // Will be updated dynamically based on backend progress
                    AIProcessingPanel.show(0); // Will update with actual count from backend

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

                        // Log response status for debugging
                        console.log('[N.A.T.A.N.] Response status:', response.status, response.statusText);

                        // Check for specific HTTP errors before parsing
                        if (response.status === 504) {
                            this.hideLoadingIndicator();
                            this.addMessage('assistant',
                                'La richiesta sta richiedendo più tempo del previsto. Il sistema potrebbe essere sotto carico. Per favore riprova tra qualche minuto.'
                            );
                            return;
                        }

                        // Read response body as text FIRST (can only read once!)
                        const responseText = await response.text();
                        console.log('[N.A.T.A.N.] Response length:', responseText.length, 'chars');

                        // Check if response looks like JSON
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            // Server returned non-JSON (probably HTML error page)
                            console.error('[N.A.T.A.N.] Server returned non-JSON response');
                            console.log('[N.A.T.A.N.] Raw response:', responseText.substring(0, 500));

                            this.hideLoadingIndicator();
                            this.addMessage('assistant',
                                'Si è verificato un errore del server. Il nostro team è stato notificato. Per favore riprova più tardi.'
                            );
                            return;
                        }

                        // Try to parse as JSON
                        let data;
                        try {
                            data = JSON.parse(responseText);
                            console.log('[N.A.T.A.N.] Parsed data:', {
                                success: data.success,
                                hasMessage: !!data.message,
                                hasResponse: !!data.response,
                                hasError: !!data.error,
                                actsProcessed: data.acts_processed || 0
                            });

                            // Update AI Processing Panel with actual stats from backend
                            if (data.acts_processed) {
                                AIProcessingPanel.updateStats({
                                    acts: data.acts_processed,
                                    relevance: data.relevance_score || 0
                                });
                                AIProcessingPanel.updateProgress(50); // Mid-progress when data arrives
                            }

                        } catch (parseError) {
                            console.error('[N.A.T.A.N.] JSON Parse Error:', parseError);
                            console.log('[N.A.T.A.N.] First 500 chars:', responseText.substring(0, 500));

                            AIProcessingPanel.hide();
                            this.addMessage('assistant',
                                'Errore nel parsing della risposta del server. Il nostro team è stato notificato.'
                            );
                            return;
                        }

                        // 🚀 PHASE 3: Detect chunking mode and start polling
                        if (data.mode === 'chunking' && data.session_id) {
                            console.log('[N.A.T.A.N.] 🔄 Chunking mode activated', {
                                sessionId: data.session_id,
                                totalChunks: data.total_chunks,
                                totalActs: data.total_acts,
                                strategy: data.strategy
                            });

                            // Show chunking panel instead of normal progress
                            AIProcessingPanel.showChunking(data.total_chunks, data.total_acts);

                            // Start polling loop
                            this.startChunkingPoll(data.session_id);
                            return; // Exit here, polling will handle completion
                        }

                        // Complete AI processing and hide panel (normal mode)
                        AIProcessingPanel.complete();

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
                            // Show error message from backend (localized or specific)
                            this.addMessage('assistant', data.message ||
                                'Mi dispiace, si è verificato un errore sconosciuto.');
                        }

                    } catch (error) {
                        console.error('[N.A.T.A.N.] Network/Parsing Error:', error);
                        console.error('[N.A.T.A.N.] Error type:', error.constructor.name);
                        console.error('[N.A.T.A.N.] Error message:', error.message);
                        console.error('[N.A.T.A.N.] Stack trace:', error.stack);

                        this.hideLoadingIndicator();

                        // Determine error type for better user feedback
                        let errorMessage = 'Mi dispiace, si è verificato un errore imprevisto.';

                        if (error.name === 'SyntaxError') {
                            errorMessage =
                                'Errore nel parsing della risposta del server. Il nostro team è stato notificato.';
                            console.error('[N.A.T.A.N.] CRITICAL: Server returned non-JSON response');
                        } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                            errorMessage =
                                'Mi dispiace, non riesco a connettermi al servizio AI. Verifica la tua connessione e riprova.';
                        } else if (error.message) {
                            errorMessage = `Errore tecnico: ${error.message}`;
                        }

                        this.addMessage('assistant', errorMessage);
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Send to API with Server-Sent Events (SSE) streaming
                 *
                 * Real-time progress tracking for semantic search + AI analysis.
                 *
                 * @param {string} message - User query
                 */
                async sendToApiWithSSE(message) {
                    console.log('🚀🚀🚀 [SSE] sendToApiWithSSE CALLED!', message);

                    this.setLoading(true);
                    
                    // Check if AIProcessingPanel exists
                    if (typeof AIProcessingPanel === 'undefined') {
                        console.error('❌ AIProcessingPanel NOT LOADED!');
                        return;
                    }
                    
                    console.log('✅ AIProcessingPanel exists, calling show()...');
                    AIProcessingPanel.show(0);
                    console.log('✅ AIProcessingPanel.show() completed');

                    try {
                        // Use SSE endpoint instead of traditional POST
                        const url = '/pa/natan/analyze-stream';

                        // Get FRESH CSRF token from meta tag (not cached config)
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            this.config.csrfToken;

                        console.log('📡 [SSE] Calling URL:', url);
                        console.log('📡 [SSE] Message:', message);
                        console.log('🔐 [SSE] CSRF Token:', csrfToken ? 'Present' : 'MISSING!');

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'text/event-stream'
                            },
                            body: JSON.stringify({
                                query: message,
                                limit: 500 // Default limit, or get from slider
                            })
                        });

                        console.log('✅ [SSE] Response received:', response.status, response.statusText);

                        if (!response.ok) {
                            console.error('❌ [SSE] Response not OK:', response.status);
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        console.log('📖 [SSE] Starting to read stream...');

                        // Create ReadableStream reader
                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        let buffer = '';

                        // Read stream
                        while (true) {
                            const {
                                done,
                                value
                            } = await reader.read();

                            if (done) break;

                            // Decode chunk
                            buffer += decoder.decode(value, {
                                stream: true
                            });

                            // Process complete events (separated by \n\n)
                            const events = buffer.split('\n\n');
                            buffer = events.pop(); // Keep incomplete event in buffer

                            for (const eventText of events) {
                                if (!eventText.trim()) continue;

                                // Parse SSE format: "event: name\ndata: {json}\n"
                                const lines = eventText.split('\n');
                                let eventName = '';
                                let eventData = '';

                                for (const line of lines) {
                                    if (line.startsWith('event:')) {
                                        eventName = line.substring(6).trim();
                                    } else if (line.startsWith('data:')) {
                                        eventData = line.substring(5).trim();
                                    }
                                }

                                if (!eventName || !eventData) continue;

                                // Parse event data
                                let data;
                                try {
                                    data = JSON.parse(eventData);
                                } catch (e) {
                                    console.error('[SSE] Parse error:', e, eventData);
                                    continue;
                                }

                                // Handle event
                                this.handleSSEEvent(eventName, data);
                            }
                        }

                    } catch (error) {
                        console.error('[SSE] Error:', error);
                        AIProcessingPanel.hide();
                        this.addMessage('assistant', `Errore durante l'elaborazione: ${error.message}`);
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Handle SSE event
                 *
                 * @param {string} event - Event name
                 * @param {object} data - Event data
                 */
                handleSSEEvent(event, data) {
                    console.log(`[SSE] Event: ${event}`, data);

                    switch (event) {
                        case 'semantic_search_start':
                            AIProcessingPanel.updateStage('search', 'active', `Ricerca con ${data.model}`);
                            break;

                        case 'semantic_search_complete':
                            AIProcessingPanel.updateStage('search', 'completed', `${data.acts_found} atti trovati`);
                            AIProcessingPanel.updateStats({
                                acts: data.acts_found,
                                relevance: data.avg_relevance
                            });
                            AIProcessingPanel.updateProgress(30);
                            break;

                        case 'ai_analysis_start':
                            AIProcessingPanel.updateStage('ai', 'active', `Modello: ${data.model}`);
                            AIProcessingPanel.updateProgress(50);
                            break;

                        case 'persona_selected':
                            // Show which N.A.T.A.N. expert persona is responding
                            console.log('[SSE] Persona selected:', data.persona_name, `(${data.confidence}% confidence)`);
                            AIProcessingPanel.updateStage('ai', 'active', `${data.persona_name} (${data.confidence}%)`);
                            break;

                        case 'cost_update':
                            // Show cost tracking panel (PA direct billing - EUR only)
                            AIProcessingPanel.updateCostTracking({
                                inputTokens: data.input_tokens,
                                outputTokens: data.output_tokens,
                                costEur: data.cost_eur
                            });
                            break;

                        case 'response_generation_start':
                            AIProcessingPanel.updateStage('response', 'active', 'Generazione risposta');
                            AIProcessingPanel.updateProgress(75);
                            break;

                        case 'response_generation_complete':
                            AIProcessingPanel.updateStage('response', 'completed', 'Completata');
                            AIProcessingPanel.updateProgress(100);

                            // Add response to chat
                            this.addMessage('assistant', data.response);

                            // ❌ REMOVED auto-hide - User must manually close panel to see final stats
                            // setTimeout(() => {
                            //     AIProcessingPanel.hide();
                            // }, 3000);
                            break;

                        case 'done':
                            console.log('[SSE] Stream completed:', data);
                            if (data.status === 'no_acts') {
                                this.addMessage('assistant', data.message);
                                AIProcessingPanel.hide();
                            }
                            // ❌ REMOVED auto-hide on success - User closes manually with button
                            break;

                        case 'error':
                            console.error('[SSE] Error event:', data);
                            AIProcessingPanel.hide();
                            this.addMessage('assistant', `Errore: ${data.message}`);
                            break;

                        default:
                            console.warn('[SSE] Unknown event:', event, data);
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
                 * Start polling for chunking progress
                 *
                 * @param {string} sessionId - The chunking session ID
                 * @package App\PA\NatanChat
                 * @author Padmin D. Curtis (AI Partner OS3.0)
                 * @version 1.0.0 (FlorenceEGI - NATAN Intelligent Chunking Phase 3)
                 * @date 2025-01-27
                 * @purpose Poll backend for chunk processing progress until completion
                 */
                startChunkingPoll(sessionId) {
                    console.log('[N.A.T.A.N.] 🔄 Starting polling for session:', sessionId);

                    let pollAttempts = 0;
                    const maxPollAttempts = 150; // 150 attempts × 2 seconds = 5 minutes max
                    const pollInterval = 2000; // 2 seconds

                    const pollProgress = async () => {
                        pollAttempts++;

                        // Timeout check
                        if (pollAttempts >= maxPollAttempts) {
                            console.error('[N.A.T.A.N.] ⏱️ Polling timeout after 5 minutes');
                            AIProcessingPanel.hide();
                            this.showChunkingError(
                                '⚠️ {{ __('natan.chunking.timeout_error') }}. Il processamento potrebbe ancora essere in corso in background.',
                                sessionId
                            );
                            this.setLoading(false);
                            return;
                        }

                        try {
                            const response = await fetch(`/pa/natan/chunking-progress/${sessionId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.config.csrfToken
                                }
                            });

                            if (!response.ok) {
                                if (response.status === 404) {
                                    console.error('[N.A.T.A.N.] ❌ Session not found');
                                    AIProcessingPanel.hide();
                                    this.showChunkingError('{{ __('natan.chunking.session_not_found') }}', null);
                                    this.setLoading(false);
                                    return;
                                } else if (response.status === 403) {
                                    console.error('[N.A.T.A.N.] 🔒 Unauthorized access');
                                    AIProcessingPanel.hide();
                                    this.showChunkingError('{{ __('natan.chunking.unauthorized') }}', null);
                                    this.setLoading(false);
                                    return;
                                } else if (response.status === 429) {
                                    console.warn('[N.A.T.A.N.] ⚠️ Rate limit hit, continuing polling...');
                                    setTimeout(pollProgress, pollInterval);
                                    return;
                                }
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }

                            const data = await response.json();
                            console.log('[N.A.T.A.N.] 📊 Progress update:', {
                                currentChunk: data.current_chunk,
                                chunkProgress: data.chunk_progress,
                                actsInChunk: data.acts_in_chunk,
                                completedChunks: data.completed_chunks?.length || 0,
                                allCompleted: data.all_completed
                            });

                            // Update chunk progress bar
                            AIProcessingPanel.updateChunkProgress(data.current_chunk, data.chunk_progress);

                            // Update acts counter
                            if (data.acts_in_chunk) {
                                AIProcessingPanel.updateStats({
                                    acts: data.acts_in_chunk,
                                    relevance: 0 // Will be updated on final
                                });
                            }

                            // Mark completed chunks
                            if (data.last_completed && data.last_completed_index !== undefined) {
                                AIProcessingPanel.completeChunk(data.last_completed_index);
                            }

                            // Check if all chunks completed
                            if (data.all_completed) {
                                console.log('[N.A.T.A.N.] ✅ All chunks completed, fetching final result...');
                                await this.fetchChunkingFinal(sessionId);
                                return; // Stop polling
                            }

                            // Continue polling
                            setTimeout(pollProgress, pollInterval);

                        } catch (error) {
                            console.error('[N.A.T.A.N.] ❌ Polling error:', error);

                            // Retry on network errors
                            if (pollAttempts < maxPollAttempts) {
                                console.log('[N.A.T.A.N.] 🔄 Retrying after error...');
                                setTimeout(pollProgress, pollInterval);
                            } else {
                                AIProcessingPanel.hide();
                                this.showChunkingError(
                                    `{{ __('natan.chunking.polling_error') }}: ${error.message}`,
                                    sessionId
                                );
                                this.setLoading(false);
                            }
                        }
                    };

                    // Start polling immediately
                    pollProgress();
                },

                /**
                 * Fetch final aggregated chunking result
                 *
                 * @param {string} sessionId - The chunking session ID
                 * @package App\PA\NatanChat
                 * @author Padmin D. Curtis (AI Partner OS3.0)
                 * @version 1.0.0 (FlorenceEGI - NATAN Intelligent Chunking Phase 3)
                 * @date 2025-01-27
                 * @purpose Retrieve and display final aggregated response after all chunks processed
                 */
                async fetchChunkingFinal(sessionId) {
                    console.log('[N.A.T.A.N.] 📦 Fetching final result for session:', sessionId);

                    try {
                        const response = await fetch(`/pa/natan/chunking-final/${sessionId}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.config.csrfToken
                            }
                        });

                        if (!response.ok) {
                            if (response.status === 425) {
                                console.warn('[N.A.T.A.N.] ⚠️ Processing not complete yet, waiting...');
                                // Wait and poll progress again
                                setTimeout(() => this.startChunkingPoll(sessionId), 2000);
                                return;
                            }
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        const data = await response.json();
                        console.log('[N.A.T.A.N.] ✅ Final result received:', {
                            totalRelevantActs: data.total_relevant_acts,
                            chunksProcessed: data.chunks_processed,
                            sourcesCount: data.sources?.length || 0
                        });

                        // Update final stats
                        AIProcessingPanel.updateStats({
                            acts: data.total_relevant_acts || 0,
                            relevance: data.relevance_score || 0
                        });

                        // Complete processing
                        AIProcessingPanel.complete();

                        // Display aggregated response in chat
                        if (data.aggregated_response) {
                            this.addMessage(
                                'assistant',
                                data.aggregated_response,
                                data.sources,
                                data.persona,
                                data.message_id,
                                false, // not elaboration
                                null, // no reference content
                                data.web_sources || null
                            );

                            // Update session ID if provided
                            if (data.session_id) {
                                this.config.sessionId = data.session_id;
                            }
                        } else {
                            throw new Error('No aggregated response in final result');
                        }

                    } catch (error) {
                        console.error('[N.A.T.A.N.] ❌ Error fetching final result:', error);
                        AIProcessingPanel.hide();
                        this.showChunkingError(
                            `{{ __('natan.chunking.final_error') }}: ${error.message}`,
                            sessionId
                        );
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Show chunking error with retry button
                 *
                 * @param {string} errorMessage - The error message to display
                 * @param {string} sessionId - The chunking session ID (optional, for retry)
                 * @package App\PA\NatanChat
                 * @author Padmin D. Curtis (AI Partner OS3.0)
                 * @version 1.0.0 (FlorenceEGI - NATAN Chunking Error Recovery)
                 * @date 2025-01-27
                 * @purpose Display error message with retry button for chunking failures
                 */
                showChunkingError(errorMessage, sessionId = null) {
                    console.log('[N.A.T.A.N.] 🚨 Showing chunking error with retry option');

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'flex justify-start mb-4';
                    errorDiv.innerHTML = `
                        <div class="max-w-2xl rounded-2xl rounded-tl-sm bg-red-50 border-l-4 border-red-500 px-4 py-3 shadow-sm">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-red-800 font-medium mb-2">${errorMessage}</p>
                                    ${sessionId ? `
                                                                <button
                                                                    onclick="NatanChat.retryChunking('${sessionId}')"
                                                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors duration-200"
                                                                >
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                    </svg>
                                                                    {{ __('natan.chunking.retry_button') }}
                                                                </button>
                                                            ` : ''}
                                </div>
                            </div>
                        </div>
                    `;

                    this.elements.chatMessages.appendChild(errorDiv);
                    this.scrollToBottom();
                },

                /**
                 * Retry chunking analysis
                 *
                 * @param {string} sessionId - The failed session ID
                 * @package App\PA\NatanChat
                 * @author Padmin D. Curtis (AI Partner OS3.0)
                 * @version 1.0.0 (FlorenceEGI - NATAN Chunking Error Recovery)
                 * @date 2025-01-27
                 * @purpose Restart polling for a failed chunking session
                 */
                retryChunking(sessionId) {
                    console.log('[N.A.T.A.N.] 🔄 Retrying chunking for session:', sessionId);

                    // Clear loading state
                    this.setLoading(false);

                    // Re-show chunking panel
                    AIProcessingPanel.show(0);
                    AIProcessingPanel.showChunking(5, 0); // Will be updated by first poll

                    // Restart polling
                    this.startChunkingPoll(sessionId);
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

            // Expose globally for History Sidebar access (NEW v3.1)
            window.NatanChat = NatanChat;

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
                        // ✨ NEW v4.0 - Get active project ID for Priority RAG
                        const activeProjectId = window.activeProject?.id || null;

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
                                session_id: this.config.sessionId,
                                project_id: activeProjectId // ✨ NEW v4.0 - Project context for Priority RAG
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

    {{-- Projects Modal --}}
    <div id="projectsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm"
        role="dialog" aria-labelledby="projectsModalTitle" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-4xl transform rounded-2xl bg-white shadow-2xl transition-all">
                {{-- Modal Header --}}
                <div
                    class="flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-icons text-3xl text-white">folder_special</span>
                        <h2 id="projectsModalTitle" class="text-2xl font-bold text-white">
                            {{ __('projects.projects') }}
                        </h2>
                    </div>
                    <button type="button" onclick="closeProjectsModal()"
                        class="rounded-lg p-2 text-white/80 transition-colors hover:bg-white/10 hover:text-white">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6">
                    {{-- Projects List --}}
                    @if ($projects->count() > 0)
                        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($projects as $project)
                                <div
                                    class="{{ $activeProject && $activeProject->id === $project->id ? 'border-[#D4A574] bg-gradient-to-br from-[#D4A574]/10 to-white shadow-lg' : 'border-gray-200 bg-white hover:border-[#D4A574]/50 hover:shadow-md' }} group relative overflow-hidden rounded-xl border-2 p-4 transition-all">
                                    {{-- Active Badge --}}
                                    @if ($activeProject && $activeProject->id === $project->id)
                                        <div
                                            class="absolute right-2 top-2 flex items-center gap-1 rounded-full bg-[#D4A574] px-2 py-1">
                                            <span class="material-icons text-xs text-white">check_circle</span>
                                            <span class="text-xs font-medium text-white">Attivo</span>
                                        </div>
                                    @endif

                                    {{-- Project Header --}}
                                    <div class="mb-3 flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg"
                                            style="background-color: {{ $project->color ?? '#1B365D' }}20">
                                            <span class="material-icons text-2xl"
                                                style="color: {{ $project->color ?? '#1B365D' }}">{{ $project->icon ?? 'folder' }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-[#1B365D] group-hover:text-[#D4A574]">
                                                {{ $project->name }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Project Description --}}
                                    @if ($project->description)
                                        <p class="mb-3 line-clamp-2 text-sm text-gray-600">
                                            {{ $project->description }}
                                        </p>
                                    @endif

                                    {{-- Project Stats --}}
                                    <div class="mb-3 flex items-center gap-4 text-xs text-gray-500">
                                        <div class="flex items-center gap-1">
                                            <span class="material-icons text-sm">description</span>
                                            <span>{{ $project->documents()->count() }} documenti</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="material-icons text-sm">chat</span>
                                            <span>{{ $project->chatMessages()->count() }} chat</span>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex items-center gap-2 border-t border-gray-200 pt-3">
                                        {{-- Upload Document Button --}}
                                        <button type="button"
                                            onclick="event.stopPropagation(); triggerProjectUpload({{ $project->id }})"
                                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-[#D4A574] px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-[#B89968]">
                                            <span class="material-icons text-sm">upload_file</span>
                                            <span>{{ __('projects.upload_document') }}</span>
                                        </button>

                                        {{-- Select/Activate Button --}}
                                        @if (!$activeProject || $activeProject->id !== $project->id)
                                            <button type="button" onclick="selectProject({{ $project->id }})"
                                                class="flex flex-1 items-center justify-center gap-2 rounded-lg border-2 border-[#1B365D] bg-white px-3 py-2 text-sm font-medium text-[#1B365D] transition-colors hover:bg-[#1B365D] hover:text-white">
                                                <span class="material-icons text-sm">check_circle</span>
                                                <span>{{ __('projects.activate') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-6 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <span class="material-icons mb-3 text-5xl text-gray-400">folder_open</span>
                            <h4 class="mb-2 font-bold text-gray-700">{{ __('projects.no_projects') }}</h4>
                            <p class="text-sm text-gray-500">{{ __('projects.create_first') }}</p>
                        </div>
                    @endif

                    {{-- Create Project Form --}}
                    <div id="createProjectForm"
                        class="hidden rounded-xl border-2 border-[#D4A574] bg-gradient-to-br from-[#D4A574]/5 to-white p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-[#1B365D]">
                                <span class="material-icons mr-2 align-middle">add_circle</span>
                                {{ __('projects.new_project') }}
                            </h3>
                            <button type="button" onclick="toggleCreateForm()"
                                class="text-gray-500 hover:text-gray-700">
                                <span class="material-icons">close</span>
                            </button>
                        </div>

                        <form id="newProjectForm" class="space-y-4">
                            @csrf
                            {{-- Name --}}
                            <div>
                                <label for="projectName"
                                    class="mb-1 block text-sm font-medium text-gray-700">{{ __('projects.name') }}
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="projectName" name="name" required maxlength="100"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]/20"
                                    placeholder="{{ __('projects.name_placeholder') }}">
                                <span class="hidden text-xs text-red-500" id="nameError"></span>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="projectDescription"
                                    class="mb-1 block text-sm font-medium text-gray-700">{{ __('projects.description') }}</label>
                                <textarea id="projectDescription" name="description" rows="3" maxlength="1000"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]/20"
                                    placeholder="{{ __('projects.description_placeholder') }}"></textarea>
                            </div>

                            {{-- Icon Picker --}}
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700">{{ __('projects.icon') }}</label>
                                <input type="hidden" id="projectIcon" name="icon" value="folder">
                                <div class="grid grid-cols-8 gap-2 rounded-lg border border-gray-300 bg-white p-3"
                                    style="max-height: 150px; overflow-y: auto;">
                                    @foreach (['folder', 'folder_special', 'work', 'school', 'account_balance', 'gavel', 'description', 'assignment', 'assessment', 'business_center', 'library_books', 'event_note'] as $icon)
                                        <button type="button" onclick="selectIcon('{{ $icon }}')"
                                            class="icon-option {{ $icon === 'folder' ? 'border-[#D4A574] bg-[#D4A574]/10' : 'hover:bg-gray-50' }} flex h-10 w-10 items-center justify-center rounded-lg border-2 border-transparent transition-all hover:border-[#D4A574]"
                                            data-icon="{{ $icon }}">
                                            <span class="material-icons text-gray-700">{{ $icon }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Color Picker --}}
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700">{{ __('projects.color') }}</label>
                                <input type="hidden" id="projectColor" name="color" value="#1B365D">
                                <div class="flex flex-wrap gap-2">
                                    @foreach (['#1B365D', '#2D5016', '#D4A574', '#6B6B6B', '#C13120', '#E67E22', '#8E44AD'] as $color)
                                        <button type="button" onclick="selectColor('{{ $color }}')"
                                            class="color-option {{ $color === '#1B365D' ? 'border-white ring-2 ring-[#D4A574]' : 'border-transparent' }} h-10 w-10 rounded-lg border-2 transition-all hover:scale-110"
                                            style="background-color: {{ $color }}"
                                            data-color="{{ $color }}">
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" onclick="toggleCreateForm()"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                    {{ __('projects.cancel') }}
                                </button>
                                <button type="submit" id="submitProjectBtn"
                                    class="rounded-lg border-0 bg-[#D4A574] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#B89968] disabled:cursor-not-allowed disabled:opacity-50">
                                    <span class="material-icons mr-2 align-middle text-sm">save</span>
                                    {{ __('projects.create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                    @if ($activeProject)
                        <button type="button" onclick="removeProject()"
                            class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50">
                            <span class="material-icons mr-2 text-sm">close</span>
                            {{ __('projects.remove_context') }}
                        </button>
                    @endif
                    <button type="button" onclick="closeProjectsModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        {{ __('projects.close') }}
                    </button>
                    <button type="button" onclick="toggleCreateForm()"
                        class="rounded-lg border-0 bg-[#D4A574] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#B89968]">
                        <span class="material-icons mr-2 text-sm">add</span>
                        {{ __('projects.create_new') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden File Inputs for Each Project --}}
        @foreach ($projects as $project)
            <input type="file" id="projectUploadInput_{{ $project->id }}" accept=".pdf,.docx,.txt,.md"
                class="hidden" onchange="handleProjectDocumentUpload(event, {{ $project->id }})">
        @endforeach
    </div>

    @push('scripts')
        <script>
            // ✨ NEW v4.0 - Initialize active project for Priority RAG
            window.activeProject = @json($activeProject ?? null);

            // Projects Modal Handler
            function openProjectsModal() {
                const modal = document.getElementById('projectsModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeProjectsModal() {
                const modal = document.getElementById('projectsModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }

            // Listen for modal action from sidebar
            document.addEventListener('click', function(e) {
                const target = e.target.closest('[data-action="open-projects-modal"]');
                if (target) {
                    e.preventDefault();
                    openProjectsModal();
                }
            });

            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeProjectsModal();
                }
            });

            // Close modal on backdrop click
            document.getElementById('projectsModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeProjectsModal();
                }
            });

            // ✨ NEW v4.0 - Project Form Management
            function toggleCreateForm() {
                const form = document.getElementById('createProjectForm');
                if (form) {
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) {
                        document.getElementById('projectName').focus();
                    } else {
                        // Reset form on close
                        document.getElementById('newProjectForm').reset();
                        document.getElementById('projectIcon').value = 'folder';
                        document.getElementById('projectColor').value = '#1B365D';
                        resetIconSelection('folder');
                        resetColorSelection('#1B365D');
                    }
                }
            }

            function selectIcon(iconName) {
                document.getElementById('projectIcon').value = iconName;
                resetIconSelection(iconName);
            }

            function resetIconSelection(activeIcon) {
                document.querySelectorAll('.icon-option').forEach(btn => {
                    if (btn.dataset.icon === activeIcon) {
                        btn.classList.add('border-[#D4A574]', 'bg-[#D4A574]/10');
                        btn.classList.remove('border-transparent');
                    } else {
                        btn.classList.remove('border-[#D4A574]', 'bg-[#D4A574]/10');
                        btn.classList.add('border-transparent');
                    }
                });
            }

            function selectColor(colorHex) {
                document.getElementById('projectColor').value = colorHex;
                resetColorSelection(colorHex);
            }

            function resetColorSelection(activeColor) {
                document.querySelectorAll('.color-option').forEach(btn => {
                    if (btn.dataset.color === activeColor) {
                        btn.classList.add('border-white', 'ring-2', 'ring-[#D4A574]');
                        btn.classList.remove('border-transparent');
                    } else {
                        btn.classList.remove('border-white', 'ring-2', 'ring-[#D4A574]');
                        btn.classList.add('border-transparent');
                    }
                });
            }

            // ✨ NEW v4.0 - AJAX Form Submit
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('newProjectForm');
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const submitBtn = document.getElementById('submitProjectBtn');
                        const nameInput = document.getElementById('projectName');
                        const nameError = document.getElementById('nameError');

                        // Client-side validation
                        if (!nameInput.value.trim()) {
                            nameError.textContent = '{{ __('projects.name_required') }}';
                            nameError.classList.remove('hidden');
                            nameInput.focus();
                            return;
                        }

                        // Disable submit button
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="material-icons mr-2 text-sm align-middle animate-spin">hourglass_empty</span>{{ __('projects.creating') }}';

                        const formData = {
                            name: nameInput.value.trim(),
                            description: document.getElementById('projectDescription').value.trim(),
                            icon: document.getElementById('projectIcon').value,
                            color: document.getElementById('projectColor').value,
                        };

                        try {
                            const response = await fetch('{{ route('pa.projects.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(formData)
                            });

                            const data = await response.json();

                            if (data.success) {
                                // Reload page to show new project in list
                                window.location.reload();
                            } else {
                                // Show error
                                nameError.textContent = data.message ||
                                    '{{ __('projects.create_error') }}';
                                nameError.classList.remove('hidden');

                                // Re-enable button
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    '<span class="material-icons mr-2 text-sm align-middle">save</span>{{ __('projects.create') }}';
                            }
                        } catch (error) {
                            console.error('[ProjectForm] Error:', error);
                            nameError.textContent = '{{ __('projects.network_error') }}';
                            nameError.classList.remove('hidden');

                            // Re-enable button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                '<span class="material-icons mr-2 text-sm align-middle">save</span>{{ __('projects.create') }}';
                        }
                    });
                }
            });

            // ✨ NEW v4.0 - Project Selection
            async function selectProject(projectId) {
                try {
                    const response = await fetch('{{ route('pa.projects.set-active') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            project_id: projectId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Reload chat page with new project context
                        window.location.reload();
                    } else {
                        console.error('[selectProject] Failed:', data.message);
                        alert(data.message || '{{ __('projects.select_error') }}');
                    }
                } catch (error) {
                    console.error('[selectProject] Error:', error);
                    alert('{{ __('projects.network_error') }}');
                }
            }

            // ✨ NEW v4.0 - Remove Project Context
            async function removeProject() {
                if (!confirm('{{ __('projects.remove_confirm') }}')) {
                    return;
                }

                try {
                    const response = await fetch('{{ route('pa.projects.remove-active') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Reload chat page without project context (generic PA chat)
                        window.location.reload();
                    } else {
                        console.error('[removeProject] Failed:', data.message);
                        alert(data.message || '{{ __('projects.remove_error') }}');
                    }
                } catch (error) {
                    console.error('[removeProject] Error:', error);
                    alert('{{ __('projects.network_error') }}');
                }
            }

            // ✨ NEW v4.0 - Document Upload Management
            function triggerDocumentUpload() {
                const input = document.getElementById('documentUploadInput');
                if (input) {
                    input.click();
                }
            }

            async function handleDocumentUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Client-side validation
                const allowedTypes = ['application/pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain',
                    'text/markdown'
                ];
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|docx|txt|md)$/i)) {
                    alert('{{ __('projects.invalid_file_type') }}');
                    event.target.value = ''; // Reset input
                    return;
                }

                if (file.size > maxSize) {
                    alert('{{ __('projects.file_too_large', ['size' => 10]) }}');
                    event.target.value = ''; // Reset input
                    return;
                }

                const activeProjectId = window.activeProject?.id;
                if (!activeProjectId) {
                    alert('{{ __('projects.no_active_project') }}');
                    return;
                }

                // Show uploading notification
                const uploadBtn = event.target.previousElementSibling;
                const originalHTML = uploadBtn.innerHTML;
                uploadBtn.disabled = true;
                uploadBtn.innerHTML =
                    '<span class="material-icons text-sm animate-spin">hourglass_empty</span><span class="hidden sm:inline">{{ __('projects.uploading') }}</span>';

                const formData = new FormData();
                formData.append('document', file);

                try {
                    const response = await fetch(`/pa/projects/${activeProjectId}/documents/upload`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show success message
                        alert(data.message || '{{ __('projects.document_uploaded_successfully', ['filename' => '']) }}');

                        // Reset input
                        event.target.value = '';

                        // Optional: Reload to update document count (or update DOM dynamically)
                        // window.location.reload();
                    } else {
                        alert(data.message || '{{ __('projects.upload_error') }}');
                        event.target.value = '';
                    }
                } catch (error) {
                    console.error('[handleDocumentUpload] Error:', error);
                    alert('{{ __('projects.network_error') }}');
                    event.target.value = '';
                } finally {
                    // Restore button
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = originalHTML;
                }
            }

            // ✨ NEW v4.0 - Project Upload from Modal
            function triggerProjectUpload(projectId) {
                const input = document.getElementById(`projectUploadInput_${projectId}`);
                if (input) {
                    input.click();
                }
            }

            async function handleProjectDocumentUpload(event, projectId) {
                const file = event.target.files[0];
                if (!file) return;

                // Client-side validation
                const allowedTypes = ['application/pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain',
                    'text/markdown'
                ];
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|docx|txt|md)$/i)) {
                    alert('{{ __('projects.invalid_file_type') }}');
                    event.target.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('{{ __('projects.file_too_large', ['size' => 10]) }}');
                    event.target.value = '';
                    return;
                }

                // Find the upload button for this project
                const uploadBtn = event.target.previousElementSibling;
                const originalHTML = uploadBtn ? uploadBtn.innerHTML : '';

                if (uploadBtn) {
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML =
                        '<span class="material-icons text-sm animate-spin">hourglass_empty</span><span>{{ __('projects.uploading') }}</span>';
                }

                const formData = new FormData();
                formData.append('document', file);

                try {
                    const response = await fetch(`/pa/projects/${projectId}/documents/upload`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show success message
                        alert(data.message || '{{ __('projects.document_uploaded_successfully', ['filename' => '']) }}');

                        // Reset input
                        event.target.value = '';

                        // Reload modal to update document count
                        window.location.reload();
                    } else {
                        alert(data.message || '{{ __('projects.upload_error') }}');
                        event.target.value = '';
                    }
                } catch (error) {
                    console.error('[handleProjectDocumentUpload] Error:', error);
                    alert('{{ __('projects.network_error') }}');
                    event.target.value = '';
                } finally {
                    // Restore button
                    if (uploadBtn) {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = originalHTML;
                    }
                }
            }
        </script>

        {{-- AI Processing Panel Controller --}}
        <script src="{{ asset('js/ai-processing-panel.js') }}?v={{ time() }}"></script>
    @endpush
</x-pa-layout>
