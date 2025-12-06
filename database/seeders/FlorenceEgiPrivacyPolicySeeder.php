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
 * @version 2.0.0 (FlorenceEGI - GDPR Compliant + AI/Blockchain Update)
 * @date 2025-01-26
 * @purpose GDPR-compliant privacy policies for FlorenceEGI marketplace
 *
 * 🎯 APPROACH: Industry-standard clarity following GDPR Article 12
 * 📏 STRUCTURE: Essential user types with clear, transparent information
 * 🌐 LOCALIZATION: Italian and English versions
 * ✅ GDPR: Art. 12 "concise, transparent, intelligible" compliance
 * 🤖 AI: Updated for AI-assisted services (Claude, NATAN, Document Analysis)
 * ⛓️ BLOCKCHAIN: Updated for Algorand integration and wallet management
 * 🔐 KMS: AWS Key Management Service for secure key custody
 */
class FlorenceEgiPrivacyPolicySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
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
    private function seedCorePrivacyPolicies(?User $admin): void {
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
    private function seedDataRetentionPolicies(?User $admin): void {
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
    private function createMainPrivacyPolicy(?User $admin, string $language = 'it'): array {
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
            'version' => '2.0.0',
            'content' => $this->getMainPrivacyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'published',
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
    private function createMarketplacePolicy(?User $admin, string $language = 'it'): array {
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
            'version' => '2.0.0',
            'content' => $this->getMarketplacePolicyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'published',
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
    private function createCookiePolicy(?User $admin, string $language = 'it'): array {
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
            'version' => '2.0.0',
            'content' => $this->getCookiePolicyContent($language),
            'summary' => json_encode([$language => $summaries[$language]]),
            'language' => $language,
            'status' => 'published',
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
    private function createUserDataRetention(?User $admin): array {
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
    private function createTransactionDataRetention(?User $admin): array {
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
    private function getMainPrivacyContent(string $language = 'it'): string {
        if ($language === 'it') {
            return $this->getMainPrivacyContentItalian();
        }

        return $this->getMainPrivacyContentEnglish();
    }

    /**
     * Main Privacy Policy Content - Italian version
     * @version 2.0.0 - Updated for AI/Blockchain/KMS features
     */
    private function getMainPrivacyContentItalian(): string {
        return "
# Informativa Privacy FlorenceEGI

**Data di Entrata in Vigore**: " . now()->format('d/m/Y') . "
**Versione**: 2.0.0
**Titolare del Trattamento**: FlorenceEGI S.r.l.
**Contatto Privacy**: privacy@florenceegi.com
**Data Protection Officer (DPO)**: dpo@florenceegi.com

---

## 1. Chi Siamo

FlorenceEGI S.r.l. (\"**FlorenceEGI**\", \"**noi**\", \"**nostro**\") è il titolare del trattamento dei tuoi dati personali. Operiamo un marketplace NFT sostenibile focalizzato su arte digitale e progetti di protezione ambientale (EPP - Environmental Protection Projects).

La nostra piattaforma integra tecnologie avanzate tra cui:
- **Blockchain Algorand** per la gestione di NFT (EGI) e transazioni
- **Intelligenza Artificiale** per assistenza, analisi documenti e miglioramento servizi
- **Sistemi di custodia sicura** per la protezione delle chiavi crittografiche

---

## 2. Informazioni che Raccogliamo

### 2.1 Dati di Registrazione e Account
- **Dati identificativi**: Nome, cognome, email, username
- **Dati fiscali**: Codice fiscale/Partita IVA, indirizzo di fatturazione (per Creator e venditori)
- **Credenziali**: Password (crittografata), chiavi di recupero account

### 2.2 Dati Blockchain e Wallet
- **Indirizzo wallet Algorand**: Identificativo pubblico sulla blockchain
- **Chiavi crittografiche**: Gestite con crittografia AWS KMS (Key Management Service) - **NON abbiamo mai accesso alle tue chiavi private in chiaro**
- **Transazioni blockchain**: Hash transazioni, timestamp, importi (dati pubblici per natura)
- **NFT posseduti**: EGI nella tua collezione, metadata associati

### 2.3 Dati di Utilizzo Piattaforma
- **Attività**: Navigazione, acquisti, vendite, interazioni
- **Preferenze**: Lingua, valuta, impostazioni notifiche
- **Dispositivo**: Tipo browser, sistema operativo, indirizzo IP
- **Comunicazioni**: Messaggi supporto, feedback, richieste GDPR

### 2.4 Dati Processati dall'Intelligenza Artificiale
- **N.A.T.A.N. Assistant**: Elabora solo metadati pubblici per assistenza e ricerca
- **Analisi Documenti**: I documenti caricati (es. per verifica EPP) sono processati da AI per estrazione dati
- **Miglioramento Servizi**: Analytics aggregate e anonimizzate

**⚠️ IMPORTANTE**: I sistemi AI non processano mai dati personali identificativi senza tuo esplicito consenso. I modelli AI utilizzati (Claude by Anthropic, OpenAI) sono conformi GDPR.

---

## 3. Perché Trattiamo i Tuoi Dati

| Finalità | Base Giuridica (GDPR Art. 6) | Dati Utilizzati |
|----------|------------------------------|-----------------|
| Erogazione servizi marketplace | Contratto (Art. 6.1.b) | Account, wallet, transazioni |
| Gestione account e autenticazione | Contratto (Art. 6.1.b) | Email, password, wallet |
| Processamento pagamenti/transazioni | Contratto (Art. 6.1.b) | Wallet, dati fiscali |
| Adempimenti fiscali (DAC7, KYC/AML) | Obbligo legale (Art. 6.1.c) | Dati identificativi, fiscali |
| Prevenzione frodi e sicurezza | Interesse legittimo (Art. 6.1.f) | Log attività, IP, device |
| Assistenza AI e miglioramento servizi | Interesse legittimo (Art. 6.1.f) | Interazioni (anonimizzate) |
| Marketing e comunicazioni promozionali | Consenso (Art. 6.1.a) | Email, preferenze |
| Contributi a progetti EPP | Contratto (Art. 6.1.b) | Transazioni EPP |

---

## 4. Tecnologie e Trattamenti Specifici

### 4.1 Blockchain Algorand
I dati registrati su blockchain sono:
- **Pubblici**: Visibili a chiunque su explorer blockchain
- **Permanenti**: Non possono essere modificati o cancellati
- **Pseudonimi**: L'indirizzo wallet non rivela direttamente la tua identità

### 4.2 Custodia Chiavi (AWS KMS)
Le chiavi private dei wallet custodiali sono:
- **Crittografate**: Con chiavi master AWS KMS
- **Distribuite**: Architettura multi-key per massima sicurezza
- **Accessibili solo a te**: Nessun dipendente FlorenceEGI può accedere

### 4.3 Intelligenza Artificiale
Utilizziamo AI per:
- **N.A.T.A.N.**: Assistente specializzato in arte e sostenibilità (processa solo dati pubblici)
- **Analisi documenti**: Estrazione automatica dati da certificazioni EPP
- **Raccomandazioni**: Suggerimenti personalizzati basati su preferenze

I provider AI utilizzati:
- **Anthropic (Claude)**: Elaborazione linguaggio naturale
- **OpenAI**: Embeddings per ricerca semantica
- **Ollama (locale)**: Opzione GDPR-compliant per elaborazioni on-premise

---

## 5. Condivisione e Trasferimento Dati

### 5.1 Con Chi Condividiamo
| Destinatario | Dati Condivisi | Finalità |
|--------------|----------------|----------|
| Rete Algorand | Transazioni, indirizzi wallet | Registrazione blockchain |
| Provider verifica identità (KYC) | Documenti identità | Conformità normativa |
| Provider AI (Anthropic, OpenAI) | Solo metadati pubblici | Servizi AI |
| AWS | Chiavi crittografate | Custodia sicura |
| Autorità fiscali | Dati fiscali | Obblighi DAC7 |
| Autorità giudiziarie | Come richiesto | Obblighi legali |

### 5.2 Trasferimenti Extra-UE
Alcuni provider operano fuori UE. Garantiamo protezione tramite:
- **Clausole Contrattuali Standard (SCC)** approvate dalla Commissione UE
- **Certificazioni** (es. AWS è certificato secondo vari standard internazionali)
- **Data Processing Agreement** con tutti i fornitori

**Non vendiamo mai i tuoi dati personali.**

---

## 6. Sicurezza dei Dati

Implementiamo misure tecniche e organizzative ai sensi dell'Art. 32 GDPR:

- **Crittografia**: TLS 1.3 in transito, AES-256 a riposo
- **Gestione chiavi**: AWS KMS con rotazione automatica
- **Accessi**: Autenticazione multi-fattore, principio del minimo privilegio
- **Monitoraggio**: Log di audit completi, alert automatici
- **Backup**: Crittografati, georidondanti
- **Formazione**: Staff formato su protezione dati
- **Test sicurezza**: Penetration test e vulnerability assessment periodici

---

## 7. Conservazione dei Dati

| Categoria Dati | Periodo Conservazione | Motivazione |
|----------------|----------------------|-------------|
| Dati account | Durata account + 7 anni | Obblighi fiscali |
| Dati transazioni | 10 anni | Normative finanziarie |
| Log di sicurezza | 2 anni | Prevenzione frodi |
| Dati marketing | Fino a revoca consenso | Consenso |
| Dati blockchain | Permanente | Immutabilità blockchain |
| Dati AI (conversazioni) | 90 giorni | Miglioramento servizio |

---

## 8. I Tuoi Diritti GDPR

Ai sensi degli Articoli 15-22 GDPR hai diritto a:

| Diritto | Descrizione | Come Esercitarlo |
|---------|-------------|------------------|
| **Accesso** (Art. 15) | Ottenere copia dei tuoi dati | Dashboard GDPR o email |
| **Rettifica** (Art. 16) | Correggere dati inesatti | Dashboard o email |
| **Cancellazione** (Art. 17) | \"Diritto all'oblio\" | Email (con limitazioni per blockchain) |
| **Limitazione** (Art. 18) | Sospendere trattamento | Email |
| **Portabilità** (Art. 20) | Ricevere dati in formato leggibile | Dashboard (export JSON) |
| **Opposizione** (Art. 21) | Opporti a trattamento | Email |
| **Revoca consenso** | Ritirare consensi | Dashboard o email |

**⚠️ NOTA**: I dati registrati su blockchain NON possono essere cancellati a causa della natura immutabile della tecnologia.

**Contatto**: privacy@florenceegi.com
**Risposta**: Entro 30 giorni dalla richiesta

---

## 9. Decisioni Automatizzate e Profilazione

Utilizziamo processi decisionali automatizzati per:
- **Rilevamento frodi**: Blocco automatico transazioni sospette
- **Raccomandazioni**: Suggerimenti basati su preferenze

Hai diritto a richiedere intervento umano, esprimere opinione e contestare decisioni automatizzate contattando privacy@florenceegi.com.

---

## 10. Cookie e Tecnologie di Tracciamento

Per informazioni dettagliate sui cookie, consulta la nostra **Cookie Policy** separata.

---

## 11. Modifiche all'Informativa

Potremmo aggiornare questa informativa. In caso di modifiche sostanziali:
- Pubblicheremo la nuova versione con data aggiornata
- Ti invieremo notifica via email
- Se necessario, richiederemo nuovo consenso

---

## 12. Contatti

**Titolare del Trattamento**
FlorenceEGI S.r.l.
Email: privacy@florenceegi.com

**Data Protection Officer**
Email: dpo@florenceegi.com

**Reclami**
Puoi presentare reclamo all'autorità di controllo:
- **Italia**: Garante per la Protezione dei Dati Personali (www.garanteprivacy.it)
- **UE**: Autorità del tuo Stato membro

---

*Ultimo aggiornamento: " . now()->format('d/m/Y') . "*
";
    }

    /**
     * Main Privacy Policy Content - English version
     * @version 2.0.0 - Updated for AI/Blockchain/KMS features
     */
    private function getMainPrivacyContentEnglish(): string {
        return "
# FlorenceEGI Privacy Policy

**Effective Date**: " . now()->format('F j, Y') . "
**Version**: 2.0.0
**Data Controller**: FlorenceEGI S.r.l.
**Privacy Contact**: privacy@florenceegi.com
**Data Protection Officer (DPO)**: dpo@florenceegi.com

---

## 1. Who We Are

FlorenceEGI S.r.l. (\"**FlorenceEGI**\", \"**we**\", \"**our**\") is the data controller of your personal data. We operate a sustainable NFT marketplace focused on digital art and environmental protection projects (EPP - Environmental Protection Projects).

Our platform integrates advanced technologies including:
- **Algorand Blockchain** for NFT (EGI) and transaction management
- **Artificial Intelligence** for assistance, document analysis, and service improvement
- **Secure custody systems** for cryptographic key protection

---

## 2. Information We Collect

### 2.1 Registration and Account Data
- **Identification data**: First name, last name, email, username
- **Tax data**: Tax ID/VAT number, billing address (for Creators and sellers)
- **Credentials**: Password (encrypted), account recovery keys

### 2.2 Blockchain and Wallet Data
- **Algorand wallet address**: Public identifier on the blockchain
- **Cryptographic keys**: Managed with AWS KMS (Key Management Service) encryption - **We NEVER have access to your private keys in clear text**
- **Blockchain transactions**: Transaction hashes, timestamps, amounts (public data by nature)
- **Owned NFTs**: EGIs in your collection, associated metadata

### 2.3 Platform Usage Data
- **Activity**: Navigation, purchases, sales, interactions
- **Preferences**: Language, currency, notification settings
- **Device**: Browser type, operating system, IP address
- **Communications**: Support messages, feedback, GDPR requests

### 2.4 Data Processed by Artificial Intelligence
- **N.A.T.A.N. Assistant**: Processes only public metadata for assistance and research
- **Document Analysis**: Uploaded documents (e.g., for EPP verification) are processed by AI for data extraction
- **Service Improvement**: Aggregated and anonymized analytics

**⚠️ IMPORTANT**: AI systems never process personally identifiable data without your explicit consent. The AI models used (Claude by Anthropic, OpenAI) are GDPR compliant.

---

## 3. Why We Process Your Data

| Purpose | Legal Basis (GDPR Art. 6) | Data Used |
|---------|---------------------------|-----------|
| Marketplace service provision | Contract (Art. 6.1.b) | Account, wallet, transactions |
| Account management and authentication | Contract (Art. 6.1.b) | Email, password, wallet |
| Payment/transaction processing | Contract (Art. 6.1.b) | Wallet, tax data |
| Tax compliance (DAC7, KYC/AML) | Legal obligation (Art. 6.1.c) | Identification, tax data |
| Fraud prevention and security | Legitimate interest (Art. 6.1.f) | Activity logs, IP, device |
| AI assistance and service improvement | Legitimate interest (Art. 6.1.f) | Interactions (anonymized) |
| Marketing and promotional communications | Consent (Art. 6.1.a) | Email, preferences |
| Contributions to EPP projects | Contract (Art. 6.1.b) | EPP transactions |

---

## 4. Specific Technologies and Processing

### 4.1 Algorand Blockchain
Data recorded on blockchain is:
- **Public**: Visible to anyone on blockchain explorers
- **Permanent**: Cannot be modified or deleted
- **Pseudonymous**: Wallet address does not directly reveal your identity

### 4.2 Key Custody (AWS KMS)
Private keys for custodial wallets are:
- **Encrypted**: With AWS KMS master keys
- **Distributed**: Multi-key architecture for maximum security
- **Accessible only to you**: No FlorenceEGI employee can access them

### 4.3 Artificial Intelligence
We use AI for:
- **N.A.T.A.N.**: Assistant specialized in art and sustainability (processes only public data)
- **Document analysis**: Automatic data extraction from EPP certifications
- **Recommendations**: Personalized suggestions based on preferences

AI providers used:
- **Anthropic (Claude)**: Natural language processing
- **OpenAI**: Embeddings for semantic search
- **Ollama (local)**: GDPR-compliant option for on-premise processing

---

## 5. Data Sharing and Transfers

### 5.1 Who We Share With
| Recipient | Data Shared | Purpose |
|-----------|-------------|---------|
| Algorand Network | Transactions, wallet addresses | Blockchain recording |
| Identity verification providers (KYC) | Identity documents | Regulatory compliance |
| AI providers (Anthropic, OpenAI) | Only public metadata | AI services |
| AWS | Encrypted keys | Secure custody |
| Tax authorities | Tax data | DAC7 obligations |
| Judicial authorities | As required | Legal obligations |

### 5.2 Extra-EU Transfers
Some providers operate outside the EU. We ensure protection through:
- **Standard Contractual Clauses (SCC)** approved by the EU Commission
- **Certifications** (e.g., AWS is certified under various international standards)
- **Data Processing Agreements** with all providers

**We never sell your personal data.**

---

## 6. Data Security

We implement technical and organizational measures pursuant to GDPR Art. 32:

- **Encryption**: TLS 1.3 in transit, AES-256 at rest
- **Key management**: AWS KMS with automatic rotation
- **Access**: Multi-factor authentication, principle of least privilege
- **Monitoring**: Complete audit logs, automatic alerts
- **Backups**: Encrypted, geo-redundant
- **Training**: Staff trained on data protection
- **Security testing**: Regular penetration tests and vulnerability assessments

---

## 7. Data Retention

| Data Category | Retention Period | Reason |
|---------------|------------------|--------|
| Account data | Account duration + 7 years | Tax obligations |
| Transaction data | 10 years | Financial regulations |
| Security logs | 2 years | Fraud prevention |
| Marketing data | Until consent withdrawn | Consent |
| Blockchain data | Permanent | Blockchain immutability |
| AI data (conversations) | 90 days | Service improvement |

---

## 8. Your GDPR Rights

Pursuant to Articles 15-22 GDPR you have the right to:

| Right | Description | How to Exercise |
|-------|-------------|-----------------|
| **Access** (Art. 15) | Obtain a copy of your data | GDPR Dashboard or email |
| **Rectification** (Art. 16) | Correct inaccurate data | Dashboard or email |
| **Erasure** (Art. 17) | \"Right to be forgotten\" | Email (with blockchain limitations) |
| **Restriction** (Art. 18) | Suspend processing | Email |
| **Portability** (Art. 20) | Receive data in readable format | Dashboard (JSON export) |
| **Objection** (Art. 21) | Object to processing | Email |
| **Withdraw consent** | Withdraw consents | Dashboard or email |

**⚠️ NOTE**: Data recorded on blockchain CANNOT be deleted due to the immutable nature of the technology.

**Contact**: privacy@florenceegi.com
**Response**: Within 30 days of request

---

## 9. Automated Decisions and Profiling

We use automated decision-making for:
- **Fraud detection**: Automatic blocking of suspicious transactions
- **Recommendations**: Suggestions based on preferences

You have the right to request human intervention, express your opinion, and contest automated decisions by contacting privacy@florenceegi.com.

---

## 10. Cookies and Tracking Technologies

For detailed information about cookies, please refer to our separate **Cookie Policy**.

---

## 11. Policy Updates

We may update this policy. In case of substantial changes:
- We will publish the new version with updated date
- We will notify you by email
- If necessary, we will request new consent

---

## 12. Contact

**Data Controller**
FlorenceEGI S.r.l.
Email: privacy@florenceegi.com

**Data Protection Officer**
Email: dpo@florenceegi.com

**Complaints**
You can file a complaint with the supervisory authority:
- **Italy**: Garante per la Protezione dei Dati Personali (www.garanteprivacy.it)
- **EU**: Your Member State's authority

---

*Last updated: " . now()->format('F j, Y') . "*
";
    }

    /**
     * NFT Marketplace Policy Content
     */
    private function getMarketplacePolicyContent(string $language = 'it'): string {
        if ($language === 'it') {
            return $this->getMarketplacePolicyContentItalian();
        }

        return $this->getMarketplacePolicyContentEnglish();
    }

    /**
     * Marketplace Policy - Italian version
     */
    private function getMarketplacePolicyContentItalian(): string {
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
    private function getMarketplacePolicyContentEnglish(): string {
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
    private function getCookiePolicyContent(string $language = 'it'): string {
        if ($language === 'it') {
            return $this->getCookiePolicyContentItalian();
        }

        return $this->getCookiePolicyContentEnglish();
    }

    /**
     * Cookie Policy - Italian version
     * @version 2.0.0 - Updated for current platform technologies
     */
    private function getCookiePolicyContentItalian(): string {
        return "
# Cookie Policy FlorenceEGI

**Data di Entrata in Vigore**: " . now()->format('d/m/Y') . "
**Versione**: 2.0.0

---

## 1. Cosa Sono i Cookie

I cookie sono piccoli file di testo che vengono memorizzati sul tuo dispositivo (computer, smartphone, tablet) quando visiti un sito web. Sono ampiamente utilizzati per far funzionare i siti web, migliorarne l'efficienza e fornire informazioni ai proprietari del sito.

---

## 2. Tipi di Cookie Utilizzati

### 2.1 Cookie Strettamente Necessari (Sempre Attivi)

Questi cookie sono essenziali per il funzionamento della piattaforma e non possono essere disattivati. Includono:

| Cookie | Scopo | Durata |
|--------|-------|--------|
| `XSRF-TOKEN` | Protezione CSRF per sicurezza form | Sessione |
| `florenceegi_session` | Identificazione sessione utente | 2 ore |
| `remember_web` | \"Ricordami\" per login persistente | 30 giorni |
| `wallet_session` | Sessione connessione wallet | Sessione |
| `locale` | Preferenza lingua | 1 anno |
| `currency` | Preferenza valuta | 1 anno |
| `cookie_consent` | Memorizza scelte cookie | 1 anno |

### 2.2 Cookie di Funzionalità (Con Consenso)

Migliorano l'esperienza utente memorizzando preferenze:

| Cookie | Scopo | Durata |
|--------|-------|--------|
| `theme` | Tema chiaro/scuro | 1 anno |
| `sidebar_collapsed` | Stato sidebar | 30 giorni |
| `collection_view` | Preferenza visualizzazione | 30 giorni |
| `natan_history` | Cronologia assistente AI | 7 giorni |

### 2.3 Cookie Analitici (Con Consenso)

Ci aiutano a comprendere come utilizzi la piattaforma:

| Cookie | Provider | Scopo | Durata |
|--------|----------|-------|--------|
| `_ga` | Google Analytics | Distingue utenti | 2 anni |
| `_ga_*` | Google Analytics | Mantiene stato sessione | 2 anni |
| `_gid` | Google Analytics | Distingue utenti | 24 ore |
| `_gat` | Google Analytics | Limita frequenza richieste | 1 minuto |

**Nota**: Utilizziamo Google Analytics 4 con anonimizzazione IP attiva.

### 2.4 Cookie di Marketing (Con Consenso)

Utilizzati per pubblicità personalizzata e tracciamento conversioni:

| Cookie | Provider | Scopo | Durata |
|--------|----------|-------|--------|
| `_fbp` | Facebook | Pixel tracking | 90 giorni |
| `_gcl_au` | Google Ads | Conversioni | 90 giorni |
| Social buttons | Vari | Condivisione social | Variabile |

---

## 3. Cookie di Terze Parti

La nostra piattaforma integra servizi di terze parti che possono impostare propri cookie:

### 3.1 Algorand/Blockchain
- **PeraWallet**: Cookie per connessione wallet (se utilizzato)
- I dati blockchain non utilizzano cookie tradizionali

### 3.2 Servizi AI
- **Anthropic/OpenAI**: Non impostano cookie direttamente sulla piattaforma
- Le interazioni AI sono gestite server-side

### 3.3 Servizi di Pagamento
- Cookie dei gateway di pagamento durante le transazioni

---

## 4. Gestione delle Preferenze Cookie

### 4.1 Banner Cookie
Al primo accesso, ti verrà mostrato un banner per scegliere quali categorie di cookie accettare.

### 4.2 Centro Preferenze
Puoi modificare le tue preferenze in qualsiasi momento:
- Clicca sull'icona cookie nel footer
- Accedi a Impostazioni > Privacy > Preferenze Cookie

### 4.3 Impostazioni Browser
Puoi anche gestire i cookie tramite il tuo browser:
- **Chrome**: Impostazioni > Privacy e sicurezza > Cookie
- **Firefox**: Preferenze > Privacy e sicurezza
- **Safari**: Preferenze > Privacy
- **Edge**: Impostazioni > Cookie e autorizzazioni sito

---

## 5. Tecnologie Simili

Oltre ai cookie, utilizziamo:

### 5.1 Local Storage
Per memorizzare dati lato client in modo persistente:
- Preferenze UI
- Cache dati non sensibili
- Stato applicazione

### 5.2 Session Storage
Per dati temporanei durante la sessione:
- Stato form in compilazione
- Dati navigazione temporanei

---

## 6. Do Not Track (DNT)

Rispettiamo il segnale \"Do Not Track\" del browser. Se attivato:
- Non caricheremo cookie analitici
- Non caricheremo cookie marketing
- Solo cookie essenziali saranno attivi

---

## 7. Impatto sulla Funzionalità

| Categoria Disabilitata | Impatto |
|------------------------|---------|
| Cookie Essenziali | ⛔ Sito non funzionante |
| Cookie Funzionalità | ⚠️ Esperienza degradata, preferenze non salvate |
| Cookie Analitici | ✅ Nessun impatto funzionale |
| Cookie Marketing | ✅ Nessun impatto funzionale |

---

## 8. Aggiornamenti

Questa Cookie Policy può essere aggiornata periodicamente. In caso di modifiche sostanziali:
- Aggiorneremo la data di efficacia
- Ti chiederemo nuovo consenso se necessario

---

## 9. Contatti

Per domande sulla Cookie Policy:
- **Email**: cookies@florenceegi.com
- **Privacy**: privacy@florenceegi.com

---

*Ultimo aggiornamento: " . now()->format('d/m/Y') . "*
";
    }

    /**
     * Cookie Policy - English version
     * @version 2.0.0 - Updated for current platform technologies
     */
    private function getCookiePolicyContentEnglish(): string {
        return "
# FlorenceEGI Cookie Policy

**Effective Date**: " . now()->format('F j, Y') . "
**Version**: 2.0.0

---

## 1. What Are Cookies

Cookies are small text files that are stored on your device (computer, smartphone, tablet) when you visit a website. They are widely used to make websites work, improve their efficiency, and provide information to website owners.

---

## 2. Types of Cookies We Use

### 2.1 Strictly Necessary Cookies (Always Active)

These cookies are essential for the platform to function and cannot be disabled. They include:

| Cookie | Purpose | Duration |
|--------|---------|----------|
| `XSRF-TOKEN` | CSRF protection for form security | Session |
| `florenceegi_session` | User session identification | 2 hours |
| `remember_web` | \"Remember me\" for persistent login | 30 days |
| `wallet_session` | Wallet connection session | Session |
| `locale` | Language preference | 1 year |
| `currency` | Currency preference | 1 year |
| `cookie_consent` | Stores cookie choices | 1 year |

### 2.2 Functionality Cookies (With Consent)

Enhance user experience by storing preferences:

| Cookie | Purpose | Duration |
|--------|---------|----------|
| `theme` | Light/dark theme | 1 year |
| `sidebar_collapsed` | Sidebar state | 30 days |
| `collection_view` | View preference | 30 days |
| `natan_history` | AI assistant history | 7 days |

### 2.3 Analytics Cookies (With Consent)

Help us understand how you use the platform:

| Cookie | Provider | Purpose | Duration |
|--------|----------|---------|----------|
| `_ga` | Google Analytics | Distinguishes users | 2 years |
| `_ga_*` | Google Analytics | Maintains session state | 2 years |
| `_gid` | Google Analytics | Distinguishes users | 24 hours |
| `_gat` | Google Analytics | Throttle request rate | 1 minute |

**Note**: We use Google Analytics 4 with IP anonymization enabled.

### 2.4 Marketing Cookies (With Consent)

Used for personalized advertising and conversion tracking:

| Cookie | Provider | Purpose | Duration |
|--------|----------|---------|----------|
| `_fbp` | Facebook | Pixel tracking | 90 days |
| `_gcl_au` | Google Ads | Conversions | 90 days |
| Social buttons | Various | Social sharing | Variable |

---

## 3. Third-Party Cookies

Our platform integrates third-party services that may set their own cookies:

### 3.1 Algorand/Blockchain
- **PeraWallet**: Cookies for wallet connection (if used)
- Blockchain data does not use traditional cookies

### 3.2 AI Services
- **Anthropic/OpenAI**: Do not set cookies directly on the platform
- AI interactions are handled server-side

### 3.3 Payment Services
- Payment gateway cookies during transactions

---

## 4. Managing Cookie Preferences

### 4.1 Cookie Banner
On your first visit, you will be shown a banner to choose which cookie categories to accept.

### 4.2 Preference Center
You can change your preferences at any time:
- Click the cookie icon in the footer
- Go to Settings > Privacy > Cookie Preferences

### 4.3 Browser Settings
You can also manage cookies through your browser:
- **Chrome**: Settings > Privacy and security > Cookies
- **Firefox**: Preferences > Privacy & Security
- **Safari**: Preferences > Privacy
- **Edge**: Settings > Cookies and site permissions

---

## 5. Similar Technologies

In addition to cookies, we use:

### 5.1 Local Storage
To store data client-side persistently:
- UI preferences
- Non-sensitive data cache
- Application state

### 5.2 Session Storage
For temporary data during the session:
- Form state while filling
- Temporary navigation data

---

## 6. Do Not Track (DNT)

We respect the \"Do Not Track\" browser signal. If enabled:
- We will not load analytics cookies
- We will not load marketing cookies
- Only essential cookies will be active

---

## 7. Impact on Functionality

| Disabled Category | Impact |
|-------------------|--------|
| Essential Cookies | ⛔ Site will not work |
| Functionality Cookies | ⚠️ Degraded experience, preferences not saved |
| Analytics Cookies | ✅ No functional impact |
| Marketing Cookies | ✅ No functional impact |

---

## 8. Updates

This Cookie Policy may be updated periodically. In case of substantial changes:
- We will update the effective date
- We will ask for new consent if necessary

---

## 9. Contact

For questions about the Cookie Policy:
- **Email**: cookies@florenceegi.com
- **Privacy**: privacy@florenceegi.com

---

*Last updated: " . now()->format('F j, Y') . "*
";
    }
}
