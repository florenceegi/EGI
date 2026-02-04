<?php

/**
 * AI Sidebar - English Translations
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'AI Assistant',
    'subtitle' => 'Helping you complete your profile',
    'toggle_title' => 'Open onboarding assistant',
    'close' => 'Close',
    'send' => 'Send',
    'chat_placeholder' => 'Ask something...',
    'assistant_name' => 'EGI Assistant',
    'quick_actions_label' => 'Suggested next steps:',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => 'Welcome! Let me help you complete your profile setup.',
        'progress_low' => 'Great start! You\'ve completed :completed of :total steps. Next: :nextStep',
        'progress_high' => 'Almost there! Just :remaining more steps to go.',
        'complete' => 'Congratulations! Your profile is fully set up. 🎉',
        'all_done' => 'All setup steps completed!',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Setup progress',
        'title' => 'Onboarding Checklist',
    ],

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Hi :name! I\'m here to help you complete your profile.',
        'progress_intro' => 'You\'ve completed :completed out of :total steps. Let\'s see what\'s missing to make your profile perfect.',
        'missing_title' => 'Here\'s what you still need to do:',
        'suggestion_intro' => 'I suggest starting with',
        'click_hint' => 'Click on any item in the list below to complete it.',
        'complete_title' => 'Fantastic!',
        'complete_text' => 'Your profile is complete and ready to be discovered. Now you can focus on creating your artworks and growing your community.',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Upload your avatar',
            'description' => 'Add a profile photo to be recognized',
        ],
        'banner' => [
            'title' => 'Customize your banner',
            'description' => 'Add a cover image to your profile',
        ],
        'bio' => [
            'title' => 'Write your bio',
            'description' => 'Tell us who you are and what you create',
        ],
        'stripe' => [
            'title' => 'Set up payments',
            'description' => 'Connect Stripe to receive payments',
        ],
        'collection' => [
            'title' => 'Create your first collection',
            'description' => 'Organize your works into a collection',
        ],
        'first_egi' => [
            'title' => 'Create your first EGI',
            'description' => 'Publish your first digital artwork',
        ],
        'social_links' => [
            'title' => 'Add social links',
            'description' => 'Connect your social profiles',
        ],
        'verify_email' => [
            'title' => 'Verify email',
            'description' => 'Confirm your email address',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Request failed. Please try again.',
        'connection_error' => 'Connection error. Please check your internet.',
        'invalid_user_type' => 'Invalid user type.',
        'unauthorized' => 'Unauthorized to access this resource.',
    ],
];
