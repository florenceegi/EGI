<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Legal Rights & Copyright)
 * @date 2025-10-21
 * @purpose Traduzioni italiane per diritti legali, diritto d'autore e diritto di seguito
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Legal Rights - Traduzioni Italiane
    |--------------------------------------------------------------------------
    |
    | Informazioni legali su diritto d'autore (L. 633/1941) e diritto di seguito
    | per documenti pubblici e UI
    |
    */

    // Titoli sezioni
    'section_title' => 'Diritti d\'Autore & Diritto di Seguito',
    'section_subtitle' => 'Normativa italiana ed europea: cosa spetta al Creator, cosa acquisisce l\'Owner',

    // Disclaimer
    'disclaimer_title' => 'Premessa Importante',
    'disclaimer_text' => 'Le informazioni seguenti sono fornite a scopo informativo e divulgativo. Non costituiscono consulenza legale. Per questioni specifiche, consultare un avvocato specializzato in diritto d\'autore.',
    'disclaimer_legal' => 'Le informazioni riportate sono fornite a scopo informativo generale e non costituiscono consulenza legale professionale. La normativa sul diritto d\'autore è complessa e soggetta a interpretazioni. Per questioni legali specifiche, si raccomanda di consultare un avvocato specializzato in proprietà intellettuale e diritto dell\'arte. FlorenceEGI non assume responsabilità per decisioni prese sulla base di queste informazioni.',

    // Diritti Creator
    'creator_rights_title' => 'Diritti del Creator (Sempre e Comunque)',
    'creator_rights_subtitle' => 'Il Creator conserva questi diritti anche dopo la vendita dell\'opera',

    'moral_rights_title' => 'Diritti Morali (Inalienabili)',
    'moral_rights_subtitle' => 'Legge 633/1941 Art. 20 - Mai cedibili, anche dopo la vendita',
    'moral_rights' => [
        'paternity' => 'Paternità: Diritto di essere sempre riconosciuto come autore dell\'opera',
        'integrity' => 'Integrità: Diritto di opporsi a modifiche, deformazioni o alterazioni che danneggino la reputazione',
        'attribution' => 'Attribuzione: L\'Owner deve sempre citare correttamente l\'artista',
        'owner_cannot' => 'L\'Owner NON può: rimuovere firma, alterare l\'opera, attribuirla ad altri',
    ],

    'economic_rights_title' => 'Diritti Patrimoniali (Copyright)',
    'economic_rights_subtitle' => 'Legge 633/1941 Art. 12-19 - Sfruttamento economico',
    'economic_rights' => [
        'reproduction' => 'Riproduzione: Solo il Creator può fare copie/stampe dell\'opera',
        'public_communication' => 'Comunicazione pubblica: Uso in pubblicità/TV/online richiede licenza Creator',
        'distribution' => 'Distribuzione: Vendere copie/merchandise richiede autorizzazione',
        'important_note' => 'IMPORTANTE: Comprare NFT ≠ Comprare copyright',
    ],

    // Diritti Owner
    'owner_rights_title' => 'Diritti dell\'Owner (Acquirente)',
    'owner_can_title' => 'Cosa PUÒ Fare l\'Owner',
    'owner_can' => [
        'possess' => 'Possedere fisicamente l\'opera',
        'display_private' => 'Esporre privatamente (casa/ufficio)',
        'resell' => 'Rivendere l\'opera (con royalty Creator)',
        'gift' => 'Donare o lasciare in eredità',
        'photograph' => 'Fotografare per documentazione personale',
        'display_public' => 'Esporre pubblicamente senza scopo di lucro (con attribuzione Creator)',
        'restoration' => 'Restauro conservativo (senza alterare)',
    ],

    'owner_cannot_title' => 'Cosa NON PUÒ Fare (Senza Consenso Creator)',
    'owner_cannot' => [
        'reproduce' => 'Riprodurre commercialmente (stampe, poster, merchandise)',
        'modify' => 'Modificare/alterare l\'opera originale',
        'advertise' => 'Usare in pubblicità/marketing senza licenza',
        'publish' => 'Pubblicare online per scopi commerciali',
        'derivative' => 'Creare opere derivative (remix, versioni)',
        'remove_credits' => 'Rimuovere firma/crediti dell\'artista',
        'mint_nft' => 'Emettere NFT aggiuntivi della stessa opera',
        'violation' => 'Violazione = Art. 171 LDA: Multe fino €15,493 + sequestro + risarcimento danni',
    ],

    // Comparazione Royalty
    'comparison_title' => 'Diritto di Seguito vs Royalty Piattaforma',
    'comparison_subtitle' => 'Due meccanismi distinti e cumulabili',

    'comparison_table' => [
        'aspect' => 'Aspetto',
        'platform_royalty' => 'Royalty Piattaforma (FlorenceEGI)',
        'legal_droit' => 'Diritto di Seguito (Legge)',

        'legal_basis' => 'Base giuridica',
        'legal_basis_platform' => 'Contratto smart contract',
        'legal_basis_law' => 'L. 633/1941 Art. 19bis',

        'min_threshold' => 'Soglia minima',
        'min_threshold_platform' => '€0 (tutte le vendite)',
        'min_threshold_law' => '€3,000',

        'percentage' => 'Percentuale',
        'percentage_platform' => '4.5% fisso',
        'percentage_law' => '4% → 0.25% (decrescente)',

        'sale_type' => 'Tipo vendite',
        'sale_type_platform' => 'P2P dirette (piattaforma)',
        'sale_type_law' => 'Tramite professionisti (gallerie/aste)',

        'management' => 'Chi gestisce',
        'management_platform' => 'Smart contract automatico',
        'management_law' => 'SIAE (manuale)',

        'cumulative' => 'Cumulabile',
        'cumulative_yes' => 'SÌ! Il Creator può ricevere ENTRAMBI',
    ],

    // Scenari vendita
    'scenarios_title' => 'Come Funziona su FlorenceEGI',

    'scenario_primary' => [
        'title' => 'Vendita Primaria (Mint) - EGI €1,000',
        'distribution' => 'Distribuzione ricavi:',
        'creator' => 'Creator: €650-680 (65-68%)',
        'epp' => 'EPP: €200 (20%)',
        'platform' => 'Piattaforma: €100 (10%)',
        'association' => 'Associazione: €20 (2%)',
        'droit_not_applicable' => 'Diritto di seguito NON applicabile',
        'droit_reason' => 'È la prima vendita, non una rivendita',
    ],

    'scenario_secondary_low' => [
        'title' => 'Rivendita Secondaria - EGI €1,000 (P2P su FlorenceEGI)',
        'distribution' => 'Distribuzione:',
        'seller' => 'Seller riceve: €930 (93%)',
        'creator_royalty' => 'Creator royalty: €45 (4.5%)',
        'epp' => 'EPP: €10 (1%)',
        'platform' => 'Piattaforma: €10 (1%)',
        'association' => 'Associazione: €5 (0.5%)',
        'droit_not_applicable' => 'Diritto seguito legale NON applicabile',
        'droit_reason' => 'Sotto soglia €3,000',
        'platform_royalty_note' => 'Ma Creator riceve comunque 4.5% (nostro contratto)',
    ],

    'scenario_secondary_high' => [
        'title' => 'Rivendita Secondaria - EGI €50,000 (tramite Galleria/Asta)',
        'fee_platform' => 'Fee FlorenceEGI:',
        'seller' => 'Seller: €46,500 (93%)',
        'creator' => 'Creator: €2,250 (4.5%)',
        'epp' => 'EPP: €500 (1%)',
        'platform' => 'Platform: €500 (1%)',
        'association' => 'Assoc: €250 (0.5%)',
        'droit_applicable' => 'Diritto seguito legale APPLICABILE',
        'droit_rate' => 'Aliquota: 4% (fascia 0-€50k)',
        'droit_amount' => 'Importo: €2,000',
        'droit_recipient' => 'Ricevuto: Creator (via SIAE)',
        'droit_separate' => 'Separato dalle fee piattaforma',
        'total_creator' => 'TOTALE Creator: €4,250 (8.5%)',
        'example' => 'Esempio: Vendita €50,000 tramite galleria → Creator riceve €2,250 (4.5% piattaforma) + €2,000 (4% diritto seguito) = €4,250 totali (8.5%)',
    ],

    // Normativa
    'legislation_title' => 'Normativa di Riferimento',

    'law_lda' => [
        'title' => 'Legge 633/1941 (Legge sul Diritto d\'Autore - LDA)',
        'art_12_19' => 'Art. 12-19: Diritti patrimoniali (riproduzione, comunicazione, distribuzione)',
        'art_20' => 'Art. 20: Diritti morali (paternità, integrità dell\'opera)',
        'art_19bis' => 'Art. 19bis: Diritto di seguito sulle rivendite',
        'art_25' => 'Art. 25: Durata protezione (vita autore + 70 anni)',
        'art_171' => 'Art. 171: Sanzioni per violazioni (multa €51-€15,493)',
    ],

    'law_dlgs' => [
        'title' => 'D.Lgs. 118/2006 (Recepimento Direttiva UE 2001/84/CE)',
        'art_3' => 'Art. 3: Aliquote diritto di seguito (4% fino €50k, poi decrescente)',
        'art_4' => 'Art. 4: Soglia minima €3,000 per applicazione',
        'art_5' => 'Art. 5: Massimo €12,500 per singola vendita',
        'art_8' => 'Art. 8: Gestione tramite SIAE (Società Italiana Autori ed Editori)',
    ],

    'law_cc' => [
        'title' => 'Codice Civile - Art. 2575-2583',
        'description' => 'Distinzione tra proprietà dell\'oggetto fisico (Owner) e diritti sull\'opera dell\'ingegno (Creator). L\'acquisto di un\'opera d\'arte trasferisce solo il possesso materiale, non il copyright.',
    ],

    // Contratto vendita
    'contract_title' => 'Cosa Include il Contratto di Vendita EGI',
    'owner_acquires' => 'L\'Owner ACQUISISCE:',
    'owner_acquires_list' => [
        'physical' => 'Proprietà fisica dell\'opera (oggetto materiale)',
        'nft' => 'NFT digitale (certificato blockchain)',
        'enjoyment' => 'Diritto di godimento privato',
        'resale' => 'Diritto di rivendita (con royalty Creator)',
        'possession' => 'Possesso esclusivo dell\'originale',
    ],

    'creator_retains' => 'Il Creator CONSERVA:',
    'creator_retains_list' => [
        'moral_rights' => 'Tutti i diritti morali (paternità, integrità)',
        'droit_suite' => 'Diritto di seguito (4%-0.25% su rivendite >€3k)',
        'platform_royalty' => 'Royalty piattaforma (4.5% sempre)',
        'reproduction' => 'Diritti di riproduzione (stampe, copie)',
        'copyright' => 'Copyright sull\'immagine dell\'opera',
        'digital_rights' => 'Diritti digitali (uso online commerciale)',
    ],

    // Impegno FlorenceEGI
    'commitment_title' => 'Impegno FlorenceEGI',
    'commitment_subtitle' => 'FlorenceEGI si impegna a rispettare e tutelare i diritti degli artisti previsti dalla legge italiana ed europea:',
    'commitment_list' => [
        'attribution' => 'Garantiamo attribuzione corretta in tutti gli EGI (paternità)',
        'immutability' => 'Blocchiamo modifiche post-mint (integrità blockchain)',
        'royalties' => 'Royalty automatiche 4.5% su tutte le rivendite (anche sotto €3k)',
        'siae' => 'Collaboriamo con SIAE per gestione diritto di seguito su vendite >€3k tramite professionisti',
        'enforcement' => 'Smart contract impedisce elusione royalty (trustless enforcement)',
    ],
];










