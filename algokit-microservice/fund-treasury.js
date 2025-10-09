const algosdk = require("algosdk");

// Algorand Sandbox connection
const algodToken = "a".repeat(64);
const algodServer = "http://localhost";
const algodPort = 4001;
const algodClient = new algosdk.Algodv2(algodToken, algodServer, algodPort);

// KMD (Key Management Daemon) for sandbox default wallet
const kmdToken = "a".repeat(64);
const kmdServer = "http://localhost";
const kmdPort = 4002;
const kmdClient = new algosdk.Kmd(kmdToken, kmdServer, kmdPort);

// Treasury wallet to fund
const treasuryAddress =
    "Y2IGWQ5ZL2LBSNBQCKFC3QDRJFGXSJGYB5IWSXPDTHTF6UTBJTMEU5LPYE";

async function fundTreasury() {
    try {
        console.log(
            "🏦 Funding Treasury Wallet from Sandbox Default Account...\n"
        );

        // List wallets in kmd
        const wallets = await kmdClient.listWallets();
        const defaultWallet = wallets.wallets.find(
            (w) => w.name === "unencrypted-default-wallet"
        );

        if (!defaultWallet) {
            throw new Error("Default wallet not found in sandbox");
        }

        console.log(`� Found wallet: ${defaultWallet.name}`);

        // Get wallet handle
        const walletHandle = await kmdClient.initWalletHandle(
            defaultWallet.id,
            ""
        );

        // List keys in wallet
        const addresses = await kmdClient.listKeys(
            walletHandle.wallet_handle_token
        );
        const dispenserAddress = addresses.addresses[0]; // First account is pre-funded

        console.log(`💰 Dispenser Account: ${dispenserAddress}`);

        // Check dispenser balance
        const dispenserInfo = await algodClient
            .accountInformation(dispenserAddress)
            .do();
        console.log(`   Balance: ${dispenserInfo.amount / 1000000} ALGO\n`);

        // Get suggested params
        const params = await algodClient.getTransactionParams().do();

        // Create payment transaction: 100 ALGO to treasury
        const paymentTxn = algosdk.makePaymentTxnWithSuggestedParamsFromObject({
            from: dispenserAddress,
            to: treasuryAddress,
            amount: 100_000_000, // 100 ALGO
            note: new Uint8Array(Buffer.from("Initial Treasury Funding")),
            suggestedParams: params,
        });

        // Sign transaction using kmd
        console.log("✍️  Signing transaction with kmd...");
        const signedTxn = await kmdClient.signTransaction(
            walletHandle.wallet_handle_token,
            "",
            paymentTxn.toByte()
        );

        // Submit transaction
        console.log("📡 Sending 100 ALGO to Treasury...");
        const txResponse = await algodClient
            .sendRawTransaction(signedTxn.sig)
            .do();
        console.log(`✅ Transaction ID: ${txResponse.txId}\n`);

        // Wait for confirmation
        console.log("⏳ Waiting for confirmation...");
        const confirmedTxn = await algosdk.waitForConfirmation(
            algodClient,
            txResponse.txId,
            4
        );
        console.log(
            `✅ Confirmed in round: ${confirmedTxn["confirmed-round"]}\n`
        );

        // Check treasury balance
        const treasuryInfo = await algodClient
            .accountInformation(treasuryAddress)
            .do();
        console.log(
            `🎉 Treasury Balance: ${treasuryInfo.amount / 1000000} ALGO`
        );

        // Release wallet handle
        await kmdClient.releaseWalletHandle(walletHandle.wallet_handle_token);
    } catch (error) {
        console.error("❌ Error funding treasury:", error.message);
        console.error(error);
        process.exit(1);
    }
}

fundTreasury();
