<?php

/**
 * @Oracode Translation File: Organization Data Management - German
 * 🎯 Purpose: Complete German translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with German base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\De
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // SEITENTITEL UND KOPFZEILEN
    'management_title' => 'Organisationsdaten',
    'management_subtitle' => 'Verwalte die Daten deines Unternehmens oder deiner Organisation',
    'company_title' => 'Unternehmensdaten',
    'company_subtitle' => 'Juristische und operative Details',
    'contacts_title' => 'Unternehmenskontakte',
    'contacts_subtitle' => 'Kontakte und Referenzen',
    'certifications_title' => 'Zertifikate',
    'certifications_subtitle' => 'Umwelt- und Qualitätszertifikate',

    // ORGANISATIONSTYPEN
    'organization_types' => [
        'corporation' => 'Kapitalgesellschaft',
        'partnership' => 'Personengesellschaft',
        'sole_proprietorship' => 'Einzelunternehmen',
        'cooperative' => 'Genossenschaft',
        'non_profit' => 'Gemeinnützige Organisation',
        'foundation' => 'Stiftung',
        'association' => 'Verein',
        'government' => 'Öffentliche Einrichtung',
        'educational' => 'Bildungseinrichtung',
        'research' => 'Forschungsinstitut',
        'startup' => 'Innovatives Start-up',
        'other' => 'Andere',
    ],

    'legal_forms' => [
        'srl' => 'GmbH - Gesellschaft mit beschränkter Haftung',
        'spa' => 'AG - Aktiengesellschaft',
        'srls' => 'Einfache GmbH',
        'snc' => 'OHG - Offene Handelsgesellschaft',
        'sas' => 'KG - Kommanditgesellschaft',
        'ditta_individuale' => 'Einzelunternehmen',
        'cooperativa' => 'Genossenschaft',
        'onlus' => 'Gemeinnützige Organisation (ONLUS)',
        'aps' => 'Verein zur sozialen Förderung',
        'ets' => 'Organisation des Dritten Sektors',
        'fondazione' => 'Stiftung',
        'ente_pubblico' => 'Öffentliche Einrichtung',
    ],

    // FORMULARABSCHNITTE
    'legal_information' => 'Juristische Informationen',
    'legal_information_desc' => 'Juristische und registrierte Angaben der Organisation',
    'operational_information' => 'Betriebliche Informationen',
    'operational_information_desc' => 'Betriebs- und Tätigkeitsdaten',
    'contact_information' => 'Kontaktinformationen',
    'contact_information_desc' => 'Kontakte und Referenzen des Unternehmens',
    'sustainability_info' => 'Nachhaltigkeitsinformationen',
    'sustainability_info_desc' => 'Umweltzertifikate und nachhaltige Praktiken',
    'epp_information' => 'EPP-Informationen',
    'epp_information_desc' => 'Spezielle Daten für EPP-Organisationen (Environmental Protection Points)',

    // FORMULARFELDER – JURISTISCH
    'legal_name' => 'Juristischer Name',
    'legal_name_placeholder' => 'Vollständiger juristischer Name der Organisation',
    'trade_name' => 'Handelsname',
    'trade_name_placeholder' => 'Handelsname oder Marke (falls abweichend)',
    'legal_form' => 'Rechtsform',
    'legal_form_placeholder' => 'Rechtsform auswählen',
    'vat_number' => 'USt-IdNr.',
    'vat_number_placeholder' => 'DE123456789',
    'tax_code' => 'Steuernummer',
    'tax_code_placeholder' => 'Steuernummer der Organisation',
    'registration_number' => 'Handelsregisternummer',
    'registration_number_placeholder' => 'Nummer im Handelsregister',
    'chamber_of_commerce' => 'Industrie- und Handelskammer',
    'chamber_of_commerce_placeholder' => 'Registrierende IHK',
    'incorporation_date' => 'Gründungsdatum',
    'incorporation_date_placeholder' => 'Datum der Unternehmensgründung',
    'share_capital' => 'Stammkapital',
    'share_capital_placeholder' => 'Stammkapital in Euro',

    // FORMULARFELDER – OPERATIV
    'business_sector' => 'Branche',
    'business_sectors' => [
        'technology' => 'Technologie & IT',
        'manufacturing' => 'Industrie',
        'services' => 'Dienstleistungen',
        'retail' => 'Einzelhandel',
        'wholesale' => 'Großhandel',
        'construction' => 'Bauwesen',
        'agriculture' => 'Landwirtschaft',
        'food_beverage' => 'Lebensmittel & Getränke',
        'fashion' => 'Mode & Bekleidung',
        'tourism' => 'Tourismus & Gastgewerbe',
        'healthcare' => 'Gesundheitswesen',
        'education' => 'Bildung',
        'finance' => 'Finanzen & Versicherungen',
        'transport' => 'Transport & Logistik',
        'energy' => 'Energie & Versorgung',
        'creative' => 'Kreativwirtschaft',
        'environmental' => 'Umwelt & Nachhaltigkeit',
        'research' => 'Forschung & Entwicklung',
        'other' => 'Andere',
    ],
    'primary_activity' => 'Haupttätigkeit',
    'primary_activity_placeholder' => 'Beschreibe die Haupttätigkeit der Organisation',
    'employee_count' => 'Anzahl der Mitarbeitenden',
    'employee_ranges' => [
        '1' => '1 Mitarbeiter',
        '2-9' => '2-9 Mitarbeitende',
        '10-49' => '10-49 Mitarbeitende',
        '50-249' => '50-249 Mitarbeitende',
        '250-999' => '250-999 Mitarbeitende',
        '1000+' => 'Mehr als 1000 Mitarbeitende',
    ],
    'annual_revenue' => 'Jahresumsatz',
    'revenue_ranges' => [
        'under_100k' => 'Unter 100.000 €',
        '100k_500k' => '100.000 € - 500.000 €',
        '500k_2m' => '500.000 € - 2.000.000 €',
        '2m_10m' => '2.000.000 € - 10.000.000 €',
        '10m_50m' => '10.000.000 € - 50.000.000 €',
        'over_50m' => 'Mehr als 50.000.000 €',
    ],

    // FORMULARFELDER – KONTAKT
    'headquarters_address' => 'Firmensitz',
    'headquarters_street' => 'Adresse des Firmensitzes',
    'headquarters_street_placeholder' => 'Straße, Hausnummer',
    'headquarters_city' => 'Stadt',
    'headquarters_postal_code' => 'PLZ',
    'headquarters_province' => 'Bundesland',
    'headquarters_country' => 'Land',
    'operational_address' => 'Betriebsstätte',
    'same_as_headquarters' => 'Wie Firmensitz',
    'operational_street' => 'Betriebsadresse',
    'phone_main' => 'Haupttelefon',
    'phone_main_placeholder' => '+49 30 1234567',
    'phone_secondary' => 'Zweites Telefon',
    'fax' => 'Fax',
    'email_general' => 'Allgemeine E-Mail',
    'email_general_placeholder' => 'info@firma.de',
    'email_admin' => 'Administrative E-Mail',
    'email_admin_placeholder' => 'admin@firma.de',
    'pec' => 'PEC (Zertifizierte E-Mail)',
    'pec_placeholder' => 'firma@pec.de',
    'website' => 'Webseite',
    'website_placeholder' => 'https://www.firma.de',

    // FELDER – NACHHALTIGKEIT & EPP
    'sustainability_commitment' => 'Nachhaltigkeits-Engagement',
    'sustainability_commitment_desc' => 'Beschreibe das Umwelt-Engagement deiner Organisation',
    'environmental_certifications' => 'Umweltzertifikate',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Umweltmanagementsystem',
        'emas' => 'EMAS - Umweltmanagement und Audit',
        'carbon_neutral' => 'CO2-Neutral-Zertifizierung',
        'leed' => 'LEED - Energie und Umwelt Design',
        'ecolabel' => 'EU Ecolabel',
        'fsc' => 'FSC - Forest Stewardship Council',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'B-Corp-Zertifizierung',
        'organic' => 'Bio-Zertifizierung',
        'fair_trade' => 'Fairtrade-Zertifikat',
        'other' => 'Andere Zertifikate',
    ],
    'epp_entity_type' => 'EPP-Organisationstyp',
    'epp_entity_types' => [
        'environmental_ngo' => 'Umwelt-NGO',
        'research_institute' => 'Forschungsinstitut',
        'green_tech_company' => 'Green-Tech-Unternehmen',
        'renewable_energy' => 'Erneuerbare Energien',
        'waste_management' => 'Abfallwirtschaft',
        'conservation_org' => 'Naturschutzorganisation',
        'sustainable_agriculture' => 'Nachhaltige Landwirtschaft',
        'environmental_consulting' => 'Umweltberatung',
        'carbon_offset' => 'CO2-Kompensation',
        'biodiversity_protection' => 'Schutz der Biodiversität',
    ],
    'epp_certification_level' => 'EPP-Zertifizierungsstufe',
    'epp_levels' => [
        'bronze' => 'Bronze - Grundlegendes Engagement',
        'silver' => 'Silber - Mittleres Engagement',
        'gold' => 'Gold - Fortgeschrittenes Engagement',
        'platinum' => 'Platin - Herausragendes Engagement',
    ],
    'sustainability_projects' => 'Nachhaltigkeitsprojekte',
    'sustainability_projects_placeholder' => 'Beschreibe die wichtigsten Umweltprojekte',

    // AKTIONEN UND BUTTONS
    'save_organization' => 'Organisationsdaten Speichern',
    'verify_legal_data' => 'Juristische Daten Prüfen',
    'upload_certificate' => 'Zertifikat Hochladen',
    'request_epp_verification' => 'EPP-Verifizierung Anfordern',
    'export_organization_data' => 'Organisationsdaten Exportieren',
    'validate_vat' => 'USt-IdNr. Prüfen',
    'check_chamber_registration' => 'Handelsregister prüfen',

    // ERFOLGS- UND FEHLERMELDUNGEN
    'update_success' => 'Organisationsdaten erfolgreich aktualisiert',
    'verification_reset_warning' => 'Kritische Daten wurden geändert, die Verifizierung wurde zurückgesetzt.',
    'data_not_found' => 'Organisationsdaten nicht gefunden. Bitte kontaktieren Sie den Support.',
    'role_not_allowed' => 'Sie haben keine Berechtigung, auf Organisationsdaten zuzugreifen.',
    'organization_saved' => 'Organisationsdaten erfolgreich gespeichert',
    'organization_error' => 'Fehler beim Speichern der Organisationsdaten',
    'legal_verification_success' => 'Juristische Daten erfolgreich geprüft',
    'legal_verification_error' => 'Fehler bei der Prüfung der juristischen Daten',
    'vat_verified' => 'USt-IdNr. erfolgreich verifiziert',
    'chamber_verified' => 'Handelsregistereintrag bestätigt',
    'epp_verification_requested' => 'EPP-Verifizierungsanfrage erfolgreich gesendet',
    'certificate_uploaded' => 'Zertifikat erfolgreich hochgeladen',

    // VALIDIERUNGSMELDUNGEN
    'validation' => [
        'legal_name_required' => 'Juristischer Name ist erforderlich',
        'legal_form_required' => 'Rechtsform ist erforderlich',
        'vat_number_invalid' => 'USt-IdNr. ist ungültig',
        'tax_code_invalid' => 'Steuernummer ist ungültig',
        'incorporation_date_valid' => 'Gründungsdatum muss gültig sein',
        'share_capital_numeric' => 'Stammkapital muss eine Zahl sein',
        'employee_count_required' => 'Mitarbeiteranzahl ist erforderlich',
        'business_sector_required' => 'Branche ist erforderlich',
        'headquarters_address_required' => 'Adresse des Firmensitzes ist erforderlich',
        'phone_main_required' => 'Haupttelefon ist erforderlich',
        'email_general_required' => 'Allgemeine E-Mail ist erforderlich',
        'email_valid' => 'E-Mail muss gültig sein',
        'website_url' => 'Website muss eine gültige URL sein',
        'pec_email' => 'PEC muss eine gültige E-Mail-Adresse sein',
    ],

    // HILFE UND BESCHREIBUNGEN
    'help' => [
        'legal_name' => 'Vollständiger Name der Organisation gemäß Eintragung',
        'trade_name' => 'Handelsname oder Marke im operativen Geschäft',
        'vat_number' => 'USt-IdNr. für Transaktionen und Rechnungsstellung',
        'rea_number' => 'Handelsregisternummer',
        'share_capital' => 'Eingezahltes Stammkapital',
        'epp_entity' => 'EPP-Organisationen können Umweltpunkte auf der Plattform vergeben',
        'sustainability_projects' => 'Projekte, die das Umweltengagement belegen',
        'certifications' => 'Zertifikate für nachhaltige Praktiken',
    ],

    // DATENSCHUTZ UND KONFORMITÄT
    'privacy' => [
        'data_usage' => 'Organisationsdaten werden verwendet für:',
        'usage_verification' => 'Unternehmensidentitätsprüfung',
        'usage_compliance' => 'Steuer- und Rechtskonformität',
        'usage_epp' => 'EPP-Verwaltung und Punktevergabe',
        'usage_marketplace' => 'Betrieb auf dem FlorenceEGI-Marktplatz',
        'data_sharing' => 'Daten können geteilt werden mit:',
        'sharing_authorities' => 'Steuer- und Aufsichtsbehörden',
        'sharing_partners' => 'Autorisierte Technologiepartner',
        'sharing_verification' => 'Zertifizierungsstellen',
        'retention_period' => 'Daten werden 10 Jahre nach Beziehungsende aufbewahrt',
        'gdpr_rights' => 'Die Organisation hat das Recht auf Zugang, Berichtigung und Löschung der Daten',
    ],
];
