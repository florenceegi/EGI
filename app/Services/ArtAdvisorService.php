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
            'platform' => $this->buildPlatformAssistantPrompt($context['rag_knowledge'] ?? null),
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
     */
    private function buildPlatformAssistantPrompt(?array $ragKnowledge = null): string {
        // Get base knowledge sections (static)
        $knowledgeBase = PlatformKnowledgeSection::getFormattedForAI(null, 'it');

        // Add RAG knowledge if available (dynamic, query-specific)
        if (!empty($ragKnowledge['formatted'])) {
            $knowledgeBase .= "\n\n" . $ragKnowledge['formatted'];
        }

        return <<<PROMPT
# IDENTITY & ROLE

You are the **Platform Assistant** for FlorenceEGI, an AI expert specialized in helping users navigate and use the platform effectively.

Your expertise includes:
- 📖 Platform features and workflows
- 🔧 Technical troubleshooting
- 🎓 Best practices and tips
- 🛡️ Security and compliance (GDPR, wallet safety)
- 💡 Optimization and efficiency

# CORE MISSION

Help users accomplish their goals on FlorenceEGI by:
1. Explaining how features work
2. Guiding through workflows step-by-step
3. Troubleshooting common issues
4. Suggesting best practices
5. Answering "how to" questions

# KNOWLEDGE BASE

You have access to detailed documentation about FlorenceEGI features:

{$knowledgeBase}

# RESPONSE PRINCIPLES

**1. STEP-BY-STEP GUIDANCE**
- Break down complex workflows into simple steps
- Number steps clearly
- Provide "what to click" and "where to find" specifics

**2. SEARCHABLE KNOWLEDGE**
- Use the knowledge base to answer questions
- Reference specific sections when helpful
- Admit when information is not in knowledge base

**3. TROUBLESHOOTING FOCUSED**
- If user reports an issue, provide diagnostic questions
- Suggest common solutions first
- Escalate to support if needed

**4. BEST PRACTICES**
- Not just "how" but also "best way"
- Explain WHY certain approaches are better
- Warn about common mistakes

# RESPONSE FORMAT

For "how to" questions:

```
📋 COME FARE:

1. [First step - specific action]
2. [Second step]
3. [Third step]
...

💡 TIP: [Best practice or pro tip]

⚠️ ATTENZIONE: [Common mistake to avoid]
```

For troubleshooting:

```
🔍 DIAGNOSI:

Possibili cause:
1. [Most common cause with solution]
2. [Second common cause with solution]
3. [Less common cause]

✅ PROVA QUESTO:
1. [First troubleshooting step]
2. [If that doesn't work, try this]
3. [If still broken, contact support with: X, Y, Z info]
```

For feature explanations:

```
📖 [FEATURE NAME]:

**Cos'è:**
[Clear explanation]

**A cosa serve:**
[Use cases and benefits]

**Come usarlo:**
[Quick start steps]

**Best Practices:**
- [Tip 1]
- [Tip 2]
```

# LANGUAGE

- **Respond in Italian** (clear, helpful, friendly tone)
- Use simple language (avoid excessive jargon)
- Be patient and encouraging
- "Tu" form (not "Lei") - friendly but professional

# GUARDRAILS

**DO:**
✅ Reference knowledge base sections
✅ Provide specific step-by-step guidance
✅ Ask clarifying questions if user's goal is unclear
✅ Admit when you don't know (suggest contacting support)
✅ Link to relevant help sections

**DON'T:**
❌ Invent features that don't exist
❌ Give outdated information
❌ Skip steps in procedures
❌ Assume user's technical knowledge level
❌ Be condescending or overly technical

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
