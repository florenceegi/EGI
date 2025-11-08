<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('wallet_welcome.title') }} - {{ config('app.name') }}</title>

    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal Overlay */
        .wallet-modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1rem;
            overflow-y: auto;
        }

        .wallet-modal-overlay.hidden {
            display: none;
        }

        /* Modal Container */
        .wallet-modal-container {
            background: #ffffff;
            border-radius: 16px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
        }

        /* Header */
        .wallet-modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 2px solid #e5e7eb;
            background: linear-gradient(135deg, #1B365D 0%, #2d5a8f 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .wallet-modal-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin: 0;
        }

        .wallet-modal-subtitle {
            font-size: 1.125rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        /* Content */
        .wallet-modal-content {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        /* Intro Box */
        .wallet-intro-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .wallet-intro-box p {
            margin: 0;
            line-height: 1.6;
            color: #065f46;
        }

        /* Sections */
        .wallet-sections {
            margin-bottom: 2rem;
        }

        .wallet-section {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .wallet-section-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }

        .wallet-section-header:hover {
            background: #f3f4f6;
        }

        .wallet-section-header[aria-expanded="true"] {
            background: #eff6ff;
            border-bottom: 1px solid #e5e7eb;
        }

        .wallet-section-title {
            font-family: 'Segoe UI', -apple-system, system-ui, BlinkMacSystemFont, 'Helvetica Neue', sans-serif;
            font-size: 1.0625rem;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: -0.02em;
        }

        .wallet-section-icon {
            color: #6b7280;
            transition: transform 0.3s ease;
        }

        .wallet-section-header[aria-expanded="true"] .wallet-section-icon {
            transform: rotate(180deg);
        }

        .wallet-section-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .wallet-section-content.expanded {
            max-height: 2000px;
            padding: 1.5rem;
        }

        /* Lists */
        .wallet-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .wallet-list li {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 0.75rem;
            line-height: 1.6;
            color: #374151;
        }

        .wallet-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: 700;
        }

        .wallet-list-success li::before {
            content: "✅";
        }

        .wallet-list-error li::before {
            content: "❌";
        }

        .wallet-list-sm li {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        /* Subsection Titles */
        .wallet-subsection-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        /* Note Box */
        .wallet-note {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #92400e;
        }

        /* Highlight Box */
        .wallet-highlight-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 1.5rem;
        }

        /* Option Boxes */
        .wallet-option-box {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .wallet-option-simple {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .wallet-option-expert {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .wallet-option-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .wallet-option-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 1rem;
        }

        /* Glossary */
        .wallet-glossary {
            display: grid;
            gap: 1rem;
        }

        .wallet-glossary-item {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.75rem;
        }

        .wallet-glossary-term {
            font-weight: 700;
            color: #047857;
            margin-bottom: 0.25rem;
        }

        .wallet-glossary-definition {
            color: #374151;
            margin: 0;
            line-height: 1.6;
        }

        /* Help Grid */
        .wallet-help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .wallet-help-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .wallet-help-card:hover {
            border-color: #1B365D;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .wallet-help-icon {
            font-size: 2.5rem;
            color: #1B365D;
            margin-bottom: 0.5rem;
        }

        .wallet-help-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .wallet-help-desc {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        /* IBAN Form */
        .wallet-iban-form {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            font-family: 'Segoe UI', -apple-system, system-ui, BlinkMacSystemFont, sans-serif;
        }

        .wallet-form-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.015em;
        }

        .wallet-form-title .material-icons {
            color: #1B365D;
        }

        .wallet-form-description {
            color: #4b5563;
            margin-bottom: 1.5rem;
            line-height: 1.65;
            font-size: 0.9375rem;
        }

        .wallet-form-group {
            margin-bottom: 1rem;
        }

        .wallet-form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
        }

        .wallet-form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9375rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            transition: all 0.2s;
        }

        .wallet-form-input:focus {
            outline: none;
            border-color: #1B365D;
            box-shadow: 0 0 0 3px rgba(27, 54, 93, 0.1);
        }

        .wallet-form-input.error {
            border-color: #ef4444;
        }

        .wallet-form-error {
            display: block;
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .wallet-form-error.hidden {
            display: none;
        }

        .wallet-form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .wallet-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        .wallet-checkbox-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
            cursor: pointer;
            letter-spacing: -0.01em;
        }

        /* Warning Box */
        .wallet-warning-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #f87171;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .wallet-warning-box.hidden {
            display: none;
        }

        .wallet-warning-icon {
            text-align: center;
            margin-bottom: 1rem;
        }

        .wallet-warning-icon .material-icons {
            font-size: 3rem;
            color: #dc2626;
        }

        .wallet-warning-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 1rem;
            text-align: center;
        }

        .wallet-warning-text {
            color: #7f1d1d;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .wallet-warning-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer */
        .wallet-modal-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* Buttons */
        .wallet-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
        }

        .wallet-btn-primary {
            background: #1B365D;
            color: white;
        }

        .wallet-btn-primary:hover {
            background: #152a4a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .wallet-btn-secondary {
            background: #6b7280;
            color: white;
        }

        .wallet-btn-secondary:hover {
            background: #4b5563;
        }

        .wallet-btn-warning {
            background: #dc2626;
            color: white;
        }

        .wallet-btn-warning:hover {
            background: #b91c1c;
        }

        .wallet-btn-ghost {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .wallet-btn-ghost:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .wallet-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Loading Overlay */
        .wallet-modal-loading {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
        }

        .wallet-modal-loading.hidden {
            display: none;
        }

        .wallet-spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid #e5e7eb;
            border-top-color: #1B365D;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .wallet-modal-container {
                max-height: 100vh;
                border-radius: 0;
            }

            .wallet-modal-header,
            .wallet-modal-content,
            .wallet-modal-footer {
                padding: 1.5rem 1rem;
            }

            .wallet-modal-title {
                font-size: 1.5rem;
            }

            .wallet-help-grid {
                grid-template-columns: 1fr;
            }

            .wallet-warning-actions {
                flex-direction: column;
            }

            .wallet-warning-actions .wallet-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    @include('components.wallet-welcome-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('walletWelcomeModal');
            const ibanForm = document.getElementById('ibanForm');
            const ibanInput = document.getElementById('ibanInput');
            const ibanError = document.getElementById('ibanError');
            const skipIbanBtn = document.getElementById('skipIbanBtn');
            const submitIbanBtn = document.getElementById('submitIbanBtn');
            const noIbanWarning = document.getElementById('noIbanWarning');
            const cancelNoIbanBtn = document.getElementById('cancelNoIbanBtn');
            const confirmNoIbanBtn = document.getElementById('confirmNoIbanBtn');
            const modalLoading = document.getElementById('modalLoading');
            const ibanFormSection = document.getElementById('ibanFormSection');
            const modalFooter = document.getElementById('modalFooter');

            // Force modal open immediately
            console.log('[RegisterWalletSetup] Opening modal immediately');
            modal.classList.remove('hidden');

            const postRegistrationRedirectUrl = "{{ $postRegistrationRedirectUrl ?? route('home') }}";

            // Collapsible sections
            document.querySelectorAll('.wallet-section-header').forEach(button => {
                button.addEventListener('click', function() {
                    const sectionId = this.getAttribute('data-section');
                    const content = document.getElementById(`section-${sectionId}`);
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';

                    this.setAttribute('aria-expanded', !isExpanded);
                    content.classList.toggle('expanded');
                });
            });

            // IBAN form submission
            ibanForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const iban = ibanInput.value.trim().replace(/\s/g, '');
                const dontShowAgain = document.getElementById('dontShowAgain').checked;

                if (!iban) {
                    showError('IBAN richiesto');
                    return;
                }

                if (!validateIban(iban)) {
                    showError('IBAN non valido. Controlla il formato.');
                    return;
                }

                clearError();
                showLoading();

                try {
                    const response = await fetch('/api/wallet/welcome/add-iban', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            iban,
                            dont_show_again: dontShowAgain
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = postRegistrationRedirectUrl;
                    } else {
                        hideLoading();
                        showError(data.error || 'Errore durante l\'aggiunta dell\'IBAN');
                    }
                } catch (error) {
                    hideLoading();
                    showError('Errore di connessione. Riprova.');
                    console.error('IBAN submission error:', error);
                }
            });

            // Skip IBAN button
            skipIbanBtn.addEventListener('click', function() {
                noIbanWarning.classList.remove('hidden');
                ibanFormSection.classList.add('hidden');
                modalFooter.classList.add('hidden');
            });

            // Cancel no IBAN
            cancelNoIbanBtn.addEventListener('click', function() {
                noIbanWarning.classList.add('hidden');
                ibanFormSection.classList.remove('hidden');
                modalFooter.classList.remove('hidden');
            });

            // Confirm no IBAN
            confirmNoIbanBtn.addEventListener('click', async function() {
                const dontShowAgain = document.getElementById('dontShowAgain').checked;

                showLoading();

                try {
                    const response = await fetch('/api/wallet/welcome/skip-iban', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            dont_show_again: dontShowAgain
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = postRegistrationRedirectUrl;
                    } else {
                        hideLoading();
                        alert(data.error || 'Errore durante il salvataggio');
                    }
                } catch (error) {
                    hideLoading();
                    alert('Errore di connessione. Riprova.');
                    console.error('Skip IBAN error:', error);
                }
            });

            // Basic IBAN validation (MOD-97)
            function validateIban(iban) {
                iban = iban.replace(/\s/g, '').toUpperCase();

                if (iban.length < 15 || iban.length > 34) {
                    return false;
                }

                if (!/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/.test(iban)) {
                    return false;
                }

                const rearranged = iban.slice(4) + iban.slice(0, 4);
                const numeric = rearranged.split('').map(char => {
                    const code = char.charCodeAt(0);
                    return code >= 65 && code <= 90 ? code - 55 : char;
                }).join('');

                let remainder = numeric;
                while (remainder.length > 2) {
                    const block = remainder.slice(0, 9);
                    remainder = (parseInt(block, 10) % 97) + remainder.slice(block.length);
                }

                return parseInt(remainder, 10) % 97 === 1;
            }

            function showError(message) {
                ibanError.textContent = message;
                ibanError.classList.remove('hidden');
                ibanInput.classList.add('error');
            }

            function clearError() {
                ibanError.textContent = '';
                ibanError.classList.add('hidden');
                ibanInput.classList.remove('error');
            }

            function showLoading() {
                modalLoading.classList.remove('hidden');
                submitIbanBtn.disabled = true;
                skipIbanBtn.disabled = true;
            }

            function hideLoading() {
                modalLoading.classList.add('hidden');
                submitIbanBtn.disabled = false;
                skipIbanBtn.disabled = false;
            }
        });
    </script>
</body>

</html>
