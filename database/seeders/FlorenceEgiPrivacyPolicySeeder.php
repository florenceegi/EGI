<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use App\Models\DataRetentionPolicy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - GDPR Compliant)
 * @date 2025-09-30
 * @purpose GDPR-compliant privacy policies for FlorenceEGI marketplace
 *
 * 🎯 APPROACH: Industry-standard clarity following GDPR Article 12
 * 📏 STRUCTURE: Essential user types with clear, transparent information
 * 🌐 LOCALIZATION: Italian and English versions
 * ✅ GDPR: Art. 12 "concise, transparent, intelligible" compliance
 */
class FlorenceEgiPrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();

        // Clear existing policies if any (use delete instead of truncate due to foreign keys)
        DB::table('privacy_policies')->delete();
        DB::table('data_retention_policies')->delete();

        // Create GDPR-compliant policy structure
        $this->seedCorePrivacyPolicies($adminUser);
        $this->seedDataRetentionPolicies($adminUser);
    }

    /**
     * Create core privacy policies (3 types x 2 languages = 6 policies)
     */
    private function seedCorePrivacyPolicies(?User $admin): void
    {
        $languages = ['it', 'en']; // Italiano come primary, inglese come secondary

        foreach ($languages as $lang) {
            $policies = [
                $this->createMainPrivacyPolicy($admin, $lang),
                $this->createMarketplacePolicy($admin, $lang),
                $this->createCookiePolicy($admin, $lang),
            ];

            foreach ($policies as $policy) {
                PrivacyPolicy::create($policy);
            }
        }
    }

    /**
     * Create data retention policies
     */
    private function seedDataRetentionPolicies(?User $admin): void
    {
        $retentionPolicies = [
            $this->createUserDataRetention($admin),
            $this->createTransactionDataRetention($admin),
        ];

        foreach ($retentionPolicies as $policy) {
            DataRetentionPolicy::create($policy);
        }
    }

    /**
     * Main Privacy Policy - GDPR compliant, concise (localized)
     */
    private function createMainPrivacyPolicy(?User $admin, string $language = 'it'): array
    {
        $titles = [
            'it' => 'Informativa Privacy FlorenceEGI',
            'en' => 'FlorenceEGI Privacy Policy'
        ];

        $summaries = [
            'it' => 'Informativa privacy conforme GDPR per il marketplace NFT sostenibile',
            'en' => 'GDPR-compliant privacy policy for sustainable NFT marketplace'
        ];

        $reviews = [
            'it' => 'Informativa privacy completa seguendo standard GDPR Articolo 12',
            'en' => 'Complete privacy policy following GDPR Article 12 standards'
        ];

        $changes = [
            'it' => 'Informativa privacy per FlorenceEGI marketplace NFT sostenibile',
            'en' => 'Privacy policy for FlorenceEGI sustainable NFT marketplace'
        ];

        return [
            'title' => $titles[$language],
            'document_type' => 'privacy_policy',
            'version' => '1.0.0',
            'content' => $this->getMainPrivacyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'legal_review_status' => 'approved',
            'legal_reviewer' => $admin?->id,
            'review_notes' => $reviews[$language],
            'change_description' => $changes[$language],
            'requires_consent' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * NFT Marketplace specific policy (localized)
     */
    private function createMarketplacePolicy(?User $admin, string $language = 'it'): array
    {
        $titles = [
            'it' => 'Policy Dati Marketplace NFT',
            'en' => 'NFT Marketplace Data Policy'
        ];

        $summaries = [
            'it' => 'Policy specifica per dati marketplace NFT',
            'en' => 'NFT marketplace data policy'
        ];

        return [
            'title' => $titles[$language],
            'document_type' => 'privacy_policy',
            'version' => '1.0',
            'content' => $this->getMarketplacePolicyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'requires_consent' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Cookie Policy (localized)
     */
    private function createCookiePolicy(?User $admin, string $language = 'it'): array
    {
        $titles = [
            'it' => 'Policy Cookie',
            'en' => 'Cookie Policy'
        ];

        $summaries = [
            'it' => 'Policy cookie e tracking',
            'en' => 'Cookie and tracking policy'
        ];

        return [
            'title' => $titles[$language],
            'document_type' => 'cookie_policy',
            'version' => '1.0',
            'content' => $this->getCookiePolicyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'requires_consent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * User data retention policy
     */
    private function createUserDataRetention(?User $admin): array
    {
        return [
            'name' => 'User Account Data Retention',
            'slug' => 'user-account-data',
            'description' => 'User account and profile data retention policy',
            'data_category' => 'user_data',
            'data_type' => 'personal_data',
            'retention_trigger' => 'time_based',
            'retention_days' => 2555, // 7 years
            'legal_basis' => 'Contract performance and legal obligations',
            'is_automated' => true,
            'is_active' => true,
            'user_can_request_deletion' => true,
            'created_by' => $admin?->id,
            'policy_effective_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Transaction data retention policy
     */
    private function createTransactionDataRetention(?User $admin): array
    {
        return [
            'name' => 'NFT Transaction Data Retention',
            'slug' => 'nft-transaction-data',
            'description' => 'NFT transaction and blockchain data retention policy',
            'data_category' => 'transaction_data',
            'data_type' => 'financial_data',
            'retention_trigger' => 'legal_basis_ends',
            'retention_days' => 3650, // 10 years for financial records
            'legal_basis' => 'Legal obligations (financial regulations)',
            'is_automated' => false, // Blockchain data cannot be deleted
            'is_active' => true,
            'user_can_request_deletion' => false, // Blockchain immutability
            'created_by' => $admin?->id,
            'policy_effective_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Main Privacy Policy Content - GDPR Art. 13-14 compliant (localized)
     */
    private function getMainPrivacyContent(string $language = 'it'): string
    {
        if ($language === 'it') {
            return $this->getMainPrivacyContentItalian();
        }

        return $this->getMainPrivacyContentEnglish();
    }

    /**
     * Main Privacy Policy Content - Italian version
     */
    private function getMainPrivacyContentItalian(): string
    {
        return "
# Informativa Privacy FlorenceEGI

**Data di Entrata in Vigore**: " . now()->format('d/m/Y') . "
**Titolare del Trattamento**: FlorenceEGI Ltd.
**Contatto**: privacy@florenceegi.com
**DPO**: dpo@florenceegi.com

## 1. Chi Siamo

FlorenceEGI gestisce un marketplace NFT sostenibile focalizzato su progetti ambientali. Siamo il titolare del trattamento dei tuoi dati personali.

## 2. Informazioni che Raccogliamo

**Dati Account**: Nome, email, indirizzo wallet, dati profilo
**Dati Transazioni**: Acquisti NFT, vendite, transazioni blockchain
**Dati Utilizzo**: Come utilizzi la piattaforma, preferenze, informazioni dispositivo
**Dati Comunicazione**: Messaggi supporto, feedback, preferenze marketing

## 3. Perché Utilizziamo le Tue Informazioni

- **Erogazione Servizio**: Operare il marketplace, processare transazioni
- **Conformità Legale**: Requisiti KYC/AML, obblighi fiscali
- **Comunicazione**: Aggiornamenti account, conferme transazioni
- **Miglioramento Piattaforma**: Analytics, bug fix, nuove funzionalità
- **Marketing**: Email promozionali (con consenso)

## 4. Base Giuridica (GDPR Articolo 6)

- **Contratto**: Erogazione servizio, processamento transazioni
- **Obbligo Legale**: Conformità normativa, reportistica fiscale
- **Interesse Legittimo**: Sicurezza piattaforma, prevenzione frodi
- **Consenso**: Comunicazioni marketing, funzionalità opzionali

## 5. Condivisione Dati

Condividiamo dati con:
- **Reti Blockchain**: Dati transazioni (pubblici per natura)
- **Processori Pagamento**: Elaborazione transazioni
- **Fornitori Servizi**: Hosting, analytics, supporto clienti
- **Autorità**: Quando legalmente richiesto

Non vendiamo mai i tuoi dati personali.

## 6. Sicurezza Dati

Implementiamo misure tecniche e organizzative appropriate incluse:
- Crittografia dati sensibili
- Valutazioni sicurezza regolari
- Controlli accesso e monitoraggio
- Formazione staff protezione dati

## 7. Conservazione Dati

- **Dati Account**: Mentre account attivo + 7 anni
- **Dati Transazioni**: 10 anni (normative finanziarie)
- **Dati Marketing**: Fino revoca consenso
- **Dati Blockchain**: Permanenti (blockchain immutabile)

## 8. I Tuoi Diritti (GDPR)

Hai diritto a:
- **Accedere** ai tuoi dati personali
- **Rettificare** informazioni inesatte
- **Cancellare** i tuoi dati (ove possibile)
- **Limitare** il trattamento
- **Portabilità dati**
- **Opporti** al trattamento
- **Revocare consenso** in qualsiasi momento

Contattaci a privacy@florenceegi.com per esercitare i tuoi diritti.

## 9. Trasferimenti Internazionali

I dati possono essere trasferiti fuori UE con garanzie appropriate (Clausole Contrattuali Standard).

## 10. Aggiornamenti

Potremmo aggiornare questa policy. Cambiamenti significativi saranno notificati via email.

## 11. Contattaci

**Email**: privacy@florenceegi.com
**Indirizzo**: FlorenceEGI Ltd., [Indirizzo Azienda]
**DPO**: dpo@florenceegi.com

Per reclami, contatta la tua autorità locale protezione dati.
";
    }

    /**
     * Main Privacy Policy Content - English version
     */
    private function getMainPrivacyContentEnglish(): string
    {
        return "
# FlorenceEGI Privacy Policy

**Effective Date**: " . now()->format('F j, Y') . "
**Controller**: FlorenceEGI Ltd.
**Contact**: privacy@florenceegi.com
**DPO**: dpo@florenceegi.com

## 1. Who We Are

FlorenceEGI operates a sustainable NFT marketplace focused on environmental projects. We are the data controller for your personal information.

## 2. Information We Collect

**Account Information**: Name, email, wallet address, profile data
**Transaction Data**: NFT purchases, sales, blockchain transactions
**Usage Data**: How you use our platform, preferences, device information
**Communication Data**: Support messages, feedback, marketing preferences

## 3. Why We Use Your Information

- **Service Provision**: Operate the marketplace, process transactions
- **Legal Compliance**: KYC/AML requirements, tax obligations
- **Communication**: Account updates, transaction confirmations
- **Platform Improvement**: Analytics, bug fixes, new features
- **Marketing**: Promotional emails (with consent)

## 4. Legal Basis (GDPR Article 6)

- **Contract**: Service provision, transaction processing
- **Legal Obligation**: Regulatory compliance, tax reporting
- **Legitimate Interest**: Platform security, fraud prevention
- **Consent**: Marketing communications, optional features

## 5. Data Sharing

We share data with:
- **Blockchain Networks**: Transaction data (public by nature)
- **Payment Processors**: Transaction processing
- **Service Providers**: Hosting, analytics, customer support
- **Regulators**: When legally required

We never sell your personal data.

## 6. Data Security

We implement appropriate technical and organizational measures including:
- Encryption of sensitive data
- Regular security assessments
- Access controls and monitoring
- Staff training on data protection

## 7. Data Retention

- **Account Data**: While account is active + 7 years
- **Transaction Data**: 10 years (financial regulations)
- **Marketing Data**: Until consent withdrawn
- **Blockchain Data**: Permanent (immutable blockchain)

## 8. Your Rights (GDPR)

You have the right to:
- **Access** your personal data
- **Rectify** inaccurate information
- **Erase** your data (where possible)
- **Restrict** processing
- **Data portability**
- **Object** to processing
- **Withdraw consent** at any time

Contact us at privacy@florenceegi.com to exercise your rights.

## 9. International Transfers

Data may be transferred outside the EU with appropriate safeguards (Standard Contractual Clauses).

## 10. Updates

We may update this policy. Significant changes will be notified by email.

## 11. Contact Us

**Email**: privacy@florenceegi.com
**Address**: FlorenceEGI Ltd., [Company Address]
**DPO**: dpo@florenceegi.com

For complaints, contact your local data protection authority.
";
    }

    /**
     * NFT Marketplace Policy Content
     */
    private function getMarketplacePolicyContent(string $language = 'it'): string
    {
        if ($language === 'it') {
            return $this->getMarketplacePolicyContentItalian();
        }

        return $this->getMarketplacePolicyContentEnglish();
    }

    /**
     * Marketplace Policy - Italian version
     */
    private function getMarketplacePolicyContentItalian(): string
    {
        return "
# Policy Dati Marketplace NFT

## Avviso Dati Blockchain

**Importante**: Le transazioni NFT sono registrate su blockchain pubbliche. Questi dati sono:
- **Pubblicamente visibili** a chiunque
- **Permanenti** e non possono essere cancellati
- **Fuori dal nostro controllo** una volta registrati

## Cosa Include i Dati Blockchain

- Indirizzi wallet (tuoi e dei partner transazione)
- Importi transazioni e timestamp
- Metadata NFT e trasferimenti proprietà
- Interazioni smart contract

## Considerazioni Privacy

- Usa wallet separati per transazioni privacy-sensitive
- Considera che i dati blockchain sono permanenti
- Gli indirizzi wallet possono essere collegati alla tua identità tramite la nostra piattaforma

## Dati Ambientali

Per progetti ambientali, potremmo raccogliere:
- Metriche impatto progetto
- Calcoli compensazione carbonio
- Dati reportistica sostenibilità

Questi dati aiutano verificare claims ambientali e migliorare trasparenza progetti.

## Diritti Creatori

I creatori NFT mantengono diritti a:
- Controllare metadata e contenuto
- Impostare termini royalty
- Aggiornare informazioni progetto
- Rimuovere contenuto dalla nostra piattaforma (non blockchain)

Contatto: marketplace@florenceegi.com
";
    }

    /**
     * Marketplace Policy - English version
     */
    private function getMarketplacePolicyContentEnglish(): string
    {
        return "
# NFT Marketplace Data Policy

## Blockchain Data Notice

**Important**: NFT transactions are recorded on public blockchains. This data is:
- **Publicly visible** to anyone
- **Permanent** and cannot be deleted
- **Outside our control** once recorded

## What Blockchain Data Includes

- Wallet addresses (yours and transaction partners)
- Transaction amounts and timestamps
- NFT metadata and ownership transfers
- Smart contract interactions

## Privacy Considerations

- Use separate wallets for privacy-sensitive transactions
- Consider that blockchain data is permanent
- Wallet addresses may be linked to your identity through our platform

## Environmental Data

For environmental projects, we may collect:
- Project impact metrics
- Carbon offset calculations
- Sustainability reporting data

This data helps verify environmental claims and improve project transparency.

## Creator Rights

NFT creators retain rights to:
- Control metadata and content
- Set royalty terms
- Update project information
- Remove content from our platform (not blockchain)

Contact: marketplace@florenceegi.com
";
    }

    /**
     * Cookie Policy Content
     */
    private function getCookiePolicyContent(string $language = 'it'): string
    {
        if ($language === 'it') {
            return $this->getCookiePolicyContentItalian();
        }

        return $this->getCookiePolicyContentEnglish();
    }

    /**
     * Cookie Policy - Italian version
     */
    private function getCookiePolicyContentItalian(): string
    {
        return "
# Policy Cookie

## Cosa Sono i Cookie

I cookie sono piccoli file di testo memorizzati sul tuo dispositivo per migliorare la tua esperienza.

## Cookie che Utilizziamo

**Cookie Essenziali** (sempre attivi):
- Sessioni login
- Carrello acquisti
- Funzionalità sicurezza
- Preferenze lingua

**Cookie Analitici** (con consenso):
- Google Analytics
- Statistiche utilizzo
- Monitoraggio performance

**Cookie Marketing** (con consenso):
- Integrazione social media
- Pubblicità mirata
- Tracciamento conversioni

## Le Tue Scelte

Puoi:
- Gestire impostazioni cookie nel tuo browser
- Rifiutare cookie non essenziali
- Usare il nostro centro preferenze cookie

**Nota**: Disabilitare cookie essenziali può influire sulla funzionalità piattaforma.

## Durata Cookie

- **Cookie sessione**: Cancellati quando chiudi browser
- **Cookie persistenti**: Scadono dopo tempo prestabilito (max 2 anni)

## Aggiornamenti

Questa policy può essere aggiornata. Controlla la data efficacia sopra.

Contatto: cookies@florenceegi.com
";
    }

    /**
     * Cookie Policy - English version
     */
    private function getCookiePolicyContentEnglish(): string
    {
        return "
# Cookie Policy

## What Are Cookies

Cookies are small text files stored on your device to improve your experience.

## Cookies We Use

**Essential Cookies** (always active):
- Login sessions
- Shopping cart
- Security features
- Language preferences

**Analytics Cookies** (with consent):
- Google Analytics
- Usage statistics
- Performance monitoring

**Marketing Cookies** (with consent):
- Social media integration
- Targeted advertising
- Conversion tracking

## Your Choices

You can:
- Manage cookie settings in your browser
- Opt out of non-essential cookies
- Use our cookie preference center

**Note**: Disabling essential cookies may affect platform functionality.

## Cookie Lifespan

- **Session cookies**: Deleted when you close browser
- **Persistent cookies**: Expire after set time (max 2 years)

## Updates

This policy may be updated. Check the effective date above.

Contact: cookies@florenceegi.com
";
    }
}