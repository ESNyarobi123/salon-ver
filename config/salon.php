<?php

/**
 * Beauty salon / saloon terminology for UI copy (English + snippets for Swahili UI).
 * Database tables and Spatie roles keep legacy names (restaurants, waiters, tables, role:waiter).
 *
 * WhatsApp bot (Node): mirror user-facing strings in saloonbot/src/lang.js (en/sw).
 */
return [

    'entity' => 'Saloon',
    'entity_plural' => 'Saloons',
    'entity_lower' => 'saloon',
    'entity_plural_lower' => 'saloons',

    'staff' => 'Stylist',
    'staff_plural' => 'Stylists',
    'staff_lower' => 'stylist',
    'staff_plural_lower' => 'stylists',

    'seat' => 'Seat',
    'seat_plural' => 'Seats',

    'services' => 'Services',
    'service' => 'Service',
    'service_menu_image' => 'Service menu image',

    'booking' => 'Booking',
    'booking_plural' => 'Bookings',
    'live_bookings' => 'Live bookings',
    'booking_history' => 'Booking history',
    /** Live / service desk column grouping preparing + floor-display “ready”. */
    'order_column_in_progress' => 'In progress (prep / ready)',

    'floor_display' => 'Floor display',
    'floor_display_short' => 'Salon floor',

    'customer' => 'Client',

    'portal_stylist' => 'Stylist portal',
    'portal_order' => 'Service desk',

    'tagline' => 'Salon services, retail & payments in one place',

    'service_catalog_title' => 'Service catalog',
    'service_catalog_subtitle' => 'Treatments, add-ons & retail (oils, cosmetics, accessories)',

    'seat_management_title' => 'Seat management',
    'seat_management_subtitle' => 'Seats, stations & WhatsApp QR codes',

    'official_saloon_qr' => 'Official saloon QR',
    'saloon_entrance_qr' => 'Entrance & marketing QR',
    'saloon_qr_help' => 'Clients scan to open WhatsApp and browse your services & products.',

    'add_service_item' => 'Add service / product',
    'edit_service_item' => 'Edit service / product',
    'all_service_items' => 'All items',

    /** Swahili API / validation copy (stylist = front-of-house staff). */
    'stylist_lookup_failed_sw' => 'Stylist hajapatikana. Angalia nambari ya pekee (TIPTAP-W-xxxxx).',

    /*
    |--------------------------------------------------------------------------
    | Manager web portal (Blade UI — navigation & dashboard hub)
    |--------------------------------------------------------------------------
    */
    'manager_portal_subtitle' => 'Manager portal',
    'manager_sidebar_section_main' => 'Operations',
    'manager_sidebar_section_finance' => 'Finance',
    'manager_sidebar_section_insights' => 'Insights & tools',
    'manager_nav_dashboard' => 'Dashboard',
    'manager_nav_payroll' => 'Payroll',
    'manager_nav_payroll_history' => 'Payroll history',
    'manager_nav_payments' => 'Payments',
    'manager_nav_reports' => 'Performance reports',
    'manager_nav_staff_history' => 'Staff work history',
    'manager_nav_api' => 'API, Selcom & KDS',
    'manager_nav_help' => 'Help & guides',
    /** Appended after `staff_plural` in nav (e.g. "Stylists & team"). */
    'manager_nav_staff_team_suffix' => ' & team',
    /** Appended after `seat_plural` in nav (e.g. "Seats & QR codes"). */
    'manager_nav_seat_qr_suffix' => ' & QR codes',
    'manager_nav_customer_feedback' => 'Client feedback',
    'manager_nav_stock' => 'Stock & alerts',
    'manager_stock_page_title' => 'Stock & low-stock alerts',
    'manager_stock_page_subtitle' => 'Track retail or consumables, set alert levels, and see what is running low.',
    'manager_system_live_badge' => 'System live',
    'manager_account_role' => 'Manager account',
    'manager_dash_page_title' => 'Manager dashboard',
    'manager_dash_hub_title' => 'Manage every area',
    'manager_dash_hub_subtitle' => 'Shortcuts to bookings, staff, finance, reports, and integrations.',
    'manager_kds_open' => 'Open floor display',
    'manager_kds_configure_hint' => 'Create or copy the KDS link under API settings.',
    'manager_staff_online_suffix' => 'on duty',

    /*
    |--------------------------------------------------------------------------
    | Stylist / team web portal (Spatie role: waiter; routes stay waiter.*)
    |--------------------------------------------------------------------------
    */
    'stylist_sidebar_section_main' => 'Today’s work',
    'stylist_sidebar_section_stats' => 'Earnings & reviews',
    'stylist_sidebar_section_account' => 'Account',
    'stylist_nav_home' => 'Home',
    'stylist_nav_calls' => 'Calls & bills',
    'stylist_nav_handover' => 'Handover',
    'stylist_nav_tips' => 'Tips',
    'stylist_nav_ratings' => 'Ratings',
    'stylist_nav_salary' => 'Payslips',
    'stylist_nav_history' => 'History',
    'stylist_nav_help' => 'Help',
    'stylist_sign_out' => 'Sign out',
    'stylist_badge_new' => 'New',

    'stylist_dash_page_title' => 'Team dashboard',
    'stylist_dash_greeting' => 'Hello',
    'stylist_dash_subtitle' => 'Here’s what’s happening with your team and bookings today.',
    'stylist_online' => 'Online',
    'stylist_offline' => 'Offline',
    'stylist_btn_go_offline' => 'Nimekamilisha – Nenda offline',
    'stylist_btn_go_online' => 'Niko kazini – Nenda online',

    'stylist_stat_badge_today' => 'Today',
    'stylist_stat_tips_label' => 'Tips today',
    'stylist_stat_live_badge' => 'Live',
    'stylist_stat_ready_label' => 'Ready now',
    'stylist_stat_calls_label' => 'Calls',

    'stylist_section_urgent' => 'Needs your attention',
    'stylist_request_bill' => 'Bill / payment request',
    'stylist_request_call' => 'Calling for stylist',
    'stylist_mark_done' => 'Mark done',

    'stylist_unassigned_title' => 'Bookings needing a stylist',
    'stylist_action_required' => 'Action needed',
    'stylist_claim_booking' => 'Take booking',
    'stylist_more_lines' => 'more lines',

    'stylist_active_bookings_title' => 'My bookings today',
    'stylist_view_all' => 'View all',
    'stylist_table_col_booking' => 'Booking',
    'stylist_table_col_status' => 'Status',
    'stylist_table_col_total' => 'Total',
    'stylist_table_lines_meta' => 'lines',
    'stylist_no_bookings_today' => 'No bookings on your slate yet today',

    'stylist_pulse_title' => 'Salon pulse',
    'stylist_pulse_total_active' => 'Active bookings (salon)',
    'stylist_pulse_ready' => 'Ready to serve',

    'stylist_service_tag_title' => 'My stylist tag',
    'stylist_your_code_label' => 'Your code',
    'stylist_share_booking_hint' => 'Share for direct bookings',
    'stylist_save_qr' => 'Save QR',
    'stylist_copy_link' => 'Copy link',
    'stylist_copy_copied' => 'Copied!',
    'stylist_copy_failed' => 'Could not copy to clipboard',

    'stylist_order_portal_badge' => 'Service desk access',
    'stylist_order_portal_open' => 'Open service desk',

    'stylist_feedback_recent' => 'Recent client feedback',
    'stylist_no_ratings_yet' => 'No feedback yet',

    /** Display-only labels for booking row status (API values unchanged). */
    'stylist_booking_status_display' => [
        'pending' => 'Pending',
        'preparing' => 'In progress',
        'ready' => 'Ready',
        'served' => 'Served',
        'paid' => 'Paid',
    ],

    'stylist_not_linked_page_title' => 'Join your salon',
    'stylist_not_linked_copy' => 'Copy',
    'stylist_not_linked_copied' => 'Copied!',

    /*
    |--------------------------------------------------------------------------
    | Code shapes (TipTap ID vs saloon tags vs WhatsApp QR) — display copy only
    |--------------------------------------------------------------------------
    */
    'code_legend_title' => 'Your codes (3 different shapes)',
    'code_legend_tiptap_id' => 'TipTap ID (e.g. TIPTAP-W-…): your account everywhere — manager search, payslips, and WhatsApp if you type it as a message.',
    'code_legend_salon_tag' => 'Saloon tag on your desk card (e.g. ABC-W01): ABC is that saloon’s prefix; -W- here means “stylist slot” at that location only (not your TipTap ID).',
    'code_legend_whatsapp_qr' => 'WhatsApp QR opens START_saloonId_SyourUserId — the letter S marks the stylist user id so it is not confused with ABC-W01.',

    /*
    |--------------------------------------------------------------------------
    | Admin portal (super_admin) — same salon vocabulary as manager & stylist UIs
    |--------------------------------------------------------------------------
    */
    'admin_stat_active_bookings' => 'Active bookings',
    'admin_dashboard_newest_partners' => 'Newest partners',
    'admin_table_location' => 'Location',
    'admin_settings_whatsapp_number_hint' => 'Used for WhatsApp entry: clients scan saloon & seat QR codes so the chat opens with this business number.',

    /** Stylist history empty state (Swahili UI). */
    'stylist_history_empty_hint_sw' => 'Utakapounganishwa na saluni (kupitia manager), matukio yataonekana hapa.',

    /** Admin order detail — internal user id (not TipTap-W code). */
    'admin_order_stylist_internal_id' => 'Stylist account #',
];
