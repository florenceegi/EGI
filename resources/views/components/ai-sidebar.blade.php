{{--
    AI Sidebar - Onboarding Assistant Component
    Shopify-style: Chat AI top + Stripe-style checklist bottom

    USAGE:
    <x-ai-sidebar
        :user="$user"
        :userType="'creator'"
        :checklist="$onboardingChecklist"
    />

    PROPS:
    - user: User model - The profile owner
    - userType: string - 'creator' | 'company' | 'collector'
    - checklist: array - From OnboardingChecklistService

    P0-0: Vanilla JS only (NO Alpine/Livewire)
    P0-2: Translation keys only
--}}

@props([
    'user' => null,
    'userType' => 'creator',
    'checklist' => [],
])

@php
    $isOwner = auth()->check() && auth()->id() === $user?->id;
    $completedCount = collect($checklist)->where('completed', true)->count();
    $totalCount = count($checklist);
    $progressPercent = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
@endphp

{{-- Show only to profile owner --}}
@if ($isOwner && $totalCount > 0)
    @push('styles')
    <style>
        /* AI Sidebar Animations */
        .ai-sidebar {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .ai-sidebar.collapsed {
            transform: translateX(100%);
            opacity: 0;
            pointer-events: none;
        }

        .ai-sidebar-toggle {
            transition: all 0.3s ease;
        }

        .ai-sidebar-toggle:hover {
            transform: scale(1.05);
        }

        /* Checklist item hover */
        .checklist-item {
            transition: all 0.2s ease;
        }

        .checklist-item:not(.completed):hover {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .checklist-item.completed {
            opacity: 0.7;
        }

        /* Progress bar animation */
        .progress-fill {
            transition: width 0.5s ease-out;
        }

        /* AI message typing animation */
        .ai-typing::after {
            content: '▋';
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .ai-sidebar {
                width: 100% !important;
                max-width: 100% !important;
                right: 0 !important;
                border-radius: 0 !important;
            }
        }
    </style>
    @endpush

    {{-- Toggle Button (FAB) --}}
    <button
        id="ai-sidebar-toggle"
        class="fixed z-40 flex items-center justify-center text-white rounded-full shadow-2xl ai-sidebar-toggle bottom-6 right-6 h-14 w-14 bg-gradient-to-br from-indigo-600 to-purple-600 hover:shadow-indigo-500/50"
        title="{{ __('ai_sidebar.toggle_title') }}"
        aria-label="{{ __('ai_sidebar.toggle_title') }}"
        data-sidebar-open="false"
    >
        {{-- Sparkle icon --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>

        {{-- Progress badge --}}
        @if ($progressPercent < 100)
            <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                {{ $totalCount - $completedCount }}
            </span>
        @else
            <span class="absolute flex items-center justify-center w-5 h-5 text-white bg-green-500 rounded-full -right-1 -top-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </button>

    {{-- Sidebar Panel --}}
    <aside
        id="ai-sidebar"
        class="fixed bottom-0 z-30 flex flex-col overflow-hidden border shadow-2xl ai-sidebar collapsed right-4 top-20 w-80 rounded-2xl border-gray-700/50 bg-gradient-to-b from-gray-900 via-gray-850 to-gray-900 md:w-96"
        data-user-id="{{ $user->id }}"
        data-user-type="{{ $userType }}"
        data-checklist="{{ json_encode($checklist) }}"
        aria-hidden="true"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700/50 bg-gradient-to-r from-indigo-900/40 to-purple-900/40">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ __('ai_sidebar.title') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('ai_sidebar.subtitle') }}</p>
                </div>
            </div>

            <button
                id="ai-sidebar-close"
                class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-700/50 hover:text-white"
                aria-label="{{ __('ai_sidebar.close') }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- AI Chat Section (Top) --}}
        <div class="flex-1 overflow-hidden border-b border-gray-700/50">
            {{-- AI Message Area --}}
            <div id="ai-sidebar-chat" class="h-full p-4 pt-8 overflow-y-auto">
                {{-- Initial AI message - Discorsivo basato su cosa manca --}}
                <div class="p-4 ai-message rounded-xl bg-gradient-to-r from-indigo-900/30 to-purple-900/30">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-gradient-to-br from-indigo-500 to-purple-500">
                            ✨
                        </div>
                        <span class="text-xs font-medium text-indigo-300">{{ __('ai_sidebar.assistant_name') }}</span>
                    </div>

                    {{-- Messaggio discorsivo generato in PHP --}}
                    @php
                        $incompleteItems = collect($checklist)->where('completed', false);
                        $completedItems = collect($checklist)->where('completed', true);
                        $totalIncomplete = $incompleteItems->count();
                    @endphp

                    <div class="space-y-3 text-base font-normal leading-relaxed text-gray-200">
                        @if ($totalIncomplete === 0)
                            {{-- Tutto completato --}}
                            <p>🎉 <strong>{{ __('ai_sidebar.discourse.complete_title') }}</strong></p>
                            <p>{{ __('ai_sidebar.discourse.complete_text') }}</p>
                        @else
                            {{-- Saluto e stato --}}
                            <p>{{ __('ai_sidebar.discourse.greeting', ['name' => $user->name]) }}</p>

                            <p>{{ __('ai_sidebar.discourse.progress_intro', ['completed' => $completedItems->count(), 'total' => $totalCount]) }}</p>

                            {{-- Analisi di cosa manca --}}
                            <p><strong>{{ __('ai_sidebar.discourse.missing_title') }}</strong></p>

                            <ul class="ml-4 space-y-1 text-gray-300 list-disc">
                                @foreach ($incompleteItems as $item)
                                    <li>{{ __($item['title_key']) }}</li>
                                @endforeach
                            </ul>

                            {{-- Suggerimento prioritario --}}
                            @php
                                $firstIncomplete = $incompleteItems->first();
                            @endphp

                            @if ($firstIncomplete)
                                <p class="pl-3 mt-3 text-indigo-200 border-l-2 border-indigo-500">
                                    💡 {{ __('ai_sidebar.discourse.suggestion_intro') }}
                                    <strong>{{ __($firstIncomplete['title_key']) }}</strong>.
                                    @if (isset($firstIncomplete['description_key']))
                                        {{ __($firstIncomplete['description_key']) }}
                                    @endif
                                </p>
                            @endif

                            <p class="mt-2 text-xs text-gray-400">
                                {{ __('ai_sidebar.discourse.click_hint') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Chat Input (for real AI questions) --}}
            <div class="p-3 border-t border-gray-700/50 bg-gray-800/50">
                <form id="ai-sidebar-form" class="flex gap-2">
                    <input
                        type="text"
                        id="ai-sidebar-input"
                        class="flex-1 px-3 py-2 text-sm text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500/50"
                        placeholder="{{ __('ai_sidebar.chat_placeholder') }}"
                        autocomplete="off"
                        maxlength="500"
                    >
                    <button
                        type="submit"
                        id="ai-sidebar-send"
                        class="px-3 py-2 text-white transition-all rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                        title="{{ __('ai_sidebar.send') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Checklist Section (Bottom) - Stripe-style --}}
        <div class="max-h-[40%] overflow-y-auto">
            {{-- Progress Header --}}
            <div class="sticky top-0 px-4 py-3 border-b border-gray-700/50 bg-gray-900/95 backdrop-blur">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-400">{{ __('ai_sidebar.checklist.progress') }}</span>
                    <span class="text-xs font-semibold text-white">{{ $completedCount }}/{{ $totalCount }}</span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-gray-700">
                    <div
                        class="h-full rounded-full progress-fill bg-gradient-to-r from-indigo-500 to-purple-500"
                        style="width: {{ $progressPercent }}%"
                    ></div>
                </div>
            </div>

            {{-- Checklist Items --}}
            <ul id="ai-sidebar-checklist" class="p-2 divide-y divide-gray-700/50">
                @foreach ($checklist as $index => $item)
                    <li
                        class="checklist-item {{ $item['completed'] ? 'completed' : '' }} cursor-pointer rounded-lg px-3 py-2.5"
                        data-step-id="{{ $item['id'] }}"
                        data-action="{{ $item['action'] ?? '' }}"
                        data-modal="{{ $item['modal'] ?? '' }}"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Status Icon --}}
                            <div class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full {{ $item['completed'] ? 'bg-green-500' : 'border-2 border-gray-500' }}">
                                @if ($item['completed'])
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <span class="text-[10px] font-bold text-gray-500">{{ $index + 1 }}</span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium {{ $item['completed'] ? 'text-gray-500 line-through' : 'text-white' }}">
                                    {{ __($item['title_key']) }}
                                </p>
                                @if (!$item['completed'] && isset($item['description_key']))
                                    <p class="mt-0.5 text-xs text-gray-400 truncate">
                                        {{ __($item['description_key']) }}
                                    </p>
                                @endif
                            </div>

                            {{-- Arrow for incomplete items --}}
                            @if (!$item['completed'])
                                <svg class="flex-shrink-0 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>
@endif

@push('scripts')
<script src="{{ asset('js/ai-sidebar.js') }}" defer></script>
@endpush
