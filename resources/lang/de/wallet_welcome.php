<?php

return [
    // Modal Header
    'title' => 'Willkommen bei FlorenceEGI!',
    'subtitle' => 'Ihre Digitale Wallet ist bereit',

    // Intro
    'intro' => 'Bei der Registrierung haben wir automatisch eine <strong>Algorand Digital Wallet</strong> erstellt, die mit Ihrem Konto verknüpft ist. Diese Wallet ist notwendig, um Ihre <strong>Digitalen Echtheitszertifikate (EGI)</strong> zu erhalten, wenn Sie Kunstwerke auf der Plattform kaufen.',

    // Section 1: Security
    'security_title' => '🔒 Sicherheit und DSGVO-Datenschutz',
    'security_items' => [
        'Ihre Wallet ist mit <strong>XChaCha20-Poly1305-Verschlüsselung</strong> geschützt',
        'Private Schlüssel werden mit <strong>AWS Key Management Service (KMS)</strong> und Envelope-Verschlüsselung (DEK + KEK) verschlüsselt',
        'Sichere Speicherung in DSGVO-konformer Datenbank',
        'Sie können <strong>jederzeit</strong> Ihre Wallet-Anmeldedaten anfordern (25-Wörter geheime Phrase)',
        'Sie können die Wallet in <strong>Pera Wallet</strong> oder andere Algorand-kompatible Clients importieren',
        'Sie können die <strong>endgültige Löschung</strong> der Wallet aus unseren Systemen beantragen',
    ],
    'security_note' => '<strong>Hinweis:</strong> Sobald Sie die geheime Phrase exportiert und die Wallet von unseren Servern gelöscht haben, wird die Verwaltung vollständig <strong>nicht-verwahrend</strong> und liegt in Ihrer alleinigen Verantwortung.',

    // Section 2: Content
    'content_title' => '💎 Was Ihre Wallet enthält',
    'content_has_title' => '✅ Enthält:',
    'content_has' => [
        'Ihre <strong>EGI-Zertifikate</strong> (einzigartige Kunstwerk-NFTs)',
        'Metadaten zertifizierter Kunstwerke',
        'On-Chain-Authentizitätshistorie',
    ],
    'content_not_has_title' => '❌ Enthält NICHT:',
    'content_not_has' => [
        'ALGO (Algorand-Kryptowährung)',
        'Stablecoins oder andere fungible Token',
        'Geldmittel oder finanzielle Vermögenswerte',
    ],
    'content_note' => 'Die Wallet ist <strong>ausschließlich</strong> digitalen Zertifikaten gewidmet. Sie kann nicht für Finanzoperationen verwendet werden.',

    // Section 3: Payments
    'payments_title' => '💶 Zahlungen und FIAT-Quittungen',
    'payments_how_title' => 'So funktionieren Zahlungen:',
    'payments_how' => [
        'Alle Ihre Käufe erfolgen in <strong>Euro (€)</strong> per Kreditkarte, Banküberweisung oder anderen traditionellen Methoden',
        'Die Wallet wird <strong>nur</strong> verwendet, um das digitale Zertifikat des Kunstwerks zu erhalten, nicht zur Zahlungsabwicklung',
        'Zahlungstransaktionen werden von unserem zertifizierten PSP (Payment Service Provider) abgewickelt',
    ],
    'payments_iban_title' => '💳 FIAT-Zahlungen erhalten möchten?',
    'payments_iban_intro' => 'Wenn Sie ein <strong>Creator</strong> sind und Erlöse aus Ihren Verkäufen direkt auf Ihr Bankkonto erhalten möchten, können Sie Ihre <strong>IBAN</strong> in den Profileinstellungen hinzufügen.',
    'payments_iban_security_title' => 'Ihre IBAN wird:',
    'payments_iban_security' => [
        'Mit Banking-Sicherheitsstandards verschlüsselt (AES-256)',
        'Mit SHA-256-Hash + Pepper für Eindeutigkeit geschützt',
        'Nur für Zahlungen an Sie verwendet',
        'In vollständiger DSGVO-Konformität verwaltet',
        'Nur die letzten 4 Zeichen für UI gespeichert',
    ],

    // Section 4: Compliance
    'compliance_title' => '🔐 Regulierungskonformität (MiCA-safe)',
    'compliance_intro' => 'Diese Modalität stellt <strong>"begrenzte technische Verwahrung nicht-finanzieller digitaler Vermögenswerte"</strong> dar und:',
    'compliance_items' => [
        '<strong>Stellt keine CASP-Aktivität dar</strong> (Krypto-Asset-Dienstleister)',
        'Operiert <strong>außerhalb des MiCA-Perimeters</strong> (Verordnung über Märkte für Krypto-Assets)',
        'Unterliegt ausschließlich DSGVO-Verpflichtungen zum Schutz personenbezogener Daten',
    ],
    'compliance_platform_title' => 'FlorenceEGI:',
    'compliance_platform' => [
        '✅ Stellt digitale Zertifikate aus (einzigartige NFTs)',
        '✅ Bietet temporäre technische Verwahrung von Schlüsseln',
        '❌ Führt KEINE Wechselgeschäfte durch',
        '❌ Verwahrt KEINE Gelder oder Kryptowährungen',
        '❌ Vermittelt KEINE Finanztransaktionen',
    ],

    // Section 5: Options
    'options_title' => '📱 Was Sie tun können',
    'option1_title' => '✨ Option 1 - Automatische Verwaltung',
    'option1_subtitle' => '(Empfohlen für Anfänger)',
    'option1_items' => [
        'Wallet bleibt "unsichtbar" und automatisch verwaltet',
        'Erhalten Sie Ihre Zertifikate ohne Sorgen um Blockchain',
        'Ideal bei Unvertrautheit mit Kryptowährungen',
        'Maximale Benutzerfreundlichkeit',
    ],
    'option2_title' => '🔓 Option 2 - Volle Kontrolle',
    'option2_subtitle' => '(Für erfahrene Benutzer)',
    'option2_items' => [
        'Laden Sie geheime Phrase (25 Wörter) von <strong>Einstellungen → Sicherheit</strong> herunter',
        'Importieren Sie sie in Pera Wallet oder anderen Algorand-Client',
        'Verwalten Sie Ihre Zertifikate unabhängig',
        'Beantragen Sie Wallet-Löschung von unseren Servern',
    ],

    // Section 6: Glossary
    'glossary_title' => '📖 Glossar Technischer Begriffe',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Algorand Wallet:',
            'definition' => 'Digitales Portfolio auf Algorand-Blockchain. Enthält Ihre EGI-Zertifikate (einzigartige NFTs).',
        ],
        'egi' => [
            'term' => 'EGI (Digitales Zertifikat):',
            'definition' => 'Einzigartiges NFT, das Kunstwerk-Authentizität bescheinigt. Enthält unveränderliche und nachverfolgbare Metadaten.',
        ],
        'envelope_encryption' => [
            'term' => 'Envelope-Verschlüsselung (DEK+KEK):',
            'definition' => 'Zwei-Ebenen-Verschlüsselungssystem. Ein Schlüssel (DEK) verschlüsselt Daten, ein zweiter Schlüssel (KEK) verschlüsselt den ersten. AWS KMS verwaltet den KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Geheime Phrase (Seed Phrase):',
            'definition' => '25-Wörter-Sequenz, die Wallet-Zugangswiederherstellung ermöglicht. <strong>Niemals mit jemandem teilen!</strong>',
        ],
        'non_custodial' => [
            'term' => 'Nicht-verwahrende Wallet:',
            'definition' => 'Wallet, bei der nur Sie die privaten Schlüssel besitzen. Die Plattform kann nicht auf Ihre Assets zugreifen.',
        ],
        'gdpr' => [
            'term' => 'DSGVO:',
            'definition' => 'Datenschutz-Grundverordnung. Garantiert Ihre Datenschutzrechte und Sicherheit persönlicher Daten in der EU.',
        ],
        'mica' => [
            'term' => 'MiCA (Märkte für Krypto-Assets):',
            'definition' => 'EU-Verordnung über Krypto-Asset-Märkte. FlorenceEGI operiert außerhalb des MiCA-Perimeters, da keine finanziellen Assets verwaltet werden.',
        ],
        'casp' => [
            'term' => 'CASP:',
            'definition' => 'Krypto-Asset-Dienstleister. Entität, die Wechsel-, Verwahrungs- oder Übertragungsdienste für Kryptowährungen anbietet. FlorenceEGI ist kein CASP.',
        ],
    ],

    // Section 7: Help
    'help_title' => '🆘 Fragen?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Vollständiger Leitfaden',
    'help_support' => 'Support',
    'help_support_desc' => '24/7 Unterstützung',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Schnelle Antworten',

    // Footer
    'dont_show_again' => 'Diese Nachricht nicht mehr anzeigen',
    'btn_add_iban' => 'IBAN hinzufügen',
    'btn_continue' => 'Verstanden, fortfahren',
];


