<div class="group relative mb-4 overflow-hidden rounded-xl border border-gray-700 bg-gray-800 p-6 shadow-lg">
    <!-- Glow effect -->
    <div
        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-500/20 blur-3xl transition-all duration-500 group-hover:bg-blue-500/30">
    </div>

    <div class="relative z-10 flex items-start">
        <!-- Icon -->
        <div class="mr-4 rounded-lg border border-blue-500/20 bg-blue-500/10 p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd"
                    d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                    clip-rule="evenodd" />
            </svg>
        </div>

        <div class="flex-1">
            <h3 class="mb-1 text-lg font-bold text-white">{{ __('commerce.notifications.sold.title') }}</h3>
            <p class="mb-4 text-sm text-gray-400">
                {{ __('commerce.notifications.sold.part_user') }}
                <span class="font-bold text-gray-200">{{ $notification->data['buyer_name'] ?? 'N/A' }}</span>
                {{ __('commerce.notifications.sold.part_bought') }}
                <span class="font-bold text-gray-200">{{ $notification->data['egi_name'] ?? 'N/A' }}</span>
                {{ __('commerce.notifications.sold.part_for') }}
                <span class="font-bold text-gray-200">{{ $notification->data['amount'] ?? 'N/A' }}.</span>
                <span class="ml-2 rounded bg-gray-900 px-1 text-xs text-gray-600">ID: {{ $notification->id }}</span>
            </p>

            <!-- Shipping Details Box -->
            @if (isset($notification->data['shipping_snapshot']) && is_array($notification->data['shipping_snapshot']))
                <div class="mb-4 rounded-lg border border-gray-700/50 bg-gray-900/50 p-4">
                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{ __('commerce.notifications.sold.shipping_address') }}</h4>
                    <p class="text-sm leading-relaxed text-gray-300">
                        {{ $notification->data['shipping_snapshot']['full_name'] ?? 'N/A' }}<br>
                        {{ $notification->data['shipping_snapshot']['address_line_1'] ?? '' }}<br>
                        @if (!empty($notification->data['shipping_snapshot']['address_line_2']))
                            {{ $notification->data['shipping_snapshot']['address_line_2'] }}<br>
                        @endif
                        {{ $notification->data['shipping_snapshot']['city'] ?? '' }},
                        {{ $notification->data['shipping_snapshot']['state'] ?? '' }}
                        {{ $notification->data['shipping_snapshot']['postal_code'] ?? '' }}<br>
                        {{ $notification->data['shipping_snapshot']['country'] ?? '' }}<br>
                        <span
                            class="mt-1 block text-xs text-gray-500">{{ $notification->data['shipping_snapshot']['phone'] ?? '' }}</span>
                    </p>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-2 flex space-x-3">
                <!-- Main Action: Ship -->
                <button
                    onclick="document.getElementById('shipment-modal-{{ $notification->id }}').classList.remove('hidden'); document.getElementById('shipment-modal-{{ $notification->id }}').classList.add('flex')"
                    class="flex items-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow-lg shadow-blue-900/20 transition-all duration-200 hover:bg-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    {{ __('commerce.notifications.sold.action_ship') }}
                </button>

                <!-- Vanilla JS Modal -->
                <div id="shipment-modal-{{ $notification->id }}"
                    class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/80 px-4 py-6 backdrop-blur-sm transition-all duration-300 sm:px-0">
                    <div
                        class="relative w-full max-w-2xl transform rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-xl transition-all">

                        <!-- Header -->
                        <div class="mb-4">
                            <h2 class="text-lg font-medium text-white">
                                {{ __('commerce.notifications.sold.modal_title') }}
                            </h2>
                            <p class="text-sm text-gray-400">
                                {{ __('commerce.notifications.sold.modal_desc') }}
                            </p>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('notifications.commerce.shipped') }}">
                            @csrf
                            <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                            <input type="hidden" name="action" value="shipped">

                            <div class="mb-4">
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-400">{{ __('commerce.notifications.sold.carrier') }}</label>
                                <input type="text" name="carrier" required
                                    class="w-full rounded-lg border border-gray-700 bg-gray-900 p-2 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="{{ __('commerce.notifications.sold.placeholder_carrier') }}" />
                            </div>

                            <div class="mb-6">
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-400">{{ __('commerce.notifications.sold.tracking_code') }}</label>
                                <input type="text" name="tracking_code" required
                                    class="w-full rounded-lg border border-gray-700 bg-gray-900 p-2 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    placeholder="{{ __('commerce.notifications.sold.placeholder_tracking') }}" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="button"
                                    onclick="document.getElementById('shipment-modal-{{ $notification->id }}').classList.add('hidden'); document.getElementById('shipment-modal-{{ $notification->id }}').classList.remove('flex')"
                                    class="mr-3 rounded-lg bg-gray-700 px-4 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                                    {{ __('commerce.notifications.sold.cancel') }}
                                </button>
                                <button type="submit"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-white shadow-lg transition-colors hover:bg-blue-500">
                                    {{ __('commerce.notifications.sold.confirm') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timestamp -->
        <span class="ml-4 whitespace-nowrap text-xs text-gray-500">
            {{ $notification->created_at->diffForHumans() }}
        </span>
    </div>
</div>
