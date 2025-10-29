{{-- Art Advisor Modal Component --}}
{{--
    AI Art Advisor - Reusable Modal Component
    
    USAGE:
    <x-art-advisor-modal 
        :context="['egi_id' => $egi->id, 'title' => $egi->title, ...]"
        :mode="'generate_description'"
        :auto-open="false"
    />
    
    PROPS:
    - context: array - EGI/Collection context data
    - mode: string - Initial mode (generate_description, suggest_traits, pricing_advice, general)
    - autoOpen: bool - Auto-open modal on page load (default: false)
--}}

@props([
    'context' => [],
    'mode' => 'general',
    'autoOpen' => false
])

{{-- Modal Overlay --}}
<div id="art-advisor-modal" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm"
     data-context="{{ json_encode($context) }}"
     data-mode="{{ $mode }}"
     data-auto-open="{{ $autoOpen ? 'true' : 'false' }}">
    
    {{-- Modal Container --}}
    <div class="relative flex h-[90vh] w-[95vw] max-w-4xl flex-col overflow-hidden rounded-2xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 shadow-2xl">
        
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-700/50 bg-gradient-to-r from-blue-900/30 to-purple-900/30 px-6 py-4">
            <div>
                <h2 class="text-xl font-bold text-white">
                    {{ __('art_advisor.title') }}
                </h2>
                <p class="text-sm text-gray-400">
                    {{ __('art_advisor.subtitle') }}
                </p>
            </div>
            
            <button id="art-advisor-close" 
                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-700/50 hover:text-white"
                    aria-label="{{ __('art_advisor.close') }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Expert Selector --}}
        <div class="border-b border-gray-700/50 bg-gray-800/50 px-6 py-3">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-400">{{ __('art_advisor.chat.select_expert') }}:</span>
                
                <button data-expert="creative" 
                        class="expert-selector-btn active flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg transition-all hover:shadow-xl">
                    <span>🎨</span>
                    <span>{{ __('art_advisor.experts.creative') }}</span>
                </button>
                
                <button data-expert="platform" 
                        class="expert-selector-btn flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-gray-300 transition-all hover:bg-gray-600">
                    <span>📖</span>
                    <span>{{ __('art_advisor.experts.platform') }}</span>
                </button>

                {{-- Vision Toggle --}}
                <button id="vision-toggle" 
                        class="ml-auto flex items-center gap-2 rounded-lg border border-gray-600 bg-gray-700/50 px-3 py-2 text-sm text-gray-300 transition-all hover:border-blue-500 hover:bg-gray-600 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span id="vision-toggle-text">{{ __('art_advisor.actions.vision_mode') }}</span>
                </button>
            </div>
        </div>

        {{-- Context Info (collapsible) --}}
        @if (!empty($context))
        <details class="border-b border-gray-700/50 bg-gray-800/30" open>
            <summary class="cursor-pointer px-6 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700/30">
                📋 {{ __('art_advisor.context.title') }}
            </summary>
            <div class="space-y-2 px-6 pb-4 text-sm">
                @if (isset($context['egi_number']) || isset($context['egi_id']))
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">{{ __('art_advisor.context.egi_number') }}:</span>
                    <span class="font-mono text-blue-400">#{{ $context['egi_number'] ?? $context['egi_id'] }}</span>
                </div>
                @endif

                @if (isset($context['title']))
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">{{ __('art_advisor.context.title') }}:</span>
                    <span class="text-white">{{ $context['title'] }}</span>
                </div>
                @endif

                @if (isset($context['collection_name']))
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">{{ __('art_advisor.context.collection') }}:</span>
                    <span class="text-gray-300">{{ $context['collection_name'] }}</span>
                </div>
                @endif

                @if (isset($context['price_eur']))
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">{{ __('art_advisor.context.price') }}:</span>
                    <span class="font-semibold text-green-400">€{{ number_format($context['price_eur'], 2) }}</span>
                </div>
                @endif

                @if (isset($context['existing_traits']) && !empty($context['existing_traits']))
                <div class="flex items-start gap-2">
                    <span class="text-gray-500">{{ __('art_advisor.context.traits') }}:</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($context['existing_traits'] as $category => $value)
                        <span class="rounded-full bg-gray-700 px-2 py-0.5 text-xs text-gray-300">
                            {{ $value }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </details>
        @endif

        {{-- Chat Messages Area --}}
        <div id="art-advisor-messages" 
             class="flex-1 space-y-4 overflow-y-auto p-6"
             style="scroll-behavior: smooth;">
            
            {{-- Welcome message will be inserted here by JS --}}
        </div>

        {{-- Examples (optional hint) --}}
        <div id="art-advisor-examples" class="border-t border-gray-700/50 bg-gray-800/30 px-6 py-3">
            <div class="text-xs text-gray-500">
                💡 {{ __('art_advisor.tips.vision_tip') }}
            </div>
        </div>

        {{-- Input Area --}}
        <div class="border-t border-gray-700/50 bg-gray-800/50 p-4">
            <form id="art-advisor-form" class="flex gap-3">
                <input type="text" 
                       id="art-advisor-input"
                       class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                       placeholder="{{ __('art_advisor.chat.placeholder') }}"
                       autocomplete="off"
                       maxlength="1000">
                
                <button type="submit" 
                        id="art-advisor-send-btn"
                        class="rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 font-medium text-white shadow-lg transition-all hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50">
                    {{ __('art_advisor.chat.send') }}
                </button>
            </form>
            
            {{-- Character counter --}}
            <div class="mt-2 text-right text-xs text-gray-500">
                <span id="char-count">0</span> / 1000
            </div>
        </div>
    </div>
</div>

{{-- Floating Trigger Button (optional - can be customized per page) --}}
<button id="art-advisor-trigger" 
        class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-purple-600 text-2xl text-white shadow-2xl transition-all hover:scale-110 hover:shadow-blue-500/50"
        title="{{ __('art_advisor.title') }}">
    🎨
</button>

{{-- Include JavaScript --}}
@push('scripts')
<script src="{{ asset('js/art-advisor.js') }}"></script>
@endpush

