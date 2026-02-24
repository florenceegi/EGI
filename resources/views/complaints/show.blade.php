@extends('layouts.gdpr')

@section('title', __('complaints.detail_title') . ' - ' . $complaint->complaint_reference)

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8" role="main" aria-labelledby="complaint-detail-title">
    <div class="max-w-4xl mx-auto">

        {{-- Back Link --}}
        <div class="mb-6">
            <a href="{{ route('complaints.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 transition-colors hover:text-gray-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('complaints.back_to_list') }}
            </a>
        </div>

        {{-- Header Card --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 id="complaint-detail-title" class="text-2xl font-bold text-gray-900">
                        {{ __('complaints.detail_title') }}
                    </h1>
                    <div class="flex items-center gap-3 mt-2">
                        <code class="px-3 py-1 text-sm font-mono rounded-lg bg-gray-100 text-gray-700">{{ $complaint->complaint_reference }}</code>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @switch($complaint->status)
                                @case('received') bg-gray-100 text-gray-800 @break
                                @case('under_review') bg-yellow-100 text-yellow-800 @break
                                @case('action_taken') bg-blue-100 text-blue-800 @break
                                @case('dismissed') bg-red-100 text-red-800 @break
                                @case('appealed') bg-orange-100 text-orange-800 @break
                                @case('resolved') bg-green-100 text-green-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ __('complaints.statuses.' . $complaint->status) }}
                        </span>
                    </div>
                </div>
                <div class="hidden sm:block" aria-hidden="true">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Complaint Details --}}
        <div class="p-6 mb-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <dl class="space-y-5">
                {{-- Date --}}
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.submitted_on') }}</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">
                        <time datetime="{{ $complaint->created_at->toISOString() }}">
                            {{ $complaint->created_at->format('d/m/Y H:i') }}
                        </time>
                    </dd>
                </div>

                {{-- Type --}}
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.complaint_type_label') }}</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">
                        {{ __('complaints.types.' . $complaint->type) }}
                    </dd>
                </div>

                {{-- Reported Content (if any) --}}
                @if($complaint->reported_content_type)
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.reported_content') }}</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">
                        {{ __('complaints.content_types.' . $complaint->reported_content_type) }}
                        @if($complaint->reported_content_id)
                            <span class="ml-1 text-gray-500">(ID: {{ $complaint->reported_content_id }})</span>
                        @endif
                    </dd>
                </div>
                @endif

                {{-- Description --}}
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.description_label') }}</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2 whitespace-pre-line">{{ $complaint->description }}</dd>
                </div>

                {{-- Evidence URLs --}}
                @if(!empty($complaint->evidence_urls))
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.evidence_label') }}</dt>
                    <dd class="text-sm sm:col-span-2">
                        <ul class="space-y-1">
                            @foreach($complaint->evidence_urls as $url)
                                <li>
                                    <a href="{{ $url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="text-amber-600 hover:text-amber-800 hover:underline break-all">
                                        {{ $url }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
                @endif

                {{-- Status --}}
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-gray-500">{{ __('complaints.current_status') }}</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">
                        {{ __('complaints.statuses.' . $complaint->status) }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Decision Section (if decided) --}}
        @if($complaint->decision)
        <div class="p-6 mb-6 border shadow-lg bg-blue-50/80 backdrop-blur-lg rounded-2xl border-blue-200/50">
            <h2 class="flex items-center mb-4 text-lg font-semibold text-blue-900">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('complaints.decision') }}
            </h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-blue-700">{{ __('complaints.decision') }}</dt>
                    <dd class="mt-1 text-sm text-blue-900 whitespace-pre-line">{{ $complaint->decision }}</dd>
                </div>
                @if($complaint->decided_at)
                <div>
                    <dt class="text-sm font-medium text-blue-700">{{ __('complaints.decision_date') }}</dt>
                    <dd class="mt-1 text-sm text-blue-900">{{ $complaint->decided_at->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @else
        <div class="p-6 mb-6 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center text-gray-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm">{{ __('complaints.no_decision_yet') }}</p>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('complaints.current_status') }}</h2>
            <div class="relative">
                @php
                    $timelineSteps = ['received', 'under_review', 'action_taken', 'resolved'];
                    if (in_array($complaint->status, ['dismissed', 'appealed'])) {
                        $timelineSteps = ['received', 'under_review', $complaint->status];
                    }
                    $currentIndex = array_search($complaint->status, $timelineSteps);
                    if ($currentIndex === false) $currentIndex = 0;
                @endphp

                <div class="flex items-center justify-between">
                    @foreach($timelineSteps as $i => $step)
                        <div class="flex flex-col items-center flex-1">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full
                                @if($i <= $currentIndex)
                                    @if($step === 'dismissed') bg-red-500 text-white
                                    @elseif($step === 'resolved') bg-green-500 text-white
                                    @else bg-amber-500 text-white
                                    @endif
                                @else bg-gray-200 text-gray-400
                                @endif">
                                @if($i < $currentIndex)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif($i === $currentIndex)
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                @endif
                            </div>
                            <p class="mt-2 text-xs font-medium text-center
                                @if($i <= $currentIndex) text-gray-900 @else text-gray-400 @endif">
                                {{ __('complaints.timeline.' . $step) }}
                            </p>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 -mt-6
                                @if($i < $currentIndex) bg-amber-500 @else bg-gray-200 @endif">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
