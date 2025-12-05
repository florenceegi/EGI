{{-- 💼 DASHBOARD MONETIZATION MODAL --}}
<div id="dashboardModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 backdrop-blur-sm"
    aria-labelledby="dashboardModalLabel" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="relative w-full max-w-6xl rounded-2xl border border-gray-700 bg-gray-900 shadow-2xl">
            {{-- Header --}}
            <div
                class="flex items-center justify-between border-b border-gray-700 bg-gradient-to-r from-indigo-900/30 to-purple-900/30 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600/20">
                        <span class="material-symbols-outlined text-xl text-indigo-400">dashboard</span>
                    </div>
                    <div>
                        <h3 id="dashboardModalLabel" class="text-xl font-bold text-white">
                            {{ __('collection.show.dashboard.title') }}
                        </h3>
                        <p class="text-sm text-gray-400">{{ $collection->collection_name }}</p>
                    </div>
                </div>
                <button id="closeDashboardX" class="text-gray-400 transition-colors hover:text-white">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            {{-- Content --}}
            <div class="p-6">
                {{-- Tabs Navigation --}}
                <div class="mb-6 flex gap-2 overflow-x-auto border-b border-gray-700">
                    <button class="dashboard-tab active" data-tab="monetization">
                        <span class="material-symbols-outlined mr-2">payments</span>
                        {{ __('collection.show.dashboard.tab_monetization') }}
                    </button>
                    <button class="dashboard-tab" data-tab="statistics">
                        <span class="material-symbols-outlined mr-2">analytics</span>
                        {{ __('collection.show.dashboard.tab_statistics') }}
                    </button>
                    <button class="dashboard-tab" data-tab="subscription">
                        <span class="material-symbols-outlined mr-2">subscriptions</span>
                        {{ __('collection.show.dashboard.tab_subscription') }}
                    </button>
                </div>

                {{-- Tab: Monetization --}}
                <div id="tab-monetization" class="dashboard-tab-content">
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Current Status --}}
                        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
                            <h4 class="mb-4 flex items-center text-lg font-semibold text-white">
                                <span class="material-symbols-outlined mr-2">info</span>
                                {{ __('collection.show.dashboard.current_monetization') }}
                            </h4>

                            @php
                                $monetizationType = $collection->monetization_type ?? 'epp';
                                $isEpp = $monetizationType === 'epp';
                                $isCompanyCollection = $collection->is_epp_voluntary || ($collection->creator && $collection->creator->usertype === 'company');
                            @endphp

                            @if ($isCompanyCollection)
                                {{-- COMPANY MODE: Subscription Required + Voluntary EPP Donation --}}
                                <div class="flex items-start gap-4 rounded-lg border border-indigo-500/20 bg-indigo-500/10 p-4">
                                    <div class="flex-shrink-0 rounded-lg bg-indigo-500/20 p-2">
                                        <span class="material-symbols-outlined text-2xl text-indigo-400">business</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <h5 class="font-semibold text-indigo-400">
                                                {{ __('collection.show.dashboard.company_mode') }}</h5>
                                            @if ($collection->subscription_status === 'active')
                                                <span class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400">
                                                    {{ __('collection.show.dashboard.active') }}
                                                </span>
                                            @else
                                                <span class="rounded-full border border-yellow-500/30 bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400">
                                                    {{ __('collection.show.dashboard.subscription_required') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mb-3 text-sm text-gray-300">
                                            {{ __('collection.company_requires_subscription') }}
                                        </p>
                                        <div class="space-y-1 text-sm text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-400" style="font-size: 16px;">check_circle</span>
                                                {{ __('collection.show.dashboard.subscription_access') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-green-400" style="font-size: 16px;">volunteer_activism</span>
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
                                                <strong class="text-green-400">{{ $collection->eppProject->name }}</strong>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ __('collection.donation_percentage_label') }}: 
                                                <strong class="text-green-400">{{ $collection->epp_donation_percentage ?? 0 }}%</strong>
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Donation Percentage Slider --}}
                                    <div class="mb-4">
                                        <label for="companyDonationPercentage" class="mb-2 block text-sm font-medium text-gray-300">
                                            {{ __('collection.donation_percentage_label') }}
                                        </label>
                                        <div class="flex items-center gap-4">
                                            <input type="range" id="companyDonationPercentage" 
                                                class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700 accent-green-500"
                                                min="0" max="100" step="1" 
                                                value="{{ $collection->epp_donation_percentage ?? 0 }}">
                                            <span id="donationPercentageValue" class="min-w-[3rem] text-center text-lg font-bold text-green-400">
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
                                    class="flex items-start gap-4 rounded-lg border border-indigo-500/20 bg-indigo-500/10 p-4">
                                    <div class="flex-shrink-0 rounded-lg bg-indigo-500/20 p-2">
                                        <span
                                            class="material-symbols-outlined text-2xl text-indigo-400">subscriptions</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <h5 class="font-semibold text-indigo-400">{{ $tierInfo['name'] }}
                                                {{ __('collection.show.dashboard.plan') }}</h5>
                                            @if ($status === 'active')
                                                <span
                                                    class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400">
                                                    {{ __('collection.show.dashboard.active') }}
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full border border-yellow-500/30 bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400">
                                                    {{ ucfirst($status) }}
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
                        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
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
                                                <div class="text-lg font-bold text-indigo-400">{{ $tierData['price'] }}
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
                                            $monthlyPrice = $tierPrices[$tier] ?? 4.9;
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
                            $plan = \App\Models\FeaturePricing::where(
                                'tier_code',
                                $collection->subscription_tier,
                            )->first();
                            if ($plan && $plan->egi_max !== null) {
                                $monthlyLimit = $plan->egi_max;
                            }
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
                        <div class="space-y-6">
                            {{-- Subscription Details --}}
                            <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
                                <h4 class="mb-4 text-lg font-semibold text-white">
                                    {{ __('collection.show.dashboard.subscription_details') }}</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">{{ __('collection.show.dashboard.plan') }}:</span>
                                        <span
                                            class="font-semibold text-white">{{ $tierInfo['name'] ?? __('collection.show.dashboard.unknown') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span
                                            class="text-gray-400">{{ __('collection.show.dashboard.status') }}:</span>
                                        <span
                                            class="{{ $status === 'active' ? 'text-green-400' : 'text-yellow-400' }} font-semibold">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span
                                            class="text-gray-400">{{ __('collection.show.dashboard.price') }}:</span>
                                        <span
                                            class="font-semibold text-white">{{ $tierInfo['price'] ?? 'N/A' }}/{{ __('collection.show.dashboard.month') }}</span>
                                    </div>
                                    @if ($collection->subscription_started_at)
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-400">{{ __('collection.show.dashboard.started') }}:</span>
                                            <span
                                                class="text-white">{{ \Carbon\Carbon::parse($collection->subscription_started_at)->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($collection->subscription_expires_at)
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-400">{{ __('collection.show.dashboard.expires') }}:</span>
                                            <span
                                                class="text-white">{{ \Carbon\Carbon::parse($collection->subscription_expires_at)->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
                                <h4 class="mb-4 text-lg font-semibold text-white">
                                    {{ __('collection.show.dashboard.manage_subscription') }}</h4>
                                <div class="space-y-3">
                                    @if ($collection->subscription_stripe_id)
                                        <a href="#"
                                            class="block w-full rounded-lg border border-indigo-500/30 px-4 py-3 text-center text-white transition-colors hover:bg-indigo-500/10">
                                            {{ __('collection.show.dashboard.manage_payment_methods') }}
                                        </a>
                                    @endif
                                    <button onclick="cancelSubscription()"
                                        class="w-full rounded-lg border border-red-500/30 px-4 py-3 text-red-400 transition-colors hover:bg-red-500/10">
                                        {{ __('collection.show.dashboard.cancel_subscription') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-gray-700 bg-gray-800/30 p-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-600">subscriptions</span>
                            <p class="mt-2 text-gray-400">{{ __('collection.show.dashboard.no_active_subscription') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ __('collection.show.dashboard.using_epp_mode') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-700 bg-gray-800/30 px-6 py-4">
                <button id="closeDashboardBtn"
                    class="rounded-lg border border-gray-600 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-700">
                    {{ __('collection.show.dashboard.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
