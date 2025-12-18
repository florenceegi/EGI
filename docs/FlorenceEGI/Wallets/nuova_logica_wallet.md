Obiettivo

Sostituire la generazione di address “fake” con la creazione reale di:

Wallet Algorand (address + mnemonic / private key)

Wallet IBAN (cifrato nel DB, hash per ricerca)
Il service sarà usato in fase di registrazione (dipende da usertype) e per la creazione dei wallet “piattaforma” già esistenti (Natan, EPP, Frangette sono già configurati).

Scelte operative (raccomandate)

Creare il wallet Algorand reale sul backend usando Algorand SDK (consigliato: algosdk → Node.js) e non nel browser.

Custodire la mnemonic con Envelope Encryption (DEK per-utente cifrata da KEK in KMS). Vedi lo schema CustodialWalletService che abbiamo già abbozzato.

IBAN: cifrato con cast encrypted Laravel (o AES app-layer) + iban_hash per ricerca/univocità + iban_last4 per UI.

Password wallet: offri 2 opzioni nello UX:

opz. A (convenience): usa la stessa password dell’account (meno friction) — consiglio: non default.

opz. B (recommended): chiedi una passphrase wallet separata (obbligatoria se user vuole export).

Se l’utente non sceglie passphrase separata, imposta l’export come possibile solo dopo creazione passphrase e step-up auth.

Requisiti IBAN in form: IBAN required prima di submit per gli usertype che ricevono payout (Creator, Mecenate, EPP, Company). Per Collector/Visitor non obbligatorio.

Consenso: checkbox obbligatoria accept_custody_seed + info GDPR.

2FA/Step-up: abilitare 2FA al primo export di mnemonic.

Flusso UX (registrazione)

Utente compila form registrazione + sceglie usertype.

Form include se necessario il campo iban (validazione client+server) e campo opzionale wallet_passphrase.

Submit → server valida (incluso checksum IBAN) e prima di creare persistente:

crea il wallet Algorand (address + mnemonic) sul server

cifra mnemonic con DEK e DEK con KMS

salva record user_wallets (type='algorand') e (if present) iban_encrypted in user_wallets

crea la default collection e associa i wallet di piattaforma (già esistenti)

Render su success: mostra address (public) e conferma salvataggio; NON mostra la mnemonic in chiaro. Se l’utente ha scelto la wallet_passphrase esegui export cifrato immediato o fornisci file cifrato scaricabile.

Se utente vuole visualizzare mnemonic: step-up (login again + 2FA + export passphrase) → audit log.

DB: migrazioni essenziali

Aggiungi/aggiorna user_wallets come già descritto — qui richiamiamo i campi necessari:

// migration: create_user_wallets_table.php
Schema::create('user_wallets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['algorand','iban'])->index();
    $table->string('address')->nullable(); // algorand address public
    $table->binary('secret_ciphertext')->nullable(); // mnemonic cifrata (Algorand)
    $table->binary('secret_nonce')->nullable();
    $table->binary('secret_tag')->nullable(); // opzionale per AES-GCM
    $table->binary('dek_encrypted')->nullable();
    $table->text('iban_encrypted')->nullable();
    $table->string('iban_hash', 64)->nullable()->index();
    $table->string('iban_last4', 8)->nullable();
    $table->json('meta')->nullable();
    $table->string('cipher_algo',32)->nullable();
    $table->integer('version')->default(1);
    $table->timestamps();
    $table->softDeletes();
});

Service: WalletProvisioningService (diagramma e responsabilità)

Responsabilità:

generare account Algorand

cifrare mnemonic (envelope)

memorizzare address e IBAN cifrato

associare wallet alla collection di default

log & audit

Dipendenze:

KmsClient (astrazione)

UltraLogManager, AuditLogService, ConsentService, ErrorManager

AlgorandClient (adattatore per chiamare algosdk service o microservice Node)

Esempio implementazione (Laravel service skeleton)
1) Microservizio Node.js (algokit-microservice)

Il microservizio espone endpoint protetti per creazione account e firma transazioni.
Richiede autenticazione tramite **Bearer Token**.

Header richiesto: `Authorization: Bearer <ALGOKIT_API_TOKEN>`
2) WalletProvisioningService::provisionUserWallet(User $u, array $payload)

(Estratto PHP — integra con il CustodialWalletService già definito)

public function provisionUserWallet(User $user, array $data): UserWallet
{
    // 1. log start
    $this->log->info('wallet.provision.start', ['user_id'=>$user->id]);

    // 2. call algorand generator (Node microservice or internal)
    $algResp = $this->algorandClient->createAccount(); // returns ['address','mnemonic']
    $address = $algResp['address'];
    $mnemonic = $algResp['mnemonic'];

    // 3. Store mnemonic securely (envelope encryption)
    $dek = random_bytes(32);
    $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

    $cipher = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($mnemonic, '', $nonce, $dek);
    $dekEnc = $this->kms->encrypt($dek);
    sodium_memzero($dek);

    // 4. create wallet record (algorand)
    $wallet = UserWallet::create([
        'user_id' => $user->id,
        'type' => 'algorand',
        'address' => $address,
        'secret_ciphertext' => $cipher,
        'secret_nonce' => $nonce,
        'dek_encrypted' => $dekEnc,
        'cipher_algo' => 'xchacha20',
    ]);

    // 5. If IBAN supplied and required
    if (!empty($data['iban'])) {
        $ibanNorm = strtoupper(preg_replace('/\s+/', '', $data['iban']));
        // validate IBAN here...
        $walletIban = UserWallet::create([
            'user_id'=>$user->id,
            'type'=>'iban',
            'iban_encrypted'=>$ibanNorm, // cast encrypted in model
            'iban_hash'=>hash('sha256',$ibanNorm . config('app.iban_pepper')),
            'iban_last4'=>substr($ibanNorm,-4),
            'meta'=>['added_via'=>'registration']
        ]);
    }

    // 6. link default collection & platform wallets (existing code)
    $this->collectionManager->assignDefaultCollection($user, $wallet);

    // 7. audit + logs
    $this->audit->logActivity($user, GdprActivityCategory::WALLET_CREATED, 'Algorand wallet provisioned');
    $this->log->info('wallet.provision.ok', ['user_id'=>$user->id,'wallet_id'=>$wallet->id]);

    // 8. wipe mnemonic
    sodium_memzero($mnemonic);

    return $wallet;
}

Form di registrazione: regole e validazione (RegisterController)

Validations (server)

$rules = [
  'name'=>'required|string',
  'email'=>'required|email|unique:users',
  'password'=>'required|min:10|confirmed',
  'usertype'=>'required|in:creator,mecenate,epp,collector,company',
  'iban' => ['nullable','string','min:15','max:34', new ValidIban()],
  'wallet_passphrase' => ['nullable','min:12'], // suggerito 16+
  'accept_custody_seed' => ['required_if:needs_iban,true','accepted'],
];


Mapping usertype → needs_iban:

creator, mecenate, epp, company → true

others → false

Front-end: prima di submit, se needs_iban mostra campo IBAN required e tooltip spiegazione. Se manca, blocca submit client-side e server-side.

Password wallet: policy e comportamento

Opzione consigliata: chiedi una wallet_passphrase separata e consigliala fortemente.

Fallback: se l’utente non la fornisce, puoi:

non permettere export della mnemonic finché non definisce una passphrase, oppure

offrire reuse della account password solo dopo conferma esplicita (checkbox “uso la stessa password” + avviso rischio).

Derivazione: quando crei blob export per l’utente, deriva la chiave con Argon2id (salt random) — non PBKDF2 — e cifra l’output.

Export / show mnemonic: procedure sicure

Richiedi:

re-login (token revalidato)

2FA TOTP o WebAuthn

inserimento wallet_passphrase (se impostata)

Log + audit: WALLET_SECRET_ACCESSED con motivo

Mostra la mnemonic solo se tutte le condizioni verificate. Suggerisci all’utente di salvarla offline, poi offri tasto “Ho salvato” che disabilita immediatamente l’accesso per un periodo (cooldown).

Test & QA (obbligatori)

Unit test per:

creazione account Algorand mockato

encrypt/decrypt roundtrip via KMS mock

validazione IBAN e hash dedupe

provisioning atomicity (DB transaction)

Integration test:

registrazione completa (with/without iban)

caso di errore KMS (rollback DB)

Pen test: verifica leak di mnemonic nei logs / stack traces.

Checklist rapida per partire (todo immediati)

 Implementare algorandClient (microservice Node + endpoint interno) per createAccount().

 Aggiungere migration user_wallets (vedi snippet).

 Implementare WalletProvisioningService usando CustodialWalletService per envelope encryption.

 Aggiornare RegisterController per chiamare provisionUserWallet() in transaction.

 Frontend: rendere IBAN required condizionale e campo wallet_passphrase visibile.

 Policy/Consent: accept_custody_seed obbligatoria per usertypes che richiedono IBAN.

 Tests: unit + integration + mock KMS.

 Documentazione: aggiungere sezione “Registration wallet provisioning” in WALLET_SECURITY_MODULE.md.