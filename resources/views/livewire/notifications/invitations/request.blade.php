<div class="mb-4 rounded-lg bg-gray-600 p-4" itemscope itemtype="https://schema.org/InformAction"
    aria-label="Notifica: Invito alla Collection">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <!-- Contenitore dei Dettagli -->
        <div class="w-full rounded-lg bg-gray-800 p-4 shadow-md" itemprop="result" itemscope
            itemtype="https://schema.org/Message">
            <!-- Header della Notifica -->
            <div class="mb-4 flex flex-col items-start border-b border-gray-600 pb-2">
                <div class="flex items-center">
                    <div class="mr-3 rounded-full bg-gray-700 p-2" aria-label="Icona notifica">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" itemprop="name">{{ __('Invito alla Collection') }}</h3>
                </div>
            </div>

            <!-- Contenuto della Notifica -->
            <div class="mb-4 space-y-2">
                <p class="text-gray-300" itemprop="description">{{ $notification->data['message'] }}</p>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium" itemprop="sender">{{ $notification->data['sender'] }}</span>
                </div>

                <div
                    class="{{ $notification->outcome === 'rejected' ? 'text-red-300' : 'text-emerald-300' }} flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium" itemprop="email">{{ $notification->data['email'] }}</span>
                </div>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium" itemprop="about">{{ $notification->data['collection_name'] }}</span>
                </div>
            </div>

            <!-- Footer con Timestamp -->
            <div class="flex items-center border-t border-gray-600 pt-3 text-sm text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <time
                    datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
            </div>
        </div>


        <div class="notification-item mb-3 flex space-x-3" data-notification-id="{{ $notification->id }}"
            data-payload="{{ App\Enums\NotificationHandlerType::INVITATION->value }}"
            data-payload-id={{ $notification->model->id }} aria-label="Azioni per la notifica di creazione del wallet">
            <div class="notification-actions flex space-x-3">
                @if ($notification->outcome === App\Enums\NotificationStatus::PENDING->value)
                    <button 
                        wire:click="response('accepted')"
                        wire:confirm="Sei sicuro? Accettando entrerai nel team della collection."
                        class="flex flex-1 items-center justify-center rounded-lg bg-green-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-green-600"
                        aria-label="Accetta l'invito alla collection">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('label.accept') }}
                    </button>

                    <button 
                        wire:click="response('rejected')"
                        wire:confirm="Sei sicuro? Rifiutando non entrerai nel team della collection."
                        class="flex flex-1 items-center justify-center rounded-lg bg-red-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-red-600"
                        aria-label="Rifiuta l'invito alla collection">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('label.decline') }}
                    </button>
                @endif

            </div>

            <!-- Pulsante Archive (sempre presente nel DOM ma nascosto se non necessario) -->

            <button
                class="invitation-archive-btn mt-3 rounded-lg bg-emerald-500 px-4 py-2 font-medium text-white hover:bg-emerald-700"
                id="invitation-archive-btn-{{ $notification->id }}" data-notification-id="{{ $notification->id }}"
                data-action={{ App\Enums\NotificationStatus::ARCHIVED->value }} aria-label="Archivia questa notifica"
                style="{{ $notification->outcome === 'Accepted' ? 'display: block;' : 'display: none;' }}">
                🗄️ {{ __('label.archive') }}
            </button>
        </div>
    </div>

    @script
    <script>
        // Ascolta la risposta dal backend
        Livewire.on('notification-response', (data) => {
            // Livewire 3 event format: data is first element of array
            const event = Array.isArray(data) ? data[0] : data;

            if (event.success) {
                Swal.fire(
                    event.option === 'accepted' ? 'Invito accettato!' : 'Invito rifiutato!',
                    'Operazione completata con successo.',
                    'success'
                ).then(() => {
                    // Ricarica la pagina per aggiornare la lista notifiche
                    window.location.reload();
                });
            } else {
                // Gestione specifica per l'errore "già membro"
                if (event.error === 'ALREADY_MEMBER') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Già membro!',
                        text: event.message || 'Sei già membro di questa collezione!',
                        confirmButtonText: 'Ho capito',
                        confirmButtonColor: '#3B82F6',
                        background: 'rgba(255, 255, 255, 0.95)',
                        backdrop: 'rgba(0, 0, 0, 0.5)'
                    });
                } else {
                    // Altri errori - mostra errore generico
                    Swal.fire(
                        'Errore!',
                        event.message || event.error || 'Si è verificato un errore.',
                        'error'
                    );
                }
            }
        });
    </script>
    @endscript

</div>
