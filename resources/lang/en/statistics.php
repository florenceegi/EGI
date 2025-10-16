<?php
// resources/lang/en/statistics.php
return [
    'statistics_dashboard' => 'Statistics Dashboard',
    'dashboard_subtitle' => 'Comprehensive analytics for your EGI collections',
    'refresh' => 'Refresh',
    'loading' => 'Loading',
    'calculating' => 'Calculating statistics',
    'error_loading' => 'Failed to load statistics. Please try again.',
    'last_updated' => 'Last updated',

    // Time Period Filters
    'time_period' => 'Time Period',
    'period_day' => 'Today',
    'period_week' => 'Week',
    'period_month' => 'Month',
    'period_year' => 'Year',
    'period_all' => 'All Time',

    // Phase 2: Dual Source Tabs
    'tabs_navigation' => 'Statistics Navigation',
    'mints_tab' => 'Mints (Actual Revenue)',
    'reservations_tab' => 'Reservations (Forecast)',
    'comparison_tab' => 'Comparison',

    // Phase 2: Mint Statistics
    'mint_statistics' => 'Mint Statistics',
    'total_mints' => 'Total Mints',
    'total_revenue' => 'Total Revenue',
    'total_revenue_eur' => 'Total Revenue (EUR)',
    'avg_mint_price' => 'Average Mint Price',
    'mints_count' => 'Mints Count',
    'mint_revenue_by_collection' => 'Mint Revenue by Collection',
    'mint_revenue_by_user_type' => 'Revenue by User Type',
    'no_mint_data' => 'No completed mints yet',

    // Phase 2: Dual Source Comparison
    'conversion_rate' => 'Conversion Rate',
    'conversion_rate_description' => 'Percentage of reservations converted to mints',
    'forecast_vs_reality' => 'Forecast vs Reality',
    'forecast_eur' => 'Forecast (EUR)',
    'reality_eur' => 'Reality (EUR)',
    'delta_eur' => 'Delta (EUR)',
    'delta_percentage' => 'Delta %',
    'comparison_by_collection' => 'Comparison by Collection',
    'reservations_count' => 'Reservations',
    'mints_count_short' => 'Mints',

    // KPI Labels
    'total_likes' => 'Total Likes',
    'total_reservations' => 'Total Reservations',
    'total_amount' => 'Total Amount',
    'epp_quota' => 'EPP Quota',

    // Section Titles
    'likes_by_collection' => 'Likes by Collection',
    'reservations_by_collection' => 'Reservations by Collection',
    'top_egis' => 'Top 3 Liked EGIs',
    'epp_breakdown' => 'EPP Quota Breakdown',

    // Empty States
    'no_data' => 'No data available',
    'no_collections' => 'No collections with likes yet',
    'no_reservations' => 'No active reservations',
    'no_top_egis' => 'No EGIs with likes yet',
    'no_epp_data' => 'No EPP data available',

    // GDPR
    'gdpr_check' => 'GDPR Compliance Check',
    'gdpr_coming_soon' => 'Privacy compliance alerts coming in the next MVP phase',

    // Payment Distribution Statistics
    'volume' => 'VOLUME',
    'epp' => 'EPP',
    'collections' => 'COLLECTIONS',
    'sell_collections' => 'SELL COLLECTIONS',
    'egis' => 'EGIS',
    'sell_egis' => 'SELL EGIS',

    // Statistics descriptions (for tooltips/help)
    'volume_description' => 'Total amount distributed',
    'epp_description' => 'Total distributed to EPPs',
    'collections_description' => 'Total number of collections',
    'sell_collections_description' => 'Collections with active distributions',
    'egis_description' => 'Total number of EGIs',
    'sell_egis_description' => 'EGIs with active reservations',

    // Likes Analytics
    'likes_analytics' => 'Likes Analytics',
    'total_likes_received' => 'Total Likes Received',
    'likes_received' => 'Likes Received',
    'likes_given' => 'Likes Given',
    'top_liked_egis' => 'Top Liked EGIs',
    'top_liking_users' => 'Most Active Users',
    'total_given' => 'Total Given',
    'no_liked_egis' => 'No EGIs have received likes yet',
    'no_liking_users' => 'No likes given yet',
    'liked_by' => 'Liked by',
    'users' => 'users',
];
