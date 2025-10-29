<?php

/**
 * AI Art Advisor - EN Translations
 * @package FlorenceEGI
 * @subpackage Translations
 * @language en
 * @version 1.0.0
 */

return [
    // Modal UI
    'title' => 'AI Art Advisor',
    'subtitle' => 'Your intelligent creative assistant',
    'close' => 'Close',

    // Experts
    'experts' => [
        'creative' => 'Creative Advisor',
        'creative_description' => 'Expert in art, NFT and marketing',
        'platform' => 'Platform Assistant',
        'platform_description' => 'Guide to using FlorenceEGI',
    ],

    // Chat Interface
    'chat' => [
        'placeholder' => 'Ask the AI something...',
        'send' => 'Send',
        'thinking' => 'AI is thinking...',
        'analyzing_image' => 'Analyzing image...',
        'select_expert' => 'Choose an expert',
    ],

    // Context Display
    'context' => [
        'title' => 'EGI Context',
        'egi_number' => 'EGI',
        'collection' => 'Collection',
        'price' => 'Price',
        'traits' => 'Existing traits',
        'status' => 'Status',
        'minted' => 'Minted on-chain',
        'pre_mint' => 'Pre-Mint (editable)',
    ],

    // Action Buttons
    'actions' => [
        'copy' => 'Copy',
        'apply' => 'Apply to Form',
        'copy_success' => 'Copied to clipboard!',
        'apply_success' => 'Applied to form!',
        'vision_mode' => 'Analyze Image',
        'vision_active' => 'Vision mode active',
    ],

    // Quick Prompts / Examples
    'examples' => [
        'title' => 'Example questions',
        'creative' => [
            'Suggest 5 traits for this artwork',
            'Improve description for luxury collectors',
            'Analyze colors and suggest mood',
            'What price do you recommend for this piece?',
        ],
        'platform' => [
            'How do I mint this EGI?',
            'Difference between Classic and Living EGI?',
            'How do royalties work?',
            'How can I promote my artworks?',
        ],
    ],

    // Welcome Messages
    'welcome' => [
        'general' => 'Hi! I\'m your AI Art Advisor. How can I help you today?',
        'generate_description' => 'Perfect! Let\'s create an effective description for your artwork. Tell me:

1. **What emotion** do you want to convey? (calm, energy, mystery, joy...)
2. **Who is your target audience?** (luxury collectors, young creators, corporate/PA...)
3. **Prefer to emphasize**: artistic technique or concept/storytelling?
4. Should I **visually analyze the image** for precise details?',
        
        'suggest_traits' => 'Let\'s analyze your artwork to suggest optimal traits.

Do you want me to examine the image visually or would you prefer to describe the main characteristics yourself?',
        
        'pricing_advice' => 'I\'ll help you define strategic pricing. Tell me:

1. Are you an **emerging artist** or do you have an established portfolio/track record?
2. Do you prefer **fixed price** or want to test the market with **auction/offers**?
3. How much time did you spend creating this artwork?',
    ],

    // Errors
    'error_occurred' => 'An error occurred. Please try again in a moment.',
    'error_vision_failed' => 'Visual analysis failed. Try text mode instead.',
    'error_rate_limit' => 'Too many requests. Wait a moment before trying again.',
    'error_invalid_expert' => 'Invalid expert selected.',

    // Status Messages
    'status' => [
        'connecting' => 'Connecting to AI...',
        'connected' => 'Connected',
        'streaming' => 'Receiving response...',
        'complete' => 'Complete',
        'error' => 'Error',
    ],

    // Tips & Hints
    'tips' => [
        'vision_tip' => 'Tip: Ask the AI to "look at the image" for detailed visual analysis',
        'apply_tip' => 'You can apply suggestions directly to the form with the "Apply" button',
        'copy_tip' => 'Click "Copy" to copy text to clipboard',
    ],

    // Mode-specific labels
    'modes' => [
        'generate_description' => 'Description Generation',
        'suggest_traits' => 'Trait Suggestions',
        'pricing_advice' => 'Pricing Consultation',
        'general' => 'General Assistance',
    ],
];

