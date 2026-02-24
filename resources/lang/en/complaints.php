<?php

return [

    // Page
    'title' => 'DSA Complaints and Reports',
    'subtitle' => 'Report illegal content or submit a complaint under the Digital Services Act (EU Reg. 2022/2065)',
    'dsa_info_title' => 'Your rights under the DSA',
    'dsa_info_text' => 'Under Regulation (EU) 2022/2065 (Digital Services Act), you have the right to report content that you believe is illegal (Art. 16) and to file complaints against the platform\'s moderation decisions (Art. 20). Every report is examined by qualified personnel within reasonable timeframes.',
    'legal_contact' => 'For urgent reports you can also write to',

    // Types
    'types' => [
        'content_report' => 'Illegal content report',
        'ip_violation' => 'Intellectual property violation',
        'fraud' => 'Fraud or fraudulent activity',
        'moderation_appeal' => 'Complaint against moderation decision',
        'general' => 'General report',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Illegal, offensive content, or content that violates our terms of use',
        'ip_violation' => 'Counterfeit works, plagiarism, copyright or trademark violations',
        'fraud' => 'Scams, payment fraud, or deceptive behavior',
        'moderation_appeal' => 'Challenge a decision made by the platform regarding your content',
        'general' => 'Any other report not falling into the previous categories',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Work (EGI)',
        'collection' => 'Collection',
        'user_profile' => 'User profile',
        'comment' => 'Comment',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Received',
        'under_review' => 'Under review',
        'action_taken' => 'Action taken',
        'dismissed' => 'Dismissed',
        'appealed' => 'Appeal filed',
        'resolved' => 'Resolved',
    ],

    // Form labels
    'form_title' => 'New report',
    'select_type' => 'Select the report type',
    'complaint_type' => 'Report type',
    'reported_content_type' => 'Type of reported content',
    'select_content_type' => 'Select content type',
    'reported_content_id' => 'Content ID',
    'reported_content_id_help' => 'Enter the ID of the content you wish to report (visible on the content page)',
    'description' => 'Detailed description',
    'description_placeholder' => 'Describe in detail the reason for the report, including all elements useful for evaluation. Minimum 20 characters.',
    'description_chars' => ':count / :max characters',
    'evidence_urls' => 'Evidence URLs (optional)',
    'evidence_urls_help' => 'Enter links to screenshots, web pages or other elements supporting the report. Maximum 5 URLs.',
    'add_evidence_url' => 'Add URL',
    'remove_evidence_url' => 'Remove',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Data processing consent',
    'consent_text' => 'I consent to the processing of personal data necessary for managing this report, in accordance with Reg. EU 2016/679 (GDPR) and Reg. EU 2022/2065 (DSA). I declare that the information provided is truthful and in good faith.',

    // Actions
    'submit' => 'Submit report',
    'submitting' => 'Submitting...',
    'cancel' => 'Cancel',
    'back_to_list' => 'Back to reports',
    'view_details' => 'Details',

    // Messages
    'submitted_successfully' => 'Your report has been successfully submitted. Reference number: :reference. You will receive a confirmation email.',
    'no_complaints' => 'You have not yet submitted any reports or complaints.',

    // Table headers
    'date' => 'Date',
    'reference' => 'Reference',
    'type' => 'Type',
    'status' => 'Status',
    'actions' => 'Actions',

    // Previous complaints section
    'your_complaints' => 'Your reports',
    'your_complaints_description' => 'History of reports and complaints you have submitted',

    // Validation
    'validation' => [
        'type_required' => 'Select the report type.',
        'type_invalid' => 'The selected report type is not valid.',
        'description_required' => 'Description is required.',
        'description_min' => 'Description must contain at least 20 characters.',
        'description_max' => 'Description cannot exceed 5000 characters.',
        'content_id_required' => 'Content ID is required when selecting a content type.',
        'evidence_urls_max' => 'You can enter a maximum of 5 evidence URLs.',
        'evidence_url_format' => 'Each evidence URL must be a valid web address.',
        'consent_required' => 'You must consent to data processing to proceed.',
    ],

    // Detail page
    'detail_title' => 'Report details',
    'submitted_on' => 'Submitted on',
    'current_status' => 'Current status',
    'complaint_type_label' => 'Type',
    'reported_content' => 'Reported content',
    'description_label' => 'Description',
    'evidence_label' => 'Evidence attached',
    'decision' => 'Decision',
    'decision_date' => 'Decision date',
    'decided_by_label' => 'Decided by',
    'no_decision_yet' => 'Awaiting review from the team.',
    'appeal_section' => 'Appeal / Complaint',
    'no_appeal' => 'No appeal filed.',
    'content_id_label' => 'Content ID',
    'content_type_label' => 'Content type',
    'reported_user_label' => 'Reported user',

    // Timeline
    'timeline' => [
        'received' => 'Report received',
        'under_review' => 'Under review',
        'action_taken' => 'Action taken',
        'dismissed' => 'Report dismissed',
        'appealed' => 'Appeal filed',
        'resolved' => 'Case resolved',
    ],

    // Notification email
    'notification' => [
        'subject' => 'DSA Report confirmation - :reference',
        'greeting' => 'Dear :name,',
        'body' => 'Your report has been received and registered with reference number **:reference**.',
        'body_2' => 'We will examine your report and contact you within the timeframes provided by the Digital Services Act (EU Reg. 2022/2065).',
        'reference_label' => 'Reference number',
        'type_label' => 'Report type',
        'date_label' => 'Submission date',
        'closing' => 'The FlorenceEGI team',
    ],

];
