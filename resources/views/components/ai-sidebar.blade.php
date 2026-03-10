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
    'contextMessage' => null, // Optional: Custom page-specific message
    'showChecklist' => true,  // Optional: Toggle checklist section
])

@php
    $isOwner = auth()->check() && auth()->id() === $user?->id;
    $completedCount = collect($checklist)->where('completed', true)->count();
    $totalCount = count($checklist);
    $progressPercent = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

    // Show sidebar if: owner with checklist OR custom context message provided
    $shouldShowSidebar = ($isOwner && $totalCount > 0) || $contextMessage;
@endphp

{{-- Show to profile owner with checklist OR when custom context message is provided --}}
@if ($shouldShowSidebar)
    {{-- Inline style: rendered at component position, not via @push (guest layout renders header before slot) --}}
    <style>
        /* AI Sidebar z-index — above ALL page content, carousels, hero banners, navbar (z-50), stacking contexts (z-20) */
        #ai-sidebar {
            z-index: 1000 !important;
        }
        #ai-sidebar-toggle {
            z-index: 1010 !important;
        }

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

            0%,
            50% {
                opacity: 1;
            }

            51%,
            100% {
                opacity: 0;
            }
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

    {{-- Toggle Button (FAB) --}}
    <button id="ai-sidebar-toggle"
        class="ai-sidebar-toggle fixed bottom-6 right-6 z-[1010] flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-2xl hover:shadow-indigo-500/50"
        title="{{ __('ai_sidebar.toggle_title') }}" aria-label="{{ __('ai_sidebar.toggle_title') }}"
        data-sidebar-open="false">
        {{-- Sparkle icon --}}
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>

        {{-- Progress badge --}}
        @if ($progressPercent < 100)
            <span
                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                {{ $totalCount - $completedCount }}
            </span>
        @else
            <span
                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-white">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </button>

    {{-- Sidebar Panel --}}
    <aside id="ai-sidebar"
        class="ai-sidebar collapsed via-gray-850 fixed bottom-0 right-4 top-20 z-[1000] flex w-80 flex-col overflow-hidden rounded-2xl border border-gray-700/50 bg-gradient-to-b from-gray-900 to-gray-900 shadow-2xl md:w-96"
        data-user-id="{{ $user?->id ?? 0 }}" data-user-type="{{ $userType }}"
        data-checklist="{{ json_encode($checklist) }}" aria-hidden="true">
        {{-- Header --}}
        <div
            class="flex items-center justify-between border-b border-gray-700/50 bg-gradient-to-r from-indigo-900/40 to-purple-900/40 px-4 py-3">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ __('ai_sidebar.title') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('ai_sidebar.subtitle') }}</p>
                </div>
            </div>

            <button id="ai-sidebar-close"
                class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-700/50 hover:text-white"
                aria-label="{{ __('ai_sidebar.close') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- AI Chat Section (Top) --}}
        <div class="flex-1 overflow-hidden border-b border-gray-700/50">
            {{-- AI Message Area --}}
            <div id="ai-sidebar-chat" class="h-full overflow-y-auto p-4 pt-8">
                {{-- Initial AI message - Context-aware or checklist-based --}}
                <div class="ai-message rounded-xl bg-gradient-to-r from-indigo-900/30 to-purple-900/30 p-4">
                    <div class="mb-3 flex items-center gap-2">
                        <div
                            class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs">
                            ✨
                        </div>
                        <span class="text-xs font-medium text-indigo-300">{{ __('ai_sidebar.assistant_name') }}</span>
                    </div>

                    @if ($contextMessage)
                        {{-- Custom page-specific message --}}
                        <div class="space-y-3 text-base font-normal leading-relaxed text-gray-200">
                            {!! $contextMessage !!}
                        </div>
                    @else
                        {{-- Messaggio discorsivo generato in PHP basato su checklist --}}
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
                                <p>{{ __('ai_sidebar.discourse.greeting') }}
                                    <strong>{{ $user->name }}</strong>{{ __('ai_sidebar.discourse.greeting_suffix') }}
                                </p>

                                <p>{{ __('ai_sidebar.discourse.progress_intro') }}
                                    <strong>{{ $completedItems->count() }}</strong>
                                    {{ __('ai_sidebar.discourse.progress_of') }}
                                    <strong>{{ $totalCount }}</strong>{{ __('ai_sidebar.discourse.progress_suffix') }}
                                </p>
                                </p>

                                {{-- Analisi di cosa manca --}}
                                <p><strong>{{ __('ai_sidebar.discourse.missing_title') }}</strong></p>

                                <ul class="ml-4 list-disc space-y-1 text-gray-300">
                                    @foreach ($incompleteItems as $item)
                                        <li>{{ __($item['title_key']) }}</li>
                                    @endforeach
                                </ul>

                                {{-- Suggerimento prioritario --}}
                                @php
                                    $firstIncomplete = $incompleteItems->first();
                                @endphp

                                @if ($firstIncomplete)
                                    <p class="mt-3 border-l-2 border-indigo-500 pl-3 text-indigo-200">
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
                    @endif
                </div>
            </div>

            {{-- Chat Input (for real AI questions) --}}
            <div class="border-t border-gray-700/50 bg-gray-800/50 p-3">
                {{-- Quick suggestion chips --}}
                <div id="ai-sidebar-suggestions" class="mb-2 flex flex-wrap gap-1.5">
                    <button type="button"
                        class="ai-suggestion-chip rounded-full border border-indigo-500/40 bg-indigo-900/30 px-3 py-1 text-xs text-indigo-200 transition-colors hover:bg-indigo-800/50 hover:text-white"
                        data-message="{{ __('ai_sidebar.suggestion_create_egi_msg') }}">
                        ✨ {{ __('ai_sidebar.suggestion_create_egi_label') }}
                    </button>
                </div>
                <form id="ai-sidebar-form" class="flex gap-2">
                    <input type="text" id="ai-sidebar-input"
                        class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500/50"
                        placeholder="{{ __('ai_sidebar.chat_placeholder') }}" autocomplete="off" maxlength="500">
                    <button type="submit" id="ai-sidebar-send"
                        class="rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-3 py-2 text-white transition-all hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                        title="{{ __('ai_sidebar.send') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Checklist Section (Bottom) - Stripe-style --}}
        @if ($showChecklist && $totalCount > 0)
            <div class="max-h-[40%] overflow-y-auto">
                {{-- Progress Header --}}
                <div class="sticky top-0 border-b border-gray-700/50 bg-gray-900/95 px-4 py-3 backdrop-blur">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400">{{ __('ai_sidebar.checklist.progress') }}</span>
                        <span class="text-xs font-semibold text-white">{{ $completedCount }}/{{ $totalCount }}</span>
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-gray-700">
                        <div class="progress-fill h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"
                            style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>

                {{-- Checklist Items --}}
                <ul id="ai-sidebar-checklist" class="divide-y divide-gray-700/50 p-2">
                    @foreach ($checklist as $index => $item)
                        <li class="checklist-item {{ $item['completed'] ? 'completed' : '' }} cursor-pointer rounded-lg px-3 py-2.5"
                            data-step-id="{{ $item['id'] }}" data-action="{{ $item['action'] ?? '' }}"
                            data-modal="{{ $item['modal'] ?? '' }}">
                            <div class="flex items-start gap-3">
                                {{-- Status Icon --}}
                                <div
                                    class="{{ $item['completed'] ? 'bg-green-500' : 'border-2 border-gray-500' }} mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full">
                                    @if ($item['completed'])
                                        <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-500">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="{{ $item['completed'] ? 'text-gray-500 line-through' : 'text-white' }} text-sm font-medium">
                                        {{ __($item['title_key']) }}
                                    </p>
                                    @if (!$item['completed'] && isset($item['description_key']))
                                        <p class="mt-0.5 truncate text-xs text-gray-400">
                                            {{ __($item['description_key']) }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Arrow for incomplete items --}}
                                @if (!$item['completed'])
                                    <svg class="h-4 w-4 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </aside>
@endif

@push('scripts')
    <script src="{{ asset('js/ai-sidebar.js') }}" defer></script>
    <script>
        // Suggestion chips: click → fill input + submit
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.ai-suggestion-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    var input = document.getElementById('ai-sidebar-input');
                    var form = document.getElementById('ai-sidebar-form');
                    if (!input || !form) return;
                    input.value = chip.dataset.message || '';
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                });
            });
        });
    </script>
@endpush
