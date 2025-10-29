{{-- resources/views/gdpr/activity-log.blade.php --}}
@extends('layouts.gdpr')

@section('page-title', __('gdpr.activity_log.title'))

@php
    $pageSubtitle = __('gdpr.activity_log.subtitle');
    $breadcrumbItems = [
        ['label' => __('gdpr.activity_log.breadcrumb'), 'url' => null]
    ];
@endphp

@push('styles')
<style>
    .activity-filters {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-btn {
        padding: 0.75rem 1.5rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        height: fit-content;
    }

    .filter-btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .activity-timeline {
        position: relative;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
    }

    .activity-item {
        position: relative;
        margin-left: 60px;
        margin-bottom: 2rem;
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #3b82f6;
    }

    .activity-item.high-risk {
        border-left-color: #ef4444;
        background: #fef2f2;
    }

    .activity-item.security {
        border-left-color: #f59e0b;
        background: #fffbeb;
    }

    .activity-item.gdpr {
        border-left-color: #10b981;
        background: #f0fdf4;
    }

    .activity-icon {
        position: absolute;
        left: -54px;
        top: 1.5rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .activity-icon.normal {
        background: #3b82f6;
    }

    .activity-icon.warning {
        background: #f59e0b;
    }

    .activity-icon.danger {
        background: #ef4444;
    }

    .activity-icon.success {
        background: #10b981;
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .activity-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .activity-timestamp {
        color: #6b7280;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-description {
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .activity-details {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .activity-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .activity-detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .activity-detail-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
    }

    .activity-detail-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 600;
    }

    .activity-context {
        background: #f3f4f6;
        border-radius: 6px;
        padding: 0.75rem;
        margin-top: 1rem;
        font-family: monospace;
        font-size: 0.8rem;
        color: #374151;
        max-height: 150px;
        overflow-y: auto;
    }

    .activity-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .activity-tag {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .activity-tag.category-auth {
        background: #dbeafe;
        color: #1e40af;
    }

    .activity-tag.category-data {
        background: #d1fae5;
        color: #065f46;
    }

    .activity-tag.category-security {
        background: #fef3c7;
        color: #92400e;
    }

    .activity-tag.category-system {
        background: #f3e8ff;
        color: #6b21a8;
    }

    .activity-tag.category-gdpr {
        background: #ecfdf5;
        color: #065f46;
    }

    .activity-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .no-activities {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .no-activities svg {
        margin: 0 auto 1rem auto;
    }

    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    .activity-export-btn {
        background: #10b981;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-export-btn:hover {
        background: #059669;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('gdpr-content')
    {{-- Activity Summary --}}
    <div class="activity-summary">
        <div class="summary-card">
            <div class="summary-number">{{ $activitySummary['total_activities'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.activity_log.summary.total') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $activitySummary['this_month'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.activity_log.summary.this_month') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $activitySummary['security_events'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.activity_log.summary.security_events') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $activitySummary['gdpr_actions'] ?? 0 }}</div>
            <div class="summary-label">{{ __('gdpr.activity_log.summary.gdpr_actions') }}</div>
        </div>
    </div>

    {{-- Activity Filters --}}
    <form method="GET" class="activity-filters">
        <div class="filter-group">
            <label class="filter-label">{{ __('gdpr.activity_log.filters.category') }}</label>
            <select name="category" class="filter-input">
                <option value="">{{ __('gdpr.activity_log.filters.all_categories') }}</option>
                @foreach($availableCategories as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                        {{ __('gdpr.activity_log.categories.' . $category) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">{{ __('gdpr.activity_log.filters.action_type') }}</label>
            <select name="action_type" class="filter-input">
                <option value="">{{ __('gdpr.activity_log.filters.all_actions') }}</option>
                @foreach($availableActionTypes as $actionType)
                    <option value="{{ $actionType }}" {{ request('action_type') === $actionType ? 'selected' : '' }}>
                        {{ __('gdpr.activity_log.action_types.' . $actionType) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">{{ __('gdpr.activity_log.filters.date_from') }}</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
        </div>

        <div class="filter-group">
            <label class="filter-label">{{ __('gdpr.activity_log.filters.date_to') }}</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
        </div>

        <div class="filter-group">
            <button type="submit" class="filter-btn">
                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                </svg>
                {{ __('gdpr.activity_log.filters.apply') }}
            </button>
        </div>

        <div class="filter-group">
            <a href="{{ route('gdpr.activity-log.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="activity-export-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('gdpr.activity_log.export') }}
            </a>
        </div>
    </form>

    {{-- Activity Timeline --}}
    @if($activities && $activities->count() > 0)
        <div class="activity-timeline">
            @foreach($activities as $activity)
                <div class="activity-item {{ $activity->getRiskLevelClass() }}">
                    <div class="activity-icon {{ $activity->getIconClass() }}">
                        {!! $activity->getIconSvg() !!}
                    </div>

                    <div class="activity-header">
                        <h3 class="activity-title">
                            {{ __('gdpr.activity_log.actions.' . $activity->action_type) }}
                        </h3>
                        <div class="activity-timestamp">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $activity->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <p class="activity-description">
                        {{ $activity->description ?: __('gdpr.activity_log.default_descriptions.' . $activity->action_type) }}
                    </p>

                    <div class="activity-details">
                        <div class="activity-details-grid">
                            <div class="activity-detail-item">
                                <span class="activity-detail-label">{{ __('gdpr.activity_log.details.category') }}</span>
                                <span class="activity-detail-value">
                                    {{ __('gdpr.activity_log.categories.' . $activity->category->value) }}
                                </span>
                            </div>

                            @if($activity->ip_address)
                                <div class="activity-detail-item">
                                    <span class="activity-detail-label">{{ __('gdpr.activity_log.details.ip_address') }}</span>
                                    <span class="activity-detail-value">{{ $activity->ip_address }}</span>
                                </div>
                            @endif

                            @if($activity->location)
                                <div class="activity-detail-item">
                                    <span class="activity-detail-label">{{ __('gdpr.activity_log.details.location') }}</span>
                                    <span class="activity-detail-value">{{ $activity->location }}</span>
                                </div>
                            @endif

                            @if($activity->device_info)
                                <div class="activity-detail-item">
                                    <span class="activity-detail-label">{{ __('gdpr.activity_log.details.device') }}</span>
                                    <span class="activity-detail-value">{{ $activity->getFormattedDeviceInfo() }}</span>
                                </div>
                            @endif

                            @if($activity->risk_level)
                                <div class="activity-detail-item">
                                    <span class="activity-detail-label">{{ __('gdpr.activity_log.details.risk_level') }}</span>
                                    <span class="activity-detail-value">
                                        {{ __('gdpr.activity_log.risk_levels.' . $activity->risk_level) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($activity->context_data && !empty($activity->context_data))
                        <details>
                            <summary style="cursor: pointer; font-weight: 600; margin-bottom: 0.5rem;">
                                {{ __('gdpr.activity_log.technical_details') }}
                            </summary>
                            <div class="activity-context">
                                {{ json_encode($activity->context_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                            </div>
                        </details>
                    @endif

                    <div class="activity-tags">
                        <span class="activity-tag category-{{ $activity->category->value }}">
                            {{ __('gdpr.activity_log.categories.' . $activity->category->value) }}
                        </span>

                        @if($activity->is_sensitive)
                            <span class="activity-tag" style="background: #fee2e2; color: #991b1b;">
                                {{ __('gdpr.activity_log.sensitive_data') }}
                            </span>
                        @endif

                        @if($activity->requires_attention)
                            <span class="activity-tag" style="background: #fef3c7; color: #92400e;">
                                {{ __('gdpr.activity_log.requires_attention') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $activities->links() }}
        </div>
    @else
        <div class="no-activities">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 style="margin-bottom: 1rem;">{{ __('gdpr.activity_log.no_activities') }}</h3>
            <p>{{ __('gdpr.activity_log.no_activities_description') }}</p>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[GDPR Activity Log] Page initialized');

        // Auto-refresh every 60 seconds if there are recent activities
        const hasRecentActivities = {{ $activities && $activities->where('created_at', '>=', now()->subMinutes(5))->count() > 0 ? 'true' : 'false' }};

        if (hasRecentActivities) {
            setInterval(() => {
                // Only refresh if user hasn't interacted recently
                if (document.visibilityState === 'visible') {
                    window.location.reload();
                }
            }, 60000);
        }

        // Add smooth scroll to timeline items
        const timelineItems = document.querySelectorAll('.activity-item');
        timelineItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            item.style.animation = 'fadeInUp 0.6s ease forwards';
        });
    });

    // CSS animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .activity-item {
            opacity: 0;
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
