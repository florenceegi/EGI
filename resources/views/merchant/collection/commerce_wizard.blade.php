<x-app-layout page-title="Collection Commerce Setup - {{ $collection->collection_name }}">

    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Collection Commerce Setup</h1>
                <p class="mt-2 text-gray-600">Enable commercial features for:
                    <strong>{{ $collection->collection_name }}</strong>
                </p>
            </div>

            {{-- Progress Indicator --}}
            <div class="mb-8">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-medium text-gray-700">
                        @if ($collection->commercial_status?->value === 'draft')
                            Step 1/3
                        @elseif($collection->commercial_status?->value === 'configured')
                            Step 3/3
                        @else
                            Completed
                        @endif
                    </span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-blue-600 transition-all duration-300"
                        style="width: {{ $collection->commercial_status?->value === 'draft' ? '33%' : ($collection->commercial_status?->value === 'configured' ? '100%' : '100%') }}">
                    </div>
                </div>
            </div>

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
            <form method="POST" action="{{ route('collections.commerce.wizard.update', $collection) }}"
                class="rounded-lg bg-white p-6 shadow-md">
                @csrf

                {{-- Step 1: Delivery Policy --}}
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">1. Delivery Policy</h2>
                    <p class="mb-4 text-sm text-gray-600">Choose what type of items can be sold in this collection</p>

                    <div class="space-y-3">
                        <label
                            class="@if (old('delivery_policy', $collection->delivery_policy) === 'DIGITAL_ONLY') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-start rounded-lg border p-4 transition hover:bg-gray-50">
                            <input type="radio" name="delivery_policy" value="DIGITAL_ONLY" class="mr-3 mt-1"
                                @if (old('delivery_policy', $collection->delivery_policy) === 'DIGITAL_ONLY') checked @endif>
                            <div>
                                <div class="font-medium text-gray-900">Digital Only</div>
                                <div class="text-sm text-gray-600">Only digital items (no physical shipping)</div>
                            </div>
                        </label>

                        <label
                            class="@if (old('delivery_policy', $collection->delivery_policy) === 'PHYSICAL_ALLOWED') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-start rounded-lg border p-4 transition hover:bg-gray-50">
                            <input type="radio" name="delivery_policy" value="PHYSICAL_ALLOWED" class="mr-3 mt-1"
                                @if (old('delivery_policy', $collection->delivery_policy) === 'PHYSICAL_ALLOWED') checked @endif>
                            <div>
                                <div class="font-medium text-gray-900">Physical Allowed</div>
                                <div class="text-sm text-gray-600">Both digital and physical items allowed</div>
                            </div>
                        </label>

                        <label
                            class="@if (old('delivery_policy', $collection->delivery_policy) === 'PHYSICAL_REQUIRED') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-start rounded-lg border p-4 transition hover:bg-gray-50">
                            <input type="radio" name="delivery_policy" value="PHYSICAL_REQUIRED" class="mr-3 mt-1"
                                @if (old('delivery_policy', $collection->delivery_policy) === 'PHYSICAL_REQUIRED') checked @endif>
                            <div>
                                <div class="font-medium text-gray-900">Physical Required</div>
                                <div class="text-sm text-gray-600">Only physical items (shipping required)</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Step 2: Payment Methods --}}
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">2. Payment Methods</h2>
                    <p class="mb-4 text-sm text-gray-600">Current payment methods (configured in settings)</p>

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
                            <p class="text-sm text-red-600">⚠️ No payment methods enabled. Configure payment methods in
                                settings.</p>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Impact Mode --}}
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">3. Impact Mode</h2>
                    <p class="mb-4 text-sm text-gray-600">Choose how this collection creates impact</p>

                    <div class="space-y-3">
                        <label
                            class="@if (old('impact_mode', $collection->impact_mode) === 'EPP') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-start rounded-lg border p-4 transition hover:bg-gray-50">
                            <input type="radio" name="impact_mode" value="EPP" class="mr-3 mt-1"
                                @if (old('impact_mode', $collection->impact_mode) === 'EPP') checked @endif>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">EPP Donation</div>
                                <div class="mb-2 text-sm text-gray-600">Support an Environmental Protection Project
                                </div>
                                <select name="epp_project_id"
                                    class="mt-2 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                    <option value="">Select EPP Project...</option>
                                    @if ($collection->epp_project_id)
                                        <option value="{{ $collection->epp_project_id }}" selected>Current Project (ID:
                                            {{ $collection->epp_project_id }})</option>
                                    @endif
                                </select>
                            </div>
                        </label>

                        <label
                            class="@if (old('impact_mode', $collection->impact_mode) === 'SUBSCRIPTION') border-blue-500 bg-blue-50 @endif flex cursor-pointer items-start rounded-lg border p-4 transition hover:bg-gray-50">
                            <input type="radio" name="impact_mode" value="SUBSCRIPTION" class="mr-3 mt-1"
                                @if (old('impact_mode', $collection->impact_mode) === 'SUBSCRIPTION') checked @endif>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Subscription Plan</div>
                                <div class="mb-2 text-sm text-gray-600">Require subscription for access</div>
                                <input type="number" name="subscription_plan_id"
                                    value="{{ old('subscription_plan_id', $collection->subscription_plan_id) }}"
                                    placeholder="Plan ID"
                                    class="mt-2 w-full rounded-md border-gray-300 text-sm shadow-sm">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between border-t pt-6">
                    <a href="{{ route('home.collections.show', $collection) }}"
                        class="text-gray-600 hover:text-gray-900">
                        ← Back to Collection
                    </a>

                    <div class="space-x-3">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-6 py-2 text-white transition hover:bg-blue-700">
                            Save Settings
                        </button>

                        @if ($collection->commercial_status?->value === 'configured')
                            <form method="POST" action="{{ route('collections.commerce.enable', $collection) }}"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="rounded-lg bg-green-600 px-6 py-2 text-white transition hover:bg-green-700">
                                    ✓ Enable Commerce
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Status Info --}}
            <div class="mt-6 rounded border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                <strong>Current Status:</strong>
                {{ ucfirst(str_replace('_', ' ', $collection->commercial_status?->value ?? 'draft')) }}
                @if ($collection->commercial_status?->value === 'commercial_enabled')
                    ✓ This collection is enabled for commerce
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
