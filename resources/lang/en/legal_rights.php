<?php

/**
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Legal Rights & Copyright)
 * @date 2025-10-21
 * @purpose English translations for legal rights, copyright and resale rights
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Legal Rights - English Translations
    |--------------------------------------------------------------------------
    |
    | Legal information on copyright (Italian Law 633/1941) and resale rights
    | for public documents and UI
    |
    */

    // Section titles
    'section_title' => 'Copyright & Resale Rights',
    'section_subtitle' => 'Italian and European legislation: what belongs to the Creator, what the Owner acquires',

    // Disclaimer
    'disclaimer_title' => 'Important Notice',
    'disclaimer_text' => 'The following information is provided for informational and educational purposes only. It does not constitute legal advice. For specific matters, consult a lawyer specialized in copyright law.',
    'disclaimer_legal' => 'The information provided is for general informational purposes only and does not constitute professional legal advice. Copyright law is complex and subject to interpretation. For specific legal matters, we recommend consulting a lawyer specialized in intellectual property and art law. FlorenceEGI assumes no responsibility for decisions made based on this information.',

    // Creator Rights
    'creator_rights_title' => 'Creator Rights (Always and Forever)',
    'creator_rights_subtitle' => 'The Creator retains these rights even after selling the artwork',

    'moral_rights_title' => 'Moral Rights (Inalienable)',
    'moral_rights_subtitle' => 'Italian Law 633/1941 Art. 20 - Never transferable, even after sale',
    'moral_rights' => [
        'paternity' => 'Paternity: Right to always be recognized as the author of the work',
        'integrity' => 'Integrity: Right to oppose modifications, deformations or alterations that damage reputation',
        'attribution' => 'Attribution: The Owner must always correctly cite the artist',
        'owner_cannot' => 'The Owner CANNOT: remove signature, alter the work, attribute it to others',
    ],

    'economic_rights_title' => 'Economic Rights (Copyright)',
    'economic_rights_subtitle' => 'Italian Law 633/1941 Art. 12-19 - Economic exploitation',
    'economic_rights' => [
        'reproduction' => 'Reproduction: Only the Creator can make copies/prints of the work',
        'public_communication' => 'Public communication: Use in advertising/TV/online requires Creator license',
        'distribution' => 'Distribution: Selling copies/merchandise requires authorization',
        'important_note' => 'IMPORTANT: Buying NFT ≠ Buying copyright',
    ],

    // Owner Rights
    'owner_rights_title' => 'Owner Rights (Buyer)',
    'owner_can_title' => 'What the Owner CAN Do',
    'owner_can' => [
        'possess' => 'Physically possess the artwork',
        'display_private' => 'Display privately (home/office)',
        'resell' => 'Resell the artwork (with Creator royalty)',
        'gift' => 'Gift or bequeath',
        'photograph' => 'Photograph for personal documentation',
        'display_public' => 'Display publicly for non-commercial purposes (with Creator attribution)',
        'restoration' => 'Conservative restoration (without altering)',
    ],

    'owner_cannot_title' => 'What the Owner CANNOT Do (Without Creator Consent)',
    'owner_cannot' => [
        'reproduce' => 'Commercially reproduce (prints, posters, merchandise)',
        'modify' => 'Modify/alter the original work',
        'advertise' => 'Use in advertising/marketing without license',
        'publish' => 'Publish online for commercial purposes',
        'derivative' => 'Create derivative works (remixes, versions)',
        'remove_credits' => 'Remove artist\'s signature/credits',
        'mint_nft' => 'Mint additional NFTs of the same work',
        'violation' => 'Violation = Art. 171 LDA: Fines up to €15,493 + seizure + damages',
    ],

    // Comparison Royalty
    'comparison_title' => 'Resale Right vs Platform Royalty',
    'comparison_subtitle' => 'Two distinct and cumulative mechanisms',

    'comparison_table' => [
        'aspect' => 'Aspect',
        'platform_royalty' => 'Platform Royalty (FlorenceEGI)',
        'legal_droit' => 'Resale Right (Legal)',

        'legal_basis' => 'Legal basis',
        'legal_basis_platform' => 'Smart contract agreement',
        'legal_basis_law' => 'Italian Law 633/1941 Art. 19bis',

        'min_threshold' => 'Minimum threshold',
        'min_threshold_platform' => '€0 (all sales)',
        'min_threshold_law' => '€3,000',

        'percentage' => 'Percentage',
        'percentage_platform' => '4.5% fixed',
        'percentage_law' => '4% → 0.25% (decreasing)',

        'sale_type' => 'Sale type',
        'sale_type_platform' => 'P2P direct (platform)',
        'sale_type_law' => 'Through professionals (galleries/auctions)',

        'management' => 'Management',
        'management_platform' => 'Automatic smart contract',
        'management_law' => 'SIAE (manual)',

        'cumulative' => 'Cumulative',
        'cumulative_yes' => 'YES! Creator can receive BOTH',
    ],

    // Sale scenarios
    'scenarios_title' => 'How It Works on FlorenceEGI',

    'scenario_primary' => [
        'title' => 'Primary Sale (Mint) - EGI €1,000',
        'distribution' => 'Revenue distribution:',
        'creator' => 'Creator: €650-680 (65-68%)',
        'epp' => 'EPP: €200 (20%)',
        'platform' => 'Platform: €100 (10%)',
        'association' => 'Association: €20 (2%)',
        'droit_not_applicable' => 'Resale right NOT applicable',
        'droit_reason' => 'This is the first sale, not a resale',
    ],

    'scenario_secondary_low' => [
        'title' => 'Secondary Resale - EGI €1,000 (P2P on FlorenceEGI)',
        'distribution' => 'Distribution:',
        'seller' => 'Seller receives: €930 (93%)',
        'creator_royalty' => 'Creator royalty: €45 (4.5%)',
        'epp' => 'EPP: €10 (1%)',
        'platform' => 'Platform: €10 (1%)',
        'association' => 'Association: €5 (0.5%)',
        'droit_not_applicable' => 'Legal resale right NOT applicable',
        'droit_reason' => 'Below €3,000 threshold',
        'platform_royalty_note' => 'But Creator still receives 4.5% (our contract)',
    ],

    'scenario_secondary_high' => [
        'title' => 'Secondary Resale - EGI €50,000 (via Gallery/Auction)',
        'fee_platform' => 'FlorenceEGI Fee:',
        'seller' => 'Seller: €46,500 (93%)',
        'creator' => 'Creator: €2,250 (4.5%)',
        'epp' => 'EPP: €500 (1%)',
        'platform' => 'Platform: €500 (1%)',
        'association' => 'Assoc: €250 (0.5%)',
        'droit_applicable' => 'Legal resale right APPLICABLE',
        'droit_rate' => 'Rate: 4% (0-€50k bracket)',
        'droit_amount' => 'Amount: €2,000',
        'droit_recipient' => 'Received: Creator (via SIAE)',
        'droit_separate' => 'Separate from platform fees',
        'total_creator' => 'TOTAL Creator: €4,250 (8.5%)',
        'example' => 'Example: €50,000 sale via gallery → Creator receives €2,250 (4.5% platform) + €2,000 (4% resale right) = €4,250 total (8.5%)',
    ],

    // Legislation
    'legislation_title' => 'Legislative Framework',

    'law_lda' => [
        'title' => 'Italian Law 633/1941 (Copyright Act - LDA)',
        'art_12_19' => 'Art. 12-19: Economic rights (reproduction, communication, distribution)',
        'art_20' => 'Art. 20: Moral rights (paternity, integrity of work)',
        'art_19bis' => 'Art. 19bis: Resale right on resales',
        'art_25' => 'Art. 25: Protection duration (author\'s life + 70 years)',
        'art_171' => 'Art. 171: Sanctions for violations (fines €51-€15,493)',
    ],

    'law_dlgs' => [
        'title' => 'Legislative Decree 118/2006 (EU Directive 2001/84/EC Implementation)',
        'art_3' => 'Art. 3: Resale right rates (4% up to €50k, then decreasing)',
        'art_4' => 'Art. 4: Minimum threshold €3,000 for application',
        'art_5' => 'Art. 5: Maximum €12,500 per sale',
        'art_8' => 'Art. 8: Management via SIAE (Italian Authors and Publishers Society)',
    ],

    'law_cc' => [
        'title' => 'Civil Code - Art. 2575-2583',
        'description' => 'Distinction between ownership of the physical object (Owner) and rights over the intellectual work (Creator). Purchasing an artwork transfers only material possession, not copyright.',
    ],

    // Sale contract
    'contract_title' => 'What the EGI Sale Contract Includes',
    'owner_acquires' => 'The Owner ACQUIRES:',
    'owner_acquires_list' => [
        'physical' => 'Physical ownership of the work (material object)',
        'nft' => 'Digital NFT (blockchain certificate)',
        'enjoyment' => 'Right to private enjoyment',
        'resale' => 'Right to resale (with Creator royalty)',
        'possession' => 'Exclusive possession of the original',
    ],

    'creator_retains' => 'The Creator RETAINS:',
    'creator_retains_list' => [
        'moral_rights' => 'All moral rights (paternity, integrity)',
        'droit_suite' => 'Resale right (4%-0.25% on resales >€3k)',
        'platform_royalty' => 'Platform royalty (4.5% always)',
        'reproduction' => 'Reproduction rights (prints, copies)',
        'copyright' => 'Copyright on the image of the work',
        'digital_rights' => 'Digital rights (commercial online use)',
    ],

    // FlorenceEGI Commitment
    'commitment_title' => 'FlorenceEGI Commitment',
    'commitment_subtitle' => 'FlorenceEGI commits to respecting and protecting artists\' rights provided by Italian and European law:',
    'commitment_list' => [
        'attribution' => 'We guarantee correct attribution in all EGIs (paternity)',
        'immutability' => 'We block post-mint modifications (blockchain integrity)',
        'royalties' => 'Automatic royalties 4.5% on all resales (even below €3k)',
        'siae' => 'We collaborate with SIAE for managing resale rights on sales >€3k through professionals',
        'enforcement' => 'Smart contract prevents royalty evasion (trustless enforcement)',
    ],
];











