{{-- Modal overlay --}}
<div id="walletWelcomeModal" class="wallet-modal-overlay hidden" role="dialog" aria-modal="true"
    aria-labelledby="modalTitle">
    {{-- Modal container --}}
    <div class="wallet-modal-container">
        {{-- Header --}}
        <div class="wallet-modal-header">
            <div>
                <h2 id="modalTitle" class="wallet-modal-title">
                    {{ __('wallet_welcome.title') }}
                </h2>
                <p class="wallet-modal-subtitle">{{ __('wallet_welcome.subtitle') }}</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="wallet-modal-content">
            {{-- Intro --}}
            <div class="wallet-intro-box">
                <p>{!! __('wallet_welcome.intro') !!}</p>
            </div>

            {{-- Collapsible Sections --}}
            <div class="wallet-sections">
                {{-- Section 1: Security --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="security" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.security_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-security">
                        <ul class="wallet-list">
                            @foreach (__('wallet_welcome.security_items') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        <div class="wallet-note">{!! __('wallet_welcome.security_note') !!}</div>
                    </div>
                </div>

                {{-- Section 2: Content --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="content" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.content_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-content">
                        <h4 class="wallet-subsection-title">{{ __('wallet_welcome.content_has_title') }}</h4>
                        <ul class="wallet-list wallet-list-success">
                            @foreach (__('wallet_welcome.content_has') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        <h4 class="wallet-subsection-title mt-4">{{ __('wallet_welcome.content_not_has_title') }}</h4>
                        <ul class="wallet-list wallet-list-error">
                            @foreach (__('wallet_welcome.content_not_has') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        <div class="wallet-note">{!! __('wallet_welcome.content_note') !!}</div>
                    </div>
                </div>

                {{-- Section 3: Payments --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="payments" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.payments_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-payments">
                        <h4 class="wallet-subsection-title">{{ __('wallet_welcome.payments_how_title') }}</h4>
                        <ul class="wallet-list">
                            @foreach (__('wallet_welcome.payments_how') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>

                        <div class="wallet-highlight-box mt-6">
                            <h4 class="wallet-subsection-title">{{ __('wallet_welcome.payments_iban_title') }}</h4>
                            <p class="mb-3">{!! __('wallet_welcome.payments_iban_intro') !!}</p>
                            <h5 class="mb-2 text-sm font-semibold">
                                {{ __('wallet_welcome.payments_iban_security_title') }}</h5>
                            <ul class="wallet-list wallet-list-sm">
                                @foreach (__('wallet_welcome.payments_iban_security') as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Compliance --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="compliance" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.compliance_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-compliance">
                        <p class="mb-4">{!! __('wallet_welcome.compliance_intro') !!}</p>
                        <ul class="wallet-list">
                            @foreach (__('wallet_welcome.compliance_items') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        <h4 class="wallet-subsection-title mt-6">{{ __('wallet_welcome.compliance_platform_title') }}
                        </h4>
                        <ul class="wallet-list">
                            @foreach (__('wallet_welcome.compliance_platform') as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Section 5: Options --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="options" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.options_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-options">
                        <div class="wallet-option-box wallet-option-simple">
                            <h4 class="wallet-option-title">{{ __('wallet_welcome.option1_title') }}</h4>
                            <p class="wallet-option-subtitle">{{ __('wallet_welcome.option1_subtitle') }}</p>
                            <ul class="wallet-list wallet-list-sm">
                                @foreach (__('wallet_welcome.option1_items') as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="wallet-option-box wallet-option-expert mt-4">
                            <h4 class="wallet-option-title">{{ __('wallet_welcome.option2_title') }}</h4>
                            <p class="wallet-option-subtitle">{{ __('wallet_welcome.option2_subtitle') }}</p>
                            <ul class="wallet-list wallet-list-sm">
                                @foreach (__('wallet_welcome.option2_items') as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Section 6: Glossary --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="glossary" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.glossary_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-glossary">
                        <dl class="wallet-glossary">
                            @foreach (__('wallet_welcome.glossary') as $key => $entry)
                                <div class="wallet-glossary-item">
                                    <dt class="wallet-glossary-term">{{ $entry['term'] }}</dt>
                                    <dd class="wallet-glossary-definition">{!! $entry['definition'] !!}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                {{-- Section 7: Help --}}
                <div class="wallet-section">
                    <button class="wallet-section-header" data-section="help" aria-expanded="false">
                        <span class="wallet-section-title">{{ __('wallet_welcome.help_title') }}</span>
                        <span class="material-icons wallet-section-icon">expand_more</span>
                    </button>
                    <div class="wallet-section-content" id="section-help">
                        <div class="wallet-help-grid">
                            <a href="{{ route('info.white-paper-finanziario') }}" class="wallet-help-card"
                                target="_blank">
                                <span class="material-icons wallet-help-icon">description</span>
                                <h5 class="wallet-help-title">{{ __('wallet_welcome.help_whitepaper') }}</h5>
                                <p class="wallet-help-desc">{{ __('wallet_welcome.help_whitepaper_desc') }}</p>
                            </a>
                            <a href="{{ route('support') }}" class="wallet-help-card">
                                <span class="material-icons wallet-help-icon">support_agent</span>
                                <h5 class="wallet-help-title">{{ __('wallet_welcome.help_support') }}</h5>
                                <p class="wallet-help-desc">{{ __('wallet_welcome.help_support_desc') }}</p>
                            </a>
                            <a href="{{ route('faq') }}" class="wallet-help-card">
                                <span class="material-icons wallet-help-icon">help_outline</span>
                                <h5 class="wallet-help-title">{{ __('wallet_welcome.help_faq') }}</h5>
                                <p class="wallet-help-desc">{{ __('wallet_welcome.help_faq_desc') }}</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IBAN Form --}}
            <div id="ibanFormSection" class="wallet-iban-form">
                <h3 class="wallet-form-title">
                    <span class="material-icons">account_balance</span>
                    {{ __('wallet_welcome.payments_iban_title') }}
                </h3>
                <p class="wallet-form-description">{{ __('wallet_welcome.payments_iban_intro') }}</p>

                <form id="ibanForm">
                    <div class="wallet-form-group">
                        <label for="ibanInput" class="wallet-form-label">IBAN</label>
                        <input type="text" id="ibanInput" name="iban" class="wallet-form-input"
                            placeholder="IT00 A000 0000 0000 0000 0000 000" maxlength="34" autocomplete="off">
                        <span id="ibanError" class="wallet-form-error hidden"></span>
                    </div>

                    <div class="wallet-form-checkbox">
                        <input type="checkbox" id="dontShowAgain" name="dont_show_again" class="wallet-checkbox">
                        <label for="dontShowAgain" class="wallet-checkbox-label">
                            {{ __('wallet_welcome.dont_show_again') }}
                        </label>
                    </div>
                </form>
            </div>

            {{-- Warning Box (hidden by default) --}}
            <div id="noIbanWarning" class="wallet-warning-box hidden">
                <div class="wallet-warning-icon">
                    <span class="material-icons">warning</span>
                </div>
                <div class="wallet-warning-content">
                    <h4 class="wallet-warning-title">⚠️ Attenzione: Nessun IBAN configurato</h4>
                    <p class="wallet-warning-text">
                        Procedendo <strong>senza IBAN</strong>, non potrai ricevere pagamenti in <strong>€
                            (euro)</strong> per le tue opere.<br><br>
                        Inoltre, fino a quando <strong>non riscatterai il tuo wallet</strong> (scaricando la frase
                        segreta di 25 parole),
                        i tuoi <strong>Certificati EGI non saranno vendibili</strong> ad altri utenti.<br><br>
                        Potrai comunque aggiungere l'IBAN in qualsiasi momento dalle <strong>Impostazioni → Profilo →
                            Pagamenti</strong>.
                    </p>
                    <div class="wallet-warning-actions">
                        <button type="button" id="cancelNoIbanBtn" class="wallet-btn wallet-btn-secondary">
                            <span class="material-icons">arrow_back</span>
                            Torna indietro
                        </button>
                        <button type="button" id="confirmNoIbanBtn" class="wallet-btn wallet-btn-warning">
                            <span class="material-icons">check_circle</span>
                            Ho capito, procedi senza IBAN
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="wallet-modal-footer" id="modalFooter">
            <button type="button" id="skipIbanBtn" class="wallet-btn wallet-btn-ghost">
                {{ __('wallet_welcome.btn_continue') }}
            </button>
            <button type="submit" form="ibanForm" id="submitIbanBtn" class="wallet-btn wallet-btn-primary">
                <span class="material-icons">account_balance</span>
                {{ __('wallet_welcome.btn_add_iban') }}
            </button>
        </div>

        {{-- Loading overlay --}}
        <div id="modalLoading" class="wallet-modal-loading hidden">
            <div class="wallet-spinner"></div>
        </div>
    </div>
</div>

{{-- Include styles --}}
@push('styles')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
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
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
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
        }

        .wallet-form-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .wallet-form-title .material-icons {
            color: #1B365D;
        }

        .wallet-form-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .wallet-form-group {
            margin-bottom: 1rem;
        }

        .wallet-form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .wallet-form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
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
            color: #6b7280;
            cursor: pointer;
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
@endpush

{{-- Include JavaScript --}}
@push('scripts')
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

            // Check if modal should be shown
            // Priority 1: Check if user was created in the last 5 minutes (just registered)
            @php
                $shouldShow = false;
                if (auth()->check()) {
                    $user = auth()->user();
                    $minutesSinceCreation = $user->created_at->diffInMinutes(now());
                    $hasSeenModal = $user->preferences['hide_wallet_welcome'] ?? false;
                    $shouldShow = $minutesSinceCreation < 5 && !$hasSeenModal;
                }
            @endphp

            console.log('[WalletWelcome] Modal check:', {
                authenticated: {{ auth()->check() ? 'true' : 'false' }},
                shouldShow: {{ $shouldShow ? 'true' : 'false' }},
                @if (auth()->check())
                    userCreatedAt: '{{ auth()->user()->created_at }}',
                    minutesAgo: {{ auth()->user()->created_at->diffInMinutes(now()) }},
                    hasSeenModal: {{ auth()->user()->preferences['hide_wallet_welcome'] ?? false ? 'true' : 'false' }}
                @endif
            });

            @if ($shouldShow)
                console.log('[WalletWelcome] Opening modal - user registered recently');
                modal.classList.remove('hidden');
            @else
                console.log('[WalletWelcome] Modal will not open');
            @endif

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
                        // Success - redirect to dashboard
                        window.location.href = '/dashboard';
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
                // Show warning first
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
                        // Success - redirect to dashboard
                        window.location.href = '/dashboard';
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

            // Check modal state from server
            async function checkModalState() {
                try {
                    const response = await fetch('/api/wallet/welcome/data', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();

                        if (data.success && data.data.should_show) {
                            modal.classList.remove('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Failed to check modal state:', error);
                }
            }

            // Basic IBAN validation
            function validateIban(iban) {
                // Remove spaces and convert to uppercase
                iban = iban.replace(/\s/g, '').toUpperCase();

                // Check length (15-34 chars)
                if (iban.length < 15 || iban.length > 34) {
                    return false;
                }

                // Check format (2 letters + 2 digits + alphanumeric)
                if (!/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/.test(iban)) {
                    return false;
                }

                // MOD-97 checksum validation
                const rearranged = iban.slice(4) + iban.slice(0, 4);
                const numeric = rearranged.split('').map(char => {
                    const code = char.charCodeAt(0);
                    return code >= 65 && code <= 90 ? code - 55 : char;
                }).join('');

                // Calculate mod 97
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
@endpush
