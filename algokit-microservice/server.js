const express = require("express");
const cors = require("cors");
const algosdk = require("algosdk");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());

// SECURITY: Authentication Middleware
// Protects all routes except health checks
const apiToken = process.env.ALGOKIT_API_TOKEN;
const authMiddleware = (req, res, next) => {
    // Skip auth for health/root
    if (req.path === '/' || req.path === '/health') return next();

    const authHeader = req.headers['authorization'];
    
    // Strict token check
    if (!apiToken || !authHeader || authHeader !== `Bearer ${apiToken}`) {
        console.warn(`[SECURITY] Unauthorized access attempt from ${req.ip} to ${req.path}`);
        return res.status(401).json({ 
            success: false, 
            error: 'Unauthorized: Invalid or missing API Token' 
        });
    }
    next();
};

app.use(authMiddleware);

// ========================================
// DUAL MODE CONFIGURATION
// ========================================
const NETWORK_MODE = process.env.ALGORAND_NETWORK || "sandbox"; // sandbox | testnet | mainnet

let algodToken, algodServer, algodPort;

if (NETWORK_MODE === "testnet") {
    // TestNet configuration (Public API)
    algodToken = ""; // Public node doesn't require token
    algodServer = "https://testnet-api.algonode.cloud";
    algodPort = 443;
    console.log("🌐 MODE: TESTNET (Public API)");
} else if (NETWORK_MODE === "mainnet") {
    // MainNet configuration (Public API)
    algodToken = ""; // Public node doesn't require token
    algodServer = "https://mainnet-api.algonode.cloud";
    algodPort = 443;
    console.log("🌐 MODE: MAINNET (Public API)");
} else {
    // Sandbox configuration (Local Docker)
    algodToken =
        "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
    algodServer = "http://localhost";
    algodPort = 4001;
    console.log("🌐 MODE: SANDBOX (Local Docker)");
}

const algodClient = new algosdk.Algodv2(algodToken, algodServer, algodPort);

// Treasury account - REAL generated mnemonic
// IMPORTANT: In production, store mnemonic in secure vault (AWS Secrets Manager, HashiCorp Vault)
// IMPORTANT: In production, store mnemonic in secure vault (AWS Secrets Manager, HashiCorp Vault)
const treasuryMnemonic = process.env.ALGORAND_TREASURY_MNEMONIC;

if (!treasuryMnemonic) {
    console.error("❌ CRITICAL ERROR: ALGORAND_TREASURY_MNEMONIC environment variable is missing!");
    process.exit(1);
}

const treasuryAccount = algosdk.mnemonicToSecretKey(treasuryMnemonic);

console.log("🚀 REAL BLOCKCHAIN MICROSERVICE STARTING...");
console.log("📍 Network Mode:", NETWORK_MODE.toUpperCase());
console.log("📍 Algod Server:", algodServer);
console.log("📍 Treasury Address:", treasuryAccount.addr);

// Health check endpoint
app.get("/health", async (req, res) => {
    try {
        const status = await algodClient.status().do();
        const accountInfo = await algodClient
            .accountInformation(treasuryAccount.addr)
            .do();

        res.json({
            status: "healthy",
            network: NETWORK_MODE,
            round: status["last-round"],
            treasury: {
                address: treasuryAccount.addr,
                balance: accountInfo.amount / 1000000, // Convert microAlgos to Algos
                assets: accountInfo.assets ? accountInfo.assets.length : 0,
            },
            algod_server: algodServer,
            mode: "REAL_BLOCKCHAIN",
        });
    } catch (error) {
        res.status(500).json({
            status: "unhealthy",
            network: NETWORK_MODE,
            error: error.message,
            algod_server: algodServer,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// REAL EGI MINT ENDPOINT - NO MOCK
app.post("/mint-egi-token", async (req, res) => {
    try {
        console.log("🔥 REAL BLOCKCHAIN MINT REQUEST:", req.body);

        const { egi_id, metadata } = req.body;

        if (!egi_id || !metadata) {
            return res.status(400).json({
                success: false,
                error: "Missing egi_id or metadata",
            });
        }

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create ASA configuration
        const asaNote = new Uint8Array(
            Buffer.from(`EGI-${egi_id}-${Date.now()}`)
        );
        // Fix: metadata.title might be array/object - extract string safely
        const titleStr =
            typeof metadata.title === "string"
                ? metadata.title
                : Array.isArray(metadata.title)
                ? metadata.title[0]
                : "Unknown";
        const asaName = `EGI-${titleStr || "Unknown"}`.substring(0, 32);
        const unitName = `EGI${egi_id}`.substring(0, 8);

        // Create ASA transaction
        const txn = algosdk.makeAssetCreateTxnWithSuggestedParams(
            treasuryAccount.addr, // creator
            asaNote, // note (Uint8Array)
            1, // total supply
            0, // decimals
            false, // default frozen
            treasuryAccount.addr, // manager
            treasuryAccount.addr, // reserve
            treasuryAccount.addr, // freeze
            treasuryAccount.addr, // clawback
            unitName, // unit name
            asaName, // asset name
            metadata.image_url || "", // URL
            undefined, // metadata hash
            params // suggested params
        );

        // Sign transaction
        const signedTxn = txn.signTxn(treasuryAccount.sk);

        // Submit to REAL blockchain
        console.log("📡 Submitting to REAL Algorand blockchain...");
        const txResponse = await algodClient.sendRawTransaction(signedTxn).do();

        // Wait for confirmation
        console.log("⏳ Waiting for blockchain confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        const asaId = confirmation["asset-index"];

        console.log("✅ REAL BLOCKCHAIN MINT SUCCESS!");
        console.log("   ASA ID:", asaId);
        console.log("   TX ID:", txResponse.txId);
        console.log("   Round:", confirmation["confirmed-round"]);

        res.json({
            success: true,
            data: {
                asaId: asaId,
                txId: txResponse.txId,
                treasury_address: treasuryAccount.addr,
                certificate_number: `EGI-${egi_id}-${asaId}`,
                asset_url: metadata.image_url || "",
                blockchain_round: confirmation["confirmed-round"],
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error("❌ REAL BLOCKCHAIN MINT FAILED:", error);

        res.status(500).json({
            success: false,
            error: error.message,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// ANCHOR DOCUMENT ENDPOINT - N.A.T.A.N. PA Acts
app.post("/anchor-document", async (req, res) => {
    try {
        console.log("🔐 DOCUMENT ANCHORING REQUEST:", req.body);

        const { document_hash, note, metadata } = req.body;

        if (!document_hash) {
            return res.status(400).json({
                success: false,
                error: "Missing document_hash",
            });
        }

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create payment transaction with note containing document hash
        // Note: We use a minimal payment (0 ALGO) just to anchor the hash
        const noteData =
            note ||
            JSON.stringify({
                type: "document_anchor",
                hash: document_hash,
                protocol: metadata?.protocol_number,
                timestamp: new Date().toISOString(),
            });

        const txn = algosdk.makePaymentTxnWithSuggestedParams(
            treasuryAccount.addr, // from (treasury)
            treasuryAccount.addr, // to (self)
            0, // amount (0 ALGO)
            undefined, // close remainder to
            new Uint8Array(Buffer.from(noteData)), // note with document hash
            params // suggested params
        );

        // Sign transaction
        const signedTxn = txn.signTxn(treasuryAccount.sk);

        // Submit to REAL blockchain
        console.log("📡 Submitting document anchor to blockchain...");
        const txResponse = await algodClient.sendRawTransaction(signedTxn).do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ DOCUMENT ANCHORED ON BLOCKCHAIN!");
        console.log("   TX ID:", txResponse.txId);
        console.log("   Round:", confirmation["confirmed-round"]);
        console.log("   Hash:", document_hash.substring(0, 16) + "...");

        res.json({
            success: true,
            data: {
                txid: txResponse.txId,
                block: confirmation["confirmed-round"],
                network: "algorand-sandbox",
                hash: document_hash,
                timestamp: new Date().toISOString(),
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error("❌ DOCUMENT ANCHORING FAILED:", error);

        res.status(500).json({
            success: false,
            error: error.message,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// CREATE NEW ALGORAND ACCOUNT ENDPOINT
app.post("/create-account", async (req, res) => {
    try {
        console.log("🔐 CREATE ACCOUNT REQUEST");

        // Generate new account using algosdk
        const account = algosdk.generateAccount();
        const mnemonic = algosdk.secretKeyToMnemonic(account.sk);

        console.log("✅ NEW ACCOUNT GENERATED");
        console.log("   Address:", account.addr);

        res.json({
            success: true,
            data: {
                address: account.addr,
                mnemonic: mnemonic,
                privateKeyBase64: Buffer.from(account.sk).toString("base64"),
            },
        });
    } catch (error) {
        console.error("❌ CREATE ACCOUNT FAILED:", error);

        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

// FUND WALLET ENDPOINT - Transfer ALGO from Treasury to new user wallet
// Enables opt-in capability for ASAs
app.post("/fund-wallet", async (req, res) => {
    try {
        const { address, amount } = req.body;

        // Validate address
        if (!address || address.length !== 58) {
            return res.status(400).json({
                success: false,
                error: "Invalid Algorand address (must be 58 characters)",
            });
        }

        // Default amount: 0.3 ALGO (300000 microAlgos)
        // - 0.1 ALGO minimum balance
        // - 0.1 ALGO per ASA opt-in
        // - 0.1 ALGO margin for transaction fees
        const amountMicroAlgos = amount || 300000;

        console.log(`💰 FUND WALLET REQUEST: ${address}`);
        console.log(`   Amount: ${amountMicroAlgos / 1000000} ALGO`);

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create payment transaction from Treasury to new wallet
        const txn = algosdk.makePaymentTxnWithSuggestedParams(
            treasuryAccount.addr, // from (Treasury)
            address, // to (new user wallet)
            amountMicroAlgos, // amount in microAlgos
            undefined, // close remainder to
            new Uint8Array(Buffer.from(`EGI-WALLET-FUND-${Date.now()}`)), // note
            params // suggested params
        );

        // Sign transaction
        const signedTxn = txn.signTxn(treasuryAccount.sk);

        // Submit to blockchain
        console.log("📡 Submitting fund transaction to blockchain...");
        const txResponse = await algodClient.sendRawTransaction(signedTxn).do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ WALLET FUNDED SUCCESSFULLY!");
        console.log("   TX ID:", txResponse.txId);
        console.log("   Round:", confirmation["confirmed-round"]);
        console.log("   Amount:", amountMicroAlgos / 1000000, "ALGO");

        res.json({
            success: true,
            data: {
                txId: txResponse.txId,
                address: address,
                amount: amountMicroAlgos,
                amount_algo: amountMicroAlgos / 1000000,
                from: treasuryAccount.addr,
                block: confirmation["confirmed-round"],
                network: NETWORK_MODE,
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error(`❌ FUND WALLET FAILED: ${req.body.address}`, error);

        res.status(500).json({
            success: false,
            error: error.message,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// Get account information endpoint
app.get("/account/:address", async (req, res) => {
    try {
        const { address } = req.params;

        if (!address || address.length !== 58) {
            return res.status(400).json({
                success: false,
                error: "Invalid Algorand address (must be 58 characters)",
            });
        }

        console.log(`📊 ACCOUNT INFO REQUEST: ${address}`);

        // Get account information from Algorand network
        const accountInfo = await algodClient.accountInformation(address).do();

        console.log(`✅ ACCOUNT INFO RETRIEVED: ${address}`, {
            balance: accountInfo.amount,
            assets: accountInfo.assets ? accountInfo.assets.length : 0,
        });

        res.json({
            success: true,
            data: {
                address: accountInfo.address,
                amount: accountInfo.amount, // microAlgos
                assets: accountInfo.assets || [],
                created_assets: accountInfo["created-assets"] || [],
                min_balance: accountInfo["min-balance"],
                status: accountInfo.status,
            },
        });
    } catch (error) {
        console.error(`❌ ACCOUNT INFO FAILED: ${req.params.address}`, error);

        // Handle account not found (404) vs other errors (500)
        if (error.message && error.message.includes("account does not exist")) {
            return res.status(404).json({
                success: false,
                error: `Account ${req.params.address} does not exist on ${NETWORK_MODE}`,
            });
        }

        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

// ========================================
// OPT-IN ASA ENDPOINT
// ========================================
// Allows a user wallet to opt-in to receive a specific ASA (EGI)
// REQUIRES: User wallet must have been funded first (min 0.1 ALGO per ASA)
// NOTE: This endpoint requires the user's mnemonic to sign the opt-in transaction
app.post("/opt-in-asa", async (req, res) => {
    try {
        const { user_mnemonic, asa_id } = req.body;

        // Validate inputs
        if (!user_mnemonic) {
            return res.status(400).json({
                success: false,
                error: "Missing user_mnemonic - required to sign opt-in transaction",
            });
        }

        if (!asa_id) {
            return res.status(400).json({
                success: false,
                error: "Missing asa_id - the ASA to opt-in to",
            });
        }

        // Reconstruct user account from mnemonic
        let userAccount;
        try {
            userAccount = algosdk.mnemonicToSecretKey(user_mnemonic);
        } catch (mnemonicError) {
            return res.status(400).json({
                success: false,
                error: "Invalid mnemonic format",
            });
        }

        console.log(`🔑 OPT-IN ASA REQUEST`);
        console.log(`   User Address: ${userAccount.addr}`);
        console.log(`   ASA ID: ${asa_id}`);

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create opt-in transaction (asset transfer of 0 to self)
        // This is the standard Algorand opt-in pattern
        const optInTxn = algosdk.makeAssetTransferTxnWithSuggestedParams(
            userAccount.addr, // from (user)
            userAccount.addr, // to (user - self transfer)
            undefined, // close remainder to
            undefined, // revocation target
            0, // amount (0 for opt-in)
            new Uint8Array(Buffer.from(`EGI-OPT-IN-${asa_id}-${Date.now()}`)), // note
            parseInt(asa_id), // asset index
            params // suggested params
        );

        // Sign with user's private key
        const signedOptInTxn = optInTxn.signTxn(userAccount.sk);

        // Submit to blockchain
        console.log("📡 Submitting opt-in transaction to blockchain...");
        const txResponse = await algodClient
            .sendRawTransaction(signedOptInTxn)
            .do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ OPT-IN SUCCESSFUL!");
        console.log(`   TX ID: ${txResponse.txId}`);
        console.log(`   Round: ${confirmation["confirmed-round"]}`);
        console.log(`   ASA ID: ${asa_id}`);

        res.json({
            success: true,
            data: {
                txId: txResponse.txId,
                user_address: userAccount.addr,
                asa_id: parseInt(asa_id),
                block: confirmation["confirmed-round"],
                network: NETWORK_MODE,
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error(`❌ OPT-IN FAILED:`, error);

        // Handle specific Algorand errors
        let errorMessage = error.message;
        if (error.message && error.message.includes("underflow")) {
            errorMessage =
                "Insufficient ALGO balance for opt-in. Wallet needs at least 0.1 ALGO per ASA.";
        } else if (
            error.message &&
            error.message.includes("asset already in account")
        ) {
            errorMessage = "User has already opted-in to this ASA.";
        }

        res.status(500).json({
            success: false,
            error: errorMessage,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// ========================================
// BATCH OPT-IN ASA ENDPOINT
// ========================================
// Allows a user wallet to opt-in to multiple ASAs in a single atomic transaction group
// More efficient than individual opt-ins for wallet redemption
app.post("/batch-opt-in-asa", async (req, res) => {
    try {
        const { user_mnemonic, asa_ids } = req.body;

        // Validate inputs
        if (!user_mnemonic) {
            return res.status(400).json({
                success: false,
                error: "Missing user_mnemonic",
            });
        }

        if (!asa_ids || !Array.isArray(asa_ids) || asa_ids.length === 0) {
            return res.status(400).json({
                success: false,
                error: "Missing or invalid asa_ids array",
            });
        }

        // Algorand limit: max 16 transactions per atomic group
        if (asa_ids.length > 16) {
            return res.status(400).json({
                success: false,
                error: `Too many ASAs (${asa_ids.length}). Maximum 16 per batch. Split into multiple batches.`,
            });
        }

        // Reconstruct user account from mnemonic
        let userAccount;
        try {
            userAccount = algosdk.mnemonicToSecretKey(user_mnemonic);
        } catch (mnemonicError) {
            return res.status(400).json({
                success: false,
                error: "Invalid mnemonic format",
            });
        }

        console.log(`🔑 BATCH OPT-IN ASA REQUEST`);
        console.log(`   User Address: ${userAccount.addr}`);
        console.log(`   ASA IDs: ${asa_ids.join(", ")}`);
        console.log(`   Count: ${asa_ids.length}`);

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create opt-in transactions for each ASA
        const optInTxns = asa_ids.map((asaId) => {
            return algosdk.makeAssetTransferTxnWithSuggestedParams(
                userAccount.addr,
                userAccount.addr,
                undefined,
                undefined,
                0,
                new Uint8Array(
                    Buffer.from(`EGI-BATCH-OPT-IN-${asaId}-${Date.now()}`)
                ),
                parseInt(asaId),
                params
            );
        });

        // Assign group ID to all transactions (atomic group)
        algosdk.assignGroupID(optInTxns);

        // Sign all transactions
        const signedTxns = optInTxns.map((txn) => txn.signTxn(userAccount.sk));

        // Submit atomic group to blockchain
        console.log(
            `📡 Submitting ${asa_ids.length} opt-in transactions as atomic group...`
        );
        const txResponse = await algodClient
            .sendRawTransaction(signedTxns)
            .do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ BATCH OPT-IN SUCCESSFUL!");
        console.log(`   Group TX ID: ${txResponse.txId}`);
        console.log(`   Round: ${confirmation["confirmed-round"]}`);
        console.log(`   ASAs opted-in: ${asa_ids.length}`);

        res.json({
            success: true,
            data: {
                groupTxId: txResponse.txId,
                user_address: userAccount.addr,
                asa_ids: asa_ids.map((id) => parseInt(id)),
                count: asa_ids.length,
                block: confirmation["confirmed-round"],
                network: NETWORK_MODE,
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error(`❌ BATCH OPT-IN FAILED:`, error);

        let errorMessage = error.message;
        if (error.message && error.message.includes("underflow")) {
            errorMessage =
                "Insufficient ALGO balance. Wallet needs 0.1 ALGO per ASA + transaction fees.";
        }

        res.status(500).json({
            success: false,
            error: errorMessage,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// ========================================
// TRANSFER ASA ENDPOINT
// ========================================
// Transfers an ASA (EGI) from Treasury to a user wallet
// REQUIRES: User must have already opted-in to the ASA
app.post("/transfer-asa", async (req, res) => {
    try {
        const { to_address, asa_id, amount } = req.body;

        // Validate inputs
        if (!to_address || to_address.length !== 58) {
            return res.status(400).json({
                success: false,
                error: "Invalid to_address (must be 58 characters)",
            });
        }

        if (!asa_id) {
            return res.status(400).json({
                success: false,
                error: "Missing asa_id",
            });
        }

        // Default amount is 1 (for NFTs/EGIs)
        const transferAmount = amount || 1;

        console.log(`📤 TRANSFER ASA REQUEST`);
        console.log(`   From: ${treasuryAccount.addr} (Treasury)`);
        console.log(`   To: ${to_address}`);
        console.log(`   ASA ID: ${asa_id}`);
        console.log(`   Amount: ${transferAmount}`);

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create asset transfer transaction from Treasury to user
        const transferTxn = algosdk.makeAssetTransferTxnWithSuggestedParams(
            treasuryAccount.addr, // from (Treasury)
            to_address, // to (user wallet)
            undefined, // close remainder to
            undefined, // revocation target
            transferAmount, // amount
            new Uint8Array(
                Buffer.from(`EGI-TRANSFER-${asa_id}-${Date.now()}`)
            ), // note
            parseInt(asa_id), // asset index
            params // suggested params
        );

        // Sign with Treasury's private key
        const signedTransferTxn = transferTxn.signTxn(treasuryAccount.sk);

        // Submit to blockchain
        console.log("📡 Submitting transfer transaction to blockchain...");
        const txResponse = await algodClient
            .sendRawTransaction(signedTransferTxn)
            .do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ TRANSFER SUCCESSFUL!");
        console.log(`   TX ID: ${txResponse.txId}`);
        console.log(`   Round: ${confirmation["confirmed-round"]}`);

        res.json({
            success: true,
            data: {
                txId: txResponse.txId,
                from: treasuryAccount.addr,
                to: to_address,
                asa_id: parseInt(asa_id),
                amount: transferAmount,
                block: confirmation["confirmed-round"],
                network: NETWORK_MODE,
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error(`❌ TRANSFER FAILED:`, error);

        let errorMessage = error.message;
        if (
            error.message &&
            error.message.includes("asset not found in account")
        ) {
            errorMessage =
                "Recipient has not opted-in to this ASA. Opt-in required before transfer.";
        } else if (
            error.message &&
            error.message.includes("underflow on sender")
        ) {
            errorMessage = "Treasury does not have enough of this ASA.";
        }

        res.status(500).json({
            success: false,
            error: errorMessage,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// ========================================
// BATCH TRANSFER ASA ENDPOINT
// ========================================
// Transfers multiple ASAs from Treasury to a user wallet in atomic group
// More efficient than individual transfers for wallet redemption
app.post("/batch-transfer-asa", async (req, res) => {
    try {
        const { to_address, asa_ids } = req.body;

        // Validate inputs
        if (!to_address || to_address.length !== 58) {
            return res.status(400).json({
                success: false,
                error: "Invalid to_address (must be 58 characters)",
            });
        }

        if (!asa_ids || !Array.isArray(asa_ids) || asa_ids.length === 0) {
            return res.status(400).json({
                success: false,
                error: "Missing or invalid asa_ids array",
            });
        }

        // Algorand limit: max 16 transactions per atomic group
        if (asa_ids.length > 16) {
            return res.status(400).json({
                success: false,
                error: `Too many ASAs (${asa_ids.length}). Maximum 16 per batch.`,
            });
        }

        console.log(`📤 BATCH TRANSFER ASA REQUEST`);
        console.log(`   From: ${treasuryAccount.addr} (Treasury)`);
        console.log(`   To: ${to_address}`);
        console.log(`   ASA IDs: ${asa_ids.join(", ")}`);
        console.log(`   Count: ${asa_ids.length}`);

        // Get network parameters
        const params = await algodClient.getTransactionParams().do();

        // Create transfer transactions for each ASA
        const transferTxns = asa_ids.map((asaId) => {
            return algosdk.makeAssetTransferTxnWithSuggestedParams(
                treasuryAccount.addr,
                to_address,
                undefined,
                undefined,
                1, // amount = 1 for each EGI (NFT)
                new Uint8Array(
                    Buffer.from(`EGI-BATCH-TRANSFER-${asaId}-${Date.now()}`)
                ),
                parseInt(asaId),
                params
            );
        });

        // Assign group ID to all transactions (atomic group)
        algosdk.assignGroupID(transferTxns);

        // Sign all transactions with Treasury key
        const signedTxns = transferTxns.map((txn) =>
            txn.signTxn(treasuryAccount.sk)
        );

        // Submit atomic group to blockchain
        console.log(
            `📡 Submitting ${asa_ids.length} transfer transactions as atomic group...`
        );
        const txResponse = await algodClient
            .sendRawTransaction(signedTxns)
            .do();

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmation = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );

        console.log("✅ BATCH TRANSFER SUCCESSFUL!");
        console.log(`   Group TX ID: ${txResponse.txId}`);
        console.log(`   Round: ${confirmation["confirmed-round"]}`);
        console.log(`   ASAs transferred: ${asa_ids.length}`);

        res.json({
            success: true,
            data: {
                groupTxId: txResponse.txId,
                from: treasuryAccount.addr,
                to: to_address,
                asa_ids: asa_ids.map((id) => parseInt(id)),
                count: asa_ids.length,
                block: confirmation["confirmed-round"],
                network: NETWORK_MODE,
                mode: "REAL_BLOCKCHAIN",
            },
        });
    } catch (error) {
        console.error(`❌ BATCH TRANSFER FAILED:`, error);

        let errorMessage = error.message;
        if (
            error.message &&
            error.message.includes("asset not found in account")
        ) {
            errorMessage =
                "Recipient has not opted-in to one or more ASAs. All opt-ins required before batch transfer.";
        }

        res.status(500).json({
            success: false,
            error: errorMessage,
            mode: "REAL_BLOCKCHAIN",
        });
    }
});

// Start server
app.listen(PORT, () => {
    console.log(`🔥 REAL BLOCKCHAIN MICROSERVICE running on port ${PORT}`);
    console.log(`📍 Treasury: ${treasuryAccount.addr}`);
    console.log(`🌐 Algorand Sandbox: ${algodServer}:${algodPort}`);
    console.log("🚨 REAL BLOCKCHAIN MODE - NO MOCK!");
    console.log("📋 Available endpoints:");
    console.log("   POST /mint-egi-token     - Mint new EGI (ASA)");
    console.log("   POST /anchor-document    - Anchor document hash");
    console.log("   POST /create-account     - Generate new wallet");
    console.log("   POST /fund-wallet        - Fund wallet with ALGO");
    console.log("   POST /opt-in-asa         - Opt-in to single ASA");
    console.log("   POST /batch-opt-in-asa   - Opt-in to multiple ASAs");
    console.log("   POST /transfer-asa       - Transfer single ASA");
    console.log("   POST /batch-transfer-asa - Transfer multiple ASAs");
    console.log("   GET  /account/:address   - Get account info");
    console.log("   GET  /health             - Health check");
});
