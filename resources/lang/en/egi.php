<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - English Translations
    |--------------------------------------------------------------------------
    |
    | Translations for EGI CRUD system in FlorenceEGI
    | Version: 1.0.0 - Oracode System 2.0 Compliant
    |
    */

    // Meta and SEO
    'meta_description_default' => 'Details for EGI: :title',
    'image_alt_default' => 'EGI Artwork',
    'view_full' => 'View Full',
    'artwork_loading' => 'Artwork Loading...',

    // Basic Information
    'by_author' => 'by :name',
    'unknown_creator' => 'Unknown Creator',

    // Main Actions
    'like_button_title' => 'Add to Favorites',
    'share_button_title' => 'Share this EGI',
    'current_price' => 'Current Price',
    'not_currently_listed' => 'To Activate',
    'contact_owner_availability' => 'Contact owner for availability',
    'not_for_sale' => 'Not for sale',
    'not_for_sale_description' => 'This EGI is not currently available for purchase',
    'liked' => 'Liked',
    'add_to_favorites' => 'Add to Favorites',
    'reserve_this_piece' => 'Activate It',

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - NFT Cards System
    |--------------------------------------------------------------------------
    */

    // Badges and Status
    'badge' => [
        'owned' => 'OWNED',
        'media_content' => 'Media Content',
    ],

    // Titles
    'title' => [
        'untitled' => '✨ Untitled EGI',
    ],

    // Platform
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Creator
    'creator' => [
        'created_by' => '👨‍🎨 Created by:',
    ],

    // Prices
    'price' => [
        'purchased_for' => '💳 Purchased for',
        'price' => '💰 Price',
        'floor' => '📊 Floor',
    ],

    // Status
    'status' => [
        'not_for_sale' => '🚫 Not for sale',
        'draft' => '⏳ Draft',
    ],

    // Actions
    'actions' => [
        'view' => 'View',
        'view_details' => 'View EGI details',
        'reserve' => 'Activate It',
        'outbid' => 'Bid Higher to Activate',
    ],

    // Reservation details
    'reservation' => [
        'highest_bid' => 'Highest Bid',
        'fegi_reservation' => 'FEGI Reservation',
        'strong_bidder' => 'Best Bidder',
        'weak_bidder' => 'FEGI Code',
        'activator' => 'Co Creator',
        'activated_by' => 'Activated by',
    ],

    // Original currency note
    'originally_reserved_in' => 'Originally reserved in :currency for :amount',
    'originally_reserved_in_short' => 'Res. :currency :amount',

    // Information Sections
    'properties' => 'Properties',
    'supports_epp' => 'Supports EPP',
    'asset_type' => 'Asset Type',
    'format' => 'Format',
    'about_this_piece' => 'About This Piece',
    'default_description' => 'This unique digital artwork represents a moment of creative expression, capturing the essence of digital artistry in the blockchain era.',
    'provenance' => 'Provenance',
    'view_full_collection' => 'View Full Collection',

    /*
    |--------------------------------------------------------------------------
    | CRUD System - Editing System
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Header and Navigation
        'edit_egi' => 'Edit EGI',
        'toggle_edit_mode' => 'Toggle Edit Mode',
        'start_editing' => 'Start Editing',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',

        // Title Field
        'title' => 'Title',
        'title_placeholder' => 'Enter artwork title...',
        'title_hint' => 'Maximum 60 characters',
        'characters_remaining' => 'characters remaining',

        // Description Field
        'description' => 'Description',
        'description_placeholder' => 'Describe your artwork, its story and meaning...',
        'description_hint' => 'Tell the story behind your creation',

        // Price Field
        'price' => 'Price',
        'price_placeholder' => '0.00',
        'price_hint' => 'Price in ALGO (leave empty if not for sale)',
        'price_locked_message' => 'Price locked - EGI already reserved',

        // Creation Date Field
        'creation_date' => 'Creation Date',
        'creation_date_hint' => 'When did you create this artwork?',

        // Published Field
        'is_published' => 'Published',
        'is_published_hint' => 'Make artwork publicly visible',

        // View Mode - Current State
        'current_title' => 'Current Title',
        'no_title' => 'No title set',
        'current_price' => 'Current Price',
        'price_not_set' => 'Price not set',
        'current_status' => 'Publication Status',
        'status_published' => 'Published',
        'status_draft' => 'Draft',

        // Delete System
        'delete_egi' => 'Delete EGI',
        'delete_confirmation_title' => 'Confirm Deletion',
        'delete_confirmation_message' => 'Are you sure you want to delete this EGI? This action cannot be undone.',
        'delete_confirm' => 'Delete Permanently',

        // Validation Messages
        'title_required' => 'Title is required',
        'title_max_length' => 'Title cannot exceed 60 characters',
        'price_numeric' => 'Price must be a valid number',
        'price_min' => 'Price cannot be negative',
        'creation_date_format' => 'Invalid date format',

        // Success Messages
        'update_success' => 'EGI updated successfully!',
        'delete_success' => 'EGI deleted successfully.',

        // Error Messages
        'update_error' => 'Error updating EGI.',
        'delete_error' => 'Error deleting EGI.',
        'permission_denied' => 'You do not have permission for this action.',
        'not_found' => 'EGI not found.',

        // General Messages
        'no_changes_detected' => 'No changes detected.',
        'unsaved_changes_warning' => 'You have unsaved changes. Are you sure you want to leave?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Labels - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Edit',
        'save_short' => 'Save',
        'delete_short' => 'Delete',
        'cancel_short' => 'Cancel',
        'published_short' => 'Pub.',
        'draft_short' => 'Draft',
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Labels - Screen Readers
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'EGI edit form',
        'delete_button' => 'Delete EGI button',
        'toggle_edit' => 'Toggle edit mode',
        'save_form' => 'Save EGI changes',
        'close_modal' => 'Close confirmation dialog',
        'required_field' => 'Required field',
        'optional_field' => 'Optional field',
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage Multi-Content Carousel
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'title' => 'Discover the Renaissance',
        'subtitle' => 'Explore artworks, creators, collections, and collectors in the FlorenceEGI ecosystem',

        // Content Type Buttons
        'content_types' => [
            'egi_list' => 'EGI List View',
            'egi_card' => 'EGI Card View',
            'creators' => 'Featured Creators',
            'collections' => 'Art Collections',
            'collectors' => 'Top Collectors'
        ],

        // View Mode Buttons
        'view_modes' => [
            'carousel' => 'Carousel View',
            'list' => 'List View'
        ],

        // Mode Labels
        'carousel_mode' => 'Carousel',
        'list_mode' => 'List',

        // Content Labels
        'creators' => 'Creators',
        'collections' => 'Collections',
        'collectors' => 'Collectors',

        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artists',
            'collections' => 'Collections',
            'collectors' => 'Activators'
        ],

        // Navigation
        'navigation' => [
            'previous' => 'Previous',
            'next' => 'Next',
            'slide' => 'Go to slide :number'
        ],

        // Empty States
        'empty_state' => [
            'title' => 'No Content Available',
            'subtitle' => 'Check back soon for new content!',
            'no_egis' => 'No EGI artworks available at the moment.',
            'no_creators' => 'No creators available at the moment.',
            'no_collections' => 'No collections available at the moment.',
            'no_collectors' => 'No collectors available at the moment.'
        ],

        // Carousel sections
        'sections' => [
            'egis' => 'Featured EGIs',
            'creators' => 'Emerging Artists',
            'collections' => 'Exclusive Collections',
            'collectors' => 'Top Collectors'
        ],
        'view_all' => 'View All',
        'items' => 'items',

        // Legacy (for backwards compatibility)
        'two_columns' => 'List View',
        'three_columns' => 'Card View'
    ],

    /*
    |--------------------------------------------------------------------------
    | List View - Homepage List Mode
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Browse by Category',
        'subtitle' => 'Navigate through different categories to find what you\'re looking for',

        'content_types' => [
            'egi_list' => 'EGI List',
            'creators' => 'Artists List',
            'collections' => 'Collections List',
            'collectors' => 'Collectors List'
        ],

        'headers' => [
            'egi_list' => 'EGI Artworks',
            'creators' => 'Artists',
            'collections' => 'Collections',
            'collectors' => 'Collectors'
        ],

        'empty_state' => [
            'title' => 'No Items Found',
            'subtitle' => 'Try selecting a different category',
            'no_egis' => 'No EGI artworks found.',
            'no_creators' => 'No artists found.',
            'no_collections' => 'No collections found.',
            'no_collectors' => 'No collectors found.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Desktop Carousel - Desktop Only EGI Carousel
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Featured Digital Artworks',
        'subtitle' => 'The best NFT creations from our community',
        'navigation' => [
            'previous' => 'Previous',
            'next' => 'Next',
            'slide' => 'Go to slide :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Toggle - Mobile View Toggle
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Explore FlorenceEGI',
        'subtitle' => 'Choose how you want to browse content',
        'carousel_mode' => 'Carousel View',
        'list_mode' => 'List View',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Hero Section with 3D Coverflow Effect
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activating an EGI means leaving your mark.',
        'subtitle' => 'Your name remains forever alongside the Creator\'s: without you, the artwork wouldn\'t exist.',
        'carousel_mode' => 'Carousel View',
        'list_mode' => 'Grid View',
        'carousel_label' => 'Featured artworks carousel',
        'no_egis' => 'No featured artworks available at the moment.',
        'navigation' => [
            'previous' => 'Previous artwork',
            'next' => 'Next artwork',
        ],
    ],

    // Collection Collaborators
    'collection_collaborators' => 'Collaborators',
    'owner' => 'Owner',
    'creator' => 'Creator',
    'no_other_collaborators' => 'No other collaborators',

];
