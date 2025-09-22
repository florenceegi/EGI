<?php
// Test currency conversion manually
echo "<!DOCTYPE html>
<html>
<head>
    <title>Currency Test</title>
    <meta name='csrf-token' content='" . csrf_token() . "'>
</head>
<body>
    <h1>Currency Conversion Test</h1>
    <div id='results'></div>

    <script>
        async function testConversion() {
            const amount = 250;

            try {
                // Get EUR rate
                const eurResponse = await fetch('/api/currency/rate/EUR');
                const eurData = await eurResponse.json();

                // Get USD rate
                const usdResponse = await fetch('/api/currency/rate/USD');
                const usdData = await usdResponse.json();

                const eurRate = eurData.data.rate_to_algo;  // 0.231814
                const usdRate = usdData.data.rate_to_algo;  // 0.27084

                // OLD (wrong) logic
                const algoOld = amount / eurRate;
                const convertedOld = algoOld * usdRate;

                // NEW (correct) logic
                const algoNew = amount * eurRate;
                const convertedNew = algoNew / usdRate;

                document.getElementById('results').innerHTML = `
                    <h2>Converting 250 EUR to USD</h2>
                    <p>EUR Rate: ${eurRate} ALGO per 1 EUR</p>
                    <p>USD Rate: ${usdRate} ALGO per 1 USD</p>

                    <h3 style='color: red'>OLD Logic (WRONG):</h3>
                    <p>${amount} / ${eurRate} = ${algoOld . toFixed(6)} ALGO</p>
                    <p>${algoOld . toFixed(6)} * ${usdRate} = ${convertedOld . toFixed(2)} USD ❌</p>

                    <h3 style='color: green'>NEW Logic (CORRECT):</h3>
                    <p>${amount} * ${eurRate} = ${algoNew . toFixed(6)} ALGO</p>
                    <p>${algoNew . toFixed(6)} / ${usdRate} = ${convertedNew . toFixed(2)} USD ✅</p>
                `;
            } catch (error) {
                document.getElementById('results').innerHTML = 'Error: ' + error.message;
            }
        }

        testConversion();
    </script>
</body>
</html>";
