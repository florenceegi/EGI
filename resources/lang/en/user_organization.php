<?php

/**
 * @Oracode Translation File: Organization Data Management - English
 * 🎯 Purpose: Complete English translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with English base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // PAGE TITLES AND HEADERS
    'management_title' => 'Organization Data',
    'management_subtitle' => 'Manage your organization or business data',
    'company_title' => 'Business Information',
    'company_subtitle' => 'Legal and operational business details',
    'contacts_title' => 'Business Contacts',
    'contacts_subtitle' => 'Contacts and reference information',
    'certifications_title' => 'Certifications',
    'certifications_subtitle' => 'Environmental and quality certifications',

    // ORGANIZATION TYPES
    'organization_types' => [
        'corporation' => 'Corporation',
        'partnership' => 'Partnership',
        'sole_proprietorship' => 'Sole Proprietorship',
        'cooperative' => 'Cooperative',
        'non_profit' => 'Non-Profit Organization',
        'foundation' => 'Foundation',
        'association' => 'Association',
        'government' => 'Public Entity',
        'educational' => 'Educational Institution',
        'research' => 'Research Institute',
        'startup' => 'Innovative Startup',
        'other' => 'Other',
    ],

    'legal_forms' => [
        'srl' => 'LLC - Limited Liability Company',
        'spa' => 'PLC - Joint Stock Company',
        'srls' => 'Simplified LLC',
        'snc' => 'General Partnership',
        'sas' => 'Limited Partnership',
        'ditta_individuale' => 'Sole Proprietorship',
        'cooperativa' => 'Cooperative',
        'onlus' => 'Non-Profit Organization (ONLUS)',
        'aps' => 'Social Promotion Association',
        'ets' => 'Third Sector Entity',
        'fondazione' => 'Foundation',
        'ente_pubblico' => 'Public Entity',
    ],

    // FORM SECTIONS
    'legal_information' => 'Legal Information',
    'legal_information_desc' => 'Legal and registration details of the organization',
    'operational_information' => 'Operational Information',
    'operational_information_desc' => 'Operational and business data',
    'contact_information' => 'Contact Information',
    'contact_information_desc' => 'Company contacts and references',
    'sustainability_info' => 'Sustainability Information',
    'sustainability_info_desc' => 'Environmental certifications and sustainable practices',
    'epp_information' => 'EPP Information',
    'epp_information_desc' => 'Specific data for EPP entities (Environmental Protection Points)',

    // FORM FIELDS - LEGAL INFORMATION
    'legal_name' => 'Legal Name',
    'legal_name_placeholder' => 'Full legal name of the organization',
    'trade_name' => 'Trade Name',
    'trade_name_placeholder' => 'Trade name or brand (if different)',
    'legal_form' => 'Legal Form',
    'legal_form_placeholder' => 'Select legal form',
    'vat_number' => 'VAT Number',
    'vat_number_placeholder' => 'IT12345678901',
    'tax_code' => 'Tax Code',
    'tax_code_placeholder' => 'Organization tax code',
    'registration_number' => 'Business Registration Number',
    'registration_number_placeholder' => 'Chamber of Commerce registration number',
    'chamber_of_commerce' => 'Chamber of Commerce',
    'chamber_of_commerce_placeholder' => 'Registration Chamber of Commerce',
    'incorporation_date' => 'Incorporation Date',
    'incorporation_date_placeholder' => 'Date of organization incorporation',
    'share_capital' => 'Share Capital',
    'share_capital_placeholder' => 'Share capital in euros',

    // FORM FIELDS - OPERATIONAL
    'business_sector' => 'Business Sector',
    'business_sectors' => [
        'technology' => 'Technology & IT',
        'manufacturing' => 'Manufacturing',
        'services' => 'Services',
        'retail' => 'Retail',
        'wholesale' => 'Wholesale',
        'construction' => 'Construction',
        'agriculture' => 'Agriculture',
        'food_beverage' => 'Food & Beverage',
        'fashion' => 'Fashion & Apparel',
        'tourism' => 'Tourism & Hospitality',
        'healthcare' => 'Healthcare',
        'education' => 'Education',
        'finance' => 'Finance & Insurance',
        'transport' => 'Transport & Logistics',
        'energy' => 'Energy & Utilities',
        'creative' => 'Creative Industries',
        'environmental' => 'Environment & Sustainability',
        'research' => 'Research & Development',
        'other' => 'Other',
    ],
    'primary_activity' => 'Primary Activity',
    'primary_activity_placeholder' => 'Describe the main activity of the organization',
    'employee_count' => 'Number of Employees',
    'employee_ranges' => [
        '1' => '1 employee',
        '2-9' => '2-9 employees',
        '10-49' => '10-49 employees',
        '50-249' => '50-249 employees',
        '250-999' => '250-999 employees',
        '1000+' => '1000+ employees',
    ],
    'annual_revenue' => 'Annual Revenue',
    'revenue_ranges' => [
        'under_100k' => 'Under €100,000',
        '100k_500k' => '€100,000 - €500,000',
        '500k_2m' => '€500,000 - €2,000,000',
        '2m_10m' => '€2,000,000 - €10,000,000',
        '10m_50m' => '€10,000,000 - €50,000,000',
        'over_50m' => 'Over €50,000,000',
    ],

    // FORM FIELDS - CONTACT INFORMATION
    'headquarters_address' => 'Registered Office',
    'headquarters_street' => 'Registered Office Address',
    'headquarters_street_placeholder' => 'Street, building number',
    'headquarters_city' => 'City',
    'headquarters_postal_code' => 'Postal Code',
    'headquarters_province' => 'Province',
    'headquarters_country' => 'Country',
    'operational_address' => 'Operational Headquarters',
    'same_as_headquarters' => 'Same as registered office',
    'operational_street' => 'Operational Address',
    'phone_main' => 'Main Phone',
    'phone_main_placeholder' => '+39 06 1234567',
    'phone_secondary' => 'Secondary Phone',
    'fax' => 'Fax',
    'email_general' => 'General Email',
    'email_general_placeholder' => 'info@company.com',
    'email_admin' => 'Admin Email',
    'email_admin_placeholder' => 'admin@company.com',
    'pec' => 'PEC (Certified Email)',
    'pec_placeholder' => 'company@pec.it',
    'website' => 'Website',
    'website_placeholder' => 'https://www.company.com',

    // FORM FIELDS - SUSTAINABILITY & EPP
    'sustainability_commitment' => 'Sustainability Commitment',
    'sustainability_commitment_desc' => 'Describe your organization’s environmental sustainability commitment',
    'environmental_certifications' => 'Environmental Certifications',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Environmental Management System',
        'emas' => 'EMAS - Eco-Management and Audit Scheme',
        'carbon_neutral' => 'Carbon Neutral Certification',
        'leed' => 'LEED - Leadership in Energy and Environmental Design',
        'ecolabel' => 'EU Ecolabel',
        'fsc' => 'FSC - Forest Stewardship Council',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'B-Corp Certification',
        'organic' => 'Organic Certification',
        'fair_trade' => 'Fair Trade Certification',
        'other' => 'Other Certifications',
    ],
    'epp_entity_type' => 'EPP Entity Type',
    'epp_entity_types' => [
        'environmental_ngo' => 'Environmental NGO',
        'research_institute' => 'Research Institute',
        'green_tech_company' => 'Green Tech Company',
        'renewable_energy' => 'Renewable Energy',
        'waste_management' => 'Waste Management',
        'conservation_org' => 'Conservation Organization',
        'sustainable_agriculture' => 'Sustainable Agriculture',
        'environmental_consulting' => 'Environmental Consulting',
        'carbon_offset' => 'Carbon Offset',
        'biodiversity_protection' => 'Biodiversity Protection',
    ],
    'epp_certification_level' => 'EPP Certification Level',
    'epp_levels' => [
        'bronze' => 'Bronze - Basic Commitment',
        'silver' => 'Silver - Medium Commitment',
        'gold' => 'Gold - Advanced Commitment',
        'platinum' => 'Platinum - Excellent Commitment',
    ],
    'sustainability_projects' => 'Sustainability Projects',
    'sustainability_projects_placeholder' => 'Describe your organization’s main environmental projects',

    // ACTIONS AND BUTTONS
    'save_organization' => 'Save Organization Data',
    'verify_legal_data' => 'Verify Legal Data',
    'upload_certificate' => 'Upload Certificate',
    'request_epp_verification' => 'Request EPP Verification',
    'export_organization_data' => 'Export Organization Data',
    'validate_vat' => 'Validate VAT Number',
    'check_chamber_registration' => 'Check Chamber of Commerce Registration',

    // SUCCESS AND ERROR MESSAGES
    'update_success' => 'Organization data updated successfully',
    'verification_reset_warning' => 'Critical data has been modified, verification has been reset.',
    'data_not_found' => 'Organization data not found. Please contact support.',
    'role_not_allowed' => 'You do not have permission to access organization data.',
    'organization_saved' => 'Organization data saved successfully',
    'organization_error' => 'Error saving organization data',
    'legal_verification_success' => 'Legal data successfully verified',
    'legal_verification_error' => 'Error verifying legal data',
    'vat_verified' => 'VAT number successfully verified',
    'chamber_verified' => 'Chamber of Commerce registration verified',
    'epp_verification_requested' => 'EPP verification request sent successfully',
    'certificate_uploaded' => 'Certificate uploaded successfully',

    // VALIDATION MESSAGES
    'validation' => [
        'legal_name_required' => 'Legal name is required',
        'legal_form_required' => 'Legal form is required',
        'vat_number_invalid' => 'VAT number is not valid',
        'tax_code_invalid' => 'Tax code is not valid',
        'incorporation_date_valid' => 'Incorporation date must be valid',
        'share_capital_numeric' => 'Share capital must be a number',
        'employee_count_required' => 'Number of employees is required',
        'business_sector_required' => 'Business sector is required',
        'headquarters_address_required' => 'Registered office address is required',
        'phone_main_required' => 'Main phone is required',
        'email_general_required' => 'General email is required',
        'email_valid' => 'Email address must be valid',
        'website_url' => 'Website must be a valid URL',
        'pec_email' => 'PEC must be a valid email address',
    ],

    // HELP AND DESCRIPTIONS
    'help' => [
        'legal_name' => 'Full name of the organization as legally registered',
        'trade_name' => 'Trade or brand name used in operations',
        'vat_number' => 'VAT number for business transactions and invoicing',
        'rea_number' => 'Chamber of Commerce registration number',
        'share_capital' => 'Paid-up share capital of the organization',
        'epp_entity' => 'EPP entities can allocate environmental points on the platform',
        'sustainability_projects' => 'Projects demonstrating the organization’s environmental commitment',
        'certifications' => 'Environmental certifications attesting to sustainable practices',
    ],

    // PRIVACY AND COMPLIANCE
    'privacy' => [
        'data_usage' => 'Organization data is used for:',
        'usage_verification' => 'Company identity verification',
        'usage_compliance' => 'Tax and legal compliance',
        'usage_epp' => 'EPP system management and point allocation',
        'usage_marketplace' => 'Operations in the FlorenceEGI marketplace',
        'data_sharing' => 'Data may be shared with:',
        'sharing_authorities' => 'Tax and supervisory authorities',
        'sharing_partners' => 'Authorized technology partners',
        'sharing_verification' => 'Certification bodies',
        'retention_period' => 'Data retained for 10 years after relationship termination',
        'gdpr_rights' => 'The organization has the right to access, rectify, or delete data',
    ],
];
