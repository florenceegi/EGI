<?php

/**
 * @Oracode Translation File: Organization Data Management - Italian
 * 🎯 Purpose: Complete Italian translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with Italian base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\It
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // ===================================================================
    // PAGE TITLES AND HEADERS
    // ===================================================================
    'management_title' => 'Dati Organizzazione',
    'management_subtitle' => 'Gestisci i dati della tua organizzazione o azienda',
    'company_title' => 'Informazioni Aziendali',
    'company_subtitle' => 'Dati legali e operativi dell\'azienda',
    'contacts_title' => 'Contatti Aziendali',
    'contacts_subtitle' => 'Referenti e informazioni di contatto',
    'certifications_title' => 'Certificazioni',
    'certifications_subtitle' => 'Certificazioni ambientali e di qualità',

    // ===================================================================
    // ORGANIZATION TYPES
    // ===================================================================
    'organization_types' => [
        'corporation' => 'Società di Capitali',
        'partnership' => 'Società di Persone',
        'sole_proprietorship' => 'Ditta Individuale',
        'cooperative' => 'Cooperativa',
        'non_profit' => 'Organizzazione Non Profit',
        'foundation' => 'Fondazione',
        'association' => 'Associazione',
        'government' => 'Ente Pubblico',
        'educational' => 'Istituto Educativo',
        'research' => 'Ente di Ricerca',
        'startup' => 'Startup Innovativa',
        'other' => 'Altro',
    ],

    'legal_forms' => [
        'srl' => 'S.r.l. - Società a Responsabilità Limitata',
        'spa' => 'S.p.A. - Società per Azioni',
        'srls' => 'S.r.l.s. - Società a Responsabilità Limitata Semplificata',
        'snc' => 'S.n.c. - Società in Nome Collettivo',
        'sas' => 'S.a.s. - Società in Accomandita Semplice',
        'ditta_individuale' => 'Ditta Individuale',
        'cooperativa' => 'Società Cooperativa',
        'onlus' => 'ONLUS - Organizzazione Non Lucrativa di Utilità Sociale',
        'aps' => 'APS - Associazione di Promozione Sociale',
        'ets' => 'ETS - Ente del Terzo Settore',
        'fondazione' => 'Fondazione',
        'ente_pubblico' => 'Ente Pubblico',
    ],

    // ===================================================================
    // FORM SECTIONS
    // ===================================================================
    'legal_information' => 'Informazioni Legali',
    'legal_information_desc' => 'Dati legali e di registrazione dell\'organizzazione',
    'operational_information' => 'Informazioni Operative',
    'operational_information_desc' => 'Dati operativi e di business',
    'contact_information' => 'Informazioni di Contatto',
    'contact_information_desc' => 'Recapiti e referenti aziendali',
    'sustainability_info' => 'Informazioni Sostenibilità',
    'sustainability_info_desc' => 'Certificazioni ambientali e pratiche sostenibili',
    'epp_information' => 'Informazioni EPP',
    'epp_information_desc' => 'Dati specifici per entità EPP (Environmental Protection Points)',

    // ===================================================================
    // FORM FIELDS - LEGAL INFORMATION
    // ===================================================================
    'legal_name' => 'Ragione Sociale',
    'legal_name_placeholder' => 'Nome legale completo dell\'organizzazione',
    'trade_name' => 'Nome Commerciale',
    'trade_name_placeholder' => 'Nome commerciale o brand (se diverso)',
    'legal_form' => 'Forma Giuridica',
    'legal_form_placeholder' => 'Seleziona la forma giuridica',
    'vat_number' => 'Partita IVA',
    'vat_number_placeholder' => 'IT12345678901',
    'tax_code' => 'Codice Fiscale',
    'tax_code_placeholder' => 'Codice fiscale dell\'organizzazione',
    'registration_number' => 'Numero REA',
    'registration_number_placeholder' => 'Numero Registro Imprese',
    'chamber_of_commerce' => 'Camera di Commercio',
    'chamber_of_commerce_placeholder' => 'Camera di Commercio di registrazione',
    'incorporation_date' => 'Data Costituzione',
    'incorporation_date_placeholder' => 'Data di costituzione dell\'organizzazione',
    'share_capital' => 'Capitale Sociale',
    'share_capital_placeholder' => 'Capitale sociale in euro',

    // ===================================================================
    // FORM FIELDS - OPERATIONAL
    // ===================================================================
    'business_sector' => 'Settore di Attività',
    'business_sectors' => [
        'technology' => 'Tecnologia e IT',
        'manufacturing' => 'Manifatturiero',
        'services' => 'Servizi',
        'retail' => 'Commercio al Dettaglio',
        'wholesale' => 'Commercio all\'Ingrosso',
        'construction' => 'Costruzioni',
        'agriculture' => 'Agricoltura',
        'food_beverage' => 'Alimentare e Bevande',
        'fashion' => 'Moda e Abbigliamento',
        'tourism' => 'Turismo e Hospitality',
        'healthcare' => 'Sanità',
        'education' => 'Educazione',
        'finance' => 'Finanza e Assicurazioni',
        'transport' => 'Trasporti e Logistica',
        'energy' => 'Energia e Utilities',
        'creative' => 'Industrie Creative',
        'environmental' => 'Ambiente e Sostenibilità',
        'research' => 'Ricerca e Sviluppo',
        'other' => 'Altro',
    ],
    'primary_activity' => 'Attività Principale',
    'primary_activity_placeholder' => 'Descrivi l\'attività principale dell\'organizzazione',
    'employee_count' => 'Numero Dipendenti',
    'employee_ranges' => [
        '1' => '1 dipendente',
        '2-9' => '2-9 dipendenti',
        '10-49' => '10-49 dipendenti',
        '50-249' => '50-249 dipendenti',
        '250-999' => '250-999 dipendenti',
        '1000+' => '1000+ dipendenti',
    ],
    'annual_revenue' => 'Fatturato Annuo',
    'revenue_ranges' => [
        'under_100k' => 'Sotto 100.000 €',
        '100k_500k' => '100.000 - 500.000 €',
        '500k_2m' => '500.000 - 2.000.000 €',
        '2m_10m' => '2.000.000 - 10.000.000 €',
        '10m_50m' => '10.000.000 - 50.000.000 €',
        'over_50m' => 'Oltre 50.000.000 €',
    ],

    // ===================================================================
    // FORM FIELDS - CONTACT INFORMATION
    // ===================================================================
    'headquarters_address' => 'Sede Legale',
    'headquarters_street' => 'Indirizzo Sede Legale',
    'headquarters_street_placeholder' => 'Via, numero civico',
    'headquarters_city' => 'Città',
    'headquarters_postal_code' => 'Codice Postale',
    'headquarters_province' => 'Provincia',
    'headquarters_country' => 'Paese',
    'operational_address' => 'Sede Operativa',
    'same_as_headquarters' => 'Uguale alla sede legale',
    'operational_street' => 'Indirizzo Sede Operativa',
    'phone_main' => 'Telefono Principale',
    'phone_main_placeholder' => '+39 06 1234567',
    'phone_secondary' => 'Telefono Secondario',
    'fax' => 'Fax',
    'email_general' => 'Email Generale',
    'email_general_placeholder' => 'info@azienda.com',
    'email_admin' => 'Email Amministrativa',
    'email_admin_placeholder' => 'admin@azienda.com',
    'pec' => 'PEC (Posta Certificata)',
    'pec_placeholder' => 'azienda@pec.it',
    'website' => 'Sito Web',
    'website_placeholder' => 'https://www.azienda.com',

    // ===================================================================
    // FORM FIELDS - SUSTAINABILITY & EPP
    // ===================================================================
    'sustainability_commitment' => 'Impegno per la Sostenibilità',
    'sustainability_commitment_desc' => 'Descrivi l\'impegno dell\'organizzazione per la sostenibilità ambientale',
    'environmental_certifications' => 'Certificazioni Ambientali',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Sistema di Gestione Ambientale',
        'emas' => 'EMAS - Eco-Management and Audit Scheme',
        'carbon_neutral' => 'Carbon Neutral Certification',
        'leed' => 'LEED - Leadership in Energy and Environmental Design',
        'ecolabel' => 'EU Ecolabel',
        'fsc' => 'FSC - Forest Stewardship Council',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'B-Corp Certification',
        'organic' => 'Certificazione Biologica',
        'fair_trade' => 'Fair Trade Certification',
        'other' => 'Altre Certificazioni',
    ],
    'epp_entity_type' => 'Tipo Entità EPP',
    'epp_entity_types' => [
        'environmental_ngo' => 'ONG Ambientale',
        'research_institute' => 'Istituto di Ricerca',
        'green_tech_company' => 'Azienda Green Tech',
        'renewable_energy' => 'Energia Rinnovabile',
        'waste_management' => 'Gestione Rifiuti',
        'conservation_org' => 'Organizzazione di Conservazione',
        'sustainable_agriculture' => 'Agricoltura Sostenibile',
        'environmental_consulting' => 'Consulenza Ambientale',
        'carbon_offset' => 'Compensazione Carbonio',
        'biodiversity_protection' => 'Protezione Biodiversità',
    ],
    'epp_certification_level' => 'Livello Certificazione EPP',
    'epp_levels' => [
        'bronze' => 'Bronzo - Impegno di Base',
        'silver' => 'Argento - Impegno Medio',
        'gold' => 'Oro - Impegno Avanzato',
        'platinum' => 'Platino - Impegno Eccellente',
    ],
    'sustainability_projects' => 'Progetti di Sostenibilità',
    'sustainability_projects_placeholder' => 'Descrivi i principali progetti ambientali dell\'organizzazione',

    // ===================================================================
    // ACTIONS AND BUTTONS
    // ===================================================================
    'save_organization' => 'Salva Dati Organizzazione',
    'verify_legal_data' => 'Verifica Dati Legali',
    'upload_certificate' => 'Carica Certificato',
    'request_epp_verification' => 'Richiedi Verifica EPP',
    'export_organization_data' => 'Esporta Dati Organizzazione',
    'validate_vat' => 'Valida Partita IVA',
    'check_chamber_registration' => 'Verifica Registrazione Camera Commercio',

    // ===================================================================
    // SUCCESS AND ERROR MESSAGES
    // ===================================================================
    'update_success' => 'Dati organizzazione aggiornati con successo',
    'verification_reset_warning' => 'I dati critici sono stati modificati, la verifica è stata resettata.',
    'data_not_found' => 'Dati organizzazione non trovati. Contatta il supporto.',
    'role_not_allowed' => 'Non hai i permessi per accedere ai dati organizzazione.',
    'organization_saved' => 'Dati organizzazione salvati con successo',
    'organization_error' => 'Errore durante il salvataggio dei dati organizzazione',
    'legal_verification_success' => 'Dati legali verificati correttamente',
    'legal_verification_error' => 'Errore nella verifica dei dati legali',
    'vat_verified' => 'Partita IVA verificata correttamente',
    'chamber_verified' => 'Registrazione Camera di Commercio verificata',
    'epp_verification_requested' => 'Richiesta verifica EPP inviata con successo',
    'certificate_uploaded' => 'Certificato caricato con successo',

    // ===================================================================
    // VALIDATION MESSAGES
    // ===================================================================
    'validation' => [
        'legal_name_required' => 'La ragione sociale è obbligatoria',
        'legal_form_required' => 'La forma giuridica è obbligatoria',
        'vat_number_invalid' => 'La partita IVA non è valida',
        'tax_code_invalid' => 'Il codice fiscale non è valido',
        'incorporation_date_valid' => 'La data di costituzione deve essere valida',
        'share_capital_numeric' => 'Il capitale sociale deve essere un numero',
        'employee_count_required' => 'Il numero di dipendenti è obbligatorio',
        'business_sector_required' => 'Il settore di attività è obbligatorio',
        'headquarters_address_required' => 'L\'indirizzo della sede legale è obbligatorio',
        'phone_main_required' => 'Il telefono principale è obbligatorio',
        'email_general_required' => 'L\'email generale è obbligatoria',
        'email_valid' => 'L\'indirizzo email deve essere valido',
        'website_url' => 'Il sito web deve essere un URL valido',
        'pec_email' => 'La PEC deve essere un indirizzo email valido',
    ],

    // ===================================================================
    // HELP AND DESCRIPTIONS
    // ===================================================================
    'help' => [
        'legal_name' => 'Nome completo dell\'organizzazione come registrato legalmente',
        'trade_name' => 'Nome commerciale o brand utilizzato nelle operazioni',
        'vat_number' => 'Partita IVA per operazioni commerciali e fatturazione',
        'rea_number' => 'Numero di iscrizione al Registro delle Imprese',
        'share_capital' => 'Capitale sociale versato dell\'organizzazione',
        'epp_entity' => 'Le entità EPP possono allocare punti ambientali nella piattaforma',
        'sustainability_projects' => 'Progetti che dimostrano l\'impegno ambientale dell\'organizzazione',
        'certifications' => 'Certificazioni ambientali che attestano le pratiche sostenibili',
    ],

    // ===================================================================
    // PRIVACY AND COMPLIANCE
    // ===================================================================
    'privacy' => [
        'data_usage' => 'I dati dell\'organizzazione sono utilizzati per:',
        'usage_verification' => 'Verifica dell\'identità aziendale',
        'usage_compliance' => 'Adempimenti fiscali e legali',
        'usage_epp' => 'Gestione sistema EPP e allocazione punti',
        'usage_marketplace' => 'Operazioni nel marketplace FlorenceEGI',
        'data_sharing' => 'I dati possono essere condivisi con:',
        'sharing_authorities' => 'Autorità fiscali e di controllo',
        'sharing_partners' => 'Partner tecnologici autorizzati',
        'sharing_verification' => 'Enti di certificazione',
        'retention_period' => 'I dati sono conservati per 10 anni dopo la cessazione del rapporto',
        'gdpr_rights' => 'L\'organizzazione ha diritto ad accesso, rettifica, cancellazione dei dati',
    ],
];
