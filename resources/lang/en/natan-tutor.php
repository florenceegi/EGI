<?php

/**
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Natan Tutor)
 * @date 2025-01-XX
 * @purpose Natan Tutor - Operational assistant translations
 */

return [
    // === GENERAL ===
    'title' => 'Natan Tutor',
    'subtitle' => 'Your personal platform assistant',
    'tagline' => 'I guide you step by step through FlorenceEGI operations',

    // === MODES ===
    'modes' => [
        'tutoring' => [
            'name' => 'Tutorial Mode',
            'description' => 'Detailed explanations and step-by-step guidance',
            'hint' => 'Ideal for learning how the platform works',
        ],
        'expert' => [
            'name' => 'Expert Mode',
            'description' => 'Quick shortcuts for direct actions',
            'hint' => 'For experienced users who want to save time',
        ],
    ],

    // === ACTIONS ===
    'actions' => [
        'navigate' => [
            'name' => 'Navigation',
            'description' => 'I\'ll take you to the page you\'re looking for',
        ],
        'explain' => [
            'name' => 'Explanation',
            'description' => 'I\'ll explain how this feature works',
        ],
        'mint' => [
            'name' => 'Mint Assistance',
            'description' => 'I\'ll guide you through creating your EGI',
        ],
        'reserve' => [
            'name' => 'Reservation Assistance',
            'description' => 'I\'ll guide you through reserving an EGI',
        ],
        'purchase' => [
            'name' => 'Purchase Assistance',
            'description' => 'I\'ll help you buy Egili',
        ],
        'collection_create' => [
            'name' => 'Create Collection',
            'description' => 'I\'ll guide you through creating a collection',
        ],
    ],

    // === NAVIGATION DESTINATIONS ===
    'navigation' => [
        'home' => [
            'name' => 'Home',
            'explanation' => 'The main FlorenceEGI page where you can see news and featured collections.',
        ],
        'dashboard' => [
            'name' => 'Dashboard',
            'explanation' => 'Your personal control panel with statistics and recent activity.',
        ],
        'collections' => [
            'name' => 'Collections',
            'explanation' => 'View and manage your EGI collections.',
        ],
        'explore' => [
            'name' => 'Explore',
            'explanation' => 'Discover collections and EGIs from other creators on the platform.',
        ],
        'mint' => [
            'name' => 'Mint',
            'explanation' => 'Create a new EGI by uploading your digital work.',
        ],
        'profile' => [
            'name' => 'Profile',
            'explanation' => 'View and edit your public profile.',
        ],
        'wallet' => [
            'name' => 'Wallet',
            'explanation' => 'Manage your Algorand wallet and transactions.',
        ],
        'egili' => [
            'name' => 'Egili',
            'explanation' => 'Manage your Egili balance, buy credits, or view history.',
        ],
        'default_explanation' => 'This page allows you to access advanced platform features.',
    ],

    // === FEATURES EXPLANATIONS ===
    'features' => [
        'mint' => [
            'explanation' => 'Mint is the process of creating an EGI (Identity Generating Entity). You upload your digital work (image, video, audio) and register it on the Algorand blockchain.',
            'tips' => [
                'Choose a catchy and descriptive title',
                'Upload high quality media (min 1000px per side)',
                'Write a description that tells the story of the work',
            ],
        ],
        'collection' => [
            'explanation' => 'A Collection is a container for grouping your EGIs. You can have multiple themed collections.',
            'tips' => [
                'Give a name that represents the collection theme',
                'Add an attractive cover',
                'Write a description for collectors',
            ],
        ],
        'reservation' => [
            'explanation' => 'Reservation allows you to reserve an EGI before it goes on official sale.',
            'tips' => [
                'Check the price and conditions',
                'Reservations have an expiration',
                'You can cancel within the specified terms',
            ],
        ],
        'egili' => [
            'explanation' => 'Egili are platform credits. You use them to access premium features like Natan Tutor, AI trait generation, and more.',
            'tips' => [
                '1 Egili = €0.01',
                'Minimum purchase of 5000 Egili',
                'Gift credits expire after 90 days',
            ],
        ],
        'default_explanation' => 'This feature helps you interact with the platform more effectively.',
    ],

    // === MINT STEPS ===
    'mint' => [
        'step1_title' => 'Choose the title',
        'step1_desc' => 'Give your work a name. The title will be visible to everyone.',
        'step2_title' => 'Upload media',
        'step2_desc' => 'Upload the image, video, or audio that represents your work.',
        'step3_title' => 'Add description',
        'step3_desc' => 'Tell the story of your work, what inspired it.',
        'step4_title' => 'Confirm and Mint',
        'step4_desc' => 'Verify the data and confirm creation on the blockchain.',
        'tip_title' => 'Use a title that catches attention and is memorable.',
        'tip_media' => 'Supported files: JPG, PNG, GIF, MP4, MP3. Max 50MB.',
        'tip_description' => 'A good description increases sales chances.',
    ],

    // === RESERVATION STEPS ===
    'reserve' => [
        'step1_title' => 'Select EGI',
        'step1_desc' => 'Choose the EGI you want to reserve from the collection.',
        'step2_title' => 'Check availability',
        'step2_desc' => 'Check that the EGI is available for reservation.',
        'step3_title' => 'Confirm reservation',
        'step3_desc' => 'Confirm the reservation and receive notification.',
        'explanation' => 'You are reserving ":title" at the price of €:price.',
        'what_happens_next' => 'After reservation, the creator will receive a notification. You will have limited time to complete the purchase.',
    ],

    // === COLLECTION STEPS ===
    'collection' => [
        'step1_title' => 'Collection name',
        'step1_desc' => 'Choose a name that represents the theme of your EGIs.',
        'step2_title' => 'Description',
        'step2_desc' => 'Explain what this collection will contain.',
        'step3_title' => 'Cover image',
        'step3_desc' => 'Upload an image that represents the collection.',
        'tip_name' => 'A short and memorable name works best.',
        'tip_description' => 'Tell the vision behind this collection.',
        'tip_cover' => 'Use a high-resolution square image.',
        'suggestion_personal' => 'Personal Collection',
        'suggestion_art' => 'My Works',
        'suggestion_memories' => 'Digital Memories',
    ],

    // === PURCHASE SECTION ===
    'purchase' => [
        'egili_explanation' => 'Egili are the platform currency. With Egili you can access premium features like Natan Tutor assistance.',
        'value_proposition' => 'The more Egili you buy, the more bonus you get! Larger packages include free extra credits.',
    ],

    // === RECOMMENDATIONS ===
    'recommendations' => [
        'no_egis' => 'You haven\'t created any EGI yet. Want me to guide you through creating your first one?',
        'explore_collections' => 'You\'ve explored few collections. Discover what others have created!',
        'low_balance' => 'Your Egili balance is low. Consider a purchase to continue using Natan Tutor.',
    ],

    // === ERRORS ===
    'errors' => [
        'insufficient_egili' => 'You don\'t have enough Egili for this action. Buy more credits to continue.',
        'egi_not_found' => 'The requested EGI does not exist or is not available.',
        'action_not_available' => 'This action is not available at the moment.',
        'user_not_authenticated' => 'You must log in to use Natan Tutor.',
    ],

    // === WELCOME GIFT ===
    'welcome_gift' => [
        'title' => 'Welcome to FlorenceEGI!',
        'message' => 'You\'ve received :amount welcome Egili to explore the platform with Natan Tutor\'s help!',
        'expires_notice' => 'Gift credits expire in :days days.',
        'start_exploring' => 'Start exploring',
    ],

    // === UI ELEMENTS ===
    'ui' => [
        'cost_label' => 'Cost: :cost Egili',
        'balance_label' => 'Balance: :balance Egili',
        'mode_switch' => 'Switch mode',
        'help_button' => 'Help',
        'close_button' => 'Close',
        'confirm_button' => 'Confirm',
        'cancel_button' => 'Cancel',
        'next_step' => 'Next step',
        'previous_step' => 'Previous step',
        'complete' => 'Complete',
    ],

    // === TOOLTIPS ===
    'tooltips' => [
        'tutoring_mode' => 'In Tutorial mode, Natan explains each step in detail.',
        'expert_mode' => 'In Expert mode, Natan executes actions quickly without explanations.',
        'cost_varies' => 'Cost varies based on action complexity and chosen mode.',
    ],
];
