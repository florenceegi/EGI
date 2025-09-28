<?php

/**
 * Translation file for FlorenceEGI Registration Page
 * Updated with new user types: patron, collector, enterprise, trader_pro
 *
 * @oracode OS1 Compliant
 * @semantic_coherence All translations align with FlorenceEGI brand and values
 * @intentionality Each message guides user toward successful registration
 */

return [
    'read_the_terms' => 'Read the Terms of Service',

    // SEO and Meta
    'seo_title' => 'Registration - FlorenceEGI | Join the Digital Renaissance',
    'seo_description' => 'Sign up for FlorenceEGI and participate in the Digital Renaissance. Create, collect, invest, and trade cultural assets with EPP impact.',
    'seo_keywords' => 'registration, FlorenceEGI, digital art, NFT, sustainability, EPP, digital renaissance',

    'og_title' => 'Join FlorenceEGI - The Digital Renaissance Marketplace',
    'og_description' => 'Register today and become part of the cultural and technological revolution transforming art and sustainability.',

    'schema_page_name' => 'FlorenceEGI Registration',
    'schema_page_description' => 'Registration page for the FlorenceEGI platform dedicated to the Digital Renaissance',

    // Navigation and Accessibility
    'skip_to_main' => 'Skip to main content',
    'opens_new_window' => '(opens in a new window)',

    // Main Headlines
    'main_title_html' => 'Welcome to the<br><span class="text-florentine-gold">Digital Renaissance</span>',
    'subtitle' => 'Join the platform that places art, technology, and sustainability at the heart of the future.',
    'platform_grows_benefit' => 'The more the platform grows, the more everyone benefits.',

    // Form Structure
    'form_title' => 'Create Your Account',
    'form_subtitle' => 'Choose the user type that best represents your role in the community.',

    // User Types - Updated with new types
    'user_type_legend' => 'What type of user are you?',

    // Creator (unchanged)
    'user_type_creator' => 'Creator',
    'user_type_creator_desc' => 'Artists, makers, and content creators who want to monetize their creativity',

    // Patron (was: mecenate)
    'user_type_patron' => 'Patron',
    'user_type_patron_desc' => 'Supporters of art and culture who fund projects and support creators',

    // Collector (was: acquirente)
    'user_type_collector' => 'Collector',
    'user_type_collector_desc' => 'Enthusiasts who purchase and collect artworks and cultural assets',

    // Enterprise (was: azienda)
    'user_type_enterprise' => 'Enterprise',
    'user_type_enterprise_desc' => 'Organizations and businesses interested in cultural and sustainable investments',

    // Trader Pro (new)
    'user_type_trader_pro' => 'Professional Trader',
    'user_type_trader_pro_desc' => 'Professional traders of cultural assets, NFTs, and EGI tokens',

    // EPP Entity (unchanged)
    'user_type_epp_entity' => 'EPP Entity',
    'user_type_epp_entity_desc' => 'Organizations focused on environmental, social, and governance impact',

    // PA Entity (new)
    'user_type_pa_entity' => 'Public Administration',
    'user_type_pa_entity_desc' => 'Public entities, government institutions and public administration organizations',

    // Form Fields
    'label_name' => 'Full Name',
    'name_help' => 'Your real name for internal use and identification',

    'label_nick_name' => 'Nickname (public)',
    'nick_name_help' => 'If you enter a nickname, it will be displayed publicly instead of your name. If left empty, your full name will be shown.',

    'label_email' => 'Email Address',
    'email_help' => 'We will use this address for important communications',

    'label_password' => 'Password',
    'password_help' => 'Minimum 8 characters, include uppercase, lowercase, and numbers',

    'label_password_confirmation' => 'Confirm Password',
    'password_confirmation_help' => 'Repeat the password to confirm it',

    // GDPR and Privacy
    'privacy_legend' => 'Privacy and Consents',
    'privacy_subtitle' => 'Your privacy matters. Choose consciously.',

    // Required Consents
    'consent_label_privacy_policy_accepted' => 'I accept the Privacy Policy',
    'consent_desc_privacy_policy_accepted' => 'Read how we process your personal data in compliance with GDPR.',
    'privacy_policy_link_text' => 'Privacy Policy',

    'consent_label_terms_accepted' => 'I accept the Terms of Service',
    'consent_desc_terms_accepted' => 'Read the conditions for using the FlorenceEGI platform.',
    'terms_link_text' => 'Terms of Service',

    'consent_label_age_confirmation' => 'I confirm I am at least 18 years old',
    'consent_desc_age_confirmation' => 'Use of the platform is restricted to adults.',

    // Optional Consents
    'optional_consents_title' => 'Optional Consents',
    'optional_consents_subtitle' => 'You can change these preferences at any time from your profile.',

    'consent_label_optional_analytics' => 'Analytics and platform improvement',
    'consent_desc_optional_analytics' => 'Help us improve FlorenceEGI by sharing anonymous usage data.',

    'consent_label_optional_marketing' => 'Promotional communications',
    'consent_desc_optional_marketing' => 'Receive updates on new features, events, and opportunities.',

    'consent_label_optional_profiling' => 'Content personalization',
    'consent_desc_optional_profiling' => 'Allow personalization of content and recommendations.',

    // Form Submission
    'submit_button' => 'Create Account',
    'submit_help' => 'By clicking, you confirm that you have read and accepted all required documents.',

    // Error Handling
    'error_title' => 'Validation Errors',

    // Login Link
    'already_registered_prompt' => 'Already have an account?',
    'login_link' => 'Log in here',

    // Footer
    'footer_gdpr' => 'GDPR Compliant',
    'footer_data_protected' => 'Data Protected',
    'footer_real_impact' => 'Real Impact',
    'footer_compliance_note' => 'FlorenceEGI respects your privacy and operates in full compliance with European data protection regulations.',

    // User Type Capabilities (for documentation/tooltips)
    'capabilities_creator' => [
        'Create personal collections',
        'Mint NFTs of your artworks',
        'Sell artwork on the marketplace',
        'Earn EPP for sustainable impact'
    ],
    'capabilities_patron' => [
        'Fund creative projects',
        'Early access to new artworks',
        'Exclusive content from creators',
        'Recognition in the community'
    ],
    'capabilities_collector' => [
        'Purchase digital artworks',
        'Participate in exclusive auctions',
        'Curate personal collections',
        'Trade cultural assets'
    ],
    'capabilities_enterprise' => [
        'Bulk corporate purchases',
        'Corporate collections',
        'Advanced API access',
        'ESG reporting'
    ],
    'capabilities_trader_pro' => [
        'Advanced EGI and NFT trading',
        'Portfolio analytics',
        'Real-time market data',
        'Professional tools'
    ],
    'capabilities_epp_entity' => [
        'Environmental impact tracking',
        'EPP token allocation',
        'Sustainability metrics',
        'Social impact reporting'
    ],

    // Form Validation Messages (JavaScript-friendly)
    'js_errors' => [
        'name_required' => 'The name is required',
        'name_min_length' => 'The name must be at least 2 characters long',
        'email_required' => 'The email is required',
        'email_invalid' => 'Enter a valid email address',
        'password_required' => 'The password is required',
        'password_min_length' => 'The password must be at least 8 characters long',
        'password_mismatch' => 'The passwords do not match',
        'user_type_required' => 'Select a user type',
        'consents_required' => 'You must accept the required consents',
        'fix_errors' => 'Please correct the highlighted errors',
        'registration_processing' => 'Registration in progress...',
    ],

    // Success Messages
    'registration_success' => 'Registration completed successfully! Welcome to FlorenceEGI.',
    'verification_email_sent' => 'We have sent you a verification email. Please check your inbox.',

    // Professional Trader Specific Content
    'trader_pro_notice' => 'As a Professional Trader, you will have access to advanced analytics and trading tools.',
    'trader_pro_verification' => 'Your account will be subject to verification to access professional features.',

    // Enterprise Specific Content
    'enterprise_notice' => 'For enterprise accounts, contact our team for customized configurations.',
    'enterprise_contact' => 'Contact enterprise support',
];
