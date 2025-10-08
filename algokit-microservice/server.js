const express = require("express");
const cors = require("cors");
const algosdk = require("algosdk");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// Algorand Sandbox configuration
const algodToken =
    "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
const algodServer = "http://localhost";
const algodPort = 4001;

const algodClient = new algosdk.Algodv2(algodToken, algodServer, algodPort);

// Treasury account - REAL generated mnemonic
const treasuryMnemonic =
    "misery earn nose palace make together enhance february parade agent oxygen farm ghost canoe forum robot cube office ball energy split annual buddy above absent";
const treasuryAccount = algosdk.mnemonicToSecretKey(treasuryMnemonic);

console.log("🚀 REAL BLOCKCHAIN MICROSERVICE STARTING...");
console.log("📍 Treasury Address:", treasuryAccount.addr);

// Health check endpoint
app.get("/health", async (req, res) => {
    try {
        const status = await algodClient.status().do();
        res.json({
            status: "healthy",
            network: "sandbox",
            round: status["last-round"],
            treasury: treasuryAccount.addr,
            mode: "REAL_BLOCKCHAIN",
        });
    } catch (error) {
        res.status(500).json({
            status: "unhealthy",
            error: error.message,
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
        const asaNote = new Uint8Array(Buffer.from(`EGI-${egi_id}-${Date.now()}`));
        const asaName = `EGI-${metadata.title || "Unknown"}`.substring(0, 32);
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

// Start server
app.listen(PORT, () => {
    console.log(`🔥 REAL BLOCKCHAIN MICROSERVICE running on port ${PORT}`);
    console.log(`📍 Treasury: ${treasuryAccount.addr}`);
    console.log(`🌐 Algorand Sandbox: ${algodServer}:${algodPort}`);
    console.log("🚨 REAL BLOCKCHAIN MODE - NO MOCK!");
});
