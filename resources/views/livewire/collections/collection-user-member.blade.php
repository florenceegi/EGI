<div id="collection_management" class="p-6 bg-gray-800 border border-gray-700 shadow-lg rounded-2xl">
    {{-- <x-slot name="platformHeader"> --}}
    <!-- Titolo della sezione -->
    <div id="collection_management" class="p-6 bg-gray-800 border border-gray-700 shadow-lg rounded-2xl">

        <!-- Titolo della sezione -->
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div class="flex-1">
                <!-- Bottone torna alla collection -->
                <div class="mb-3">
                    <a href="{{ route('home.collections.show', ['id' => $collectionId]) }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 transition-colors duration-200 rounded-lg hover:text-white hover:bg-gray-700">
                        <span class="mr-2 material-symbols-outlined">arrow_back</span>
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
                    <button id="inviteNewMember" class="w-full btn btn-primary sm:w-auto" wire:click="openInviteModal">
                        {{ __('collection.invite_collection_member') }}
                    </button>
                    <!-- Bottone per creare un nuovo wallet -->
                    <button id="createNewWallet" name = "createNewWallet" class="w-full btn btn-primary sm:w-auto">
                        {{ __('collection.wallet.create_the_wallet') }}
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

                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->receiver->profile_photo_url }}"
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
                <div class="flex justify-end mt-4">
                    <button data-id="{{ $member->id }}" data-collection="{{ $member->collection_id }}"
                        data-user="{{ $member->receiver_id }}" {{-- Questo è l'utente che ha creato il wallet --}}
                        class="flex items-center px-3 py-1 text-sm text-white transition-colors duration-150 bg-red-600 rounded-md delete-proposal-invitation hover:bg-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
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
    <h3 class="mt-8 mb-4 text-xl font-bold text-white">{{ __('collection.wallet.wallets') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach ($wallets as $wallet)
            @include('livewire.partials.card-wallet', ['wallet' => $wallet, 'canCreateWallet' => $canCreateWallet])
        @endforeach
    </div>

    <!--  Sezione Wallet proposal -->
    <h3 class="mt-8 mb-4 text-xl font-bold text-white">{{ __('collection.wallet.wallets') }}</h3>
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
                        {{ substr($wallet->wallet, 0, 15) }}...{{ substr($wallet->wallet, -4) }}
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
                            <strong>{{ __('collection.wallet.approver') }}:</strong> {{ __('collection.wallet.unassigned') }}
                        </p>
                    @endif

                    <!-- Aggiungiamo qui il bottone Elimina -->
                    <div class="flex justify-end mt-4">
                        <button data-id="{{ $wallet->id }}" data-collection="{{ $collectionId }}"
                            data-user="{{ $wallet->receiver_id }}" {{-- Questo è l'utente che riceve  il wallet --}}
                            class="flex items-center px-3 py-1 text-sm text-white transition-colors duration-150 bg-red-600 rounded-md delete-proposal-wallet hover:bg-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none"
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
    {{-- </x-slot> --}}
</div>
