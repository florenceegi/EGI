@php
    // Determine notification priority and styling
    $priority = 'medium'; // default
    $priorityColors = [
        'high' => 'from-red-500 to-pink-600',
        'medium' => 'from-yellow-500 to-orange-500',
        'low' => 'from-green-500 to-emerald-500',
    ];

    $borderColors = [
        'high' => 'border-red-500/30 hover:border-red-400/50',
        'medium' => 'border-yellow-500/30 hover:border-yellow-400/50',
        'low' => 'border-green-500/30 hover:border-green-400/50',
    ];

    // Priority logic based on notification type and age
    if ($notif->type === 'App\Notifications\Wallets\WalletCreation') {
        $priority = 'high';
        $priorityLabel = __('Urgent');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>';
    } elseif ($notif->type === 'App\Notifications\Commerce\EgiSoldNotification') {
        $priority = 'high';
        $priorityLabel = __('New Sale');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } elseif ($notif->type === 'App\Notifications\Reservations\ReservationHighest') {
        $priority = 'high';
        $priorityLabel = __('New High Bid');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>';
    } elseif ($notif->type === 'App\Notifications\Invitations\InvitationRequest') {
        $priority = 'medium';
        $priorityLabel = __('Invitation');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>';
    } elseif ($notif->type === 'App\Notifications\Reservations\ReservationSuperseded') {
        $priority = 'medium';
        $priorityLabel = __('Superseded');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>';
    } elseif ($notif->created_at->diffInHours() < 2) {
        $priority = 'high';
        $priorityLabel = __('Recent');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } elseif ($notif->created_at->diffInDays() < 1) {
        $priority = 'medium';
        $priorityLabel = __('Today');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>';
    } else {
        $priority = 'low';
        $priorityLabel = __('Older');
        $priorityIcon =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    }

    $gradientClass = $priorityColors[$priority];
    $borderClass = $borderColors[$priority];
@endphp

<!-- Modern Notification Card with Priority System - bordi ridotti -->
<div class="notification-thumbnail group relative transform cursor-pointer overflow-hidden rounded-2xl bg-white/10 p-6 transition-all duration-300 hover:scale-105 hover:bg-white/20 hover:shadow-2xl hover:shadow-purple-500/20"
    data-notification-id="{{ $notif->id }}" data-created-at="{{ $notif->created_at }}"
    data-status="{{ $notif->model->status ?? null }}" data-priority="{{ $priority }}">

    <!-- Priority Indicator Strip -->
    <div class="{{ $gradientClass }} absolute left-0 right-0 top-0 h-1 bg-gradient-to-r"></div>

    <!-- Priority Badge (Top Right) - posizionato all'interno del box -->
    <div
        class="{{ $gradientClass }} {{ $priority === 'high' ? 'animate-pulse' : '' }} absolute right-[3px] top-0 rounded-full bg-gradient-to-r px-2 py-1 text-xs font-medium text-white shadow-lg">
        {{ $priorityLabel }}
    </div>

    <!-- Card Header with Icon and Type -->
    <div class="mb-3 flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <!-- Notification Type Icon with Priority Colors -->
            @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
                <div
                    class="{{ $gradientClass }} {{ $priority === 'high' ? 'animate-pulse' : '' }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @elseif ($notif->type === 'App\Notifications\Commerce\EgiSoldNotification')
                <div
                    class="{{ $gradientClass }} {{ $priority === 'high' ? 'animate-pulse' : '' }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @elseif ($notif->type === 'App\Notifications\Reservations\ReservationHighest')
                <div
                    class="{{ $gradientClass }} {{ $priority === 'high' ? 'animate-pulse' : '' }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @elseif ($notif->type === 'App\Notifications\Invitations\InvitationRequest')
                <div class="{{ $gradientClass }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @elseif ($notif->type === 'App\Notifications\Reservations\ReservationSuperseded')
                <div class="{{ $gradientClass }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $priorityIcon !!}
                    </svg>
                </div>
            @else
                <div class="{{ $gradientClass }} rounded-xl bg-gradient-to-r p-2">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
                    </svg>
                </div>
            @endif

            <!-- Type Badge with Priority Context -->
            <span
                class="rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-2 py-1 text-xs font-medium text-white">
                @if ($notif->type === 'App\Notifications\Wallets\WalletCreation')
                    {{ __('Wallet') }}
                @elseif($notif->type === 'App\Notifications\Commerce\EgiSoldNotification')
                    {{ __('Sale') }}
                @elseif($notif->type === 'App\Notifications\Reservations\ReservationHighest')
                    {{ __('Highest Bid') }}
                @elseif($notif->type === 'App\Notifications\Reservations\ReservationSuperseded')
                    {{ __('Superseded') }}
                @elseif($notif->type === 'App\Notifications\Invitations\InvitationRequest')
                    {{ __('Invitation') }}
                @else
                    {{ __('Alert') }}
                @endif
            </span>
        </div> <!-- Time Badge with Priority Context -->
        <div class="flex flex-col items-end space-y-1">
            <span class="rounded-lg bg-white/5 px-2 py-1 text-xs text-gray-400">
                {{ $notif->created_at->diffForHumans() }}
            </span>
            @if ($priority === 'high')
                <div class="flex items-center space-x-1">
                    <div class="h-2 w-2 animate-ping rounded-full bg-red-500"></div>
                    <span class="text-xs font-medium text-red-400">{{ __('Urgent') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Card Content -->
    <div class="space-y-2">
        <!-- Message Preview -->
        <h4 class="line-clamp-2 text-sm font-medium text-white transition-colors group-hover:text-purple-200">
            @if (!isset($notif->data['message']))
                {{ __($notif->model->message) }}
            @else
                {{ Str::limit($notif->data['message'], 80) }}
            @endif
        </h4>

        <!-- Status Indicator with Priority -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div
                    class="{{ $gradientClass }} {{ $priority === 'high' ? 'animate-pulse' : '' }} h-2 w-2 rounded-full bg-gradient-to-r">
                </div>
                <span class="text-xs text-gray-400">
                    @if ($priority === 'high')
                        {{ __('Requires immediate attention') }}
                    @elseif($priority === 'medium')
                        {{ __('Awaiting action') }}
                    @else
                        {{ __('Review when convenient') }}
                    @endif
                </span>
            </div>

            <!-- Action Hint with Priority Arrow -->
            <svg class="{{ $priority === 'high' ? 'animate-bounce' : '' }} h-4 w-4 text-gray-400 transition-colors group-hover:text-purple-400"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>

        <!-- Expiration Warning (hidden by default, shown by JS when needed) -->
        <p id="expiration-warning" class="text-xs text-transparent" style="display: none;"></p>
        <div id="text-tooltip" title=""></div>
    </div>
</div>
