<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - English Translations
    |--------------------------------------------------------------------------
    |
    | Translations for the EGI CRUD system in FlorenceEGI
    | Version: 1.0.0 - Oracode System 2.0 Compliant
    |
    */

    // Meta and SEO
    'meta_description_default' => 'Details for EGI: :title',
    'image_alt_default' => 'EGI Image',
    'view_full' => 'Full View',
    'artwork_loading' => 'Artwork Loading...',

    // Basic Information
    'by_author' => 'by :name',
    'unknown_creator' => 'Unknown Artist',

    // Main Actions
    'like_button_title' => 'Add to Favorites',
    'unlike_button_title' => 'Remove from Favorites',
    'like_button_aria' => 'Add this EGI to your favorites',
    'unlike_button_aria' => 'Remove this EGI from your favorites',
    'share_button_title' => 'Share this EGI',

    'current_price' => 'Current Price',
    'not_currently_listed' => 'To Activate',
    'contact_owner_availability' => 'Contact owner for availability',
    'not_for_sale' => 'Not for Sale',
    'not_for_sale_description' => 'This EGI is not currently available for purchase',
    'liked' => 'Liked',
    'add_to_favorites' => 'Add to Favorites',
    'reserve_this_piece' => 'Activate It',

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - NFT Card System
    |--------------------------------------------------------------------------
    */

    // Badges and States
    'badge' => [
        'owned' => 'OWNED',
        'media_content' => 'Media Content',
        'winning_bid' => 'WINNING BID',
        'outbid' => 'OUTBID',
        'not_owned' => 'NOT OWNED',
        'not_published' => 'NOT PUBLISHED', // Badge for unpublished EGI (owner only)
        'not_for_sale' => 'NOT FOR SALE', // Badge for not for sale EGI
        'to_activate' => 'TO ACTIVATE',
        'activated' => 'ACTIVATED',
        'reserved' => 'RESERVED',
        'minted' => 'MINTED',
        'auction_active' => 'TO BE MINTED',  // Badge for auction EGI
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
        'highest_bid' => '🏆 Highest Bid',
    ],

    // Reservations
    'reservation' => [
        'count' => 'Reservations',
        'highest_bidder' => 'Top Bidder',
        'by' => 'by',
        'highest_bid' => 'Highest Bid',
        'fegi_reservation' => 'FEGI Reservation',
        'strong_bidder' => 'Top Bidder',
        'weak_bidder' => 'FEGI Code',
        'activator' => 'Co Creator',
        'activated_by' => 'Activated by',
        'reserved_by' => '📝 Bid by:',
    ],

    // Original Currency Note
    'originally_reserved_in' => 'Originally bid in :currency for :amount',
    'originally_reserved_in_short' => 'Bid :currency :amount',

    // Auction System
    'auction' => [
        'auction_details' => 'Auction Details',
        'minimum_price' => 'Starting Bid',
        'starting_price' => 'Starting Price',
        'current_bid' => 'Current Bid',
        'highest_bid' => 'Highest Bid',
        'no_bids' => 'No Bids Yet',
        'starts_at' => 'Starts',
        'ends_at' => 'Ends',
        'ended' => 'Auction Ended',
        'not_started' => 'Auction Not Started',
        'time_remaining' => 'Time Remaining',
        'days' => 'days',
        'hours' => 'hours',
        'minutes' => 'minutes',
    ],

    // Status
    'status' => [
        'not_for_sale' => '🚫 Not for Sale',
        'draft' => '⏳ Draft',
        // Phase 2: Availability status
        'login_required' => '🔐 Login Required',
        'already_minted' => '✅ Already Minted',
        'not_available' => '⚠️ Not Available',
    ],

    // Actions
    'actions' => [
        'view' => 'View',
        'view_details' => 'View EGI Details',
        'reserve' => 'Activate It',
        'reserved' => 'Reserved',
        'outbid' => 'Outbid to Activate',
        'view_history' => 'History',
        'reserve_egi' => 'Reserve :title',
        'complete_purchase' => 'Complete Purchase',
        // Phase 2: Dual path actions
        'mint_now' => 'Mint Now',
        'mint_direct' => 'Mint Instantly',
        // Auction actions
        'make_offer' => 'Make an Offer',
    ],

    // Reservation History System
    'history' => [
        'title' => 'Reservation History',
        'no_reservations' => 'No reservations found',
        'total_reservations' => '{1} :count reservation|[2,*] :count reservations',
        'current_highest' => 'Current Highest Priority',
        'superseded' => 'Lower Priority',
        'created_at' => 'Created on',
        'amount' => 'Amount',
        'type_strong' => 'Strong Reservation',
        'type_weak' => 'Weak Reservation',
        'loading' => 'Loading history...',
        'error' => 'Error loading history',
    ],

    // Informative Sections
    'properties' => 'Properties',
    'supports_epp' => 'Supports EPP',
    'asset_type' => 'Asset Type',
    'format' => 'Format',
    'about_this_piece' => 'About This Piece',
    'default_description' => 'This unique digital artwork represents a moment of creative expression, capturing the essence of digital art in the blockchain era.',
    'provenance' => 'Provenance',
    'view_full_collection' => 'View Full Collection',

    /*
    |--------------------------------------------------------------------------
    | CRUD System - Edit System
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
        'title_placeholder' => 'Enter the artwork title...',
        'title_hint' => 'Maximum 60 characters',
        'characters_remaining' => 'characters remaining',

        // Description Field
        'description' => 'Description',
        'description_placeholder' => 'Describe your artwork, its story, and its meaning...',
        'description_hint' => 'Tell the story behind your creation',

        // Price Field
        'price' => 'Price',
        'price_placeholder' => '0.00',
        'price_hint' => 'Price in ALGO (leave blank if not for sale)',
        'price_locked_message' => 'Price locked - EGI already reserved',

        // Creation Date Field
        'creation_date' => 'Creation Date',
        'creation_date_hint' => 'When did you create this artwork?',

        // Published Field
        'is_published' => 'Published',
        'is_published_hint' => 'Make the artwork publicly visible',

        // View Mode - Current Status
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
        'price_required_for_fixed_price' => 'Fixed Price mode requires a price greater than zero',
        'creation_date_format' => 'Invalid date format',

        // Success Messages
        'update_success' => 'EGI updated successfully!',
        'delete_success' => 'EGI deleted successfully.',

        // Error Messages
        'update_error' => 'Error updating the EGI.',
        'delete_error' => 'Error deleting the EGI.',
        'permission_denied' => 'You do not have the necessary permissions for this action.',
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
    | EGI Carousel - Homepage Featured EGIs
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'List View',
        'three_columns' => 'Card View',
        'navigation' => [
            'previous' => 'Previous',
            'next' => 'Next',
            'slide' => 'Go to slide :number',
        ],
        'empty_state' => [
            'title' => 'No Content Available',
            'subtitle' => 'Come back soon for new content!',
            'no_egis' => 'No EGI artworks available at the moment.',
            'no_creators' => 'No artists available at the moment.',
            'no_collections' => 'No collections available at the moment.',
            'no_collectors' => 'No collectors available at the moment.'
        ],

        // Content Type Buttons
        'content_types' => [
            'egi_list' => 'EGI List View',
            'egi_card' => 'EGI Card View',
            'creators' => 'Featured Artists',
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
        'creators' => 'Artists',
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

        // Carousel sections
        'sections' => [
            'egis' => 'Featured EGIs',
            'creators' => 'Emerging Artists',
            'collections' => 'Exclusive Collections',
            'collectors' => 'Top Collectors'
        ],
        'view_all' => 'View All',
        'items' => 'items',

        // Title and subtitle for multi-content carousel
        'title' => 'Activate an EGI!',
        'subtitle' => 'Activating a work means joining it and being forever recognized as part of its history.',
    ],

    /*
    |--------------------------------------------------------------------------
    | List View - Homepage List Mode
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Explore by Category',
        'subtitle' => 'Browse through different categories to find what you\'re looking for',

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
        'subtitle' => 'The best EGI creations from our community',
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
        'subtitle' => 'Choose how you want to browse the content',
        'carousel_mode' => 'Carousel View',
        'list_mode' => 'List View',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Hero Section with 3D Coverflow Effect
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activating an EGI is leaving a mark.',
        'subtitle' => 'Your name remains forever next to the Creator\'s: without you, the work wouldn\'t exist.',
        'carousel_mode' => 'Carousel View',
        'list_mode' => 'Grid View',
        'carousel_label' => 'Featured artworks carousel',
        'no_egis' => 'No featured artworks available at the moment.',
        'navigation' => [
            'previous' => 'Previous Artwork',
            'next' => 'Next Artwork',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Labels - Screen Readers
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'EGI Edit Form',
        'delete_button' => 'Delete EGI Button',
        'toggle_edit' => 'Toggle Edit Mode',
        'save_form' => 'Save EGI Changes',
        'close_modal' => 'Close Confirmation Window',
        'required_field' => 'Required Field',
        'optional_field' => 'Optional Field',
    ],

    'collection' => [
        'part_of' => 'Part of',
    ],

    // Collection Collaborators
    'collection_collaborators' => 'Collaborators',
    'owner' => 'Owner',
    // 'creator' => 'Creator',
    'no_other_collaborators' => 'No other collaborators',

    /*
    |--------------------------------------------------------------------------
    | Certificate of Authenticity (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'No Certificate of Authenticity',
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
        'annex_coming_soon' => 'Annex management available soon!',
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

        // JavaScript Messages
        'confirm_issue' => 'Issue a Certificate of Authenticity for this EGI?',
        'issued_success' => 'Certificate issued successfully!',
        'confirm_reissue' => 'Reissue this certificate? This will create a new version.',
        'reissued_success' => 'Certificate reissued successfully!',
        'reissue_certificate_confirm' => 'Are you sure you want to reissue this certificate?',
        'certificate_reissued_successfully' => 'Certificate reissued successfully!',
        'error_reissuing_certificate' => 'Error reissuing the certificate',
        'revocation_reason' => 'Revocation Reason:',
        'confirm_revoke' => 'Revoke this certificate? This action cannot be undone.',
        'revoked_success' => 'Certificate revoked successfully!',
        'error_issuing' => 'Error issuing the certificate',
        'error_reissuing' => 'Error reissuing the certificate',
        'error_revoking' => 'Error revoking the certificate',
        'unknown_error' => 'Unknown Error',
        'verify_any_certificate' => 'Verify Any Certificate',

        // Annex Modal
        'manage_annexes_title' => 'Manage CoA Pro Annexes',
        'annexes_description' => 'Add professional documentation to enhance your certificate',
        'provenance_tab' => 'Provenance',
        'condition_tab' => 'Condition',
        'exhibitions_tab' => 'Exhibitions',
        'photos_tab' => 'Photos',
        'provenance_title' => 'Provenance Documentation',
        'provenance_description' => 'Document the ownership history and chain of authenticity',
        'condition_title' => 'Condition Report',
        'condition_description' => 'Professional assessment of the physical condition of the artwork',
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
        'ownership_history_description' => 'Document the ownership history and chain of authenticity of this artwork',
        'previous_owners' => 'Previous Owners',
        'previous_owners_placeholder' => 'List previous owners and possession dates...',
        'acquisition_details' => 'Acquisition Details',
        'acquisition_details_placeholder' => 'How was this artwork acquired? Include dates, prices, auction houses...',
        'authenticity_sources' => 'Authenticity Sources',
        'authenticity_sources_placeholder' => 'Expert opinions, catalogues raisonnés, institutional archives...',
        'save_provenance_data' => 'Save Provenance Data',

        // Condition Form
        'condition_assessment_description' => 'Professional assessment of the physical state of the artwork and conservation needs',
        'overall_condition' => 'Overall Condition',
        'condition_excellent' => 'Excellent',
        'condition_very_good' => 'Very Good',
        'condition_good' => 'Good',
        'condition_fair' => 'Fair',
        'condition_poor' => 'Poor',
        'condition_notes' => 'Condition Notes',
        'condition_notes_placeholder' => 'Detailed description of any damage, restorations, or conservation issues...',
        'conservation_history' => 'Conservation History',
        'conservation_history_placeholder' => 'Previous restorations, treatments, or conservation interventions...',
        'save_condition_data' => 'Save Condition Data',

        // Exhibitions Form
        'exhibition_history_description' => 'Record of museums, galleries, and public exhibitions where this artwork has been displayed',
        'exhibition_title' => 'Exhibition Title',
        'exhibition_title_placeholder' => 'Exhibition name...',
        'venue' => 'Venue',
        'venue_placeholder' => 'Name of museum, gallery, or institution...',
        'exhibition_dates' => 'Exhibition Dates',
        'exhibition_notes' => 'Notes',
        'exhibition_notes_placeholder' => 'Catalog number, special mentions, reviews...',
        'add_exhibition' => 'Add Exhibition',
        'save_exhibitions_data' => 'Save Exhibitions Data',

        // Photos Form
        'photo_documentation_description' => 'High-quality images for documentation and archival purposes',
        'photo_type' => 'Photo Type',
        'photo_overall' => 'Overall View',
        'photo_detail' => 'Detail',
        'photo_raking' => 'Raking Light',
        'photo_uv' => 'UV Photography',
        'photo_infrared' => 'Infrared',
        'photo_back' => 'Back/Verso',
        'photo_signature' => 'Signature/Marks',
        'photo_frame' => 'Frame/Mounting',
        'photo_description' => 'Description',
        'photo_description_placeholder' => 'Describe what this photo shows...',
        'save_photos_data' => 'Save Photos Data',

        // Additional fields for condition form
        'select_condition' => 'Select condition...',
        'detailed_assessment' => 'Detailed Assessment',
        'detailed_assessment_placeholder' => 'Detailed description of the condition, including any damage, restorations, or special features...',
        'assessor_information' => 'Assessor Information',
        'assessor_placeholder' => 'Name and credentials of the condition assessor...',
        'save_condition_report' => 'Save Condition Report',

        // Exhibitions form fields
        'major_exhibitions' => 'Major Exhibitions',
        'major_exhibitions_placeholder' => 'List major exhibitions, museums, galleries, dates...',
        'publications_catalogues' => 'Publications and Catalogues',
        'publications_placeholder' => 'Books, catalogues, articles where this artwork has been published...',
        'awards_recognition' => 'Awards and Recognition',
        'awards_placeholder' => 'Awards, recognitions, criticism received...',
        'save_exhibition_history' => 'Save Exhibition History',

        // Photos form fields
        'click_upload_images' => 'Click to upload images',
        'png_jpg_webp' => 'PNG, JPG, WEBP up to 10MB each',
        'photo_descriptions' => 'Photo Descriptions',
        'photo_descriptions_placeholder' => 'Describe the images: lighting conditions, details captured, purpose...',
        'photographer_credits' => 'Photographer Credits',
        'photographer_placeholder' => 'Photographer name and date...',
        'save_photo_documentation' => 'Save Photo Documentation',

        // Modal Actions
        'close' => 'Close',
        'error_no_certificate' => 'Error: No certificate selected',
        'saving' => 'Saving...',
        'annex_saved_success' => 'Annex data saved successfully!',
        'error_saving_annex' => 'Error saving annex data',

        // Missing translations for sidebar and CoA components
        'certificate' => 'CoA Certificate',
        'certificate_active' => 'Active Certificate',
        'serial_number' => 'Serial Number',
        'issue_date' => 'Issue Date',
        'expires' => 'Expires',
        'no_certificate_issued' => 'This EGI does not have a Certificate of Authenticity',
        'issue_certificate' => 'Issue Certificate',
        'certificate_issued_successfully' => 'Certificate issued successfully!',
        'pdf_generated_automatically' => 'PDF generated automatically!',
        'download_pdf_now' => 'Do you want to download the PDF now?',
        'digital_signatures' => 'Digital Signatures',
        'signature_by' => 'Signed by',
        'signature_role' => 'Role',
        'signature_provider' => 'Provider',
        'signature_date' => 'Signature Date',
        'unknown_signer' => 'Unknown Signer',
        'step_creating_certificate' => 'Creating certificate...',
        'step_generating_snapshot' => 'Generating snapshot...',
        'step_generating_pdf' => 'Generating PDF...',
        'step_finalizing' => 'Finalizing...',
        'generating' => 'Generating...',
        'generating_pdf' => 'Generating PDF...',
        'error_issuing_certificate' => 'Error issuing the certificate: ',
        'issuing' => 'Issuing...',
        'unlock_with_coa_pro' => 'Unlock with CoA Pro',
        'provenance_documentation' => 'Provenance Documentation',
        'condition_reports' => 'Condition Reports',
        'exhibition_history' => 'Exhibition History',
        'professional_pdf' => 'Professional PDF',
        'only_creator_can_issue' => 'Only the creator can issue certificates',

        // CoA Traits Vocabulary System
        'traits_management_title' => 'Manage CoA Traits',
        'traits_management_description' => 'Configure the technical characteristics of the artwork for the Certificate of Authenticity',
        'status_configured' => 'Configured',
        'status_not_configured' => 'Not Configured',
        'edit_traits' => 'Edit Traits',
        'no_technique_selected' => 'No technique selected',
        'no_materials_selected' => 'No materials selected',
        'no_support_selected' => 'No support selected',
        'custom' => 'custom',
        'last_updated' => 'Last Updated',
        'never_configured' => 'Never Configured',
        'clear_all' => 'Clear All',
        'saved' => 'Saved',

        // Vocabulary Modal
        'modal_title' => 'Select CoA Traits',
        'category_technique' => 'Technique',
        'category_materials' => 'Materials',
        'category_support' => 'Support',
        'search_placeholder' => 'Search terms...',
        'loading' => 'Loading...',
        'selected_items' => 'Selected Items',
        'no_items_selected' => 'No items selected',
        'add_custom' => 'Add Custom',
        'custom_term_placeholder' => 'Enter custom term (max 60 characters)',
        'add' => 'Add',
        'items_selected' => 'items selected',
        'confirm' => 'Confirm',

        // Vocabulary Components
        'terms_available' => 'terms available',
        'no_categories_available' => 'No categories available',
        'no_categories_found' => 'No vocabulary categories found.',
        'search_results' => 'Search Results',
        'results_for' => 'For',
        'terms_found' => 'terms found',
        'results_found' => 'results found',
        'no_results_found' => 'No results found',
        'no_terms_match_search' => 'No terms match the search',
        'in_category' => 'in category',
        'clear_search' => 'Clear Search',
        'no_terms_available' => 'No terms available',
        'no_terms_found_category' => 'No terms found for the category',
        'categories' => 'Categories',
        'back_to_start' => 'Back to Start',
        'retry' => 'Retry',
        'error' => 'Error',
        'unexpected_error' => 'An unexpected error occurred.',
        'professional_pdf_bundle' => 'Professional PDF Bundle',
        'public_verification' => 'Public Verification',
        'verification_description' => 'Verify the authenticity of an EGI Certificate of Authenticity',
        'verification_instructions' => 'Enter the certificate serial number to verify its authenticity',
        'enter_serial' => 'Enter Serial Number',
        'serial_help' => 'Format: ABC-123-DEF (letters, numbers, and hyphens)',
        'certificate_of_authenticity' => 'Certificate of Authenticity',
        'public_verification_display' => 'Public Verification Display',
        'verified_authentic' => 'Certificate Verified and Authentic',
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
        'revoke_certificate_confirm' => 'Revoke this certificate? This action cannot be undone.',
        'reason_for_revocation' => 'Revocation Reason:',
        'certificate_revoked_successfully' => 'Certificate revoked successfully!',
        'error_revoking_certificate' => 'Error revoking the certificate: ',
        'manage_certificate' => 'Manage Certificate',
        'annex_management_coming_soon' => 'Annex management coming soon!',
        'issue_certificate_description' => 'Issue a certificate to provide proof of authenticity and unlock Pro functions',
        'serial' => 'Serial',
        'pro_features' => 'Pro Features',
        'provenance_docs' => 'Provenance Documentation',
        'unlock_pro_features' => 'Unlock Pro Features',
        'reason_for' => 'Reason for',

        // QES Signatures Badges
        'badge_author_signed' => 'Author Signed (QES)',
        'badge_inspector_signed' => 'Inspector Signed (QES)',
        'badge_timestamped' => 'Timestamped (QES)',
        'badge_integrity_ok' => 'Integrity Verified',

        // Location UI (CoA)
        'issue_place' => 'Issue Place',
        'location_placeholder' => 'E.g., Florence, Tuscany, Italy',
        'save' => 'Save',
        'location_hint' => 'Use the format "City, Region/Province, Country" (or equivalent).',
        'location_required' => 'Location is required',
        'location_saved' => 'Location saved',
        'location_save_failed' => 'Location save failed',
        'location_updated' => 'Location updated successfully',

        // Inspector Co-sign (QES)
        'inspector_countersign' => 'Inspector Co-sign (QES)',
        'confirm_inspector_countersign' => 'Proceed with inspector co-sign?',
        'inspector_countersign_applied' => 'Inspector co-sign applied',
        'operation_failed' => 'Operation failed',
        'author_countersign' => 'Author Signature (QES)',
        'confirm_author_countersign' => 'Proceed with author signature?',
        'author_countersign_applied' => 'Author signature applied',

        // Signature Removal
        'remove_signature' => 'Remove Signature',
        'confirm_remove_signature' => 'Remove {role} signature?',
        'signature_removed' => '{role} signature removed successfully',
        'signature_removal_failed' => 'Signature removal failed',
        'signature_removal_warning' => 'Warning: The PDF will be regenerated without this signature. This action cannot be undone.',
        'regenerate_pdf' => 'Regenerate PDF',
        'pdf_regenerated' => 'PDF regenerated',
        'regenerating_pdf' => 'regenerating PDF with signature',
        'pdf_regenerate_failed' => 'PDF regeneration failed',

        // Public Verification Page
        'public_verify' => [
            'signature' => 'Signature',
            'author_signed' => 'Author Signed',
            'inspector_countersigned' => 'Inspector Countersigned',
            'timestamp_tsa' => 'TSA Timestamp',
            'qes' => 'QES',
            'wallet_signature' => 'Wallet Signature',
            'verify_signature' => 'verify signature',
            'certificate_hash' => 'Certificate Hash (SHA-256)',
            'pdf_hash' => 'PDF Hash (SHA-256)',
            'copy_hash' => 'Copy Hash',
            'copy_pdf_hash' => 'Copy PDF Hash',
            'hash_copied' => 'Hash copied to clipboard!',
            'pdf_hash_copied' => 'PDF Hash copied to clipboard!',
            'qr_code_verify' => 'QR Code Verification',
            'qr_code' => 'QR Code',
            'scan_to_verify' => 'Scan to Verify',
            'status' => 'Status',
            'valid' => 'Valid',
            'incomplete' => 'Incomplete',
            'revoked' => 'Revoked',

            // Headers and titles
            'certificate_title' => 'Certificate of Authenticity',
            'public_verification_display' => 'Public Verification Display',
            'verified_authentic' => 'Certificate Verified and Authentic',
            'verified_at' => 'Verified on',
            'serial_number' => 'Serial Number',
            'certificate_not_ready' => 'Certificate Not Ready',
            'certificate_revoked' => 'Certificate Revoked',
            'certificate_not_valid' => 'This certificate is no longer valid',
            'requires_coa_traits' => 'Requires CoA Traits',
            'certificate_not_ready_generic' => 'Certificate Not Ready - Generic Traits',

            // Artwork Information
            'artwork_title' => 'Title',
            'year' => 'Year',
            'dimensions' => 'Dimensions',
            'edition' => 'Edition',
            'author' => 'Author',
            'technique' => 'Technique',
            'material' => 'Material',
            'support' => 'Support',
            'platform' => 'Platform',
            'published_by' => 'Published by',
            'image' => 'Image',

            // Certificate Information
            'issue_date' => 'Issue Date',
            'issued_by' => 'Issued by',
            'issue_location' => 'Issue Location',
            'notes' => 'Notes',

            // Professional Annexes
            'professional_annexes' => 'Professional Annexes',
            'provenance' => 'Provenance',
            'condition_report' => 'Condition Report',
            'exhibitions_publications' => 'Exhibitions/Publications',
            'additional_photos' => 'Additional Photos',

            // On-chain Information
            'on_chain_info' => 'On-chain Information',
        ],
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
        'close' => 'Close Dossier',

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
        'no_utility_title' => 'Dossier not available',
        'no_utility_message' => 'No additional images available for this artwork.',
        'no_utility_description' => 'The additional image dossier has not yet been configured for this artwork.',

        'no_images_title' => 'No images available',
        'no_images_message' => 'The dossier exists but does not yet contain images.',
        'no_images_description' => 'Additional images will be added in the future by the artwork creator.',

        'error_title' => 'Error',
        'error_loading' => 'Error loading dossier',

        // Navigation
        'previous_image' => 'Previous Image',
        'next_image' => 'Next Image',
        'close_viewer' => 'Close Viewer',
        'of' => 'of',

        // Zoom Controls
        'zoom_help' => 'Use mouse wheel or touch for zoom • Drag to move',
        'zoom_in' => 'Zoom In',
        'zoom_out' => 'Zoom Out',
        'zoom_reset' => 'Reset Zoom',
        'zoom_fit' => 'Fit to Screen',
    ],

];
