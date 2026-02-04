<div
    class="relative mb-4 rounded-xl border border-l-4 border-b-gray-700 border-l-green-500 border-r-gray-700 border-t-gray-700 bg-gray-800 p-6 shadow-lg">
    <div class="flex items-start">
        <div class="mr-4 rounded-lg bg-green-500/10 p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="mb-2 text-lg font-bold text-white">{{ __('commerce.notifications.shipped.title_internal') }}</h3>
            <p class="mb-2 text-gray-300">
                {{ __('commerce.notifications.shipped.message_internal', ['item' => $notification->data['egi_name'] ?? 'Item']) }}
            </p>
            <div class="inline-block min-w-[250px] rounded border border-gray-700/50 bg-gray-900/50 p-3">
                <div class="text-xs uppercase text-gray-500">{{ __('commerce.notifications.shipped.tracking_info') }}
                </div>
                <div class="mt-1 font-mono text-sm text-white">
                    {{ $notification->data['carrier'] ?? 'N/A' }}<br>
                    <span
                        class="tracking-wider text-green-400">{{ $notification->data['tracking_code'] ?? 'N/A' }}</span>
                </div>
            </div>

            @if (is_null($notification->read_at))
                <div class="mt-4 flex justify-end">
                    <form action="{{ route('notifications.commerce.archive') }}" method="POST">
                        @csrf
                        <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                        <button type="submit"
                            class="rounded bg-gray-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            {{ __('label.archived') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
        <span class="ml-4 whitespace-nowrap text-xs text-gray-500">
            {{ $notification->created_at->diffForHumans() }}
        </span>
    </div>
</div>
