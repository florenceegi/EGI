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
                <!-- Bottone per creare un nuovo wallet Algorand -->
                <button id="createNewWallet" wire:click="createNewWallet" wire:loading.attr="disabled"
                    class="{{ !$canCreateWallet ? 'opacity-50 cursor-not-allowed' : '' }} btn btn-primary w-full sm:w-auto">
                    <span wire:loading.remove wire:target="createNewWallet">
                        {{ __('collection.wallet.create_the_wallet') }}
                    </span>
                    <span wire:loading wire:target="createNewWallet" class="flex items-center">
                        <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('collection.wallet.creating') }}...
                    </span>
                </button>
                <!-- Bottone per aggiungere wallet esterno -->
                <button id="addExternalWallet" wire:click="openExternalWalletModal"
                    class="{{ !$canCreateWallet ? 'opacity-50 cursor-not-allowed' : '' }} btn btn-secondary w-full sm:w-auto">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined mr-2">wallet</span>
                        {{ __('collection.wallet.add_external') }}
                    </span>
                </button>
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
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach ($wallets as $wallet)
            @include('livewire.partials.card-wallet', [
                'wallet' => $wallet,
                'canCreateWallet' => $canCreateWallet,
            ])
        @endforeach
    </div>

    <!--  Sezione Wallet proposal -->
    <h3 class="mb-4 mt-8 text-xl font-bold text-white">{{ __('collection.wallet.wallets') }}</h3>
    <div id="wallet-list" class="grid grid-cols-1 gap-6 md:grid-cols-3">
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
