<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('payment.popup_return_title') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #0a0a0f;
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }

        .card {
            background: #111827;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            max-width: 380px;
            width: 100%;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        h2 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #a5b4fc;
        }

        p {
            font-size: 0.875rem;
            color: #9ca3af;
            line-height: 1.5;
        }

        .dot {
            display: inline-block;
            animation: blink 1s infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes blink {

            0%,
            80%,
            100% {
                opacity: 0;
            }

            40% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">✅</div>
        <h2>{{ __('payment.popup_return_heading') }}</h2>
        <p>
            {{ __('payment.popup_return_closing') }}
            <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </p>
    </div>
    <script>
        (function() {
            try {
                if (window.opener && typeof window.opener.stripeOnboardingComplete === 'function') {
                    window.opener.stripeOnboardingComplete();
                }
            } catch (e) {
                // cross-origin safety — ignore
            }
            setTimeout(function() {
                window.close();
            }, 1800);
        })();
    </script>
</body>

</html>
