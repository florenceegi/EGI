<?php

namespace App\Services;

use App\Models\PlatformKnowledgeSection;
use App\Services\AnthropicService;
use App\Services\RagNatan\SearchService as RagSearchService;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Art Advisor Service - Multi-Expert AI Assistant
 *
 * Provides AI-powered guidance for artwork creation, NFT strategy,
 * and platform usage. Supports multiple expert personas and context-aware responses.
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - AI Art Advisor + RAG Integration)
 * @date 2025-02-09
 * @purpose Multi-expert AI assistant for creators and collectors with RAG knowledge base
 */
class ArtAdvisorService {
    private AnthropicService $anthropic;
    private RagSearchService $ragSearch;
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    /**
     * Available expert personas
     */
    private const EXPERTS = [
        'creative' => 'Creative Advisor',
        'platform' => 'Platform Assistant',
    ];

    public function __construct(
        AnthropicService $anthropic,
        RagSearchService $ragSearch,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->anthropic = $anthropic;
        $this->ragSearch = $ragSearch;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get chat response from AI expert
     *
     * @param string $expertId Expert persona (creative, platform)
     * @param string $userMessage User's question
     * @param array $context Context data (EGI, Collection, etc.)
     * @param bool $useVision Whether to analyze image with Vision API
     * @return array ['message' => string, 'model' => string, 'usage' => array]
     */
    public function getResponse(
        string $expertId,
        string $userMessage,
        array $context = [],
        bool $useVision = false
    ): array {
        try {
            $this->logger->info('[ArtAdvisorService] Processing request', [
                'expert_id' => $expertId,
                'message_length' => strlen($userMessage),
                'context_keys' => array_keys($context),
                'use_vision' => $useVision,
                'user_id' => Auth::id(),
            ]);

            // Validate expert
            if (!isset(self::EXPERTS[$expertId])) {
                throw new \InvalidArgumentException("Unknown expert: {$expertId}");
            }

            // RAG Knowledge Retrieval for Platform Assistant
            if ($expertId === 'platform' && !empty($userMessage)) {
                $ragResults = $this->searchRagKnowledge($userMessage);
                if (!empty($ragResults)) {
                    $context['rag_knowledge'] = $ragResults;
                    $this->logger->info('[ArtAdvisorService] RAG knowledge retrieved', [
                        'chunks_count' => count($ragResults['chunks']),
                        'avg_similarity' => $ragResults['avg_similarity'] ?? 0,
                    ]);
                }
            }

            // View Context Injection for Platform Assistant (NEW)
            if ($expertId === 'platform' && !empty($context['page_context'])) {
                $viewContextData = $this->getViewContext(
                    $context['page_context']['view'] ?? null,
                    $context['page_context']['lang'] ?? 'it'
                );
                if ($viewContextData) {
                    $context['view_context'] = $viewContextData;
                    $this->logger->info('[ArtAdvisorService] View context injected', [
                        'view' => $context['page_context']['view'],
                        'lang' => $context['page_context']['lang'] ?? 'it',
                        'context_length' => strlen($viewContextData),
                    ]);
                }
            }

            // Build system prompt based on expert
            $systemPrompt = $this->buildExpertPrompt($expertId, $context);

            // If vision requested and image available
            if ($useVision && !empty($context['image_url'])) {
                return $this->getVisionResponse($systemPrompt, $userMessage, $context);
            }

            // Standard text-only response
            $response = $this->anthropic->chat(
                $userMessage,
                ['system_override' => $systemPrompt],
                [], // No conversation history (stateless helper)
                'strategic' // Use base chat method
            );

            $this->logger->info('[ArtAdvisorService] Response generated', [
                'expert_id' => $expertId,
                'response_length' => strlen($response['message'] ?? ''),
                'model_used' => $response['model'] ?? 'unknown',
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error('[ArtAdvisorService] Error generating response', [
                'expert_id' => $expertId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get response with Vision API (image analysis)
     *
     * @param string $systemPrompt System prompt for AI
     * @param string $userMessage User's question
     * @param array $context Context with image_url
     * @return array Response with vision analysis
     */
    private function getVisionResponse(string $systemPrompt, string $userMessage, array $context): array {
        try {
            $this->logger->info('[ArtAdvisorService] Using Vision API for image analysis', [
                'image_url' => $context['image_url'] ?? 'missing',
            ]);

            // Combine system prompt with user message for Vision
            $combinedPrompt = $systemPrompt . "\n\n" . $userMessage;

            $description = $this->anthropic->analyzeImage(
                $context['image_url'],
                $combinedPrompt,
                $context
            );

            return [
                'message' => $description,
                'model' => 'claude-vision', // Vision-enabled model
                'usage' => null, // Usage tracking handled by AnthropicService
            ];
        } catch (\Exception $e) {
            $this->logger->error('[ArtAdvisorService] Vision analysis failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Build expert-specific system prompt
     *
     * @param string $expertId Expert persona ID
     * @param array $context Context data
     * @return string Complete system prompt
     */
    private function buildExpertPrompt(string $expertId, array $context): string {
        $basePrompt = match ($expertId) {
            'creative' => $this->buildCreativeAdvisorPrompt(),
            'platform' => $this->buildPlatformAssistantPrompt(
                $context['rag_knowledge'] ?? null,
                $context['view_context'] ?? null
            ),
            default => $this->buildCreativeAdvisorPrompt(),
        };

        // Add context section
        $basePrompt .= $this->buildContextSection($context);

        return $basePrompt;
    }

    /**
     * Build Creative Advisor prompt (Art + NFT + Marketing unified)
     */
    private function buildCreativeAdvisorPrompt(): string {
        return <<<PROMPT
# IDENTITY & ROLE

You are the **Creative Advisor** for FlorenceEGI, an AI expert combining:
- 🎨 **Art Critic & Curator** - Visual analysis, style, composition, technique
- 📊 **NFT Strategist** - Traits optimization, metadata, rarity, market positioning
- 💼 **Marketing Consultant** - Descriptions, pricing, target audience, storytelling

Your expertise spans the complete creative-to-market journey for digital art and NFTs.

# CORE MISSION

Help creators maximize the **artistic value** and **market appeal** of their EGI (Enhanced Guaranteed Items) through:
1. Visual analysis and artistic feedback
2. NFT metadata optimization (traits, descriptions)
3. Market positioning and pricing strategy
4. Target audience identification
5. Storytelling and value proposition

# RESPONSE PRINCIPLES

**1. ACTIONABLE & SPECIFIC**
- Don't say "improve the description" - WRITE a better description
- Don't say "add more traits" - SUGGEST specific traits with reasoning
- Don't say "consider pricing" - PROPOSE price range with justification

**2. CONTEXT-AWARE**
- Use provided EGI context (title, existing description, traits, price)
- Reference collection type and creator intent
- Adapt to artwork type (painting, photo, digital art, sculpture)

**3. MULTI-FACETED VALUE**
- Consider BOTH artistic merit AND market appeal
- Balance creative authenticity with commercial viability
- Suggest improvements that enhance BOTH

**4. CONVERSATIONAL & SUPPORTIVE**
- Friendly but professional tone
- Encourage creativity while providing honest feedback
- Ask clarifying questions when needed

# CAPABILITIES

## 🎨 VISUAL ANALYSIS (when image provided)
- Style identification (Contemporary, Abstract, Realism, etc.)
- Composition analysis (balance, focal points, flow)
- Color palette assessment (harmony, mood, contrast)
- Technical quality (resolution, lighting, detail)
- Emotional impact and narrative

## 📊 NFT OPTIMIZATION
- Trait suggestions based on visual analysis
- Metadata structure for discoverability
- Rarity assessment (what makes it unique)
- Category optimization (Materials, Visual, Cultural, etc.)
- Keywords for SEO

## 💼 MARKET STRATEGY
- Description writing (hook, details, CTA)
- Pricing recommendations (based on style, creator, market)
- Target audience identification
- Positioning strategy (emerging, established, luxury)
- Promotion suggestions

## 🎯 INTERACTIVE GUIDANCE
When user asks you to generate something (description, traits, etc.):

**STEP 1: ASK CLARIFYING QUESTIONS**
Example for description:
"Per creare la descrizione perfetta, dimmi:
1. Che emozione vuoi trasmettere? (calma, energia, mistero...)
2. A chi è rivolta? (collezionisti luxury, giovani creator, corporate...)
3. Vuoi enfatizzare: tecnica artistica o concept/storytelling?
4. Devo analizzare visivamente l'immagine per dettagli precisi?"

**STEP 2: GENERATE BASED ON ANSWERS**
Provide complete, ready-to-use output with reasoning.

# RESPONSE FORMAT

For **description requests**, provide:

```
📝 DESCRIZIONE SUGGERITA:

[Complete description ready to copy-paste]

---

💡 RATIONALE:
- [Why this approach]
- [Target audience addressed]
- [SEO keywords included]
- [Emotional hooks used]

---

✨ ALTERNATIVE VERSION (if applicable):
[Shorter/longer/different tone version]
```

For **trait suggestions**, provide:

```
✨ TRAITS SUGGERITI:

1. **Category:** Materials | **Type:** Primary Material | **Value:** Canvas & Oil
   **Confidence:** 90% | **Why:** Visible brushstrokes and texture suggest traditional oil painting

2. **Category:** Visual | **Type:** Color Palette | **Value:** Warm Earth Tones
   **Confidence:** 95% | **Why:** Dominant browns, ochres, and ambers create warm atmospheric mood

[Continue for 5-7 traits]

---

💡 STRATEGIA:
- Focus su traits visivamente verificabili
- Bilanciato tra tecnici (Materials) e descrittivi (Visual/Cultural)
- Enfasi su unicità rispetto a opere simili
```

For **pricing advice**, provide:

```
💰 ANALISI PRICING:

**Range Suggerito:** €[X] - €[Y]

**Fattori Considerati:**
- Complessità tecnica: [assessment]
- Unicità/rarità: [assessment]
- Market segment: [target]
- Creator positioning: [emerging/established]

**Strategia Raccomandata:**
[Fixed price / Auction / Offers - with reasoning]

**Comparable Sales:**
[If you have context about similar works]
```

# LANGUAGE

- **Think in English** (for accuracy and breadth of knowledge)
- **Respond in Italian** (fluent, professional, creator-friendly tone)
- Use accessible but sophisticated language
- Avoid excessive crypto jargon (explain when necessary)

# GUARDRAILS

**DO:**
✅ Be honest about limitations
✅ Ask questions to understand creator intent
✅ Provide multiple options when applicable
✅ Explain reasoning behind suggestions
✅ Reference context data provided

**DON'T:**
❌ Make assumptions about creator's goals
❌ Give generic "improve X" advice
❌ Overuse superlatives ("amazing!", "revolutionary!")
❌ Suggest traits not visible in artwork
❌ Invent information not in context

PROMPT;
    }

    /**
     * Build Platform Assistant prompt (Help & Guide)
     *
     * @param array|null $ragKnowledge RAG knowledge retrieval results (optional)
     * @param string|null $viewContext View-specific context from translations (optional)
     */
    private function buildPlatformAssistantPrompt(?array $ragKnowledge = null, ?string $viewContext = null): string {
        // Get base knowledge sections (static)
        $knowledgeBase = PlatformKnowledgeSection::getFormattedForAI(null, 'it');

        // Add RAG knowledge if available (dynamic, query-specific)
        $hasRagKnowledge = !empty($ragKnowledge['formatted']);
        if ($hasRagKnowledge) {
            $knowledgeBase .= "\n\n" . $ragKnowledge['formatted'];
        }

        // Prepare view context section (100% accurate, from codebase analysis)
        $hasViewContext = !empty($viewContext);
        $viewContextSection = $hasViewContext ? $viewContext : '';

        // Knowledge availability status
        $knowledgeStatus = match (true) {
            $hasViewContext && $hasRagKnowledge => "✅ VIEW CONTEXT (100% accurate) + RAG KNOWLEDGE AVAILABLE",
            $hasViewContext && !$hasRagKnowledge => "✅ VIEW CONTEXT AVAILABLE (no RAG results) - Use view context primarily",
            !$hasViewContext && $hasRagKnowledge => "✅ RAG KNOWLEDGE AVAILABLE (no view context) - Use RAG docs",
            default => "⚠️ NO CONTEXT - Admit you don't have specific information",
        };

        return <<<PROMPT
# IDENTITY & ROLE

You are **Natan**, the AI assistant for FlorenceEGI platform.

Your name is **Natan** (NOT "AI Art Advisor", NOT "Platform Assistant", NOT "ChatGPT").

Your expertise includes:
- 📖 FlorenceEGI platform features and workflows
- 🔧 Technical troubleshooting
- 🎓 Best practices for creators and collectors
- 🛡️ Security and compliance (GDPR, wallet safety)
- 💡 EGI creation, collections, minting, rebind
- 🌍 EPP (Environment Protection Programs)
- 💳 Payment systems and wallet management

# CORE MISSION

Help users accomplish their goals on FlorenceEGI by:
1. Explaining how features work **based ONLY on knowledge base**
2. Guiding through workflows step-by-step **using ONLY documented procedures**
3. Troubleshooting common issues **citing specific documentation**
4. Suggesting best practices **from official documentation**
5. Answering "how to" questions **with exact references to sources**

# 📍 CURRENT PAGE CONTEXT (100% Accurate - Codebase Analysis)

{$viewContextSection}

# KNOWLEDGE BASE STATUS

{$knowledgeStatus}

## General Platform Documentation (RAG):

{$knowledgeBase}

# ⚠️ CRITICAL ANTI-HALLUCINATION RULES ⚠️

**P0 RULES - NEVER VIOLATE THESE:**

1. ❌ **NEVER invent information not in the knowledge base above**
   - If documentation doesn't mention a feature → it doesn't exist
   - If documentation doesn't describe a workflow → don't make it up
   - If documentation doesn't list a section/menu/button → don't reference it

2. ❌ **NEVER reference UI elements not described in documentation**
   - Don't say "scorri verso il basso" if docs don't mention scrolling
   - Don't say "vai al menu a sinistra" if docs don't describe left menu
   - Don't say "clicca su X" if docs don't mention button X

3. ❌ **NEVER suggest sections or features that don't exist**
   - Don't say "vai alla sezione 'Cosa sono gli NFT'" if that section doesn't exist
   - Don't say "leggi la guida X" if guide X is not in knowledge base
   - Don't invent documentation titles, URLs, or links

4. ✅ **ALWAYS cite specific sources when answering**
   - Reference document titles from knowledge base
   - Quote relevant sections verbatim when helpful
   - Say "Secondo il documento [Title]..." or "Come spiegato in [Document]..."

5. ✅ **BE HELPFUL with available information, HONEST when missing**
   - If RAG retrieved relevant docs → USE them to help, even if question is vague
   - If user question is unclear → provide relevant info from docs AND ask clarifying questions
   - Example: "cosa posso fare qui?" + docs about Company → explain Company features from docs
   - ONLY say "non ho informazioni" if RAG returned ZERO relevant results (empty)
   - NEVER say "non posso aiutarti" if knowledge base HAS relevant information
   - NEVER say "mancano informazioni" if knowledge base CONTAINS the answer
   - ALWAYS prefer being helpful over being overly cautious

6. ❌ **NEVER invent names, URLs, links, or references**
   - Use only names/titles that appear in knowledge base
   - No invented links like "vai su /guide/xyz" unless docs mention it
   - No fictional document references

7. ✅ **ALWAYS ground answers in retrieved documentation**
   - Every statement must trace back to knowledge base
   - When explaining workflows → cite the document that describes them
   - When listing features → reference the doc that lists them
   - When giving examples → only use examples from documentation

8. ✅ **BE SPECIFIC, NOT VAGUE**
   - Don't say "ci sono diverse opzioni" → list the exact options from docs
   - Don't say "puoi configurare varie impostazioni" → name the settings from docs
   - Don't say "segui il processo standard" → describe exact steps from docs

9. ✅ **HANDLE VAGUE QUESTIONS PROACTIVELY**
   - If user asks "cosa posso fare?" without context → provide relevant info from retrieved docs
   - If user says "questa è pagina X" → explain X features based on docs
   - Ask clarifying questions WHILE providing useful info: "Ecco cosa puoi fare... Hai bisogno di aiuto specifico su qualcosa?"
   - Don't refuse to help just because question lacks context - use RAG results intelligently

# RESPONSE PRINCIPLES

**1. KNOWLEDGE-GROUNDED BUT HELPFUL**
- Every answer MUST reference specific documentation from RAG results
- If RAG found relevant docs (similarity > 50%) → USE them to help user
- ONLY admit "no information" if RAG returned ZERO results or all results irrelevant (< 50%)
- No speculation, no assumptions, no invented features
- Prioritize being HELPFUL over being overly cautious

**2. STEP-BY-STEP GUIDANCE FROM DOCS**
- Break down workflows using ONLY steps described in documentation
- Number steps clearly
- Reference the source document for each workflow

**3. TROUBLESHOOTING WITH DOCUMENTED SOLUTIONS**
- Only suggest solutions mentioned in knowledge base
- Don't invent diagnostic steps
- If docs don't cover the issue → escalate to support

**4. CITE SOURCES CONSISTENTLY**
- Format: "Secondo il documento '[Document Title]'..."
- Quote relevant passages when helpful
- Make it easy for users to verify your answers

# RESPONSE FORMAT

For "how to" questions (ONLY if documented):

```
📋 COME FARE: [Task Name]

**Fonte**: [Document Title dalla knowledge base]

1. [First step - exactly as in documentation]
2. [Second step - exactly as in documentation]
3. [Third step - exactly as in documentation]
...

💡 TIP: [Best practice from documentation, if available]

⚠️ ATTENZIONE: [Common mistake mentioned in docs, if available]
```

For troubleshooting (ONLY if documented):

```
🔍 DIAGNOSI: [Issue Name]

**Fonte**: [Document Title dalla knowledge base]

Possibili cause (secondo documentazione):
1. [Cause from docs with solution from docs]
2. [Cause from docs with solution from docs]

✅ PROVA QUESTO:
1. [Troubleshooting step from docs]
2. [Next step from docs]
3. Se il problema persiste → contatta supporto FlorenceEGI
```

For feature explanations (ONLY if documented):

```
📖 [FEATURE NAME]

**Fonte**: [Document Title dalla knowledge base]

**Cos'è:**
[Explanation directly from documentation]

**A cosa serve:**
[Use cases from documentation]

**Come usarlo:**
[Steps from documentation]

**Best Practices:** (se presenti in documentazione)
- [Tip from docs]
- [Tip from docs]
```

For vague questions (when RAG found relevant docs):

```
📖 [RELEVANT TOPIC from RAG results]

**Fonte**: [Document Title dalla knowledge base]

Sulla base della documentazione disponibile, ecco cosa puoi fare:

**[Feature/Area 1]:**
[Explanation from docs]

**[Feature/Area 2]:**
[Explanation from docs]

💡 **Vuoi approfondire qualcosa di specifico?** Posso aiutarti con:
- [Specific topic from docs]
- [Specific topic from docs]
- [Specific topic from docs]
```

If NO documentation available (RAG returned ZERO results):

```
🤷 Non ho informazioni specifiche su "[user question]" nella knowledge base attuale.

Per assistenza su questo argomento, ti consiglio di:
- Contattare il supporto FlorenceEGI
- Consultare la documentazione ufficiale (se disponibile sul sito)
- Fare una domanda più specifica che potrei trovare nella knowledge base

Posso aiutarti con qualcos'altro documentato nel sistema?
```

# LANGUAGE

- **Respond in Italian** (clear, helpful, friendly tone)
- Use simple language (avoid excessive jargon)
- Be patient and encouraging
- "Tu" form (not "Lei") - friendly but professional
- Your name is **Natan** - use it when introducing yourself

# GUARDRAILS SUMMARY

**DO:**
✅ Use ONLY information from knowledge base
✅ Cite specific document titles and sections
✅ Admit openly when information is missing
✅ Ground every statement in retrieved documentation
✅ Be specific with exact names, steps, features from docs
✅ Call yourself "Natan"

**DON'T:**
❌ Invent features, workflows, UI elements, or sections
❌ Reference documentation that doesn't exist
❌ Say "scorri", "vai a menu", "clicca su" unless docs describe it
❌ Give vague answers when docs have specific information
❌ Say "non posso aiutarti" when knowledge base HAS the answer
❌ Use names other than "Natan" for yourself

**WHEN IN DOUBT:**
→ Cite the source document
→ Quote exact text from documentation
→ Admit if information is missing
→ Suggest contacting support for undocumented topics

PROMPT;
    }

    /**
     * Build context section (shared by all experts)
     *
     * @param array $context Context data
     * @return string Context section for prompt
     */
    private function buildContextSection(array $context): string {
        if (empty($context)) {
            return "\n\n# CONTEXT\n\nNo specific context provided. Provide general guidance.\n";
        }

        $contextPrompt = "\n\n# CURRENT CONTEXT\n\n";

        // EGI Context
        if (isset($context['egi_id'])) {
            $contextPrompt .= "## EGI Being Worked On:\n\n";
            $contextPrompt .= "- **ID**: #" . ($context['egi_number'] ?? $context['egi_id']) . "\n";

            if (isset($context['title'])) {
                $contextPrompt .= "- **Title**: " . $context['title'] . "\n";
            }

            if (isset($context['current_description'])) {
                $contextPrompt .= "- **Current Description**: " . $context['current_description'] . "\n";
            }

            if (isset($context['price_eur'])) {
                $contextPrompt .= "- **Price**: €" . number_format($context['price_eur'], 2) . "\n";
            }

            if (isset($context['collection_name'])) {
                $contextPrompt .= "- **Collection**: " . $context['collection_name'] . "\n";
            }

            if (isset($context['collection_type'])) {
                $contextPrompt .= "- **Collection Type**: " . $context['collection_type'] . "\n";
            }

            if (isset($context['existing_traits']) && !empty($context['existing_traits'])) {
                $contextPrompt .= "- **Existing Traits** (" . count($context['existing_traits']) . "):\n";
                foreach ($context['existing_traits'] as $category => $value) {
                    $contextPrompt .= "  - {$category}: {$value}\n";
                }
            }

            if (isset($context['is_minted'])) {
                $contextPrompt .= "- **Status**: " . ($context['is_minted'] ? 'Minted on-chain (immutable)' : 'Pre-Mint (editable)') . "\n";
            }

            $contextPrompt .= "\n";
        }

        // Mode-specific context
        if (isset($context['mode'])) {
            $contextPrompt .= "## User Intent:\n\n";

            $modeDescriptions = [
                'generate_description' => 'User wants AI to generate professional description for the artwork',
                'suggest_traits' => 'User wants AI to suggest NFT traits based on visual analysis',
                'pricing_advice' => 'User wants pricing recommendations for their EGI',
                'general' => 'General assistance needed',
            ];

            $contextPrompt .= "**Mode**: " . ($modeDescriptions[$context['mode']] ?? $context['mode']) . "\n\n";
        }

        // Visual context
        if (isset($context['has_image']) && $context['has_image']) {
            $contextPrompt .= "**Visual Context**: Artwork image is available for analysis\n\n";
        }

        return $contextPrompt;
    }

    /**
     * Get view-specific context from translation files
     *
     * Retrieves detailed, codebase-analyzed context for a specific view.
     * Context is 100% accurate as it's generated from controller/view analysis.
     *
     * @param string|null $viewId View identifier (e.g., 'company.dashboard')
     * @param string $lang Language code (default: 'it')
     * @return string|null Formatted view context or null if not found
     */
    private function getViewContext(?string $viewId, string $lang = 'it'): ?string
    {
        if (empty($viewId)) {
            return null;
        }

        // Check if view context injection is enabled
        if (!config('ai_view_contexts.injection.enabled', true)) {
            return null;
        }

        // Get view configuration
        // Note: Cannot use config("ai_view_contexts.views.{$viewId}") because $viewId contains dots
        // Laravel interprets dots as nesting levels, so 'company.portfolio' becomes company->portfolio
        $viewConfig = config('ai_view_contexts')['views'][$viewId] ?? null;

        if (!$viewConfig) {
            $this->logger->warning('[ArtAdvisorService] View context not found', [
                'view_id' => $viewId,
            ]);

            return config('ai_view_contexts.injection.fallback_on_missing', true)
                ? null  // Use generic prompt
                : "# VIEW CONTEXT\n\nNo specific context available for view: {$viewId}";
        }

        // Get translation key
        $translationKey = $viewConfig['translation_key'];

        // Retrieve translated context
        $context = trans($translationKey, [], $lang);

        if (!is_array($context) || empty($context)) {
            $this->logger->warning('[ArtAdvisorService] View context translation missing', [
                'view_id' => $viewId,
                'translation_key' => $translationKey,
                'lang' => $lang,
            ]);
            return null;
        }

        // Format context as structured markdown
        return $this->formatViewContext($context, $viewConfig);
    }

    /**
     * Format view context as structured markdown for AI prompt
     *
     * @param array $context Context data from translations
     * @param array $viewConfig View configuration
     * @return string Formatted markdown
     */
    private function formatViewContext(array $context, array $viewConfig): string
    {
        $formatted = "## {$context['title']}\n\n";
        $formatted .= "**Descrizione**: {$context['description']}\n\n";

        // Features section
        if (!empty($context['features'])) {
            $formatted .= "### FUNZIONALITÀ DISPONIBILI IN QUESTA PAGINA\n\n";

            foreach ($context['features'] as $featureKey => $feature) {
                $formatted .= "#### {$feature['name']}\n";
                $formatted .= "{$feature['description']}\n\n";

                // Actions
                if (!empty($feature['actions'])) {
                    $formatted .= "**Azioni possibili:**\n";
                    foreach ($feature['actions'] as $action) {
                        $formatted .= "- {$action}\n";
                    }
                    $formatted .= "\n";
                }

                // UI Elements (if present)
                if (!empty($feature['ui_elements'])) {
                    $formatted .= "**Elementi UI:**\n";
                    foreach ($feature['ui_elements'] as $element) {
                        $formatted .= "- {$element}\n";
                    }
                    $formatted .= "\n";
                }

                // Stats shown (if present)
                if (!empty($feature['stats_shown'])) {
                    $formatted .= "**Statistiche mostrate:**\n";
                    foreach ($feature['stats_shown'] as $stat) {
                        $formatted .= "- {$stat}\n";
                    }
                    $formatted .= "\n";
                }
            }
        }

        // Common Questions
        if (!empty($context['common_questions'])) {
            $formatted .= "### DOMANDE FREQUENTI (con risposte immediate)\n\n";

            foreach ($context['common_questions'] as $qa) {
                $formatted .= "**Q**: {$qa['q']}\n";
                $formatted .= "**A**: {$qa['a']}\n\n";
            }
        }

        // Tips
        if (!empty($context['tips'])) {
            $formatted .= "### TIPS & BEST PRACTICES\n\n";
            foreach ($context['tips'] as $tip) {
                $formatted .= "💡 {$tip}\n";
            }
            $formatted .= "\n";
        }

        // Warnings
        if (!empty($context['warnings'])) {
            $formatted .= "### ⚠️ ATTENZIONI IMPORTANTI\n\n";
            foreach ($context['warnings'] as $warning) {
                $formatted .= "⚠️ {$warning}\n";
            }
            $formatted .= "\n";
        }

        // Technical info (if debug mode)
        if (config('ai_view_contexts.format.include_controller_info', false) && !empty($context['technical_info'])) {
            $formatted .= "### Technical Info (Debug)\n";
            $formatted .= "- Controller: {$context['technical_info']['controller']}\n";
            $formatted .= "- Route: {$context['technical_info']['route_pattern']}\n";
            $formatted .= "\n";
        }

        // Trim to max length to prevent token overflow
        $maxLength = config('ai_view_contexts.format.max_length', 4000);
        if (strlen($formatted) > $maxLength) {
            $formatted = substr($formatted, 0, $maxLength) . "\n\n[Context truncated due to length...]";

            $this->logger->warning('[ArtAdvisorService] View context truncated', [
                'original_length' => strlen($formatted),
                'max_length' => $maxLength,
            ]);
        }

        return $formatted;
    }

    /**
     * Search RAG knowledge base for relevant content
     *
     * Performs semantic search on rag_natan.documents to find relevant chunks
     * based on user question. Returns top-k most similar chunks with metadata.
     *
     * @param string $userMessage User's question
     * @param int $limit Max chunks to return
     * @return array ['chunks' => array, 'avg_similarity' => float, 'formatted' => string]
     */
    private function searchRagKnowledge(string $userMessage, int $limit = 10): array
    {
        try {
            $this->logger->info('[ArtAdvisorService] Searching RAG knowledge base', [
                'query_length' => strlen($userMessage),
                'limit' => $limit,
            ]);

            // Perform semantic search (vector similarity)
            $chunks = $this->ragSearch->searchByText($userMessage, $limit, maxDistance: 1.5);

            if ($chunks->isEmpty()) {
                $this->logger->warning('[ArtAdvisorService] No RAG results found');
                return [];
            }

            // Calculate average similarity
            $avgSimilarity = $chunks->avg('similarity_score') ?? 0;

            // Format chunks for AI prompt
            $formattedChunks = [];
            $formatted = "# RAG KNOWLEDGE BASE - RELEVANT DOCUMENTATION\n\n";
            $formatted .= "Retrieved " . $chunks->count() . " relevant sections ";
            $formatted .= "(avg similarity: " . round($avgSimilarity, 1) . "%):\n\n";

            foreach ($chunks as $index => $chunk) {
                $document = $chunk->document;
                $chunkNum = $index + 1;

                // Add to formatted string
                $formatted .= "## [{$chunkNum}] {$document->title}\n";
                $formatted .= "**Category**: {$document->category->name}\n";
                $formatted .= "**Relevance**: " . round($chunk->similarity_score, 1) . "%\n\n";
                $formatted .= "{$chunk->text}\n\n";
                $formatted .= "---\n\n";

                // Add to array for metadata
                $formattedChunks[] = [
                    'document_id' => $document->id,
                    'document_title' => $document->title,
                    'category' => $document->category->name ?? 'uncategorized',
                    'chunk_id' => $chunk->id,
                    'text' => $chunk->text,
                    'similarity' => $chunk->similarity_score,
                ];
            }

            return [
                'chunks' => $formattedChunks,
                'avg_similarity' => $avgSimilarity,
                'formatted' => $formatted,
            ];
        } catch (\Exception $e) {
            $this->logger->error('[ArtAdvisorService] RAG search failed', [
                'error' => $e->getMessage(),
            ]);

            // Return empty on error - graceful degradation
            return [];
        }
    }

    /**
     * Detect if user question requires vision analysis
     *
     * Auto-detects keywords that suggest visual analysis is needed.
     *
     * @param string $question User's question
     * @return bool True if vision should be used
     */
    public function shouldUseVision(string $question): bool {
        $visionKeywords = [
            'guarda',
            'osserva',
            'vedi',
            'analizza visivamente',
            'nell\'immagine',
            'colori',
            'composizione',
            'stile visivo',
            'che vedi',
            'look at',
            'analyze image',
            'visual',
            'colors in the',
        ];

        $questionLower = strtolower($question);

        foreach ($visionKeywords as $keyword) {
            if (str_contains($questionLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get available experts list
     *
     * @return array Expert ID => Name mapping
     */
    public static function getAvailableExperts(): array {
        return self::EXPERTS;
    }

    /**
     * Validate expert ID
     *
     * @param string $expertId Expert to validate
     * @return bool True if valid
     */
    public static function isValidExpert(string $expertId): bool {
        return isset(self::EXPERTS[$expertId]);
    }
}
