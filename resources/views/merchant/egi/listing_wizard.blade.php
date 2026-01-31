<x-app-layout page-title="{{ __('commerce.listing.title') }} - {{ $egi->title }}">

    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('commerce.listing.title') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('commerce.listing.subtitle') }} <strong>{{ $egi->title }}</strong>
                </p>
                <p class="text-sm text-gray-500">{{ __('commerce.listing.collection_label') }}
                    {{ $collection->collection_name }}</p>
            </div>

            {{-- Collection Status Check --}}
            @if ($collection->commercial_status?->value !== 'commercial_enabled')
                <div class="mb-6 rounded border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800">
                    {{ __('commerce.listing.not_enabled_warning') }}
                    <a href="{{ route('collections.commerce.wizard', $collection) }}" class="underline">
                        {{ __('commerce.listing.enable_link') }}
                    </a>
                </div>
            @endif

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Wizard Form --}}
            <form method="POST" action="{{ route('egi.listing.wizard.update', $egi) }}"
                class="rounded-lg bg-white p-6 shadow-md">
                @csrf

                {{-- Step 1: Basics --}}
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">{{ __('commerce.listing.basics_title') }}</h2>

                    <div class="mb-4">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700">{{ __('commerce.listing.sale_mode_label') }}</label>
                        <div class="space-y-2">
                            <label
                                class="@if (old('sale_mode', $egi->sale_mode) === 'fixed_price') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-center rounded border p-3 hover:bg-gray-50">
                                <input type="radio" name="sale_mode" value="fixed_price" class="mr-3"
                                    @if (old('sale_mode', $egi->sale_mode) === 'fixed_price') checked @endif>
                                <div>
                                    <div class="font-medium">{{ __('commerce.listing.mode_fixed') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('commerce.listing.mode_fixed_desc') }}
                                    </div>
                                </div>
                            </label>

                            <label
                                class="@if (old('sale_mode', $egi->sale_mode) === 'auction') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-center rounded border p-3 hover:bg-gray-50">
                                <input type="radio" name="sale_mode" value="auction" class="mr-3"
                                    @if (old('sale_mode', $egi->sale_mode) === 'auction') checked @endif>
                                <div>
                                    <div class="font-medium">{{ __('commerce.listing.mode_auction') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('commerce.listing.mode_auction_desc') }}
                                    </div>
                                </div>
                            </label>

                            <label
                                class="@if (old('sale_mode', $egi->sale_mode) === 'not_for_sale') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-center rounded border p-3 hover:bg-gray-50">
                                <input type="radio" name="sale_mode" value="not_for_sale" class="mr-3"
                                    @if (old('sale_mode', $egi->sale_mode) === 'not_for_sale') checked @endif>
                                <div>
                                    <div class="font-medium">{{ __('commerce.listing.mode_none') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('commerce.listing.mode_none_desc') }}
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div id="fixed-price-fields" class="@if (old('sale_mode', $egi->sale_mode) !== 'fixed_price') hidden @endif">
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700">{{ __('commerce.listing.price_label') }}</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $egi->price) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm" placeholder="0.00">
                        </div>

                        <div id="auction-fields" class="@if (old('sale_mode', $egi->sale_mode) !== 'auction') hidden @endif">
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700">{{ __('commerce.listing.min_bid_label') }}</label>
                            <input type="number" step="0.01" name="auction_minimum_price"
                                value="{{ old('auction_minimum_price', $egi->auction_minimum_price) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- Step 2: Payment Methods (Read-Only) --}}
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">{{ __('commerce.listing.payment_title') }}
                    </h2>
                    <p class="mb-4 text-sm text-gray-600">{{ __('commerce.listing.payment_desc') }}</p>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        @if ($paymentMethods->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($paymentMethods as $method)
                                    <li class="flex items-center text-sm">
                                        <svg class="mr-2 h-5 w-5 text-green-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $method->method)) }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-600">{{ __('commerce.listing.no_payments') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Shipping (Conditional) --}}
                @if ($shippingRequired || old('is_physical'))
                    <div class="mb-8">
                        <h2 class="mb-4 text-xl font-semibold text-gray-800">
                            {{ __('commerce.listing.shipping_title') }}</h2>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_physical" value="1"
                                    @if (old('is_physical', $egi->is_physical)) checked @endif
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span
                                    class="ml-2 text-sm font-medium text-gray-700">{{ __('commerce.listing.is_physical_label') }}</span>
                            </label>

                            @if ($egi->utility && $egi->utility->requires_fulfillment)
                                <p class="mt-2 text-sm text-orange-600">
                                    {{ __('commerce.listing.utility_fulfillment_warning') }}
                                </p>
                            @endif
                        </div>

                        <div id="shipping-profile-fields" class="@if (!old('is_physical', $egi->is_physical) && !$shippingRequired) hidden @endif">
                            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <h3 class="mb-3 font-medium text-gray-800">
                                    {{ __('commerce.listing.shipping_profile') }}</h3>

                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-gray-700">{{ __('commerce.listing.weight_label') }}</label>
                                        <input type="number" name="shipping_profile[weight_g]"
                                            value="{{ old('shipping_profile.weight_g', $egi->shipping_profile['weight_g'] ?? '') }}"
                                            class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                                            placeholder="e.g., 500">
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-gray-700">{{ __('commerce.listing.fragile_label') }}</label>
                                        <label class="mt-2 flex items-center">
                                            <input type="checkbox" name="shipping_profile[fragile]" value="1"
                                                @if (old('shipping_profile.fragile', $egi->shipping_profile['fragile'] ?? false)) checked @endif
                                                class="rounded border-gray-300">
                                            <span
                                                class="ml-2 text-sm">{{ __('commerce.listing.handle_care_label') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700">{{ __('commerce.listing.dimensions_label') }}</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <input type="number" name="shipping_profile[dimensions_mm][l]"
                                            value="{{ old('shipping_profile.dimensions_mm.l', $egi->shipping_profile['dimensions_mm']['l'] ?? '') }}"
                                            class="rounded-md border-gray-300 text-sm shadow-sm" placeholder="L">
                                        <input type="number" name="shipping_profile[dimensions_mm][w]"
                                            value="{{ old('shipping_profile.dimensions_mm.w', $egi->shipping_profile['dimensions_mm']['w'] ?? '') }}"
                                            class="rounded-md border-gray-300 text-sm shadow-sm" placeholder="W">
                                        <input type="number" name="shipping_profile[dimensions_mm][h]"
                                            value="{{ old('shipping_profile.dimensions_mm.h', $egi->shipping_profile['dimensions_mm']['h'] ?? '') }}"
                                            class="rounded-md border-gray-300 text-sm shadow-sm" placeholder="H">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-gray-700">{{ __('commerce.listing.notes_label') }}</label>
                                    <textarea name="shipping_profile[notes]" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                                        placeholder="{{ __('commerce.listing.notes_placeholder') }}">{{ old('shipping_profile.notes', $egi->shipping_profile['notes'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center justify-between border-t pt-6">
                    <a href="{{ route('egis.show', $egi) }}" class="text-gray-600 hover:text-gray-900">
                        {{ __('commerce.listing.back_to_egi') }}
                    </a>

                    <div class="space-x-3">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-6 py-2 text-white transition hover:bg-blue-700">
                            {{ __('commerce.listing.save_changes') }}
                        </button>

                        @if ($collection->commercial_status?->value === 'commercial_enabled')
                            <form method="POST" action="{{ route('egi.listing.publish', $egi) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="rounded-lg bg-green-600 px-6 py-2 text-white transition hover:bg-green-700">
                                    {{ __('commerce.listing.publish_listing') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Policy Enforcement Info --}}
            @if ($collection->delivery_policy)
                <div class="mt-6 rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <strong>{{ __('commerce.listing.collection_policy_label') }}</strong>
                    {{ ucfirst(str_replace('_', ' ', $collection->delivery_policy?->value ?? '')) }}
                    @if ($collection->delivery_policy?->value === 'DIGITAL_ONLY' && $egi->is_physical)
                        <span class="font-bold text-red-600">{{ __('commerce.listing.conflict_digital') }}</span>
                    @elseif($collection->delivery_policy?->value === 'PHYSICAL_REQUIRED' && !$egi->is_physical)
                        <span class="font-bold text-red-600">{{ __('commerce.listing.conflict_physical') }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle price/auction fields based on sale_mode selection
            document.querySelectorAll('input[name="sale_mode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const fixedFields = document.getElementById('fixed-price-fields');
                    const auctionFields = document.getElementById('auction-fields');
                    const shippingFields = document.getElementById('shipping-profile-fields');

                    fixedFields.classList.add('hidden');
                    auctionFields.classList.add('hidden');

                    if (this.value === 'fixed_price') {
                        fixedFields.classList.remove('hidden');
                    } else if (this.value === 'auction') {
                        auctionFields.classList.remove('hidden');
                    }
                });
            });

            // Toggle shipping profile visibility
            const physicalCheckbox = document.querySelector('input[name="is_physical"]');
            if (physicalCheckbox) {
                physicalCheckbox.addEventListener('change', function() {
                    const shippingFields = document.getElementById('shipping-profile-fields');
                    if (this.checked) {
                        shippingFields.classList.remove('hidden');
                    } else {
                        shippingFields.classList.add('hidden');
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
