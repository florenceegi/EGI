@php
    $pageTitle = __('creator_onboarding.page.title');
    $pageDescription = __('creator_onboarding.page.description');
    $stripeData = $stripeAccount ?? [];
    $chargesEnabled = data_get($stripeData, 'charges_enabled', false);
    $payoutsEnabled = data_get($stripeData, 'payouts_enabled', false);
    $detailsSubmitted = data_get($stripeData, 'details_submitted', false);
    $stripeAccountId = data_get($stripeData, 'id');
@endphp

<x-platform-layout :title="$pageTitle" :metaDescription="$pageDescription">
    <x-slot name="noHero">true</x-slot>

    <div class="max-w-5xl mx-auto px-6 py-10 space-y-8">
        <header class="space-y-4">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ __('creator_onboarding.page.heading') }}
            </h1>
            <p class="text-base text-slate-600 leading-6">
                {{ __('creator_onboarding.page.intro') }}
            </p>
        </header>

        <section class="grid gap-6 md:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">
                    {{ __('creator_onboarding.profile.title') }}
                </h2>
                <dl class="space-y-3 text-sm text-slate-700">
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.profile.user_name') }}
                        </dt>
                        <dd class="mt-1">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.profile.user_email') }}
                        </dt>
                        <dd class="mt-1">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.profile.user_type') }}
                        </dt>
                        <dd class="mt-1">{{ ucfirst($user->usertype ?? 'creator') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.profile.wallet_address') }}
                        </dt>
                        <dd class="mt-1 font-mono text-xs break-all text-slate-800">{{ $wallet->wallet }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.profile.iban_masked') }}
                        </dt>
                        <dd class="mt-1">{{ $wallet->hasIban() ? $wallet->getMaskedIbanAttribute() : __('creator_onboarding.profile.iban_missing') }}</dd>
                    </div>
                </dl>
            </article>

            <article class="rounded-2xl border border-blue-200 bg-white shadow-sm p-6 space-y-4">
                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2">
                    {{ __('creator_onboarding.stripe.title') }}
                    @if ($stripeAccountId && $chargesEnabled && $payoutsEnabled)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ __('creator_onboarding.badges.ready') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            {{ __('creator_onboarding.badges.pending') }}
                        </span>
                    @endif
                </h2>

                <dl class="space-y-3 text-sm text-slate-700">
                    <div>
                        <dt class="font-medium text-slate-500 uppercase tracking-wide">
                            {{ __('creator_onboarding.stripe.account_id') }}
                        </dt>
                        <dd class="mt-1 font-mono text-xs text-slate-800">
                            {{ $stripeAccountId ?? '—' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 p-3">
                            <p class="text-xs text-slate-500 uppercase tracking-wide">
                                {{ __('creator_onboarding.stripe.charges_enabled') }}
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ $chargesEnabled ? __('creator_onboarding.badges.ready') : __('creator_onboarding.badges.pending') }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-3">
                            <p class="text-xs text-slate-500 uppercase tracking-wide">
                                {{ __('creator_onboarding.stripe.payouts_enabled') }}
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ $payoutsEnabled ? __('creator_onboarding.badges.ready') : __('creator_onboarding.badges.pending') }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-3 sm:col-span-2">
                            <p class="text-xs text-slate-500 uppercase tracking-wide">
                                {{ __('creator_onboarding.stripe.details_submitted') }}
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ $detailsSubmitted ? __('creator_onboarding.badges.ready') : __('creator_onboarding.badges.pending') }}
                            </p>
                        </div>
                    </div>
                </dl>

                <p class="text-xs text-slate-500">
                    {{ __('creator_onboarding.stripe.status') }}:
                    @if ($stripeAccountId && $chargesEnabled && $payoutsEnabled)
                        {{ __('creator_onboarding.stripe.status_ready') }}
                    @else
                        {{ __('creator_onboarding.stripe.status_pending') }}
                    @endif
                </p>

                <div class="flex flex-wrap gap-3">
                    @if ($onboardingUrl)
                        <a href="{{ $onboardingUrl }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            {{ __('creator_onboarding.stripe.cta_onboarding') }}
                        </a>
                    @endif
                    @if ($dashboardUrl)
                        <a href="{{ $dashboardUrl }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            {{ __('creator_onboarding.stripe.cta_dashboard') }}
                        </a>
                    @endif
                </div>

                @if ($onboardingUrl)
                    <p class="text-xs text-slate-500">
                        {{ __('creator_onboarding.stripe.onboarding_hint') }}
                    </p>
                @endif
            </article>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">
                {{ __('creator_onboarding.actions.title') }}
            </h2>
            <ul class="space-y-2 text-sm text-slate-700">
                @foreach (trans('creator_onboarding.actions.checklist') as $item)
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-gradient-to-br from-blue-50 to-slate-100 p-6 space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">
                {{ __('creator_onboarding.pera.title') }}
            </h2>
            <p class="text-sm text-slate-700 leading-6">
                {{ __('creator_onboarding.pera.intro') }}
            </p>
            <p class="text-sm text-slate-700 leading-6">
                {{ __('creator_onboarding.pera.request') }}
            </p>
            <p class="text-xs text-slate-500 leading-5">
                {{ __('creator_onboarding.pera.note') }}
            </p>
            <div class="flex flex-wrap gap-3 pt-3">
                <a href="mailto:support@florenceegi.it"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('creator_onboarding.buttons.support') }}
                </a>
            </div>
        </section>
    </div>
</x-platform-layout>

