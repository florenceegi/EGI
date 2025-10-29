<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformKnowledgeSection;

/**
 * Platform Knowledge Section Seeder
 *
 * Populates knowledge base with 15 initial sections about FlorenceEGI
 * features and workflows for AI Platform Assistant.
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Art Advisor)
 * @date 2025-10-29
 * @purpose Initial knowledge base for AI platform guidance
 */
class PlatformKnowledgeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            // ====================================
            // EGIS MANAGEMENT
            // ====================================
            [
                'section_key' => 'egis.create',
                'category' => 'egis',
                'title' => 'Come creare un EGI',
                'content' => "Per creare un nuovo EGI (Enhanced Guaranteed Item):\n\n1. Vai alla tua Collezione\n2. Clicca 'Aggiungi EGI'\n3. Compila i campi obbligatori:\n   - Titolo dell'opera\n   - Carica immagine (JPG, PNG, WebP - max 5MB)\n   - Descrizione (opzionale ma raccomandata)\n   - Prezzo in EUR\n4. Opzionale: Aggiungi traits per aumentare valore e discoverability\n5. Scegli modalità di vendita: Prezzo Fisso o Asta\n6. Salva come bozza o pubblica direttamente\n\nNOTA: Un EGI inizia in modalità 'Pre-Mint' (virtuale). Puoi testarlo e modificarlo prima di mintarlo on-chain.",
                'keywords' => ['creare', 'nuovo', 'egi', 'opera', 'artwork', 'upload'],
                'priority' => 10,
                'locale' => 'it',
            ],
            
            [
                'section_key' => 'egis.edit',
                'category' => 'egis',
                'title' => 'Come modificare un EGI',
                'content' => "Puoi modificare un EGI se sei il creator e l'EGI NON è ancora stato mintato on-chain.\n\nPer modificare:\n1. Apri la pagina dell'EGI\n2. Nella colonna centrale (CRUD panel) troverai il form di editing\n3. Clicca l'icona matita per attivare la modalità edit\n4. Modifica i campi desiderati:\n   - Titolo\n   - Descrizione\n   - Prezzo (se non ci sono offerte attive)\n   - Immagine\n   - Pubblicazione (pubblica/bozza)\n5. Salva le modifiche\n\nATTENZIONE: Una volta mintato on-chain, l'EGI diventa immutabile (blockchain immutability). Potrai modificare solo metadati off-chain come descrizione estesa.",
                'keywords' => ['modificare', 'edit', 'cambiare', 'aggiornare', 'egi'],
                'priority' => 20,
                'locale' => 'it',
            ],

            [
                'section_key' => 'egis.mint',
                'category' => 'egis',
                'title' => 'Come mintare un EGI (ASA vs SmartContract)',
                'content' => "FlorenceEGI supporta DUE tipi di mint:\n\n**EGI CLASSICO (ASA - Algorand Standard Asset):**\n- Semplice, economico, veloce\n- NFT standard Algorand\n- Ideale per: arte digitale, collectibles, opere singole\n- Costo: ~0.1 ALGO\n\n**EGI VIVENTE (SmartContract):**\n- NFT con logica programmabile\n- Può evolvere nel tempo (royalties dinamiche, utility unlockable)\n- Ideale per: progetti complessi, arte generativa, membership\n- Costo: ~1-2 ALGO\n\nPROCEDURA MINT:\n1. Completa l'EGI (descrizione, traits, prezzo)\n2. Nel CRUD panel, sezione 'Prepara e Minta'\n3. Scegli tipo: Classico o Vivente\n4. Clicca 'Minta Ora'\n5. Conferma transazione\n6. Ricevi ASA ID o SmartContract address\n\nDopo il mint, l'EGI è immutabile e verificabile on-chain.",
                'keywords' => ['mint', 'mintare', 'blockchain', 'asa', 'smart contract', 'nft'],
                'priority' => 30,
                'locale' => 'it',
            ],

            [
                'section_key' => 'egis.pricing',
                'category' => 'egis',
                'title' => 'Come impostare il prezzo di un EGI',
                'content' => "Il pricing di un EGI dipende da diversi fattori:\n\n**CONSIDERAZIONI:**\n1. **Unicità dell'opera** - È pezzo unico o edizione limitata?\n2. **Reputazione creator** - Portfolio e track record\n3. **Complessità tecnica** - Livello di dettaglio e skill\n4. **Market demand** - C'è richiesta per questo stile/tema?\n5. **Traits rarity** - Traits rari aumentano valore\n\n**RANGE SUGGERITI:**\n- Emerging artist, prima opera: €50-200\n- Established artist locale: €200-1000\n- Professional con portfolio: €1000-5000\n- Premium/luxury market: €5000+\n\n**STRATEGIE:**\n- **Prezzo Fisso**: Controllo totale, no sorprese\n- **Asta**: Lascia che il mercato decida, potenziale upside\n- **Offerte**: Ricevi proposte, accetti se ti convincono\n\n**TIP:** Puoi sempre modificare il prezzo PRIMA del mint. Dopo il mint, il prezzo secondario dipende dal mercato.",
                'keywords' => ['prezzo', 'pricing', 'costo', 'valore', 'euro', 'valutare'],
                'priority' => 40,
                'locale' => 'it',
            ],

            // ====================================
            // TRAITS & METADATA
            // ====================================
            [
                'section_key' => 'traits.add',
                'category' => 'traits',
                'title' => 'Come aggiungere traits a un EGI',
                'content' => "I traits aumentano valore, discoverability e appeal del tuo EGI.\n\n**COME AGGIUNGERE:**\n1. Apri l'EGI in modalità edit\n2. Scorri fino a 'Caratteristiche (Traits)'\n3. Clicca 'Aggiungi Trait'\n4. Compila:\n   - **Category**: Materials, Visual, Dimensions, Special, Sustainability, Cultural\n   - **Type**: Sottocategoria (es: Primary Color, Art Style)\n   - **Value**: Valore specifico (es: 'Warm Tones', 'Contemporary')\n5. Salva\n\n**BEST PRACTICES:**\n- Aggiungi 3-7 traits (non troppi, non troppo pochi)\n- Sii specifico ma chiaro\n- Usa traits verificabili\n- Priorità: Materials > Visual > Special > Cultural\n\n**SUGGERIMENTO AI:**\nPuoi chiedere all'AI Art Advisor di suggerire traits analizzando la tua opera visivamente!",
                'keywords' => ['traits', 'caratteristiche', 'metadata', 'aggiungere', 'nft'],
                'priority' => 50,
                'locale' => 'it',
            ],

            [
                'section_key' => 'traits.ai',
                'category' => 'traits',
                'title' => 'Traits AI-generated vs manuali',
                'content' => "FlorenceEGI offre DUE modi per aggiungere traits:\n\n**TRAITS AI-GENERATED:**\n- L'AI analizza visivamente la tua opera\n- Suggerisce traits basati su colori, stile, composizione\n- Veloce e accurato per arte visiva\n- Costo: 1 chiamata API Anthropic Vision\n- Ideale per: opere complesse, arte astratta, quando non sai da dove iniziare\n\n**TRAITS MANUALI:**\n- Tu scegli ogni trait basandoti sulla tua conoscenza dell'opera\n- Massimo controllo\n- Gratuito\n- Ideale per: opere con storia specifica, materiali particolari, contest culturale\n\n**APPROCCIO IBRIDO (RACCOMANDATO):**\n1. Genera traits AI (base 5-7 traits)\n2. Rivedi e modifica se necessario\n3. Aggiungi traits specifici che solo tu conosci (es: 'Exhibited at Venice Biennale')\n\nRisultato: Metadata ricchi e accurati con minimo effort!",
                'keywords' => ['traits', 'ai', 'automatici', 'generati', 'vision', 'suggerimenti'],
                'priority' => 60,
                'locale' => 'it',
            ],

            // ====================================
            // COLLECTIONS
            // ====================================
            [
                'section_key' => 'collections.create',
                'category' => 'collections',
                'title' => 'Come creare una Collezione',
                'content' => "Le Collezioni sono container per i tuoi EGI. Ogni creator deve avere almeno una collezione.\n\n**PROCEDURA:**\n1. Vai a 'Le Mie Collezioni' nel menu\n2. Clicca 'Nuova Collezione'\n3. Compila:\n   - Nome collezione (es: 'Maritime Art Collection')\n   - Descrizione\n   - Tipo: Art, Music, Collectibles, Heritage, Photography, etc.\n   - Immagine cover (opzionale)\n4. Salva\n\n**DOPO LA CREAZIONE:**\n- Puoi aggiungere EGI alla collezione\n- Invitare collaboratori (editors, admins)\n- Gestire visibilità (pubblica/privata)\n- Impostare royalties collection-level\n\n**BEST PRACTICE:**\n- Tematica coerente per la collezione\n- Nome descrittivo e memorable\n- Max 3-4 collezioni per mantenere focus",
                'keywords' => ['collezione', 'collection', 'creare', 'nuova', 'container'],
                'priority' => 70,
                'locale' => 'it',
            ],

            [
                'section_key' => 'collections.roles',
                'category' => 'collections',
                'title' => 'Ruoli collaboratori nelle Collezioni',
                'content' => "FlorenceEGI supporta collaborazione multi-user sulle collezioni.\n\n**RUOLI DISPONIBILI:**\n\n**CREATOR (Owner):**\n- Tutti i permessi\n- Crea/modifica/elimina EGI\n- Gestisce collaboratori\n- Imposta royalties\n- Minta NFT\n\n**ADMIN:**\n- Crea/modifica/elimina EGI\n- Gestisce collaboratori\n- NO: cambio royalties, NO: elimina collezione\n\n**EDITOR:**\n- Crea/modifica EGI\n- NO: elimina EGI, NO: gestisce collaboratori\n\n**VIEWER:**\n- Solo visualizzazione\n- Utile per team review o consulenti\n\n**COME AGGIUNGERE COLLABORATORI:**\n1. Apri collezione\n2. Tab 'Collaboratori'\n3. Inserisci email o username\n4. Seleziona ruolo\n5. Invia invito\n\nI collaboratori ricevono notifica e possono accettare/rifiutare.",
                'keywords' => ['collaboratori', 'ruoli', 'roles', 'permissions', 'team', 'admin', 'editor'],
                'priority' => 80,
                'locale' => 'it',
            ],

            // ====================================
            // WALLET & BLOCKCHAIN
            // ====================================
            [
                'section_key' => 'wallet.connect',
                'category' => 'wallet',
                'title' => 'Come collegare wallet Algorand',
                'content' => "FlorenceEGI supporta DUE modalità wallet:\n\n**OPZIONE 1: WALLET CUSTODIAL (Automatico)**\n- Creato automaticamente alla registrazione\n- Gestito dalla piattaforma (sicuro, enterprise KMS encryption)\n- Seed phrase criptata con AWS KMS\n- Ideale per: utenti non-crypto, massima semplicità\n- Accesso: Via password account FlorenceEGI\n\n**OPZIONE 2: WALLET ESTERNO (Self-Custody)**\n- Usa tuo wallet esistente (Pera, Defly, MyAlgo, etc.)\n- Tu controlli chiavi private\n- Ideale per: crypto-savvy users, massimo controllo\n- Collegamento: Clicca 'Collega Wallet' → Scansiona QR con app wallet\n\n**SICUREZZA:**\n- Wallet custodial: Seed criptata con envelope encryption (DEK+KEK)\n- Wallet esterno: Mai richiediamo seed phrase\n- 2FA disponibile per operazioni critiche\n\n**QUALE SCEGLIERE?**\n- Nuovo a crypto? → Custodial (zero friction)\n- Esperto crypto? → Esterno (self-custody)",
                'keywords' => ['wallet', 'algorand', 'collegare', 'pera', 'defly', 'connettere'],
                'priority' => 90,
                'locale' => 'it',
            ],

            [
                'section_key' => 'wallet.security',
                'category' => 'wallet',
                'title' => 'Sicurezza wallet e seed phrase',
                'content' => "**WALLET CUSTODIAL (Gestito da FlorenceEGI):**\n\nLa tua seed phrase è protetta con:\n✅ Envelope Encryption (DEK + KEK architecture)\n✅ AWS KMS (Hardware Security Module)\n✅ XChaCha20-Poly1305 AEAD (autenticazione + encryption)\n✅ Secure memory cleanup (sodium_memzero)\n✅ GDPR compliant audit trail\n✅ Zero-knowledge: neanche noi vediamo la seed in chiaro\n\n**ACCESSO SEED:**\nPuoi visualizzare/esportare la tua seed SOLO:\n- Con autenticazione password\n- Con 2FA attivo (se configurato)\n- Operazione loggata in audit trail\n- Warning che seed = controllo totale fondi\n\n**BACKUP:**\n1. Vai a Impostazioni → Wallet\n2. Clicca 'Visualizza Seed Phrase'\n3. Conferma password + 2FA\n4. SCRIVI le 25 parole su carta (NO screenshot, NO cloud)\n5. Conserva in cassaforte fisica\n\n**SICUREZZA ENTERPRISE:**\nSe usi wallet custodial, hai:\n- Encryption same-level banche\n- HSM-backed key management\n- Disaster recovery geografico\n- Audit compliance PA/Enterprise",
                'keywords' => ['sicurezza', 'security', 'seed', 'phrase', 'backup', 'kms', 'encryption'],
                'priority' => 100,
                'locale' => 'it',
            ],

            // ====================================
            // MARKETPLACE & TRADING
            // ====================================
            [
                'section_key' => 'marketplace.buy',
                'category' => 'marketplace',
                'title' => 'Come comprare un EGI',
                'content' => "**PROCEDURA ACQUISTO:**\n\n1. **Trova l'EGI** - Esplora marketplace o collezioni\n2. **Verifica dettagli:**\n   - Prezzo\n   - Traits e metadata\n   - Creator reputation\n   - Certificato autenticità (CoA)\n3. **Acquista:**\n   - Clicca 'Acquista Ora' (se prezzo fisso)\n   - Oppure 'Fai un'Offerta' (se asta/offerte)\n4. **Pagamento:**\n   - EUR via Stripe/PayPal (FIAT standard)\n   - Oppure ALGO da tuo wallet\n5. **Ricevi:**\n   - NFT trasferito al tuo wallet\n   - Certificato CoA generato\n   - Ownership on-chain verificabile\n\n**DOPO L'ACQUISTO:**\n- L'EGI appare in 'Il Mio Portfolio'\n- Puoi rivenderlo (marketplace secondario)\n- Ricevi royalties se rivendi\n- Certificato sempre disponibile\n\n**PROTEZIONI:**\n- Escrow automatico (fondi trattenuti fino a transfer completato)\n- Dispute resolution\n- Verificabilità blockchain",
                'keywords' => ['comprare', 'acquistare', 'buy', 'purchase', 'pagamento', 'marketplace'],
                'priority' => 110,
                'locale' => 'it',
            ],

            [
                'section_key' => 'marketplace.offers',
                'category' => 'marketplace',
                'title' => 'Sistema offerte e aste',
                'content' => "**OFFERTE (Bidding System):**\n\nFlorenceEGI supporta DUE tipi di offerte:\n\n**STRONG OFFER (Identificata):**\n- Utente loggato con wallet collegato\n- Offerta tracciabile e vincolante\n- Priorità alta se accettata\n- Certificato pre-generato\n\n**WEAK OFFER (Anonima/FEGI):**\n- Utente può essere anonimo\n- Offerta indicativa\n- Creator decide se contattare\n- Priorità bassa\n\n**COME FARE OFFERTA:**\n1. Apri EGI\n2. Clicca 'Fai un'Offerta'\n3. Inserisci importo EUR\n4. Scegli tipo (Strong/Weak)\n5. Invia offerta\n\n**COME GESTIRE OFFERTE (Creator):**\n1. Vedi tutte le offerte in 'Storico Acquisti/Offerte'\n2. Ordinate per priorità:\n   - Strong offers per prime\n   - Importo decrescente\n3. Puoi:\n   - Accettare (mint immediato per acquirente)\n   - Rifiutare\n   - Negoziare (chat con offerente)\n\n**ASTE:**\nSe abiliti modalità Asta:\n- Data inizio/fine\n- Prezzo minimo (reserve)\n- Rilanci minimi\n- Auto-chiusura a scadenza\n- Vincitore notificato automaticamente",
                'keywords' => ['offerte', 'asta', 'auction', 'bid', 'bidding', 'proposta'],
                'priority' => 120,
                'locale' => 'it',
            ],

            // ====================================
            // DUAL ARCHITECTURE (Pre-Mint)
            // ====================================
            [
                'section_key' => 'premint.concept',
                'category' => 'egis',
                'title' => 'Cos\'è il Pre-Mint (modalità virtuale)',
                'content' => "**PRE-MINT MODE:**\n\nQuando crei un EGI, inizia in modalità 'Pre-Mint' (virtuale).\n\n**COSA SIGNIFICA:**\n- EGI esiste nel database FlorenceEGI\n- NON ancora sulla blockchain\n- Puoi modificarlo liberamente\n- Puoi testarlo, mostrarlo, ricevere offerte\n- NON costa nulla (no gas fees)\n\n**VANTAGGI:**\n✅ Testa mercato prima di mintare\n✅ Modifica prezzo/descrizione/traits\n✅ Ricevi feedback community\n✅ Zero costi blockchain\n✅ Decidi quando mintare\n\n**QUANDO MINTARE:**\nQuando sei soddisfatto e:\n- Hai ricevuto offerte interessanti\n- Vuoi ownership on-chain immutabile\n- Sei pronto a 'freezare' metadata\n- Vuoi massima credibilità (blockchain-verified)\n\n**AUTO-MINT:**\nPuoi anche 'riservare per auto-mint' e la piattaforma minta per te quando conveniente (batch mint = costi ridotti).",
                'keywords' => ['premint', 'pre-mint', 'virtuale', 'mint', 'modalità'],
                'priority' => 130,
                'locale' => 'it',
            ],

            // ====================================
            // PLATFORM FEATURES
            // ====================================
            [
                'section_key' => 'platform.fees',
                'category' => 'general',
                'title' => 'Fee e royalties su FlorenceEGI',
                'content' => "**STRUTTURA FEE TRASPARENTE:**\n\n**VENDITA PRIMARIA (Prima vendita dal creator):**\n- Platform fee: 2.5% del prezzo\n- Creator riceve: 97.5%\n- Payment processor (Stripe): ~2% (esterno)\n\n**VENDITA SECONDARIA (Rivendite):**\n- Platform fee: 2.5%\n- Creator royalty: 5-10% (impostata dal creator)\n- Venditore riceve: ~87.5-92.5%\n\n**MINT COSTS:**\n- ASA mint: ~0.1 ALGO (~€0.02)\n- SmartContract mint: ~1-2 ALGO (~€0.20-0.40)\n- Pagato da chi minta (creator o acquirente)\n\n**NO HIDDEN FEES:**\nTutti i costi sono mostrati PRIMA di confermare transazione.\n\n**ESEMPIO PRATICO:**\nVendi EGI a €1000:\n- Tu ricevi: €975 (€1000 - 2.5%)\n- Platform: €25\n- Stripe: ~€20 (separato)\n\nRivendita a €1500:\n- Venditore riceve: €1312.50 (€1500 - 2.5% - 10% royalty)\n- Tu (creator) ricevi: €150 (10% royalty)\n- Platform: €37.50",
                'keywords' => ['fee', 'costi', 'royalties', 'commissioni', 'guadagno', 'prezzo'],
                'priority' => 140,
                'locale' => 'it',
            ],

            [
                'section_key' => 'platform.certificates',
                'category' => 'general',
                'title' => 'Certificati di Autenticità (CoA)',
                'content' => "**CERTIFICATE OF AUTHENTICITY (CoA):**\n\nOgni EGI mintato riceve automaticamente un CoA digitale.\n\n**CONTENUTO CoA:**\n- Titolo opera e creator\n- Data mint on-chain\n- ASA ID o SmartContract address\n- Hash immagine (IPFS CID)\n- Metadata completi\n- QR code verifica blockchain\n- Firma digitale piattaforma\n\n**FORMATI:**\n- PDF scaricabile\n- Web view (shareable link)\n- NFT metadata on-chain\n\n**VERIFICA AUTENTICITÀ:**\nChiunque può verificare:\n1. Scansiona QR code sul certificato\n2. Controlla su Algorand Explorer\n3. Verifica hash immagine corrisponde\n4. Ownership attuale on-chain\n\n**VALIDITÀ LEGALE:**\n- Certificato ha valore legale (firma digitale)\n- Ammesso in perizie e valutazioni\n- Compliance PA per acquisizioni pubbliche\n- Tracciabilità completa ownership history",
                'keywords' => ['certificato', 'coa', 'autenticità', 'verifica', 'qr', 'blockchain'],
                'priority' => 150,
                'locale' => 'it',
            ],

            // ====================================
            // GENERAL PLATFORM
            // ====================================
            [
                'section_key' => 'platform.overview',
                'category' => 'general',
                'title' => 'Panoramica FlorenceEGI',
                'content' => "**FLORENCEEGI - Enhanced Guaranteed Items Platform**\n\nMarketplace enterprise per arte digitale e asset tokenizzati su blockchain Algorand.\n\n**TARGET:**\n- Artisti e creator\n- Collezionisti e investitori\n- Pubbliche Amministrazioni (acquisti certificati)\n- Gallerie e curatori\n\n**COSA PUOI FARE:**\n1. **Creare** - EGI (NFT) delle tue opere\n2. **Vendere** - Marketplace con FIAT o crypto\n3. **Collezionare** - Acquista arte digitale certificata\n4. **Mintare** - NFT su Algorand blockchain\n5. **Collaborare** - Team multi-user su collezioni\n\n**TECNOLOGIA:**\n- Blockchain: Algorand (green, veloce, economico)\n- NFT: ASA (standard) o SmartContract (programmabili)\n- Encryption: AWS KMS enterprise-grade\n- Compliance: GDPR, MiCA-safe, PA-ready\n\n**UNICITÀ:**\n- Dual Architecture (Pre-Mint + Mint)\n- FIAT first (EUR, no crypto required)\n- PA/Enterprise compliance\n- Certificati legalmente validi",
                'keywords' => ['florenceegi', 'panoramica', 'overview', 'cosa', 'piattaforma', 'funzionalità'],
                'priority' => 5,
                'locale' => 'it',
            ],

            [
                'section_key' => 'platform.support',
                'category' => 'general',
                'title' => 'Contattare supporto tecnico',
                'content' => "**SUPPORTO FLORENCEEGI:**\n\n**CANALI DISPONIBILI:**\n\n1. **AI Art Advisor** (Questo assistente!):\n   - Risposte immediate 24/7\n   - Per domande su funzionalità\n   - Suggerimenti creativi e strategici\n\n2. **Email Support:**\n   - support@florenceegi.com\n   - Risposta entro 24h lavorative\n   - Per problemi tecnici, billing, account\n\n3. **Help Center:**\n   - Documentazione completa\n   - Video tutorial\n   - FAQ\n\n4. **Live Chat** (Enterprise/PA):\n   - Disponibile per clienti enterprise\n   - Supporto prioritario\n   - SLA garantiti\n\n**QUANDO CONTATTARE:**\n- Problemi tecnici (errori, bug)\n- Questioni billing/pagamenti\n- Supporto mint/blockchain\n- Dispute resolution\n- Partnership opportunities\n\n**COSA INCLUDERE:**\n- User ID o email\n- Descrizione problema\n- Screenshot se rilevante\n- Steps per riprodurre errore",
                'keywords' => ['supporto', 'support', 'aiuto', 'help', 'assistenza', 'contatto'],
                'priority' => 160,
                'locale' => 'it',
            ],

            // ====================================
            // NFT & BLOCKCHAIN CONCEPTS
            // ====================================
            [
                'section_key' => 'nft.basics',
                'category' => 'general',
                'title' => 'Cosa sono gli NFT e perché sono utili',
                'content' => "**NFT (Non-Fungible Token):**\n\nCertificato digitale di proprietà e autenticità su blockchain.\n\n**CARATTERISTICHE:**\n- **Unico**: Ogni NFT è distinguibile (non intercambiabile)\n- **Verificabile**: Ownership on-chain, pubblicamente verificabile\n- **Immutabile**: Una volta mintato, non può essere alterato\n- **Trasferibile**: Proprietà facilmente trasferibile\n\n**PERCHÉ UTILE PER ARTE:**\n✅ Prova autenticità inequivocabile\n✅ Royalties automatiche su rivendite\n✅ Tracciabilità ownership completa\n✅ Scarsità verificabile (edizioni limitate)\n✅ Monetizzazione arte digitale\n✅ Global marketplace accessibile\n\n**BLOCKCHAIN ALGORAND:**\nFlorenceEGI usa Algorand per:\n- Green (carbon-negative, non energy-intensive)\n- Veloce (4.5s finality)\n- Economico (mint ~€0.02)\n- Scalabile (migliaia tx/secondo)\n- Sicuro (Pure Proof of Stake)\n\n**NFT ≠ Immagine:**\nNFT è il certificato, non l'immagine.\nL'immagine può esistere ovunque, ma l'NFT prova CHI possiede l'originale certificato.",
                'keywords' => ['nft', 'blockchain', 'algorand', 'cosa', 'spiegazione', 'token'],
                'priority' => 15,
                'locale' => 'it',
            ],

            [
                'section_key' => 'algorand.why',
                'category' => 'general',
                'title' => 'Perché Algorand invece di Ethereum',
                'content' => "**ALGORAND vs ETHEREUM - Comparazione:**\n\n**COSTI:**\n- Algorand mint: ~€0.02\n- Ethereum mint: €50-500 (dipende da gas)\n→ 2500x più economico\n\n**VELOCITÀ:**\n- Algorand: 4.5 secondi finality\n- Ethereum: 15 secondi - 6 minuti\n→ 10-80x più veloce\n\n**AMBIENTE:**\n- Algorand: Carbon-negative (certificato)\n- Ethereum: Proof of Stake (migliorato vs PoW)\n→ Green blockchain\n\n**SEMPLICITÀ:**\n- Algorand ASA: Standard nativo, no smart contract necessari\n- Ethereum ERC-721: Richiede deploy smart contract\n→ Più semplice per artist\n\n**ADOPTION PA/ENTERPRISE:**\n- Algorand: Partnership governi (El Salvador, Italia, etc.)\n- Compliance built-in\n- Enterprise SDK\n\n**QUANDO ETHEREUM?**\nEthereum ha senso se:\n- Vuoi massima adoption (più wallet/exchange)\n- Progetti DeFi complessi\n- Già hai community Ethereum\n\nPer arte digitale e PA: Algorand è scelta ottimale.",
                'keywords' => ['algorand', 'ethereum', 'blockchain', 'perché', 'differenza', 'confronto'],
                'priority' => 25,
                'locale' => 'it',
            ],

            // ====================================
            // MARKETING & STRATEGY
            // ====================================
            [
                'section_key' => 'marketing.description',
                'category' => 'marketing',
                'title' => 'Come scrivere descrizioni efficaci',
                'content' => "**ANATOMIA DESCRIZIONE EFFICACE:**\n\n**PARAGRAFO 1 - Hook (2-3 frasi):**\n- Cattura attenzione immediatamente\n- Emozione o concept principale\n- Perché è unica/speciale\n\n**PARAGRAFO 2 - Dettagli Tecnici/Artistici:**\n- Tecnica usata\n- Palette colori se rilevante\n- Ispirazione o storia dietro l'opera\n- Tempo di creazione\n\n**PARAGRAFO 3 - Valore e CTA:**\n- A chi è rivolta\n- Perché è un buon investimento\n- Scarsità (pezzo unico, edizione limitata)\n- Invito all'azione\n\n**BEST PRACTICES:**\n✅ 150-300 parole (non troppo breve, non wall of text)\n✅ Linguaggio evocativo ma professionale\n✅ Keywords per SEO (arte, stile, colori, tema)\n✅ Storytelling (non solo descrizione tecnica)\n✅ Benefici per acquirente (cosa ottiene)\n\n**EVITA:**\n❌ 'Bellissimo', 'Straordinario', 'Unico al mondo' (generic hype)\n❌ Gergo tecnico incomprensibile\n❌ Solo tecnica senza emozione\n❌ Troppo personale/intimo\n\n**ESEMPIO BUONO:**\n'Questa composizione marina cattura il momento sospeso tra tempesta e calma. Le tonalità blu profondo dialogano con accenti ambrati, creando tensione dinamica. Opera digitale unica, ideale per collezionisti che apprezzano il contrasto tra forza naturale e composizione contemplativa.'",
                'keywords' => ['descrizione', 'description', 'scrivere', 'marketing', 'testo'],
                'priority' => 35,
                'locale' => 'it',
            ],

            [
                'section_key' => 'marketing.target_audience',
                'category' => 'marketing',
                'title' => 'Identificare target audience per le tue opere',
                'content' => "**SEGMENTAZIONE AUDIENCE:**\n\n**1. COLLEZIONISTI LUXURY (€5k+):**\n- Cercano: Esclusività, certificazione, investment grade\n- Linguaggio: Sofisticato, storico-artistico\n- Enfasi: Rarità, provenance, valore nel tempo\n\n**2. COLLEZIONISTI EMERGING (€500-5k):**\n- Cercano: Arte accessibile, talento emergente, potential upside\n- Linguaggio: Bilanciato tecnico-emotivo\n- Enfasi: Qualità/prezzo, unicità, discovery\n\n**3. GIOVANI CREATOR/CRYPTO-NATIVE (€50-500):**\n- Cercano: Cool factor, community, flexing\n- Linguaggio: Casual, autentico, trend-aware\n- Enfasi: Style, cultural relevance, limited edition\n\n**4. CORPORATE/PA (€1k-50k):**\n- Cercano: Compliance, certificazione, rappresentanza\n- Linguaggio: Istituzionale, professionale\n- Enfasi: Autenticità, audit trail, valore rappresentativo\n\n**COME TARGETIZZARE:**\n- Pricing coerente con audience\n- Description tono appropriato\n- Traits che attraggono quel segmento\n- Canali promozionali adeguati\n\n**TIP:**\nPuoi chiedere all'AI Art Advisor di riscrivere la tua descrizione per audience specifico!",
                'keywords' => ['target', 'audience', 'pubblico', 'collezionisti', 'mercato'],
                'priority' => 45,
                'locale' => 'it',
            ],

            // ====================================
            // TECHNICAL / TROUBLESHOOTING
            // ====================================
            [
                'section_key' => 'troubleshooting.upload',
                'category' => 'technical',
                'title' => 'Problemi upload immagini',
                'content' => "**ERRORI COMUNI UPLOAD:**\n\n**1. 'File troppo grande':**\n- Limite: 5MB\n- Soluzione: Comprimi immagine con TinyPNG, Squoosh, Photoshop\n- Target: 2-3MB per bilanciare qualità/dimensione\n\n**2. 'Formato non supportato':**\n- Supportati: JPG, PNG, WebP, GIF\n- NON supportati: BMP, TIFF, RAW, PSD\n- Soluzione: Converti in JPG o PNG\n\n**3. 'Upload fallito':**\n- Verifica connessione internet\n- Riprova dopo refresh pagina\n- Prova browser diverso\n- Controlla spazio storage account\n\n**4. 'Immagine sfocata/pixelata':**\n- Risoluzione minima: 1200x1200px\n- Raccomandata: 2400x2400px o superiore\n- Mantieni aspect ratio originale\n\n**BEST PRACTICES:**\n✅ Formato: JPG per foto, PNG per grafica/trasparenze\n✅ Risoluzione: 2400x2400px (square) o 2400x3000px (portrait)\n✅ Dimensione: 2-4MB\n✅ Color space: sRGB (per web)\n✅ Compressione: 85-90% quality",
                'keywords' => ['upload', 'immagine', 'errore', 'problema', 'file', 'dimensione'],
                'priority' => 170,
                'locale' => 'it',
            ],

            [
                'section_key' => 'security.account',
                'category' => 'security',
                'title' => 'Sicurezza account e 2FA',
                'content' => "**PROTEZIONE ACCOUNT:**\n\n**PASSWORD:**\n- Minimo 8 caratteri\n- Raccomandata: 12+ caratteri con maiuscole, numeri, simboli\n- Non riutilizzare password di altri servizi\n- Usa password manager (1Password, Bitwarden, etc.)\n\n**2FA (Two-Factor Authentication):**\n- FORTEMENTE RACCOMANDATO per creator e collector\n- Abilita in: Impostazioni → Sicurezza → 2FA\n- Supporto: Google Authenticator, Authy, 1Password\n- Richiesto per: Visualizzare seed phrase, modifiche wallet, withdrawals\n\n**SESSIONI:**\n- Auto-logout dopo 24h inattività\n- Puoi vedere sessioni attive in Impostazioni\n- Logout remoto disponibile\n\n**GDPR COMPLIANCE:**\n- Tutti gli accessi loggati (audit trail)\n- IP tracking per sicurezza\n- Notifiche login da nuovi device\n- Export dati personali su richiesta\n\n**SE ACCOUNT COMPROMESSO:**\n1. Reset password immediato\n2. Logout tutte le sessioni\n3. Abilita 2FA\n4. Verifica transazioni recenti\n5. Contatta support se movimenti sospetti",
                'keywords' => ['sicurezza', 'security', '2fa', 'password', 'account', 'protezione'],
                'priority' => 180,
                'locale' => 'it',
            ],

            // ====================================
            // ADVANCED FEATURES
            // ====================================
            [
                'section_key' => 'advanced.smartcontracts',
                'category' => 'advanced',
                'title' => 'EGI Viventi (SmartContract NFT)',
                'content' => "**EGI VIVENTE (Living EGI):**\n\nNFT programmabile che può evolvere nel tempo.\n\n**DIFFERENZA vs EGI CLASSICO:**\n\n**EGI Classico (ASA):**\n- Statico, immutabile\n- Metadata fissi\n- Semplice ownership transfer\n\n**EGI Vivente (SmartContract):**\n- Logica programmabile on-chain\n- Può cambiare nel tempo (rules-based)\n- Utility unlockable\n- Royalties dinamiche\n\n**USE CASES EGI VIVENTE:**\n\n1. **Arte Generativa:**\n   - Artwork evolve ogni X giorni\n   - Traits cambiano based on on-chain events\n\n2. **Membership NFT:**\n   - Sblocca accessi premium\n   - Livelli crescenti (bronze → silver → gold)\n\n3. **Gamification:**\n   - Artwork guadagna XP\n   - Achievement unlockables\n\n4. **Royalties Dinamiche:**\n   - Royalty % cambia nel tempo\n   - Bonus per ownership long-term\n\n**COSTI:**\n- Deploy: ~1-2 ALGO\n- Interazioni: ~0.001 ALGO/tx\n\n**QUANDO USARE:**\nSe la tua opera ha 'evoluzione' concettuale o vuoi aggiungere utility nel tempo. Altrimenti, EGI Classico è sufficiente.",
                'keywords' => ['smart contract', 'vivente', 'living', 'programmabile', 'evoluzione'],
                'priority' => 190,
                'locale' => 'it',
            ],

            [
                'section_key' => 'advanced.ipfs',
                'category' => 'advanced',
                'title' => 'Storage IPFS e permanenza dati',
                'content' => "**IPFS (InterPlanetary File System):**\n\nStorage decentralizzato per immagini e metadata NFT.\n\n**COME FUNZIONA:**\n1. Carichi immagine su FlorenceEGI\n2. Al mint, immagine caricata su IPFS\n3. Ricevi CID (Content Identifier) univoco\n4. CID salvato on-chain nel NFT\n5. Immagine accessibile forever via IPFS network\n\n**VANTAGGI:**\n✅ Decentralizzato (no single point of failure)\n✅ Permanente (content-addressed, non location-based)\n✅ Verificabile (CID = hash contenuto)\n✅ Censorship-resistant\n\n**PINNING:**\nFlorenceEGI 'pinna' (mantiene online) le tue immagini su:\n- Nodi IPFS piattaforma\n- Pinata (backup service)\n- Garantiamo availability anche se IPFS network cambia\n\n**METADATA:**\nAnche metadata NFT (title, description, traits) sono su IPFS.\nIl NFT on-chain contiene solo:\n- IPFS CID del metadata JSON\n- IPFS CID dell'immagine\n\n**PERMANENZA GARANTITA:**\n- Finché FlorenceEGI attivo: pinning garantito\n- Anche se piattaforma chiude: IPFS pubblico mantiene dati\n- Puoi pin tu stesso il CID per ridondanza",
                'keywords' => ['ipfs', 'storage', 'permanenza', 'decentralizzato', 'cid'],
                'priority' => 200,
                'locale' => 'it',
            ],

            // ====================================
            // MONETIZATION
            // ====================================
            [
                'section_key' => 'monetization.royalties',
                'category' => 'monetization',
                'title' => 'Come funzionano le royalties',
                'content' => "**ROYALTIES CREATOR:**\n\nRicevi % su OGNI rivendita futura del tuo EGI.\n\n**CONFIGURAZIONE:**\n1. Imposti % royalty a livello Collection (5-10%)\n2. Royalty salvata on-chain nel NFT\n3. Automaticamente applicata su ogni vendita secondaria\n4. Pagata SEMPRE, non bypassabile\n\n**ESEMPIO:**\n- Crei EGI, vendi a €1000 (ricevi €975 dopo fee)\n- Acquirente A rivende a B per €2000\n  → Tu ricevi: €200 (10% royalty)\n  → A riceve: ~€1750 (dopo royalty + fee)\n- B rivende a C per €3000\n  → Tu ricevi: €300 (10% royalty)\n  → B riceve: ~€2625\n\n**LIFETIME VALUE:**\nSe la tua opera viene scambiata 10 volte, ricevi royalty ogni volta.\nAlcuni artist NFT hanno guadagnato più in royalties che in vendita primaria!\n\n**SMART CONTRACT ENFORCEMENT:**\n- Algorand enforza royalties a livello protocollo\n- Impossibile bypassare\n- Trasparente e verificabile\n- Pagamento automatico (no manual claims)\n\n**QUANTO IMPOSTARE:**\n- Standard market: 5-10%\n- Troppo alto (>15%): scoraggia trading\n- Troppo basso (<5%): lasci valore sul tavolo\n- Raccomandato: 7-10%",
                'keywords' => ['royalties', 'royalty', 'guadagno', 'rivendita', 'percentuale'],
                'priority' => 55,
                'locale' => 'it',
            ],

            [
                'section_key' => 'monetization.promotion',
                'category' => 'monetization',
                'title' => 'Promuovere i tuoi EGI',
                'content' => "**STRATEGIE PROMOZIONE:**\n\n**ON-PLATFORM:**\n1. **Descrizione SEO-optimized** - Keywords rilevanti\n2. **Traits completi** - Migliora discoverability\n3. **Preview accattivante** - Prima impressione conta\n4. **Pricing strategico** - Testa mercato con offerte\n\n**SOCIAL MEDIA:**\n1. **Twitter/X** - Condividi WIP, behind-the-scenes, mint announcement\n2. **Instagram** - Visual showcase, process shots\n3. **Discord/Telegram** - Build community, engage collectors\n4. **LinkedIn** (se art professionale/corporate)\n\n**CONTENT MARKETING:**\n- Blog post sul processo creativo\n- Video timelapse creazione\n- Artist statement\n- Interviste e collaborazioni\n\n**COMMUNITY BUILDING:**\n- Partecipa ad altri drop (supporta community)\n- Commenta e engage con collector\n- Join NFT Twitter Spaces\n- Collabora con altri artist\n\n**PAID PROMOTION:**\n- FlorenceEGI Featured Listings (upcoming)\n- Twitter ads a collector NFT\n- Influencer outreach\n- Gallery partnerships\n\n**ANALISI:**\nMonitora:\n- Views sul tuo EGI\n- Click-through rate\n- Conversion (views → offers)\n- Quali traits attirano più interesse",
                'keywords' => ['promuovere', 'marketing', 'promotion', 'visibilità', 'social'],
                'priority' => 65,
                'locale' => 'it',
            ],

            // ====================================
            // LEGAL & COMPLIANCE
            // ====================================
            [
                'section_key' => 'legal.copyright',
                'category' => 'legal',
                'title' => 'Copyright e diritti intellettuali',
                'content' => "**COPYRIGHT E NFT:**\n\n**COSA VENDI CON UN NFT:**\n- Certificato di autenticità digitale\n- Ownership del TOKEN (non necessariamente copyright)\n- Diritto di rivendere il NFT\n\n**COSA NON VENDI (default):**\n- Copyright dell'opera (rimane al creator)\n- Diritti di riproduzione commerciale\n- Diritti di derivative works\n\n**LICENZE OPZIONALI:**\nPuoi specificare nella descrizione:\n- **Personal Use Only** - Acquirente può solo possedere/esporre\n- **Commercial Rights** - Può usare per merch, stampe, etc.\n- **Full IP Transfer** - Vendi anche copyright (raro, premium price)\n\n**PROTEZIONE COPYRIGHT:**\n✅ FlorenceEGI non rimuove tuo copyright\n✅ Metadata on-chain provano paternità e timestamp\n✅ Certificato CoA include copyright notice\n\n**SE QUALCUNO COPIA:**\n- Blockchain timestamp prova prior art\n- DMCA takedown disponibile\n- Legal action supportata da tracciabilità on-chain\n\n**ATTENZIONE:**\nNON mintare opere di cui non possiedi diritti.\nNON usare immagini/elementi protetti da copyright altrui.",
                'keywords' => ['copyright', 'diritti', 'intellettuale', 'proprietà', 'legale'],
                'priority' => 210,
                'locale' => 'it',
            ],

            [
                'section_key' => 'legal.gdpr',
                'category' => 'legal',
                'title' => 'GDPR e Privacy su FlorenceEGI',
                'content' => "**GDPR COMPLIANCE:**\n\nFlorenceEGI è 100% GDPR compliant.\n\n**I TUOI DIRITTI:**\n1. **Diritto di accesso** - Vedi tutti i tuoi dati\n2. **Diritto di portabilità** - Export dati in JSON\n3. **Diritto di rettifica** - Correggi dati errati\n4. **Diritto di cancellazione** - Elimina account\n5. **Diritto di limitazione** - Sospendi processing\n6. **Diritto di opposizione** - Opt-out marketing\n\n**DATI RACCOLTI:**\n- Account: Email, nome, password (criptata)\n- Wallet: Algorand address (pubblico), seed (criptata KMS)\n- Usage: IP, browser, azioni (audit trail GDPR)\n- Artwork: Immagini, metadata (pubblici se published)\n\n**DATI NON RACCOLTI:**\n❌ Dati sensibili (razza, religione, orientamento)\n❌ Dati finanziari dettagliati (solo totali transazioni)\n❌ Biometria\n❌ Tracking invasivo\n\n**CONSENSI:**\nGestisci consensi in: Impostazioni → Privacy\n- Essential: Necessari per funzionamento (obbligatori)\n- Marketing: Newsletter, promozioni (opzionale)\n- Analytics: Miglioramento UX (opzionale)\n\n**EXPORT DATI:**\nImpostazioni → Privacy → Export My Data\nRicevi ZIP con tutti i dati in 24h.",
                'keywords' => ['gdpr', 'privacy', 'dati', 'personali', 'protezione', 'consensi'],
                'priority' => 220,
                'locale' => 'it',
            ],
        ];

        // Insert all sections
        foreach ($sections as $section) {
            PlatformKnowledgeSection::updateOrCreate(
                ['section_key' => $section['section_key'], 'locale' => $section['locale']],
                $section
            );
        }

        $this->command->info('✅ Created ' . count($sections) . ' platform knowledge sections (IT locale)');
    }
}
