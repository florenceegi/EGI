{{-- resources/views/egis/partials/sidebar/utility-section.blade.php --}}
{{-- 
    Sezione utility display
    VARIABILI: $egi (con $egi->utility)
--}}

{{-- Utility Display Section --}}
@if($egi->utility)
{{-- Utility Preview Card with Modal Trigger --}}
<div class="space-y-4">
    <div class="p-6 border bg-gradient-to-br from-orange-800/20 to-orange-900/20 rounded-xl border-orange-700/30">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-orange-400">
                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ __('utility.title') }}
            </h3>
            <span class="px-3 py-1 text-xs font-medium text-white border rounded-full bg-orange-500/20 border-orange-400/30">
                {{ __('utility.types.' . $egi->utility->type . '.label') }}
            </span>
        </div>
        
        <div class="mb-4">
            <h4 class="mb-2 font-medium text-white">{{ $egi->utility->title }}</h4>
            <p class="text-sm text-gray-300 line-clamp-2">{{ Str::limit($egi->utility->description, 100) }}</p>
        </div>

        @if($egi->utility->getMedia('utility_gallery')->count() > 0)
        <div class="mb-4">
            <p class="mb-2 text-xs text-orange-300">
                {{ __('utility.available_images', ['count' => $egi->utility->getMedia('utility_gallery')->count(), 'title' => $egi->utility->title]) }}
            </p>
            <div class="flex gap-2 overflow-x-auto">
                @foreach($egi->utility->getMedia('utility_gallery')->take(3) as $media)
                <img src="{{ $media->getUrl('thumb') }}" 
                     alt="Utility image" 
                     class="flex-shrink-0 object-cover w-12 h-12 border rounded-lg border-orange-500/30">
                @endforeach
                @if($egi->utility->getMedia('utility_gallery')->count() > 3)
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 border rounded-lg bg-orange-500/20 border-orange-500/30">
                    <span class="text-xs font-medium text-orange-300">+{{ $egi->utility->getMedia('utility_gallery')->count() - 3 }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <button 
            id="utility-modal-trigger"
            class="inline-flex items-center justify-center w-full px-4 py-3 font-medium text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-orange-600/80 to-orange-700/80 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{ __('utility.view_details') }}
        </button>
    </div>
</div>
@endif
