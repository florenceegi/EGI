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

    // CoA (Certificate of Authenticity) Translations
    'coa' => [
        'certificate' => 'CoA Certificate',
        'active' => 'Active',
        'none' => 'None',
        'no_certificate' => 'No Certificate',
        'certificate_active' => 'Certificate Active',
        'serial_number' => 'Serial Number',
        'issue_date' => 'Issue Date',
        'expires' => 'Expires',
        'annexes' => 'Annexes',
        'pro' => 'Pro',
        'view' => 'View',
        'pdf' => 'PDF',
        'manage' => 'Manage',
        'add_annex' => 'Add Annex',
        'reissue' => 'Reissue',
        'revoke' => 'Revoke',
        'no_certificate_issued' => 'This EGI does not have a Certificate of Authenticity',
        'issue_certificate' => 'Issue Certificate',
        'unlock_with_coa_pro' => 'Unlock with CoA Pro',
        'provenance_documentation' => 'Provenance Documentation',
        'condition_reports' => 'Condition Reports',
        'exhibition_history' => 'Exhibition History',
        'professional_pdf_bundle' => 'Professional PDF Bundle',
        'only_creator_can_issue' => 'Only the creator can issue certificates',
        'verify_any_certificate' => 'Verify any certificate',
        'public_verification' => 'Public Verification',
        'issue_certificate_confirm' => 'Issue a Certificate of Authenticity for this EGI?',
        'issuing' => 'Issuing...',
        'certificate_issued_successfully' => 'Certificate issued successfully!',
        'error_issuing_certificate' => 'Error issuing certificate: ',
        'unknown_error' => 'Unknown error',
        'reissue_certificate_confirm' => 'Reissue this certificate? This will create a new version.',
        'certificate_reissued_successfully' => 'Certificate reissued successfully!',
        'error_reissuing_certificate' => 'Error reissuing certificate: ',
        'revoke_certificate_confirm' => 'Revoke this certificate? This action cannot be undone.',
        'reason_for_revocation' => 'Reason for revocation:',
        'certificate_revoked_successfully' => 'Certificate revoked successfully!',
        'error_revoking_certificate' => 'Error revoking certificate: ',
        'manage_certificate' => 'Manage Certificate',
        'annex_management_coming_soon' => 'Annex management coming soon!',
        'issue_certificate_description' => 'Issue a certificate to provide proof of authenticity and unlock Pro features',

        // Missing translations for sidebar and CoA components
        'serial' => 'Serial',
        'issued' => 'Issued',
        'pro_features' => 'Pro Features',
        'provenance_docs' => 'Provenance Documentation',
        'professional_pdf' => 'Professional PDF',
        'unlock_pro_features' => 'Unlock Pro Features',
        'verification' => 'Verification',
        'copy' => 'Copy',
        'copied' => 'Copied!',
        'reason_for' => 'Reason for',

        // QES Badge System
        'badge_author_signed' => 'Author Signed (QES)',
        'badge_inspector_signed' => 'Inspector Signed (QES)',
        'badge_integrity_ok' => 'Integrity Verified',

        // Location UI (CoA)
        'issue_place' => 'Issue Place',
        'location_placeholder' => 'e.g., Florence, Tuscany, Italy',
        'save' => 'Save',
        'location_hint' => 'Use the format "City, Region/Province, Country" (or equivalent).',
        'location_required' => 'Location is required',
        'location_saved' => 'Location saved',
        'location_save_failed' => 'Failed to save location',
        'location_updated' => 'Location updated successfully',

        // Inspector countersign (QES)
        'inspector_countersign' => 'Inspector Countersign (QES)',
        'confirm_inspector_countersign' => 'Proceed with inspector countersign?',
        'inspector_countersign_applied' => 'Inspector countersign applied',
        'operation_failed' => 'Operation failed',
    ],
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

    /*
    |--------------------------------------------------------------------------
    | Certificate of Authenticity (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'title' => 'Certificate of Authenticity',
        'status' => 'Status',
        'issued' => 'Issued on',
        'verification' => 'Verification ID',
        'copy' => 'Copy',
        'copied' => 'Copied!',
        'view' => 'View',
        'pdf' => 'PDF',
        'reissue' => 'Reissue',
        'issue' => 'Issue Certificate',
        'annexes' => 'Annexes',
        'add_annex' => 'Add Annex',
        'annex_coming_soon' => 'Annex management coming soon!',
        'pro' => 'Pro',
        'unlock_pro' => 'Unlock with CoA Pro',
        'provenance' => 'Provenance Documentation',
        'pdf_bundle' => 'Professional PDF Bundle',
        'issue_description' => 'Issue a certificate to provide proof of authenticity and unlock Pro features',
        'creator_only' => 'Only the creator can issue certificates',
        'active' => 'Active',
        'revoked' => 'Revoked',
        'expired' => 'Expired',
        'manage_coa' => 'Manage CoA',
        'no_certificate' => 'No certificate issued yet',

        // JavaScript messages
        'confirm_issue' => 'Issue a Certificate of Authenticity for this EGI?',
        'issued_success' => 'Certificate issued successfully!',
        'confirm_reissue' => 'Reissue this certificate? This will create a new version.',
        'reissued_success' => 'Certificate reissued successfully!',
        'revocation_reason' => 'Reason for revocation:',
        'confirm_revoke' => 'Revoke this certificate? This action cannot be undone.',
        'revoked_success' => 'Certificate revoked successfully!',
        'error_issuing' => 'Error issuing certificate',
        'error_reissuing' => 'Error reissuing certificate',
        'error_revoking' => 'Error revoking certificate',
        'unknown_error' => 'Unknown error',
        'verify_any_certificate' => 'Verify any certificate',

        // Annex Modal
        'manage_annexes_title' => 'Manage CoA Pro Annexes',
        'annexes_description' => 'Add professional documentation to enhance your certificate',
        'provenance_tab' => 'Provenance',
        'condition_tab' => 'Condition',
        'exhibitions_tab' => 'Exhibitions',
        'photos_tab' => 'Photos',
        'provenance_title' => 'Provenance Documentation',
        'provenance_description' => 'Document the ownership history and authenticity chain',
        'condition_title' => 'Condition Report',
        'condition_description' => 'Professional assessment of the artwork\'s physical condition',
        'exhibitions_title' => 'Exhibition History',
        'exhibitions_description' => 'Record of public exhibitions and display history',
        'photos_title' => 'Professional Photography',
        'photos_description' => 'High-resolution documentation and detail photography',
        'save_annex' => 'Save Annex',
        'cancel' => 'Cancel',
        'upload_files' => 'Upload Files',
        'drag_drop_files' => 'Drag and drop files here, or click to select',
        'max_file_size' => 'Maximum file size: 10MB per file',
        'supported_formats' => 'Supported formats: PDF, JPG, PNG, DOCX',

        // Provenance Form
        'ownership_history_description' => 'Document the ownership history and authenticity chain of this artwork',
        'previous_owners' => 'Previous Owners',
        'previous_owners_placeholder' => 'List previous owners and dates of ownership...',
        'acquisition_details' => 'Acquisition Details',
        'acquisition_details_placeholder' => 'How was this artwork acquired? Include dates, prices, auction houses...',
        'authenticity_sources' => 'Authenticity Sources',
        'authenticity_sources_placeholder' => 'Expert opinions, catalogue raisonnés, institutional records...',
        'save_provenance_data' => 'Save Provenance Data',

        // Condition Form
        'condition_assessment_description' => 'Professional evaluation of the artwork\'s physical state and conservation needs',
        'overall_condition' => 'Overall Condition',
        'condition_excellent' => 'Excellent',
        'condition_very_good' => 'Very Good',
        'condition_good' => 'Good',
        'condition_fair' => 'Fair',
        'condition_poor' => 'Poor',
        'condition_notes' => 'Condition Notes',
        'condition_notes_placeholder' => 'Detailed description of any damage, restoration, or conservation issues...',
        'conservation_history' => 'Conservation History',
        'conservation_history_placeholder' => 'Previous restorations, treatments, or conservation work...',
        'save_condition_data' => 'Save Condition Data',

        // Exhibitions Form
        'exhibition_history_description' => 'Record of museums, galleries, and public exhibitions where this artwork was displayed',
        'exhibition_title' => 'Exhibition Title',
        'exhibition_title_placeholder' => 'Name of the exhibition...',
        'venue' => 'Venue',
        'venue_placeholder' => 'Museum, gallery, or institution name...',
        'exhibition_dates' => 'Exhibition Dates',
        'exhibition_notes' => 'Notes',
        'exhibition_notes_placeholder' => 'Catalogue number, special mentions, reviews...',
        'add_exhibition' => 'Add Exhibition',
        'save_exhibitions_data' => 'Save Exhibitions Data',

        // Photos Form
        'photo_documentation_description' => 'High-quality images for documentation and archival purposes',
        'photo_type' => 'Photo Type',
        'photo_overall' => 'Overall View',
        'photo_detail' => 'Detail Shot',
        'photo_raking' => 'Raking Light',
        'photo_uv' => 'UV Photography',
        'photo_infrared' => 'Infrared',
        'photo_back' => 'Back/Verso',
        'photo_signature' => 'Signature/Marks',
        'photo_frame' => 'Frame/Mount',
        'photo_description' => 'Description',
        'photo_description_placeholder' => 'Describe what this photo shows...',
        'save_photos_data' => 'Save Photos Data',

        // Additional condition form fields
        'select_condition' => 'Select condition...',
        'detailed_assessment' => 'Detailed Assessment',
        'detailed_assessment_placeholder' => 'Detailed description of condition, including any damages, restorations, or notable features...',
        'conservation_history_placeholder' => 'Previous conservation treatments, dates, and conservators...',
        'assessor_information' => 'Assessor Information',
        'assessor_placeholder' => 'Name and credentials of condition assessor...',
        'save_condition_report' => 'Save Condition Report',

        // Exhibition form fields
        'major_exhibitions' => 'Major Exhibitions',
        'major_exhibitions_placeholder' => 'List major exhibitions, museums, galleries, dates...',
        'publications_catalogues' => 'Publications & Catalogues',
        'publications_placeholder' => 'Books, catalogues, articles where this work has been featured...',
        'awards_recognition' => 'Awards & Recognition',
        'awards_placeholder' => 'Awards, prizes, critical recognition received...',
        'save_exhibition_history' => 'Save Exhibition History',
        'exhibition_history_description' => 'Record of exhibitions where this artwork has been displayed',

        // Photo form fields
        'click_upload_images' => 'Click to upload images',
        'png_jpg_webp' => 'PNG, JPG, WEBP up to 10MB each',
        'photo_descriptions' => 'Photo Descriptions',
        'photo_descriptions_placeholder' => 'Describe the images: lighting conditions, details captured, purpose...',
        'photographer_credits' => 'Photographer Credits',
        'photographer_placeholder' => 'Photographer name and date...',
        'save_photo_documentation' => 'Save Photo Documentation',
        'photo_documentation_description' => 'High-resolution images for documentation and insurance purposes',

        // Modal actions
        'close' => 'Close',
        'error_no_certificate' => 'Error: No certificate selected',
        'saving' => 'Saving...',
        'annex_saved_success' => 'Annex data saved successfully!',
        'error_saving_annex' => 'Error saving annex data',

        // Missing translations for sidebar and CoA components
        'certificate' => 'CoA Certificate',
        'no_certificate' => 'No Certificate',
        'certificate_active' => 'Certificate Active',
        'serial_number' => 'Serial Number',
        'issue_date' => 'Issue Date',
        'expires' => 'Expires',
        'no_certificate_issued' => 'This EGI has no Certificate of Authenticity',
        'issue_certificate' => 'Issue Certificate',
        'unlock_with_coa_pro' => 'Unlock with CoA Pro',
        'provenance_documentation' => 'Provenance Documentation',
        'condition_reports' => 'Condition Reports',
        'exhibition_history' => 'Exhibition History',
        'professional_pdf_bundle' => 'Professional PDF Bundle',
        'only_creator_can_issue' => 'Only the creator can issue certificates',
        'public_verification' => 'Public Verification',
        'verification_description' => 'Verify the authenticity of an EGI Certificate of Authenticity',
        'verification_instructions' => 'Enter the certificate serial number to verify its authenticity',
        'enter_serial' => 'Enter serial number',
        'serial_help' => 'Format: ABC-123-DEF (letters, numbers and dashes)',
        'certificate_of_authenticity' => 'Certificate of Authenticity',
        'public_verification_display' => 'Public Verification Display',
        'verified_authentic' => 'Verified and Authentic Certificate',
        'verified_at' => 'Verified on',
        'artwork_information' => 'Artwork Information',
        'artwork_title' => 'Artwork Title',
        'creator' => 'Creator',
        'description' => 'Description',
        'certificate_details' => 'Certificate Details',
        'cryptographic_verification' => 'Cryptographic Verification',
        'verify_again' => 'Verify Again',
        'print_certificate' => 'Print Certificate',
        'share_verification' => 'Share Verification',
        'powered_by_florenceegi' => 'Powered by FlorenceEGI',
        'verification_timestamp' => 'Verification Timestamp',
        'link_copied' => 'Link copied to clipboard',
        'issuing' => 'Issuing...',
        'certificate_issued_successfully' => 'Certificate issued successfully!',
        'error_issuing_certificate' => 'Error issuing certificate: ',
        'reissue_certificate_confirm' => 'Reissue this certificate? This will create a new version.',
        'certificate_reissued_successfully' => 'Certificate reissued successfully!',
        'error_reissuing_certificate' => 'Error reissuing certificate: ',
        'revoke_certificate_confirm' => 'Revoke this certificate? This action cannot be undone.',
        'reason_for_revocation' => 'Reason for revocation:',
        'certificate_revoked_successfully' => 'Certificate revoked successfully!',
        'error_revoking_certificate' => 'Error revoking certificate: ',
        'manage_certificate' => 'Manage Certificate',
        'annex_management_coming_soon' => 'Annex management coming soon!',
        'issue_certificate_description' => 'Issue a certificate to provide proof of authenticity and unlock Pro features',
        'serial' => 'Serial',
        'pro_features' => 'Pro Features',
        'provenance_docs' => 'Provenance Documentation',
        'professional_pdf' => 'Professional PDF',
        'unlock_pro_features' => 'Unlock Pro Features',
        'reason_for' => 'Reason for',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dossier System - Dossier System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Image Dossier',
        'loading' => 'Loading dossier...',
        'view_complete' => 'View complete image dossier',
        'close' => 'Close dossier',

        // Artwork Info
        'artwork_info' => 'Artwork Information',
        'author' => 'Author',
        'year' => 'Year',
        'internal_id' => 'Internal ID',

        // Dossier Info
        'dossier_info' => 'Dossier Information',
        'images_count' => 'Images',
        'type' => 'Type',
        'utility_gallery' => 'Utility Gallery',

        // Gallery
        'gallery_title' => 'Image Gallery',
        'image_number' => 'Image :number',
        'image_of_total' => 'Image :current of :total',

        // States
        'no_utility_title' => 'Dossier unavailable',
        'no_utility_message' => 'No additional images are available for this artwork.',
        'no_utility_description' => 'The additional images dossier has not yet been configured for this artwork.',

        'no_images_title' => 'No images available',
        'no_images_message' => 'The dossier exists but does not contain images yet.',
        'no_images_description' => 'Additional images will be added in the future by the artwork creator.',

        'error_title' => 'Error',
        'error_loading' => 'Error loading dossier',

        // Navigation
        'previous_image' => 'Previous image',
        'next_image' => 'Next image',
        'close_viewer' => 'Close viewer',
        'of' => 'of',

        // Zoom Controls
        'zoom_help' => 'Use mouse wheel or touch to zoom • Drag to move',
        'zoom_in' => 'Zoom in',
        'zoom_out' => 'Zoom out',
        'zoom_reset' => 'Reset zoom',
        'zoom_fit' => 'Fit to screen',
    ],

];
