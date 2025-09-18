{{-- resources/views/components/like-button.blade.php --}}
{{-- 💖 Integrated Heart + Counter Like Button Component --}}

@props([
    'resourceType' => null,  // 'egi' or 'collection'
    'resourceId' => null,    // ID of the resource
    'resource' => null,      // Alternative: pass the resource object directly
    'size' => 'medium',
    'likesCount' => null,
    'isLiked' => false,
])

@php
    $sizes = [
        'small' => [
            'button' => 'p-1',
            'heart' => 'w-4 h-4',
            'counter' => 'text-xs font-semibold min-w-[12px] h-3 leading-none',
            'badge' => 'absolute -top-1 -right-1',
        ],
        'medium' => [
            'button' => 'p-1.5',
            'heart' => 'w-5 h-5',
            'counter' => 'text-xs font-semibold min-w-[14px] h-3.5 leading-none',
            'badge' => 'absolute -top-1.5 -right-1.5',
        ],
        'large' => [
            'button' => 'p-2',
            'heart' => 'w-6 h-6',
            'counter' => 'text-sm font-semibold min-w-[16px] h-4 leading-none',
            'badge' => 'absolute -top-2 -right-2',
        ],
    ];

    $config = $sizes[$size] ?? $sizes['medium'];

    // Handle both ways of passing resource data
    if ($resource) {
        $resourceType = $resource->getMorphClass();
        $resourceId = $resource->id;
    }
    // If $resource is null, we use the individual parameters as they are

    // Determine heart color based on like state
    $hasLikes = $likesCount && $likesCount > 0;
    $userLiked = $isLiked;

    if ($userLiked) {
        // User has liked it - RED
        $heartColor = 'text-red-500 fill-current';
        $hoverColor = 'hover:text-red-400';
    } elseif ($hasLikes) {
        // Has likes but not from user - BLUE
        $heartColor = 'text-blue-500 fill-current';
        $hoverColor = 'hover:text-blue-400';
    } else {
        // No likes at all - NEUTRAL/GRAY
        $heartColor = 'text-gray-400';
        $hoverColor = 'hover:text-red-400';
    }
@endphp<button
    type="button"
    class="relative like-button group transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-opacity-50 rounded-full {{ $config['button'] }}"
    data-like-button
    data-resource-type="{{ $resourceType }}"
    data-resource-id="{{ $resourceId }}"
    data-is-liked="{{ $isLiked ? 'true' : 'false' }}"
    aria-label="{{ $isLiked ? __('like.remove_from_favorites') : __('like.add_to_favorites') }}"
    title="{{ $isLiked ? __('like.remove_from_favorites') : __('like.add_to_favorites') }}"
>
    {{-- Heart Icon --}}
    <svg
        class="{{ $config['heart'] }} transition-all duration-300 ease-in-out
               {{ $heartColor }} {{ $hoverColor }}
               group-hover:scale-110 group-active:scale-95"
        data-heart-icon
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
    </svg>

    {{-- Integrated Micro Counter Badge --}}
    @if($likesCount !== null && $likesCount > 0)
        <span
            class="{{ $config['badge'] }} {{ $config['counter'] }}
                   bg-red-500 text-white rounded-full px-1 flex items-center justify-center
                   transition-all duration-300 ease-in-out transform
                   group-hover:scale-110 shadow-sm border border-white/20"
            data-like-counter
            style="font-size: 10px; line-height: 1;"
        >{{ $likesCount > 99 ? '99+' : $likesCount }}</span>
    @endif
</button>

{{-- Pulse Animation Styles --}}
<style>
@keyframes likeHeartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes likeBadgeBounce {
    0% { transform: scale(1) translate(25%, -25%); }
    50% { transform: scale(1.15) translate(25%, -25%); }
    100% { transform: scale(1) translate(25%, -25%); }
}

.like-heart-pulse {
    animation: likeHeartPulse 0.6s ease-in-out;
}

.like-counter-bounce {
    animation: likeBadgeBounce 0.4s ease-in-out;
}

.like-toast-popup {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif !important;
}

.like-toast-title {
    font-weight: 600 !important;
    font-size: 0.875rem !important;
}

.like-toast-content {
    font-size: 0.8rem !important;
    opacity: 0.9 !important;
}

/* Micro badge styling for very small counters */
.like-button [data-like-counter] {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    font-feature-settings: 'tnum';
    letter-spacing: -0.025em;
}

/* Hide counter when it's 0 */
.like-button [data-like-counter]:empty {
    display: none;
}
</style>
