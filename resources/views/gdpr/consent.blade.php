{{-- resources/views/gdpr/consent.blade.php --}}
@php
    $pageSubtitle = __('gdpr.consent.subtitle');
    $breadcrumbItems = [
        ['label' => __('gdpr.consent.breadcrumb'), 'url' => null]
    ];
@endphp

@push('styles')
<style>
    .consent-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #10b981;
        transition: all 0.3s ease;
    }

    .consent-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .consent-card.withdrawn {
        border-left-color: #ef4444;
        background: #fef2f2;
    }

    .consent-card.expired {
        border-left-color: #f59e0b;
        background: #fffbeb;
    }

    .consent-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .consent-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .consent-status {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .consent-status.active {
        background: #d1fae5;
        color: #065f46;
    }

    .consent-status.withdrawn {
        background: #fee2e2;
        color: #991b1b;
    }

    .consent-status.expired {
        background: #fef3c7;
        color: #92400e;
    }

    .consent-description {
        color: #6b7280;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .consent-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .consent-detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .consent-detail-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
    }

    .consent-detail-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 600;
    }

    .consent-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .consent-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .consent-btn-primary {
        background: #3b82f6;
        color: white;
    }

    .consent-btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .consent-btn-danger {
        background: #ef4444;
        color: white;
    }

    .consent-btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .consent-btn-secondary {
        background: #6b7280;
        color: white;
    }

    .consent-btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-1px);
    }

    .consent-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
    }

    .summary-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .new-consent-section {
        background: #f8fafc;
        padding: 2rem;
        border-radius: 12px;
        margin-top: 2rem;
        border: 2px dashed #cbd5e0;
    }

    .new-consent-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        text-align: center;
    }
</style>
@endpush

<x-platform-layout :page-title="__('gdpr.consent.your_consents')" :page-subtitle="__('gdpr.consent.subtitle')">

    <div class="consent-summary">
        <div class="summary-card">
            <div class="summary-number">{{ $consentSummary['active_consents'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.consent.summary.active') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $consentSummary['total_consents'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.consent.summary.total') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $consentSummary['compliance_score'] ?? 0 }}%</div>
            <div class="summary-label">{{ __('gdpr.consent.summary.compliance') }}</div>
        </div>
    </div>

    {{-- Active Consents Section --}}
    <h2 style="margin-bottom: 1.5rem; color: #1f2937; font-size: 1.5rem;">
        {{ __('gdpr.consent.your_consents') }}
    </h2>

    @if($userConsents && $userConsents->count() > 0)
        @foreach($userConsents as $consent)
            <div class="consent-card {{ $consent->granted ? 'granted' : 'denied' }}">
                <div class="consent-header">
                    <h3 class="consent-title">
                        {{ __('gdpr.consent.purposes.' . $consent->purpose, ['purpose' => $consent->purpose ?? '']) }}
                    </h3>
                    <span class="consent-status {{ $consent->granted ? 'granted' : 'denied' }}">
                        {{ __('gdpr.consent.status.' . ($consent->granted ? 'granted' : 'denied')) }}
                    </span>
                </div>

                <p class="consent-description">
                    {{ __('gdpr.consent.descriptions.' . $consent->purpose) }}
                </p>

                <div class="consent-details">
                    <div class="consent-detail-item">
                        <span class="consent-detail-label">{{ __('gdpr.consent.given_at') }}</span>
                        <span class="consent-detail-value">
                            {{ $consent->given_at?->format('d/m/Y H:i') ?? __('gdpr.consent.not_given') }}
                        </span>
                    </div>

                    @if($consent->withdrawn_at)
                        <div class="consent-detail-item">
                            <span class="consent-detail-label">{{ __('gdpr.consent.withdrawn_at') }}</span>
                            <span class="consent-detail-value">
                                {{ $consent->withdrawn_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="consent-detail-item">
                        <span class="consent-detail-label">{{ __('gdpr.consent.method') }}</span>
                        <span class="consent-detail-value">
                            {{ __('gdpr.consent.methods.' . $consent->consent_method) }}
                        </span>
                    </div>

                    <div class="consent-detail-item">
                        <span class="consent-detail-label">{{ __('gdpr.consent.version') }}</span>
                        <span class="consent-detail-value">
                            {{ $consent->consentVersion?->version ?? __('gdpr.consent.unknown_version') }}
                        </span>
                    </div>
                </div>

                <div class="consent-actions">
                    @if($consent->status === 'active')
                        <form method="POST" action="{{ route('gdpr.consent.withdraw') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="consent_id" value="{{ $consent->id }}">
                            <button type="submit" class="consent-btn consent-btn-danger"
                                    onclick="return confirm('{{ __('gdpr.consent.withdraw_confirm') }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ __('gdpr.consent.withdraw') }}
                            </button>
                        </form>
                    @elseif($consent->status === 'withdrawn')
                        <form method="POST" action="{{ route('gdpr.consent.renew') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="consent_id" value="{{ $consent->id }}">
                            <button type="submit" class="consent-btn consent-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('gdpr.consent.renew') }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('gdpr.consent.history', $consent->id) }}" class="consent-btn consent-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('gdpr.consent.view_history') }}
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="consent-card" style="text-align: center; padding: 3rem;">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 style="margin-bottom: 1rem; color: #6b7280;">{{ __('gdpr.consent.no_consents') }}</h3>
            <p style="color: #9ca3af; margin-bottom: 2rem;">{{ __('gdpr.consent.no_consents_description') }}</p>
        </div>
    @endif

    @if(session('show_history') && session('consent_history'))
        <div class="mb-8">
            <h2 class="mb-6 text-2xl font-semibold text-gray-800">
                {{ __('gdpr.consent.history_title') }}
            </h2>

            <div class="space-y-4">
                @foreach(session('consent_history') as $historyItem)
                    <div class="p-4 border-l-4 rounded-lg bg-base-100 border-l-info">
                        {{-- Contenuto cronologia --}}
                        <div class="text-sm text-gray-600">
                            {{ $historyItem->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="font-medium">
                            {{ $historyItem->action }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <a href="{{ route('gdpr.consent') }}" class="btn btn-ghost">
                    {{ __('gdpr.consent.back_to_consents') }}
                </a>
            </div>
        </div>
    @endif

    {{-- New Consent Section --}}
    <div class="new-consent-section">
        <h3 class="new-consent-title">{{ __('gdpr.consent.manage_preferences') }}</h3>
        <div style="text-align: center;">
            <a href="{{ route('gdpr.consent.preferences') }}" class="consent-btn consent-btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ __('gdpr.consent.update_preferences') }}
            </a>
        </div>
    </div>
</x-platform-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[GDPR Consent] Page initialized');

        // Auto-refresh consent status every 30 seconds
        setInterval(function() {
            // Could implement AJAX refresh here if needed
        }, 30000);
    });
</script>
@endpush
