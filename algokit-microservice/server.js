const express = require("express");
const cors = require("cors");
const algosdk = require("algosdk");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

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
const treasuryMnemonic =
    process.env.TREASURY_MNEMONIC ||
    "misery earn nose palace make together enhance february parade agent oxygen farm ghost canoe forum robot cube office ball energy split annual buddy above absent";
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

// Start server
app.listen(PORT, () => {
    console.log(`🔥 REAL BLOCKCHAIN MICROSERVICE running on port ${PORT}`);
    console.log(`📍 Treasury: ${treasuryAccount.addr}`);
    console.log(`🌐 Algorand Sandbox: ${algodServer}:${algodPort}`);
    console.log("🚨 REAL BLOCKCHAIN MODE - NO MOCK!");
});
