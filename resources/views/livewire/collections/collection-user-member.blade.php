<div id="collection_management" class="rounded-2xl border border-gray-700 bg-gray-800 p-6 shadow-lg">
    {{-- <x-slot name="platformHeader"> --}}
    <!-- Titolo della sezione -->
    <div id="collection_management" class="rounded-2xl border border-gray-700 bg-gray-800 p-6 shadow-lg">

        <!-- Titolo della sezione -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="flex-1">
                <!-- Bottone torna alla collection -->
                <div class="mb-3">
                    <a href="{{ route('home.collections.show', ['id' => $collectionId]) }}"
                        class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors duration-200 hover:bg-gray-700 hover:text-white">
                        <span class="material-symbols-outlined mr-2">arrow_back</span>
                        {{ __('collection.back_to_collection') }}
                    </a>
                </div>

                <h2 class="text-2xl font-bold text-white">{{ $collectionName }}</h2>
                <p class="text-sm text-gray-400">
                    {{ __('collection.wallet.owner') }}: {{ $collectionOwner->name }} {{ $collectionOwner->last_name }}
                </p>
                <p class="text-sm text-gray-400">{{ __('collection.team_members_description') }}</p>
            </div>



            <div class="flex flex-wrap gap-4 space-x-0">
                <!-- Bottone per invitare un nuovo membro alla collection -->
                <button id="inviteNewMember" class="btn btn-primary w-full sm:w-auto" wire:click="openInviteModal">
                    {{ __('collection.invite_collection_member') }}
                </button>

                {{-- SINGOLO BOTTONE: Apre modal di selezione tipo wallet --}}
                @if ($canCreateWallet)
                    <button id="openWalletTypeModal" wire:click="openWalletTypeModal"
                        class="btn btn-primary w-full sm:w-auto">
                        <span class="flex items-center">
                            <span class="material-symbols-outlined mr-2">add_circle</span>
                            {{ __('collection.wallet.create_the_wallet') }}
                        </span>
                    </button>
                @endif
            </div>

        </div>
        <!-- Marquee ticker: Testo in movimento in stile tabellone borsa -->

        <x-quote-ticker />

    </div>

    <!-- Sezione Membri della Collection -->
    <h3 class="mb-4 text-xl font-bold text-white">{{ __('collection.members') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        @foreach ($collectionUsers as $member)
            @php
                // Recupera il wallet dell'utente membro
$memberUser = \App\Models\User::find($member->user_id);
$memberWallet = $memberUser ? $memberUser->wallet : '';
            @endphp
            @include('livewire.partials.card-collection-member', ['memberWallet' => $memberWallet])
        @endforeach

        <!-- Sezione Invitation proposal -->
        @foreach ($invitationProposal as $member)
            <div data-user-id="{{ $member->receiver_id }}" data-id="{{ $member->id }}"
                class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} rounded-xl p-4 shadow-md transition-shadow duration-300 hover:shadow-lg">

                <div class="mb-4 flex items-center">
                    <img class="h-12 w-12 rounded-full" src="{{ $member->receiver->profile_photo_url }}"
                        alt="{{ $member->receiver->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->receiver->name }}
                            {{ $member->receiver->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('collection.wallet.user_role') . ': ' . $member->role }}
                        </p>
                        <p class="text-sm text-gray-400">{{ __('email: ') . $member->receiver->email }}</p>
                    </div>
                </div>

                <!-- Aggiungiamo qui il bottone Elimina -->
                <div class="mt-4 flex justify-end">
                    <button data-id="{{ $member->id }}" data-collection="{{ $member->collection_id }}"
                        data-user="{{ $member->receiver_id }}" {{-- Questo è l'utente che ha creato il wallet --}}
                        class="delete-proposal-invitation flex items-center rounded-md bg-red-600 px-3 py-1 text-sm text-white transition-colors duration-150 hover:bg-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('label.delete') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Sezione Wallet -->
    <h3 class="mb-4 mt-8 text-xl font-bold text-white">{{ __('collection.wallet.wallets') }}</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
        @foreach ($wallets as $wallet)
            @include('livewire.partials.card-wallet', [
                'wallet' => $wallet,
                'canCreateWallet' => $canCreateWallet,
            ])
        @endforeach
    </div>

    <!--  Sezione Wallet proposal -->
    <h3 class="mb-4 mt-8 text-xl font-bold text-white">{{ __('collection.wallet.wallets') }}</h3>
    <div id="wallet-list" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
        @foreach ($walletProposals as $wallet)
            @php
                /** @param string $status */
                $status = App\Enums\NotificationStatus::fromDatabase($wallet->status);
                $isPending = Illuminate\Support\Str::contains($wallet->status, json_encode($status));
            @endphp

            <!--
                1️⃣ Prima regola:
                Se l'utente non ha i permessi ($canCreateWallet == false), oppure il wallet appartiene a "EPP" o "Natan", la schedina sarà sempre grigia (bg-gray-700 opacity-75 cursor-not-allowed) e l'utente non potrà interagire.
                2️⃣ Seconda regola:
                Se il wallet è pending, allora sarà giallo (bg-yellow-800), ma solo se l'utente ha i permessi.
                3️⃣ Terza regola:

                Se il wallet è "normale" e approvato, la schedina sarà grigia scura (bg-gray-900).
                📝 Esempi di comportamento
                $canCreateWallet	    $wallet->platform_role	  $isPending	Colore finale
                ❌ false	                qualsiasi	            ✅ true	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ❌ false	                qualsiasi	            ❌ false     ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                "Natan" / "EPP"	        ✅ true	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                "Natan" / "EPP"	        ❌ false	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                altro	                ✅ true	    🟡 bg-yellow-800
                ✅ true	                altro	                ❌ false	    ⚫ bg-gray-900
            -->
            <div id="wallet-{{ $wallet->id }}"
                class="wallet-item {{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP'])
                    ? 'bg-gray-700 opacity-75 cursor-not-allowed'
                    : ($isPending
                        ? 'bg-yellow-800'
                        : 'bg-gray-900') }} rounded-xl p-4 shadow-md transition-shadow duration-300 hover:shadow-lg">

                <div>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.user_role') }}:</strong> {{ $wallet->platform_role }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.address') }}:</strong>
                        <span class="inline-flex items-center gap-2">
                            {{-- Mobile: abbreviated --}}
                            <span
                                class="font-mono md:hidden">{{ substr($wallet->wallet, 0, 15) }}...{{ substr($wallet->wallet, -4) }}</span>
                            {{-- Desktop: full address --}}
                            <span class="hidden break-all font-mono md:inline">{{ $wallet->wallet }}</span>
                            <button onclick="copyWalletAddress('{{ $wallet->wallet }}')"
                                class="text-gray-400 transition-colors hover:text-blue-400 focus:outline-none"
                                aria-label="{{ __('collection.wallet.copy_address') }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </span>
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                    </p>

                    <!-- Nome e Cognome dell'Utente Correlato -->
                    @if ($wallet->receiver)
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.approver') }}:</strong> {{ $wallet->receiver->name }}
                            {{ $wallet->receiver->last_name }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.approver') }}:</strong>
                            {{ __('collection.wallet.unassigned') }}
                        </p>
                    @endif

                    <!-- Aggiungiamo qui il bottone Elimina -->
                    <div class="mt-4 flex justify-end">
                        <button data-id="{{ $wallet->id }}" data-collection="{{ $collectionId }}"
                            data-user="{{ $wallet->receiver_id }}" {{-- Questo è l'utente che riceve  il wallet --}}
                            class="delete-proposal-wallet flex items-center rounded-md bg-red-600 px-3 py-1 text-sm text-white transition-colors duration-150 hover:bg-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('label.delete') }}
                        </button>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Bottone che permette di aprire la collection -->
    {{-- @include('livewire.collection-manager-includes.back_to_collection_button') --}}

    @if ($show)
        <!-- Include le Modali -->
        @include('livewire.notifications.invitations.invite-user-to-collection-modal', [
            'collectionId' => $collectionId,
        ])
    @endif

    {{-- Modal: Selezione Tipo Wallet --}}
    @if ($showWalletTypeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-2xl rounded-lg bg-gray-800 p-6 shadow-xl">
                {{-- Header --}}
                <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold text-white">
                        <span class="material-symbols-outlined mr-2">wallet</span>
                        {{ __('collection.wallet.select_type_title') }}
                    </h3>
                    <button wire:click="closeWalletTypeModal" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                {{-- Description --}}
                <p class="mb-6 text-sm text-gray-400">
                    {{ __('collection.wallet.select_type_description') }}
                </p>

                {{-- Wallet Type Cards --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($availableWalletTypes as $type => $config)
                        <button wire:click="selectWalletType('{{ $type }}')"
                            class="group flex flex-col rounded-lg border border-gray-600 bg-gray-700 p-4 text-left transition-all hover:border-blue-500 hover:bg-gray-600">
                            <div class="mb-2 flex items-center">
                                <span
                                    class="material-symbols-outlined mr-3 text-3xl text-blue-400 group-hover:text-blue-300">
                                    {{ $config['icon'] }}
                                </span>
                                <span class="text-lg font-semibold text-white">
                                    {{ $config['label'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 group-hover:text-gray-300">
                                {{ $config['description'] }}
                            </p>
                        </button>
                    @endforeach
                </div>

                {{-- Cancel Button --}}
                <div class="mt-6 flex justify-end">
                    <button wire:click="closeWalletTypeModal"
                        class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                        {{ __('label.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Aggiungi Wallet Esterno --}}
    @if ($showExternalWalletModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-2xl rounded-lg bg-gray-800 p-6 shadow-xl">
                {{-- Header --}}
                <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold text-white">
                        <span class="material-symbols-outlined mr-2">wallet</span>
                        {{ __('collection.wallet.add_external_title') }}
                    </h3>
                    <button wire:click="closeExternalWalletModal" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                {{-- Description --}}
                <div class="mb-6 rounded-lg border border-blue-700 bg-blue-900 bg-opacity-30 p-4">
                    <p class="text-sm text-blue-200">
                        <span class="material-symbols-outlined mr-2">info</span>
                        {{ __('collection.wallet.add_external_description') }}
                    </p>
                </div>

                {{-- Form --}}
                <div class="space-y-4">
                    {{-- Algorand Address --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.address_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="externalWalletAddress"
                            placeholder="GABC...XYZ (58 caratteri)"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                            maxlength="58">
                        <p class="mt-1 text-xs text-gray-400">
                            {{ __('collection.wallet.address_hint') }}
                        </p>
                    </div>

                    {{-- Wallet Name (Optional) --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.name_label') }} <span
                                class="text-gray-500">({{ __('label.optional') }})</span>
                        </label>
                        <input type="text" wire:model="externalWalletName"
                            placeholder="{{ __('collection.wallet.name_placeholder') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Royalties Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Royalty Mint --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_mint_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="externalWalletRoyaltyMint" min="0"
                                    max="100" step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>

                        {{-- Royalty Rebind --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_rebind_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="externalWalletRoyaltyRebind" min="0"
                                    max="100" step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Warning: Royalty Deduction --}}
                    <div class="rounded-lg border border-yellow-600 bg-yellow-900 bg-opacity-20 p-4">
                        <p class="text-sm text-yellow-200">
                            <span class="material-symbols-outlined mr-2">warning</span>
                            {{ __('collection.wallet.royalty_deduction_warning') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeExternalWalletModal"
                        class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                        {{ __('label.cancel') }}
                    </button>
                    <button wire:click="addExternalWallet" wire:loading.attr="disabled"
                        class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="addExternalWallet">
                            {{ __('collection.wallet.add_button') }}
                        </span>
                        <span wire:loading wire:target="addExternalWallet" class="flex items-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('collection.wallet.adding') }}...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Stripe Wallet --}}
    @if ($showStripeWalletModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-2xl rounded-lg bg-gray-800 p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold text-white">
                        <span class="material-symbols-outlined mr-2">credit_card</span>
                        {{ __('collection.wallet.stripe.title') }}
                    </h3>
                    <button wire:click="closeStripeWalletModal" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                {{-- Step 1: Choose Mode --}}
                @if ($stripeMode === 'choose')
                    <div class="mb-6 rounded-lg border border-blue-700 bg-blue-900 bg-opacity-30 p-4">
                        <p class="text-sm text-blue-200">
                            <span class="material-symbols-outlined mr-2">info</span>
                            {{ __('collection.wallet.stripe.choose_description') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Option: New Account (Onboarding) --}}
                        <button wire:click="setStripeMode('onboarding')"
                            class="group flex flex-col items-center justify-center rounded-xl border-2 border-gray-600 bg-gray-700 p-6 text-center transition-all hover:border-blue-500 hover:bg-gray-600">
                            <span
                                class="material-symbols-outlined mb-3 text-4xl text-blue-400 group-hover:text-blue-300">add_circle</span>
                            <h4 class="mb-2 text-lg font-semibold text-white">
                                {{ __('collection.wallet.stripe.new_account') }}</h4>
                            <p class="text-sm text-gray-400">{{ __('collection.wallet.stripe.new_account_desc') }}</p>
                        </button>

                        {{-- Option: Link Existing Account --}}
                        <button wire:click="setStripeMode('existing')"
                            class="group flex flex-col items-center justify-center rounded-xl border-2 border-gray-600 bg-gray-700 p-6 text-center transition-all hover:border-green-500 hover:bg-gray-600">
                            <span
                                class="material-symbols-outlined mb-3 text-4xl text-green-400 group-hover:text-green-300">link</span>
                            <h4 class="mb-2 text-lg font-semibold text-white">
                                {{ __('collection.wallet.stripe.existing_account') }}</h4>
                            <p class="text-sm text-gray-400">
                                {{ __('collection.wallet.stripe.existing_account_desc') }}</p>
                        </button>
                    </div>

                    {{-- Step 2a: Onboarding Flow --}}
                @elseif ($stripeMode === 'onboarding')
                    @if ($stripeOnboardingUrl)
                        {{-- Onboarding link generated --}}
                        <div class="mb-6 rounded-lg border border-green-700 bg-green-900 bg-opacity-30 p-4">
                            <p class="text-sm text-green-200">
                                <span class="material-symbols-outlined mr-2">check_circle</span>
                                {{ __('collection.wallet.stripe.onboarding_ready') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <a href="{{ $stripeOnboardingUrl }}" target="_blank"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white transition-colors hover:bg-blue-700">
                                <span class="material-symbols-outlined mr-2">open_in_new</span>
                                {{ __('collection.wallet.stripe.complete_onboarding') }}
                            </a>
                            <p class="mt-4 text-sm text-gray-400">
                                {{ __('collection.wallet.stripe.onboarding_redirect_info') }}</p>
                        </div>
                    @else
                        {{-- Onboarding form --}}
                        <button wire:click="setStripeMode('choose')"
                            class="mb-4 flex items-center text-sm text-gray-400 hover:text-white">
                            <span class="material-symbols-outlined mr-1 text-sm">arrow_back</span>
                            {{ __('label.back') }}
                        </button>

                        <div class="mb-6 rounded-lg border border-blue-700 bg-blue-900 bg-opacity-30 p-4">
                            <p class="text-sm text-blue-200">
                                <span class="material-symbols-outlined mr-2">info</span>
                                {{ __('collection.wallet.stripe.onboarding_info') }}
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-200">
                                    {{ __('collection.wallet.stripe.account_name') }} <span
                                        class="text-gray-500">({{ __('label.optional') }})</span>
                                </label>
                                <input type="text" wire:model="stripeAccountName"
                                    placeholder="{{ __('collection.wallet.stripe.account_placeholder') }}"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-200">
                                        {{ __('collection.wallet.royalty_mint_label') }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" wire:model="stripeRoyaltyMint" min="0"
                                            max="100" step="0.01"
                                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-200">
                                        {{ __('collection.wallet.royalty_rebind_label') }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" wire:model="stripeRoyaltyRebind" min="0"
                                            max="100" step="0.01"
                                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-lg border border-yellow-700 bg-yellow-900 bg-opacity-30 p-4">
                                <p class="text-sm text-yellow-200">
                                    <span class="material-symbols-outlined mr-2">warning</span>
                                    {{ __('collection.wallet.royalty_deduction_warning') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button wire:click="closeStripeWalletModal"
                                class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                                {{ __('label.cancel') }}
                            </button>
                            <button wire:click="initiateStripeOnboarding" wire:loading.attr="disabled"
                                class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 disabled:opacity-50">
                                <span wire:loading.remove wire:target="initiateStripeOnboarding">
                                    {{ __('collection.wallet.stripe.start_onboarding') }}
                                </span>
                                <span wire:loading wire:target="initiateStripeOnboarding" class="flex items-center">
                                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    {{ __('label.loading') }}...
                                </span>
                            </button>
                        </div>
                    @endif

                    {{-- Step 2b: Link Existing Account --}}
                @elseif ($stripeMode === 'existing')
                    <button wire:click="setStripeMode('choose')"
                        class="mb-4 flex items-center text-sm text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined mr-1 text-sm">arrow_back</span>
                        {{ __('label.back') }}
                    </button>

                    <div class="mb-6 rounded-lg border border-green-700 bg-green-900 bg-opacity-30 p-4">
                        <p class="text-sm text-green-200">
                            <span class="material-symbols-outlined mr-2">info</span>
                            {{ __('collection.wallet.stripe.link_existing_info') }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.stripe.account_id_label') }} <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="stripeAccountId" placeholder="acct_1234567890"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 font-mono text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-400">{{ __('collection.wallet.stripe.account_id_hint') }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.stripe.account_name') }} <span
                                    class="text-gray-500">({{ __('label.optional') }})</span>
                            </label>
                            <input type="text" wire:model="stripeAccountName"
                                placeholder="{{ __('collection.wallet.stripe.account_placeholder') }}"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-200">
                                    {{ __('collection.wallet.royalty_mint_label') }} <span
                                        class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="stripeRoyaltyMint" min="0"
                                        max="100" step="0.01"
                                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-200">
                                    {{ __('collection.wallet.royalty_rebind_label') }} <span
                                        class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="stripeRoyaltyRebind" min="0"
                                        max="100" step="0.01"
                                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-yellow-700 bg-yellow-900 bg-opacity-30 p-4">
                            <p class="text-sm text-yellow-200">
                                <span class="material-symbols-outlined mr-2">warning</span>
                                {{ __('collection.wallet.royalty_deduction_warning') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="closeStripeWalletModal"
                            class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                            {{ __('label.cancel') }}
                        </button>
                        <button wire:click="linkExistingStripeAccount" wire:loading.attr="disabled"
                            class="rounded-lg bg-green-600 px-6 py-2 text-white transition-colors hover:bg-green-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="linkExistingStripeAccount">
                                {{ __('collection.wallet.stripe.link_button') }}
                            </span>
                            <span wire:loading wire:target="linkExistingStripeAccount" class="flex items-center">
                                <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('label.verifying') }}...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif


    {{-- Modal: PayPal Wallet --}}
    @if ($showPaypalWalletModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-2xl rounded-lg bg-gray-800 p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold text-white">
                        <span class="material-symbols-outlined mr-2">payments</span>
                        {{ __('collection.wallet.paypal.title') }}
                    </h3>
                    <button wire:click="closePaypalWalletModal" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                <div class="mb-6 rounded-lg border border-blue-700 bg-blue-900 bg-opacity-30 p-4">
                    <p class="text-sm text-blue-200">
                        <span class="material-symbols-outlined mr-2">info</span>
                        {{ __('collection.wallet.paypal.description') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.paypal.email_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="paypalEmail"
                            placeholder="{{ __('collection.wallet.paypal.email_placeholder') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.paypal.merchant_id_label') }} <span
                                class="text-gray-500">({{ __('label.optional') }})</span>
                        </label>
                        <input type="text" wire:model="paypalMerchantId"
                            placeholder="{{ __('collection.wallet.paypal.merchant_placeholder') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_mint_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="paypalRoyaltyMint" min="0" max="100"
                                    step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_rebind_label') }} <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="paypalRoyaltyRebind" min="0" max="100"
                                    step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-yellow-700 bg-yellow-900 bg-opacity-30 p-4">
                        <p class="text-sm text-yellow-200">
                            <span class="material-symbols-outlined mr-2">warning</span>
                            {{ __('collection.wallet.royalty_deduction_warning') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closePaypalWalletModal"
                        class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                        {{ __('label.cancel') }}
                    </button>
                    <button wire:click="createPaypalWallet" wire:loading.attr="disabled"
                        class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="createPaypalWallet">
                            {{ __('collection.wallet.paypal.create_button') }}
                        </span>
                        <span wire:loading wire:target="createPaypalWallet" class="flex items-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('label.creating') }}...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: IBAN Wallet --}}
    @if ($showIbanWalletModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-2xl rounded-lg bg-gray-800 p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold text-white">
                        <span class="material-symbols-outlined mr-2">account_balance</span>
                        {{ __('collection.wallet.iban.title') }}
                    </h3>
                    <button wire:click="closeIbanWalletModal" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                <div class="mb-6 rounded-lg border border-blue-700 bg-blue-900 bg-opacity-30 p-4">
                    <p class="text-sm text-blue-200">
                        <span class="material-symbols-outlined mr-2">info</span>
                        {{ __('collection.wallet.iban.description') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.iban.iban_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="ibanNumber"
                            placeholder="{{ __('collection.wallet.iban.iban_placeholder') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 font-mono text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-200">
                            {{ __('collection.wallet.iban.holder_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="ibanAccountHolder"
                            placeholder="{{ __('collection.wallet.iban.holder_placeholder') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.iban.bank_name_label') }} <span
                                    class="text-gray-500">({{ __('label.optional') }})</span>
                            </label>
                            <input type="text" wire:model="ibanBankName"
                                placeholder="{{ __('collection.wallet.iban.bank_placeholder') }}"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.iban.swift_label') }} <span
                                    class="text-gray-500">({{ __('label.optional') }})</span>
                            </label>
                            <input type="text" wire:model="ibanSwiftBic"
                                placeholder="{{ __('collection.wallet.iban.swift_placeholder') }}"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 font-mono text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_mint_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="ibanRoyaltyMint" min="0" max="100"
                                    step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-200">
                                {{ __('collection.wallet.royalty_rebind_label') }} <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="ibanRoyaltyRebind" min="0" max="100"
                                    step="0.01"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 pr-9 text-white focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-yellow-700 bg-yellow-900 bg-opacity-30 p-4">
                        <p class="text-sm text-yellow-200">
                            <span class="material-symbols-outlined mr-2">warning</span>
                            {{ __('collection.wallet.royalty_deduction_warning') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeIbanWalletModal"
                        class="rounded-lg bg-gray-700 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-600">
                        {{ __('label.cancel') }}
                    </button>
                    <button wire:click="createIbanWallet" wire:loading.attr="disabled"
                        class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="createIbanWallet">
                            {{ __('collection.wallet.iban.create_button') }}
                        </span>
                        <span wire:loading wire:target="createIbanWallet" class="flex items-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('label.creating') }}...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- </x-slot> --}}
</div>

@push('scripts')
    <script>
        /**
         * Copy wallet address to clipboard
         * @param {string} address - Algorand wallet address
         */
        function copyWalletAddress(address) {
            // Use modern Clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(address)
                    .then(() => {
                        // Show success feedback
                        showCopyFeedback(event.target, true);
                    })
                    .catch(err => {
                        console.error('Failed to copy address:', err);
                        // Fallback to old method
                        fallbackCopy(address);
                    });
            } else {
                // Fallback for older browsers
                fallbackCopy(address);
            }
        }

        /**
         * Fallback copy method for older browsers
         */
        function fallbackCopy(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                showCopyFeedback(event.target, successful);
            } catch (err) {
                console.error('Fallback copy failed:', err);
                showCopyFeedback(event.target, false);
            }

            document.body.removeChild(textArea);
        }

        /**
         * Show visual feedback when copying
         */
        function showCopyFeedback(element, success) {
            // Find the button element (might be SVG child)
            const button = element.closest('button');
            if (!button) return;

            // Store original colors
            const originalClasses = button.className;

            if (success) {
                // Green flash for success
                button.classList.remove('text-gray-400', 'hover:text-blue-400');
                button.classList.add('text-green-400');

                // Show tooltip (optional - using native title for simplicity)
                const originalTitle = button.getAttribute('aria-label');
                button.setAttribute('aria-label', '{{ __('collection.wallet.address_copied') }}');

                // Reset after 2 seconds
                setTimeout(() => {
                    button.className = originalClasses;
                    button.setAttribute('aria-label', originalTitle);
                }, 2000);
            } else {
                // Red flash for error
                button.classList.remove('text-gray-400', 'hover:text-blue-400');
                button.classList.add('text-red-400');

                setTimeout(() => {
                    button.className = originalClasses;
                }, 2000);
            }
        }
    </script>
@endpush
