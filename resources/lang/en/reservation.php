<?php

/**
 * Reservation Messages
 * @package FlorenceEGI
 * @subpackage Translations
 * @language en
 * @version 2.0.0
 */

return [
    // Success messages
    'success' => 'Your bid has been successfully placed! The certificate has been generated.',
    'cancel_success' => 'Your bid has been successfully cancelled.',
    'success_title' => 'Bid placed!',
    'view_certificate' => 'View Certificate',
    'close' => 'Close',

    // Error messages
    'unauthorized' => 'You must connect your wallet or sign in to make a bid.',
    'validation_failed' => 'Please check the entered data and try again.',
    'auth_required' => 'Authentication is required to view your bids.',
    'list_failed' => 'Unable to retrieve your bids. Please try again later.',
    'status_failed' => 'Unable to retrieve bid status. Please try again later.',
    'unauthorized_cancel' => 'You do not have permission to cancel this bid.',
    'cancel_failed' => 'Unable to cancel the bid. Please try again later.',

    // UI Buttons
    'button' => [
        'reserve' => 'Make a bid',
        'reserved' => 'Bid placed',
        'make_offer' => 'Make a bid'
    ],

    // Badges
    'badge' => [
        'highest' => 'Highest Bid',
        'superseded' => 'Outbid',
        'has_offers' => 'With Bids'
    ],

    // Bid details
    'already_reserved' => [
        'title' => 'Already Bid',
        'text' => 'You already have an active bid for this EGI.',
        'details' => 'Details of your bid:',
        'type' => 'Type',
        'amount' => 'Amount',
        'status' => 'Status',
        'view_certificate' => 'View Certificate',
        'ok' => 'OK',
        'new_reservation' => 'New Bid',
        'confirm_new' => 'Do you want to place a new bid?'
    ],

    // Bid history
    'history' => [
        'title' => 'Bid History',
        'entries' => 'Bid Entries',
        'view_certificate' => 'View Certificate',
        'no_entries' => 'No bids found.',
        'be_first' => 'Be the first to bid on this EGI!'
    ],

    // Error messages
    'errors' => [
        'button_click_error' => 'An error occurred while processing your request.',
        'form_validation' => 'Please check the entered data and try again.',
        'api_error' => 'A communication error occurred with the server.',
        'unauthorized' => 'You must connect your wallet or sign in to make a bid.'
    ],

    // Form
    'form' => [
        'title' => 'Place a bid for this EGI',
        'offer_amount_label' => 'Your Bid (EUR)',
        'offer_amount_placeholder' => 'Enter amount in EUR',
        'algo_equivalent' => 'Approximately :amount ALGO',
        'terms_accepted' => 'I accept the terms and conditions for EGI bidding',
        'contact_info' => 'Additional Contact Information (Optional)',
        'submit_button' => 'Place Reservation',
        'cancel_button' => 'Cancel'
    ],

    // Reservation type
    'type' => [
        'strong' => 'Strong Reservation',
        'weak' => 'Weak Reservation'
    ],

    // Priority levels
    'priority' => [
        'highest' => 'Active Reservation',
        'superseded' => 'Superseded',
    ],

    // Reservation status
    'status' => [
        'active' => 'Active',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired'
    ],

    // === NEW SECTION: NOTIFICATIONS ===
    'notifications' => [
        'reservation_expired' => 'Your reservation of €:amount for :egi_title has expired.',
        'superseded' => 'Your offer for :egi_title has been superseded. New highest offer: €:new_highest_amount',
        'highest' => 'Congratulations! Your offer of €:amount for :egi_title is now the highest!',
        'rank_changed' => 'Your position for :egi_title has changed: you are now in position #:new_rank',
        'competitor_withdrew' => 'A competitor has withdrawn. You have moved up to position #:new_rank for :egi_title',
        'pre_launch_reminder' => 'The on-chain mint will start soon! Confirm your reservation for :egi_title.',
        'mint_window_open' => 'It\'s your turn! You have 48 hours to complete the mint of :egi_title.',
        'mint_window_closing' => 'Attention! Only :hours_remaining hours remaining to complete the mint of :egi_title.',
        'default' => 'Update on your reservation for :egi_title',
        'archived_success' => 'Notification archived successfully.'
    ],
];