<?php

return [
    'title' => 'Invoices',
    'my_invoices' => 'My Invoices',
    'invoice' => 'Invoice',
    'invoices' => 'Invoices',
    'subtitle' => 'Manage your invoices, monthly aggregations and invoicing settings',
    'items_title' => 'Invoice Items Detail',
    
    // Tabs
    'tabs' => [
        'sales' => 'Sales Invoices',
        'purchases' => 'Purchase Invoices',
        'aggregations' => 'Monthly Aggregations',
        'settings' => 'Settings',
    ],
    
    // Status
    'status' => [
        'draft' => 'Draft',
        'pending' => 'Pending',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
        'rejected' => 'Rejected',
    ],
    
    // SDI Status
    'sdi_status' => [
        'not_sent' => 'Not Sent to SDI',
        'pending' => 'SDI Pending',
        'sent' => 'Sent to SDI',
        'delivered' => 'Delivered via SDI',
        'rejected' => 'Rejected by SDI',
    ],
    
    // Types
    'types' => [
        'sales' => 'Sales',
        'purchase' => 'Purchase',
        'credit_note' => 'Credit Note',
    ],
    
    // Fields
    'fields' => [
        'invoice_number' => 'Invoice Number',
        'invoice_code' => 'Invoice Code',
        'invoice_type' => 'Invoice Type',
        'invoice_status' => 'Invoice Status',
        'issue_date' => 'Issue Date',
        'due_date' => 'Due Date',
        'payment_date' => 'Payment Date',
        'seller' => 'Seller',
        'buyer' => 'Buyer',
        'subtotal' => 'Subtotal',
        'tax_amount' => 'Tax',
        'total' => 'Total',
        'payment_method' => 'Payment Method',
        'notes' => 'Notes',
        'managed_by' => 'Managed by',
        'item_description_default' => 'Product/Service sale',
    ],
    
    // Management mode
    'managed_by' => [
        'platform' => 'Platform',
        'user_external' => 'External System',
    ],
    
    // Aggregations
    'aggregations' => [
        'title' => 'Monthly Aggregations',
        'period' => 'Period',
        'total_sales' => 'Total Sales',
        'total_items' => 'Items Sold',
        'total_buyers' => 'Buyers',
        'multiple_buyers' => 'Multiple Buyers',
        'status' => [
            'pending' => 'Pending',
            'invoiced' => 'Invoiced',
            'exported' => 'Exported',
            'cancelled' => 'Cancelled',
        ],
    ],
    
    // Settings
    'settings' => [
        'title' => 'Invoicing Settings',
        'invoicing_mode' => 'Invoicing Mode',
        'platform_managed' => 'Platform Managed',
        'user_managed' => 'User Managed (External System)',
        'external_system_name' => 'External System Name',
        'external_system_notes' => 'External System Notes',
        'auto_generate_monthly' => 'Auto-generate Monthly',
        'invoice_frequency' => 'Invoice Frequency',
        'frequency' => [
            'instant' => 'Instant (per sale)',
            'monthly' => 'Monthly (aggregated)',
            'manual' => 'Manual',
        ],
        'notify_on_invoice_generated' => 'Notify on Invoice Generated',
        'notify_buyer_on_invoice' => 'Notify Buyer',
    ],
    
    // Actions
    'actions' => [
        'create' => 'Create Invoice',
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'download_pdf' => 'Download PDF',
        'download_xml' => 'Download XML',
        'send_to_sdi' => 'Send to SDI',
        'send_to_buyer' => 'Send to Buyer',
        'mark_as_paid' => 'Mark as Paid',
        'cancel' => 'Cancel',
        'generate_from_aggregation' => 'Generate Invoice',
        'export_aggregation' => 'Export Data',
    ],
    
    // Messages
    'messages' => [
        'no_invoices' => 'No invoices found.',
        'no_aggregations' => 'No aggregations found.',
        'invoice_created' => 'Invoice created successfully.',
        'invoice_updated' => 'Invoice updated successfully.',
        'invoice_deleted' => 'Invoice deleted successfully.',
        'invoice_sent_to_sdi' => 'Invoice sent to SDI successfully.',
        'invoice_sent_to_buyer' => 'Invoice sent to buyer successfully.',
        'aggregation_generated' => 'Invoice generated from aggregation successfully.',
        'aggregation_exported' => 'Aggregation exported successfully.',
        'settings_saved' => 'Settings saved successfully.',
    ],
    
    // Errors
    'errors' => [
        'invoice_not_found' => 'Invoice not found.',
        'aggregation_not_found' => 'Aggregation not found.',
        'cannot_delete_paid_invoice' => 'Cannot delete a paid invoice.',
        'cannot_edit_sent_invoice' => 'Cannot edit a sent invoice.',
        'sdi_error' => 'Error sending to SDI.',
        'export_error' => 'Error exporting data.',
        'unauthorized' => 'Unauthorized.',
        'already_invoiced' => 'Aggregation already invoiced.',
        'pdf_not_found' => 'Invoice PDF not found.',
    ],
    
    // Info
    'info' => [
        'platform_managed_info' => 'The platform will automatically generate and manage electronic invoices.',
        'user_managed_info' => 'You will manage invoices through your external system. The platform will provide data to import.',
        'monthly_aggregation_info' => 'Monthly sales will be aggregated and you can generate a single invoice or export the data.',
        'instant_invoicing_info' => 'An invoice will be generated for each sale.',
        'aggregation_buyers_info' => 'This invoice includes sales to multiple buyers in the indicated period.',
        'platform_managed_title' => 'Platform-Managed Invoice',
        'platform_managed_invoice_info' => 'This invoice is automatically managed by FlorenceEGI in compliance with current tax regulations.',
    ],
    
    // PDF Strings
    'pdf' => [
        'footer_line_1' => 'Electronically generated document - Valid under art. 21 D.P.R. 633/72',
        'footer_line_2' => 'FlorenceEGI S.r.l. - VAT IT12345678901 - info@florenceegi.com',
        'platform_description' => 'Asset Tokenization and Environmental Projects Platform',
        'generated_at' => 'Document generated on',
    ],
    
    // Misc
    'tagline' => 'Asset Tokenization Platform',
    'invoice' => 'Invoice',
];

