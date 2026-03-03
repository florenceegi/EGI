{{-- 💼 DASHBOARD MONETIZATION MODAL --}}
<div id="dashboardModal" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm transition-opacity"
    aria-labelledby="dashboardModalLabel" role="dialog" aria-modal="true">

    {{-- Wrapper: Full screen on mobile, Centered flex on desktop --}}
    <div class="relative h-full w-full md:flex md:h-screen md:items-center md:justify-center md:px-4 md:py-8">
        {{-- Modal Card: Full screen column on mobile, Rounded card on desktop --}}
        <div style="justify-content: flex-start !important;"
            class="flex h-full w-full flex-col justify-start bg-gray-900 shadow-2xl md:h-auto md:max-h-[90vh] md:max-w-6xl md:rounded-2xl md:border md:border-gray-700 md:bg-gray-900">

            {{-- Header: Sticky/Fixed at top --}}
            <div
                class="flex shrink-0 items-center justify-between border-b border-gray-700 bg-gradient-to-r from-indigo-900/30 to-purple-900/30 px-4 py-3 md:rounded-t-2xl md:px-6 md:py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600/20">
                        <span class="material-symbols-outlined text-xl text-indigo-400">dashboard</span>
                    </div>
                    <div>
                        <h3 id="dashboardModalLabel" class="text-lg font-bold text-white md:text-xl">
                            {{ __('collection.show.dashboard.title') }}
                        </h3>
                        <p class="text-xs text-gray-400 md:text-sm">{{ $collection->collection_name }}</p>
                    </div>
                </div>
                {{-- Close Button --}}
                <button id="closeDashboardX"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-800 hover:text-white">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            {{-- Scrollable Content Area --}}
            <div style="justify-content: flex-start !important;"
                class="flex flex-1 flex-col justify-start overflow-y-auto p-4 pb-32 md:p-6 md:pb-6">
                {{-- Tabs Navigation (Desktop Only) --}}
                <div
                    class="scrollbar-hide mb-6 hidden gap-2 overflow-x-auto whitespace-nowrap border-b border-gray-700 pb-1 md:flex">
                    <button
                        class="dashboard-tab active flex items-center whitespace-nowrap px-3 py-2 text-sm md:px-4 md:text-base"
                        data-tab="monetization">
                        <span class="material-symbols-outlined mr-2 text-lg md:text-xl">payments</span>
                        {{ __('collection.show.dashboard.tab_monetization') }}
                    </button>
                    <button
                        class="dashboard-tab flex items-center whitespace-nowrap px-3 py-2 text-sm md:px-4 md:text-base"
                        data-tab="statistics">
                        <span class="material-symbols-outlined mr-2 text-lg md:text-xl">analytics</span>
                        {{ __('collection.show.dashboard.tab_statistics') }}
                    </button>
                    <button
                        class="dashboard-tab flex items-center whitespace-nowrap px-3 py-2 text-sm md:px-4 md:text-base"
                        data-tab="subscription">
                        <span class="material-symbols-outlined mr-2 text-lg md:text-xl">subscriptions</span>
                        {{ __('collection.show.dashboard.tab_subscription') }}
                    </button>
                    <button
                        class="dashboard-tab flex items-center whitespace-nowrap px-3 py-2 text-sm md:px-4 md:text-base"
                        data-tab="payments">
                        <span class="material-symbols-outlined mr-2 text-lg md:text-xl">credit_card</span>
                        {{ __('collection.show.dashboard.tab_payments') }}
                    </button>
                </div>

                {{-- Tab: Monetization --}}
                <div id="tab-monetization" class="dashboard-tab-content">
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Current Status --}}
                        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-4 md:p-6">
                            <h4 class="mb-4 flex items-center text-lg font-semibold text-white">
                                <span class="material-symbols-outlined mr-2">info</span>
                                {{ __('collection.show.dashboard.current_monetization') }}
                            </h4>

                            @php
                                $monetizationType = $collection->monetization_type ?? 'epp';
                                $isEpp = $monetizationType === 'epp';
                                // Only show Company Mode for actual Company users, not Creators with EPP voluntary
                                $isCompanyCollection =
                                    $collection->creator &&
                                    $collection->creator->usertype ===
                                        \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;
                            @endphp

                            @if ($isCompanyCollection)
                                {{-- COMPANY MODE: Subscription Required + Voluntary EPP Donation --}}
                                <div
                                    class="flex items-start gap-4 rounded-lg border border-indigo-500/20 bg-indigo-500/10 p-3 md:p-4">
                                    <div class="flex-shrink-0 rounded-lg bg-indigo-500/20 p-2">
                                        <span class="material-symbols-outlined text-2xl text-indigo-400">business</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <h5 class="font-semibold text-indigo-400">
                                                {{ __('collection.show.dashboard.company_mode') }}</h5>
                                            @if ($collection->subscription_status === 'active')
                                                <span
                                                    class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400">
                                                    {{ __('collection.show.dashboard.active') }}
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full border border-yellow-500/30 bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400">
                                                    {{ __('collection.show.dashboard.subscription_required') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mb-3 text-sm text-gray-300">
                                            {{ __('collection.company_requires_subscription') }}
                                        </p>
                                        <div class="space-y-1 text-sm text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.subscription_access') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-green-400"
                                                    style="font-size: 16px;">volunteer_activism</span>
                                                {{ __('collection.company_epp_voluntary') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Company Voluntary EPP Donation Section --}}
                                <div class="mt-4 rounded-lg border border-green-500/20 bg-green-500/5 p-4">
                                    <h5 class="mb-3 flex items-center gap-2 font-semibold text-green-400">
                                        <span class="material-symbols-outlined">eco</span>
                                        {{ __('collection.company_donation_title') }}
                                    </h5>
                                    <p class="mb-4 text-sm text-gray-400">
                                        {{ __('collection.company_donation_subtitle') }}
                                    </p>

                                    @if ($collection->eppProject)
                                        <div class="mb-4 rounded border border-green-500/20 bg-green-500/10 p-3">
                                            <p class="text-sm text-gray-300">
                                                {{ __('collection.show.dashboard.supporting') }}:
                                                <strong
                                                    class="text-green-400">{{ $collection->eppProject->name }}</strong>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ __('collection.donation_percentage_label') }}:
                                                <strong
                                                    class="text-green-400">{{ $collection->epp_donation_percentage ?? 0 }}%</strong>
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Donation Percentage Slider --}}
                                    <div class="mb-4">
                                        <label for="companyDonationPercentage"
                                            class="mb-2 block text-sm font-medium text-gray-300">
                                            {{ __('collection.donation_percentage_label') }}
                                        </label>
                                        <div class="flex items-center gap-4">
                                            <input type="range" id="companyDonationPercentage"
                                                class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700 accent-green-500"
                                                min="0" max="100" step="1"
                                                value="{{ $collection->epp_donation_percentage ?? 0 }}">
                                            <span id="donationPercentageValue"
                                                class="min-w-[3rem] text-center text-lg font-bold text-green-400">
                                                {{ $collection->epp_donation_percentage ?? 0 }}%
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ __('collection.donation_percentage_help') }}
                                        </p>
                                    </div>

                                    <div class="flex gap-2">
                                        @if (!$collection->eppProject)
                                            <button onclick="openEppProjectSelectionModal()"
                                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 font-medium text-white transition-all hover:bg-green-700">
                                                <span class="material-symbols-outlined text-lg">eco</span>
                                                {{ __('collection.select_epp_for_donation') }}
                                            </button>
                                        @else
                                            <button onclick="updateCompanyDonation()"
                                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 font-medium text-white transition-all hover:bg-green-700">
                                                <span class="material-symbols-outlined text-lg">save</span>
                                                {{ __('collection.update_donation') }}
                                            </button>
                                            <button onclick="openEppProjectSelectionModal()"
                                                class="flex items-center justify-center gap-2 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 font-medium text-green-400 transition-all hover:bg-green-500/20">
                                                <span class="material-symbols-outlined text-lg">swap_horiz</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($isEpp)
                                {{-- EPP Mode --}}
                                <div
                                    class="flex items-start gap-4 rounded-lg border border-green-500/20 bg-green-500/10 p-4">
                                    <div class="flex-shrink-0 rounded-lg bg-green-500/20 p-2">
                                        <span class="material-symbols-outlined text-2xl text-green-400">eco</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <h5 class="font-semibold text-green-400">
                                                {{ __('collection.show.dashboard.epp_mode_free') }}</h5>
                                            <span
                                                class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400">
                                                {{ __('collection.show.dashboard.active') }}
                                            </span>
                                        </div>
                                        <p class="mb-3 text-sm text-gray-300">
                                            @if ($collection->eppProject)
                                                {{ __('collection.show.dashboard.supporting') }}: <strong
                                                    class="text-green-400">{{ $collection->eppProject->name }}</strong>
                                                <br>
                                                <span class="text-xs text-gray-400">
                                                    {{ __('collection.show.by') }}
                                                    {{ $collection->eppProject->eppUser->organizationData->organization_name ?? $collection->eppProject->eppUser->name }}
                                                </span>
                                            @elseif ($collection->epp)
                                                {{ __('collection.show.dashboard.supporting') }}: <strong
                                                    class="text-green-400">{{ $collection->epp->name }}</strong>
                                            @else
                                                <span
                                                    class="text-yellow-400">{{ __('collection.show.dashboard.no_epp_selected') }}</span>
                                            @endif
                                        </p>
                                        <div class="space-y-1 text-sm text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-green-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.mint_unlimited') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-green-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.cost_zero') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-green-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.epp_share_20') }}
                                            </div>
                                        </div>

                                        @if (!$collection->eppProject && !$collection->epp)
                                            {{-- Bottone per selezionare progetto EPP se non ancora selezionato --}}
                                            <button onclick="openEppProjectSelectionModal()"
                                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 font-medium text-white transition-all hover:bg-green-700">
                                                <span class="material-symbols-outlined text-lg">eco</span>
                                                {{ __('collection.show.dashboard.select_epp_project') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- Subscription Mode --}}
                                @php
                                    $tier = $collection->subscription_tier ?? 'tier_1_19';
                                    $tierLabels = [
                                        'tier_1_19' => [
                                            'name' => __('collection.show.dashboard.tier_starter'),
                                            'price' => '€4.90',
                                            'egis' => '1-19',
                                        ],
                                        'tier_20_49' => [
                                            'name' => __('collection.show.dashboard.tier_basic'),
                                            'price' => '€7.90',
                                            'egis' => '20-49',
                                        ],
                                        'tier_50_99' => [
                                            'name' => __('collection.show.dashboard.tier_professional'),
                                            'price' => '€9.90',
                                            'egis' => '50-99',
                                        ],
                                        'tier_100_plus' => [
                                            'name' => __('collection.show.dashboard.tier_unlimited'),
                                            'price' => '€19.90',
                                            'egis' => '100+',
                                        ],
                                    ];
                                    $tierInfo = $tierLabels[$tier] ?? $tierLabels['tier_1_19'];
                                    $status = $collection->subscription_status ?? 'pending';
                                @endphp
                                <div
                                    class="flex items-start gap-4 rounded-lg border border-indigo-500/20 bg-indigo-500/10 p-3 md:p-4">
                                    <div class="flex-shrink-0 rounded-lg bg-indigo-500/20 p-2">
                                        <span
                                            class="material-symbols-outlined text-2xl text-indigo-400">subscriptions</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <h5 class="font-semibold text-indigo-400">{{ $tierInfo['name'] }}
                                                {{ __('collection.show.dashboard.plan') }}</h5>
                                            @if ($collection->subscription_status === 'active')
                                                <span
                                                    class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400">
                                                    {{ __('collection.show.dashboard.active') }}
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full border border-yellow-500/30 bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400">
                                                    {{ ucfirst($collection->subscription_status) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mb-3 text-lg font-bold text-white">
                                            {{ $tierInfo['price'] }}/{{ __('collection.show.dashboard.month') }}</p>
                                        <div class="space-y-1 text-sm text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.up_to') }} {{ $tierInfo['egis'] }}
                                                {{ __('collection.show.dashboard.egis_per_month') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-400"
                                                    style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.no_epp_required') }}
                                            </div>
                                            @if ($collection->subscription_expires_at)
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-gray-400"
                                                        style="font-size: 16px;">event</span>
                                                    {{ __('collection.show.dashboard.renews') }}:
                                                    {{ \Carbon\Carbon::parse($collection->subscription_expires_at)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Switch Options --}}
                        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-4 md:p-6">
                            <h4 class="mb-4 flex items-center text-lg font-semibold text-white">
                                <span class="material-symbols-outlined mr-2">swap_horiz</span>
                                {{ __('collection.show.dashboard.switch_monetization') }}
                            </h4>

                            @if ($isEpp)
                                {{-- Switch to Subscription --}}
                                <div class="space-y-3">
                                    <p class="mb-4 text-sm text-gray-400">
                                        {{ __('collection.show.dashboard.switch_to_subscription_desc') }}</p>

                                    @foreach (['tier_1_19' => ['name' => __('collection.show.dashboard.tier_starter'), 'price' => '€4.90', 'egis' => '1-19'], 'tier_20_49' => ['name' => __('collection.show.dashboard.tier_basic'), 'price' => '€7.90', 'egis' => '20-49'], 'tier_50_99' => ['name' => __('collection.show.dashboard.tier_professional'), 'price' => '€9.90', 'egis' => '50-99'], 'tier_100_plus' => ['name' => __('collection.show.dashboard.tier_unlimited'), 'price' => '€19.90', 'egis' => '100+']] as $tierCode => $tierData)
                                        <button
                                            class="group w-full rounded-lg border border-gray-600 px-4 py-3 text-left transition-colors hover:border-indigo-500 hover:bg-indigo-500/10"
                                            onclick="selectSubscriptionTier('{{ $tierCode }}')">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-semibold text-white group-hover:text-indigo-400">
                                                        {{ $tierData['name'] }}</div>
                                                    <div class="text-xs text-gray-400">{{ $tierData['egis'] }}
                                                        {{ __('collection.show.dashboard.egis_per_month') }}</div>
                                                </div>
                                                <div class="text-lg font-bold text-indigo-400">
                                                    {{ $tierData['price'] }}
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                {{-- Switch to EPP --}}
                                <div class="rounded-lg border border-green-500/20 bg-green-500/10 p-4">
                                    <h5 class="mb-3 font-semibold text-green-400">
                                        {{ __('collection.show.dashboard.switch_to_epp_free') }}</h5>
                                    <p class="mb-4 text-sm text-gray-300">
                                        {{ __('collection.show.dashboard.switch_to_epp_desc') }}</p>

                                    @if ($collection->subscription_expires_at)
                                        @php
                                            $daysRemaining = \Carbon\Carbon::now()->diffInDays(
                                                \Carbon\Carbon::parse($collection->subscription_expires_at),
                                                false,
                                            );
                                            $tierPrices = [
                                                'tier_1_19' => 4.9,
                                                'tier_20_49' => 7.9,
                                                'tier_50_99' => 9.9,
                                                'tier_100_plus' => 19.9,
                                            ];
                                            $currentTier = $collection->subscription_tier ?? 'tier_1_19';
                                            $monthlyPrice = $tierPrices[$currentTier] ?? 4.9;
                                            $refund = $daysRemaining > 0 ? ($monthlyPrice / 30) * $daysRemaining : 0;
                                            $egiliCredit = round($refund * 1.4, 2);
                                        @endphp
                                        <div class="mb-4 rounded border border-gray-700 bg-gray-800/50 p-3 text-sm">
                                            <div class="mb-1 flex justify-between">
                                                <span
                                                    class="text-gray-400">{{ __('collection.show.dashboard.days_remaining') }}:</span>
                                                <span
                                                    class="font-semibold text-white">{{ max(0, $daysRemaining) }}</span>
                                            </div>
                                            <div class="mb-1 flex justify-between">
                                                <span
                                                    class="text-gray-400">{{ __('collection.show.dashboard.refund') }}
                                                    (€):</span>
                                                <span
                                                    class="font-semibold text-white">€{{ number_format($refund, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-gray-400">{{ __('collection.show.dashboard.egili_credit') }}:</span>
                                                <span
                                                    class="font-semibold text-green-400">{{ number_format($egiliCredit, 2) }}
                                                    Egili</span>
                                            </div>
                                        </div>
                                    @endif

                                    <button onclick="openEppProjectSelectionModal()"
                                        class="w-full rounded-lg bg-green-600 px-4 py-3 font-semibold text-white transition-colors hover:bg-green-700">
                                        {{ __('collection.show.dashboard.select_epp_project') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tab: Statistics --}}
                <div id="tab-statistics" class="dashboard-tab-content hidden">
                    @php
                        $totalEgis = $collection->egis()->count();
                        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                        $mintedThisMonth = $collection
                            ->egis()
                            ->whereHas('blockchain', function ($q) use ($startOfMonth) {
                                $q->where('mint_status', 'minted')->where('created_at', '>=', $startOfMonth);
                            })
                            ->count();
                        $monthlyLimit = '∞';
                        if ($collection->monetization_type === 'subscription' && $collection->subscription_tier) {
                            $tierLimits = [
                                'tier_1_19' => 19,
                                'tier_20_49' => 49,
                                'tier_50_99' => 99,
                                'tier_100_plus' => '∞',
                            ];
                            $monthlyLimit = $tierLimits[$collection->subscription_tier] ?? 19;
                        }
                        $canMint = false;
                        if ($collection->monetization_type === 'epp') {
                            $canMint = $collection->epp_project_id !== null || $collection->epp_id !== null;
                        } else {
                            $canMint = $collection->subscription_status === 'active';
                            if ($canMint && $monthlyLimit !== '∞' && $mintedThisMonth >= $monthlyLimit) {
                                $canMint = false;
                            }
                        }
                        $publishedEgis = $collection->egis()->where('is_published', true)->count();
                        $draftEgis = $totalEgis - $publishedEgis;
                        $totalMinted = $collection
                            ->egis()
                            ->whereHas('blockchain', function ($q) {
                                $q->where('mint_status', 'minted');
                            })
                            ->count();
                    @endphp

                    <div class="grid gap-6 md:grid-cols-3">
                        {{-- Total EGIs --}}
                        <div
                            class="rounded-xl border border-gray-700 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 p-6">
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-400">{{ __('collection.show.dashboard.total_egis') }}</span>
                                <span class="material-symbols-outlined text-2xl text-indigo-400">inventory_2</span>
                            </div>
                            <div class="text-3xl font-bold text-white">{{ $totalEgis }}</div>
                            <div class="mt-2 flex items-center gap-3 text-xs">
                                <span class="text-green-400">{{ $publishedEgis }}
                                    {{ __('collection.show.dashboard.published') }}</span>
                                @if ($draftEgis > 0)
                                    <span class="text-gray-400">{{ $draftEgis }}
                                        {{ __('collection.show.dashboard.draft') }}</span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-blue-400">{{ $totalMinted }}
                                {{ __('collection.show.dashboard.total_minted') }}</div>
                        </div>

                        {{-- Minted This Month --}}
                        <div
                            class="rounded-xl border border-gray-700 bg-gradient-to-br from-green-500/10 to-emerald-500/10 p-6">
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-400">{{ __('collection.show.dashboard.minted_this_month') }}</span>
                                <span class="material-symbols-outlined text-2xl text-green-400">trending_up</span>
                            </div>
                            <div class="text-3xl font-bold text-white">{{ $mintedThisMonth }}</div>
                            <p class="mt-2 text-xs text-gray-400">
                                {{ __('collection.show.dashboard.limit') }}:
                                {{ $monthlyLimit }}/{{ __('collection.show.dashboard.month') }}
                                @if ($monthlyLimit !== '∞')
                                    <span
                                        class="{{ $mintedThisMonth >= $monthlyLimit ? 'text-red-400' : 'text-green-400' }} ml-1">
                                        ({{ max(0, $monthlyLimit - $mintedThisMonth) }}
                                        {{ __('collection.show.dashboard.left') }})
                                    </span>
                                @endif
                            </p>
                        </div>

                        {{-- Can Mint Status --}}
                        <div
                            class="rounded-xl border border-gray-700 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 p-6">
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-400">{{ __('collection.show.dashboard.can_mint') }}</span>
                                <span class="material-symbols-outlined text-2xl text-blue-400">check_circle</span>
                            </div>
                            <div class="text-2xl font-bold text-white">
                                @if ($canMint)
                                    <span class="text-green-400">{{ __('collection.show.dashboard.yes') }}</span>
                                @else
                                    <span class="text-red-400">{{ __('collection.show.dashboard.no') }}</span>
                                @endif
                            </div>
                            <p class="mt-2 text-xs text-gray-400">
                                @if (!$canMint)
                                    @if ($collection->monetization_type === 'epp')
                                        {{ __('collection.show.dashboard.select_epp_to_enable') }}
                                    @elseif($monthlyLimit !== '∞' && $mintedThisMonth >= $monthlyLimit)
                                        {{ __('collection.show.dashboard.monthly_limit_reached') }}
                                    @else
                                        {{ __('collection.show.dashboard.activate_subscription') }}
                                    @endif
                                @else
                                    {{ __('collection.show.dashboard.ready_to_mint') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- More stats placeholder --}}
                    <div class="mt-6 rounded-xl border border-gray-700 bg-gray-800/30 p-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-600">analytics</span>
                        <p class="mt-2 text-gray-400">{{ __('collection.show.dashboard.advanced_analytics_soon') }}
                        </p>
                    </div>
                </div>

                {{-- Tab: Subscription --}}
                <div id="tab-subscription" class="dashboard-tab-content hidden">
                    @if ($collection->monetization_type === 'subscription')
                        {{-- Logic: If Active -> Show Details. If Not Active -> Show Selection --}}
                        @if ($collection->subscription_status === 'active')
                            <div class="space-y-6">
                                {{-- Subscription Details --}}
                                <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-4 md:p-6">
                                    <h4 class="mb-6 flex items-center gap-2 text-lg font-semibold text-white">
                                        <span class="material-symbols-outlined text-indigo-400">badge</span>
                                        {{ __('collection.show.dashboard.subscription_details') }}
                                    </h4>

                                    <div class="grid gap-y-4 md:grid-cols-2 lg:grid-cols-2">
                                        {{-- Plan Name --}}
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.plan') }}</span>
                                            <span class="text-lg font-bold text-white">
                                                @php
                                                    $tierLabels = [
                                                        'tier_1_19' => [
                                                            'name' => 'Starter (1-19 EGIs)',
                                                            'price' => '€4.90',
                                                        ],
                                                        'tier_20_49' => [
                                                            'name' => 'Growth (20-49 EGIs)',
                                                            'price' => '€7.90',
                                                        ],
                                                        'tier_50_99' => [
                                                            'name' => 'Pro (50-99 EGIs)',
                                                            'price' => '€9.90',
                                                        ],
                                                        'tier_100_plus' => [
                                                            'name' => 'Unlimited (100+ EGIs)',
                                                            'price' => '€19.90',
                                                        ],
                                                    ];
                                                    // Handle legacy/service default 'collection_basic' by mapping to Unlimited
                                                    $displayTier =
                                                        $collection->subscription_tier === 'collection_basic'
                                                            ? 'tier_100_plus'
                                                            : $collection->subscription_tier ?? 'tier_1_19';
                                                @endphp
                                                {{ $tierLabels[$displayTier]['name'] ?? $tierLabels['tier_100_plus']['name'] }}
                                            </span>
                                        </div>

                                        {{-- Status --}}
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.status') }}</span>
                                            <div>
                                                <span
                                                    class="inline-flex items-center rounded-md bg-green-500/10 px-2 py-1 text-sm font-medium text-green-400 ring-1 ring-inset ring-green-500/20">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-400"></span>
                                                    {{ ucfirst($collection->subscription_status) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Price --}}
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.price') }}</span>
                                            <span class="font-medium text-white">
                                                {{ $tierLabels[$displayTier]['price'] ?? '€19.90' }}/{{ __('collection.show.dashboard.month') }}
                                            </span>
                                        </div>

                                        {{-- Started At --}}
                                        @if ($collection->subscription_started_at)
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.started') }}</span>
                                                <span class="font-medium text-white">
                                                    {{ \Carbon\Carbon::parse($collection->subscription_started_at)->format('d M Y, H:i') }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Expires At --}}
                                        @if ($collection->subscription_expires_at)
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.expires') }}</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-white">
                                                        {{ \Carbon\Carbon::parse($collection->subscription_expires_at)->format('d M Y') }}
                                                    </span>
                                                    @php
                                                        $daysLeft = \Carbon\Carbon::now()->diffInDays(
                                                            \Carbon\Carbon::parse($collection->subscription_expires_at),
                                                            false,
                                                        );
                                                    @endphp
                                                    @if ($daysLeft > 0 && $daysLeft < 7)
                                                        <span
                                                            class="text-xs font-medium text-yellow-500">({{ ceil($daysLeft) }}
                                                            days left)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Auto-Renewal Toggle --}}
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('collection.show.dashboard.auto_renew') }}</span>
                                            <div class="flex items-center gap-2">
                                                <label class="relative inline-flex cursor-pointer items-center">
                                                    <input type="checkbox" value="" class="peer sr-only"
                                                        {{ $collection->is_auto_renew_active ? 'checked' : '' }}
                                                        onchange="toggleAutoRenew(this.checked)">
                                                    <div
                                                        class="peer h-6 w-11 rounded-full bg-gray-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300/30 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-indigo-800">
                                                    </div>
                                                </label>
                                                <span class="text-sm font-medium text-white" id="auto-renew-label">
                                                    {{ $collection->is_auto_renew_active ? __('collection.show.dashboard.enabled') : __('collection.show.dashboard.disabled') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
                                    <h4 class="mb-4 text-lg font-semibold text-white">
                                        {{ __('collection.show.dashboard.manage_subscription') }}
                                    </h4>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        @if ($collection->subscription_stripe_id)
                                            <a href="#"
                                                class="flex items-center justify-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-4 py-3 text-white transition-all hover:bg-indigo-500/20">
                                                <span class="material-symbols-outlined">credit_card</span>
                                                {{ __('collection.show.dashboard.manage_payment_methods') }}
                                            </a>
                                        @endif
                                        <button onclick="cancelSubscription()"
                                            class="flex items-center justify-center gap-2 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-400 transition-all hover:bg-red-500/20 hover:text-red-300">
                                            <span class="material-symbols-outlined">cancel</span>
                                            {{ __('collection.show.dashboard.cancel_subscription') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Not Active: Show PLAN SELECTION --}}
                            <div class="space-y-6">
                                <div class="rounded-xl border border-indigo-500/20 bg-indigo-500/10 p-6 text-center">
                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600/20">
                                        <span
                                            class="material-symbols-outlined text-3xl text-indigo-400">subscriptions</span>
                                    </div>
                                    <h2 class="mb-2 text-2xl font-bold text-white">
                                        {{ __('collection.show.dashboard.choose_your_plan') }}</h2>
                                    <p class="mx-auto max-w-lg text-gray-300">
                                        {{ __('collection.show.dashboard.choose_plan_desc') }}</p>
                                </div>

                                {{-- [FIAT] Piano caricato dinamicamente da /api/collection-subscription-plans --}}
                                <div id="subscription-plans-grid-{{ $collection->id }}"
                                    data-collection-id="{{ $collection->id }}"
                                    class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                                    {{-- Skeleton loader — sostituito dal JS dopo la fetch --}}
                                    @foreach (range(1, 4) as $i)
                                        <div class="relative flex flex-col rounded-xl border border-gray-700 bg-gray-800 p-6 animate-pulse">
                                            <div class="mb-4">
                                                <div class="h-5 w-24 rounded bg-gray-700"></div>
                                                <div class="mt-2 h-9 w-20 rounded bg-gray-700"></div>
                                            </div>
                                            <div class="mb-6 space-y-3">
                                                <div class="h-4 w-full rounded bg-gray-700"></div>
                                                <div class="h-4 w-3/4 rounded bg-gray-700"></div>
                                                <div class="h-4 w-2/3 rounded bg-gray-700"></div>
                                            </div>
                                            <div class="mt-auto h-10 w-full rounded-lg bg-gray-700"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="rounded-xl border border-gray-700 bg-gray-800/30 p-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-600">subscriptions</span>
                            <p class="mt-2 text-gray-400">
                                {{ __('collection.show.dashboard.no_active_subscription') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ __('collection.show.dashboard.using_epp_mode') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Tab: Payments (Rebuilt) - NOW INSIDE SCROLLABLE AREA --}}
                <div id="tab-payments-content" class="dashboard-tab-content hidden w-full">
                    {{-- Wrapper Card with Border --}}
                    <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-4 md:p-6">
                        {{-- Title Section --}}
                        <div class="mb-6 px-1">
                            <h4 class="mb-2 flex items-center gap-2 text-xl font-bold text-white">
                                <span class="material-symbols-outlined text-indigo-400">credit_card</span>
                                {{ __('collection.show.dashboard.payment_methods_title') }}
                            </h4>
                            <p class="text-sm text-gray-400">
                                {{ __('collection.show.dashboard.payment_methods_desc') }}
                            </p>
                        </div>

                        {{-- Methods List --}}
                        <div class="space-y-4">
                            @php
                                $user = auth()->user();
                                $userMethods = $user?->paymentMethods?->keyBy('method') ?? collect();
                                $collectionMethods = $collection->paymentMethods->keyBy('method');
                                $availableMethods = \App\Http\Controllers\PaymentSettingsController::AVAILABLE_METHODS;
                            @endphp

                            @foreach ($availableMethods as $methodKey => $methodInfo)
                                @php
                                    $userMethod = $userMethods[$methodKey] ?? null;
                                    $isUserEnabled = $userMethod?->is_enabled ?? false;
                                @endphp

                                @if ($isUserEnabled)
                                    @php
                                        $collectionMethod = $collectionMethods[$methodKey] ?? null;
                                        $isCollectionEnabled = $collectionMethod?->is_enabled ?? false;
                                    @endphp

                                    {{-- Solid Card with Solid Border --}}
                                    <div
                                        class="{{ $isCollectionEnabled ? 'ring-2 ring-indigo-500 border-transparent' : '' }} group relative flex items-center justify-between rounded-xl border border-gray-600 bg-gray-800 p-5 transition-all hover:border-gray-500">
                                        <div class="flex items-center gap-4">
                                            {{-- Icon Box --}}
                                            <div
                                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gray-700 text-gray-300">
                                                @if ($methodInfo['icon'] === 'credit-card')
                                                    <span class="material-symbols-outlined text-2xl">credit_card</span>
                                                @elseif($methodInfo['icon'] === 'coins')
                                                    <span
                                                        class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                                                @else
                                                    <span
                                                        class="material-symbols-outlined text-2xl">account_balance</span>
                                                @endif
                                            </div>

                                            <div>
                                                <h5 class="text-base font-bold text-white">{{ $methodInfo['name'] }}
                                                </h5>
                                                <p class="text-xs text-gray-400">{{ $methodInfo['description'] }}</p>
                                            </div>
                                        </div>

                                        <label class="relative z-10 inline-flex cursor-pointer items-center">
                                            <input type="checkbox" class="peer sr-only"
                                                onchange="toggleCollectionPaymentMethod('{{ $methodKey }}', this, {{ $collection->id }}); const card = this.closest('.group'); if(this.checked) { card.classList.add('ring-2', 'ring-indigo-500', 'border-transparent'); card.classList.remove('border-gray-600'); } else { card.classList.remove('ring-2', 'ring-indigo-500', 'border-transparent'); card.classList.add('border-gray-600'); }"
                                                {{ $isCollectionEnabled ? 'checked' : '' }}>
                                            <div
                                                class="peer h-6 w-11 rounded-full bg-gray-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none dark:border-gray-600 dark:bg-gray-900">
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            @endforeach

                            @if ($userMethods->where('is_enabled', true)->isEmpty())
                                <div
                                    class="rounded-xl border border-dashed border-gray-600 bg-gray-800/50 p-8 text-center text-gray-400">
                                    <span class="material-symbols-outlined mb-2 text-3xl">warning</span>
                                    <p class="mb-2">{{ __('collection.show.dashboard.no_global_methods') }}</p>
                                    <a href="#" onclick="window.paymentModal.open(); return false;"
                                        class="font-bold text-indigo-400 hover:text-indigo-300 hover:underline">
                                        {{ __('collection.show.dashboard.configure_global_methods') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer (Desktop Only) --}}
            <div class="hidden justify-end gap-3 border-t border-gray-700 bg-gray-800/30 px-6 py-4 md:flex">
                <button id="closeDashboardBtn"
                    class="rounded-lg border border-gray-600 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-700">
                    {{ __('collection.show.dashboard.close') }}
                </button>
            </div>

            {{-- Mobile Bottom Navigation Bar --}}
            <div
                class="fixed bottom-0 left-0 z-[60] grid w-full grid-cols-4 gap-1 border-t border-gray-700 bg-gray-900 px-2 py-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.3)] md:hidden">
                <button
                    class="dashboard-tab-mobile group active flex flex-col items-center justify-center rounded-xl p-2 transition-all hover:bg-gray-800"
                    data-tab="monetization">
                    <span
                        class="material-symbols-outlined mb-1 text-2xl text-gray-400 group-[.active]:text-indigo-400">payments</span>
                    <span
                        class="text-[10px] font-medium text-gray-500 group-[.active]:text-indigo-400">{{ __('collection.show.dashboard.tab_monetization') }}</span>
                </button>
                <button
                    class="dashboard-tab-mobile group flex flex-col items-center justify-center rounded-xl p-2 transition-all hover:bg-gray-800"
                    data-tab="statistics">
                    <span
                        class="material-symbols-outlined mb-1 text-2xl text-gray-400 group-[.active]:text-indigo-400">analytics</span>
                    <span
                        class="text-[10px] font-medium text-gray-500 group-[.active]:text-indigo-400">{{ __('collection.show.dashboard.tab_statistics') }}</span>
                </button>
                <button
                    class="dashboard-tab-mobile group flex flex-col items-center justify-center rounded-xl p-2 transition-all hover:bg-gray-800"
                    data-tab="subscription">
                    <span
                        class="material-symbols-outlined mb-1 text-2xl text-gray-400 group-[.active]:text-indigo-400">subscriptions</span>
                    <span
                        class="text-[10px] font-medium text-gray-500 group-[.active]:text-indigo-400">{{ __('collection.show.dashboard.tab_subscription') }}</span>
                </button>
                <button
                    class="dashboard-tab-mobile group flex flex-col items-center justify-center rounded-xl p-2 transition-all hover:bg-gray-800"
                    data-tab="payments">
                    <span
                        class="material-symbols-outlined mb-1 text-2xl text-gray-400 group-[.active]:text-indigo-400">credit_card</span>
                    <span
                        class="text-[10px] font-medium text-gray-500 group-[.active]:text-indigo-400">{{ __('collection.show.dashboard.tab_payments') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * [FIAT] Carica i piani abbonamento da /api/collection-subscription-plans
     * e sostituisce lo skeleton loader con le card reali.
     * Usa feature_code direttamente → compatibile con selectSubscriptionTier().
     */
    function loadSubscriptionPlans(collectionId) {
        const container = document.getElementById('subscription-plans-grid-' + collectionId);
        if (!container) return;

        fetch('/api/collection-subscription-plans', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.plans.length) {
                container.innerHTML = '<div class="col-span-4 text-center text-gray-400 py-8">Nessun piano disponibile al momento.</div>';
                return;
            }
            container.innerHTML = data.plans.map(plan => {
                const price  = '€' + parseFloat(plan.cost_fiat_eur).toFixed(2).replace('.', ',');
                const maxEgi = plan.max_egis ? (plan.max_egis < 9999 ? '1–' + plan.max_egis : '100+') : '∞';
                const benefitsList = (plan.benefits || ['Full Analytics', 'Priority Support'])
                    .map(b => `<li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-green-400" style="font-size:18px">check</span>
                                    ${b}
                               </li>`).join('');
                return `<div class="relative flex flex-col rounded-xl border border-gray-700 bg-gray-800 p-6 transition-all hover:-translate-y-1 hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-500/10">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-white">${plan.name}</h3>
                                <div class="mt-2 text-3xl font-bold text-white">
                                    ${price}<span class="text-sm font-normal text-gray-400">/mo</span></div>
                            </div>
                            <ul class="mb-6 flex-1 space-y-3 text-sm text-gray-300">
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-green-400" style="font-size:18px">check</span>
                                    ${maxEgi} EGIs
                                </li>
                                ${benefitsList}
                            </ul>
                            <button onclick="selectSubscriptionTier('${plan.feature_code}')"
                                class="mt-auto w-full rounded-lg bg-indigo-600 py-2.5 font-semibold text-white transition-colors hover:bg-indigo-700">
                                {{ __('subscription.select_plan') }}
                            </button>
                        </div>`;
            }).join('');
        })
        .catch(() => {
            container.innerHTML = '<div class="col-span-4 text-center text-red-400 py-8">Errore nel caricamento dei piani.</div>';
        });
    }

    // Auto-init: se questo modal contiene il piano-grid loader (subscription non attiva), carica i piani
    document.addEventListener('DOMContentLoaded', function () {
        const grid = document.querySelector('[id^="subscription-plans-grid-"]');
        if (grid) {
            loadSubscriptionPlans(grid.dataset.collectionId);
        }
    });

    function toggleAutoRenew(isActive) {
        const label = document.getElementById('auto-renew-label');
        const url = "{{ route('home.collections.subscription.toggle-auto-renew', $collection->id) }}";

        // Visual feedback
        label.style.opacity = '0.5';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    active: isActive
                })
            })
            .then(response => response.json())
            .then(data => {
                label.style.opacity = '1';
                if (data.success) {
                    label.textContent = data.message;
                } else {
                    console.error(data.message);
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                label.style.opacity = '1';
                console.error('Error:', error);
                alert('Si è verificato un errore di rete.');
            });
    }

    function cancelSubscription() {
        Swal.fire({
            title: '{{ __('collection.show.dashboard.cancel_confirm_title') }}',
            text: '{{ __('collection.show.dashboard.cancel_confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __('collection.show.dashboard.cancel_confirm_btn') }}',
            cancelButtonText: '{{ __('collection.show.dashboard.cancel_cancel_btn') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{{ route('home.collections.subscription.cancel', $collection->id) }}";

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                '{{ __('collection.show.dashboard.cancelled') }}',
                                data.message + (data.refund_amount > 0 ? '\nRefund: ' + data
                                    .refund_amount + ' Egili' : ''),
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                data.message,
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error',
                            'Network error occurred.',
                            'error'
                        );
                    });
            }
        })
    }

    function toggleCollectionPaymentMethod(method, checkbox, collectionId) {
        const url = '/collections/' + collectionId + '/settings/payments/' + method + '/toggle';
        const originalState = !checkbox.checked; // State before click

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Server Error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Optional: Show toast
                    const toast = window.Swal || window.alert;
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                } else {
                    checkbox.checked = originalState; // Revert
                    alert(data.message || 'Error toggling payment method');
                }
            })
            .catch(error => {
                checkbox.checked = originalState; // Revert
                console.error('Payment Error:', error);
                const msg = error.message || 'Generic network error';

                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ops...',
                        text: msg,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                } else {
                    alert(msg);
                }
            });
    }
</script>
