<div class="p-4 mb-4 bg-gray-600 rounded-lg" itemscope itemtype="https://schema.org/InformAction" aria-label="Notifica: Invito alla Collection">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <!-- Contenitore dei Dettagli -->
        <div class="w-full p-4 bg-gray-800 rounded-lg shadow-md" itemprop="result" itemscope itemtype="https://schema.org/Message">
            <!-- Header della Notifica -->
            <div class="flex flex-col items-start pb-2 mb-4 border-b border-gray-600">
                <div class="flex items-center">
                    <div class="p-2 mr-3 bg-gray-700 rounded-full" aria-label="Icona notifica">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" itemprop="name">{{ __('Invito alla Collection') }}</h3>
                </div>
            </div>

            <!-- Contenuto della Notifica -->
            <div class="mb-4 space-y-2">
                <p class="text-gray-300" itemprop="description">{{ $notification->data['message'] }}</p>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium" itemprop="sender">{{ $notification->data['sender'] }}</span>
                </div>

                <div class="flex items-center {{ $notification->outcome === 'rejected' ? 'text-red-300' : 'text-emerald-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium" itemprop="email">{{ $notification->data['email'] }}</span>
                </div>

                <div class="flex items-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium" itemprop="about">{{ $notification->data['collection_name'] }}</span>
                </div>
            </div>

            <!-- Footer con Timestamp -->
            <div class="flex items-center pt-3 text-sm text-gray-400 border-t border-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
            </div>
        </div>


        <div class="flex mb-3 space-x-3 notification-item"
            data-notification-id="{{ $notification->id }}"
            data-payload="{{ App\Enums\NotificationHandlerType::INVITATION->value }}"
            data-payload-id={{ $notification->model->id }}
            aria-label="Azioni per la notifica di creazione del wallet">
            <div class="flex space-x-3 notification-actions">
                @if($notification->outcome === App\Enums\NotificationStatus::PENDING->value)
                    <button
                        id="invitation-response-btn-{{ $notification->id }}"
                        class="flex items-center justify-center flex-1 px-4 py-2 text-white transition-colors duration-200 bg-green-500 rounded-lg invitation-response-btn hover:bg-green-600"
                        data-payload-id="{{ $notification->model->id }}"
                        data-action={{ App\Enums\NotificationStatus::ACCEPTED->value }}
                        aria-label="Accetta la notifica di creazione del wallet">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('label.accept') }}
                    </button>

                    <button
                        id="invitation-reject-btn-{{ $notification->id }}"
                        class="flex items-center justify-center flex-1 px-4 py-2 text-white transition-colors duration-200 bg-red-500 rounded-lg invitation-reject-btn hover:bg-red-600"
                        data-payload-id="{{ $notification->model->id }}"
                        data-action={{ App\Enums\NotificationStatus::REJECTED->value }}
                        aria-label="Rifiuta la notifica di creazione del wallet">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('label.decline') }}
                    </button>
                @endif

            </div>

            <!-- Pulsante Archive (sempre presente nel DOM ma nascosto se non necessario) -->

            <button class="px-4 py-2 mt-3 font-medium text-white rounded-lg invitation-archive-btn bg-emerald-500 hover:bg-emerald-700"
                id="invitation-archive-btn-{{ $notification->id }}"
                data-notification-id="{{ $notification->id }}"
                data-action={{ App\Enums\NotificationStatus::ARCHIVED->value }}
                aria-label="Archivia questa notifica"
                style="{{ $notification->outcome === 'Accepted' ? 'display: block;' : 'display: none;' }}">
                🗄️ {{ __('label.archive') }}
            </button>
        </div>
    </div>

        <script>

            const acceptButton = document.getElementById('accept-invitation');
            const rejectButton = document.getElementById('reject-invitation');

            // Gestione messaggio di conferma per accettare
            acceptButton.addEventListener('click', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Sei sicuro?',
                    text: 'Accettando entrerai nel team della collection.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sì, accetto!',
                    cancelButtonText: 'No, annulla',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Invito accettato!');
                        $wire.dispatch('response', { option: 'accepted' });
                    }
                });
            });

            // Gestione messaggio di conferma per rifiutare
            rejectButton.addEventListener('click', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Sei sicuro?',
                    text: 'Rifiutando non entrerai nel team della collection.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sì, rifiuto!',
                    cancelButtonText: 'No, annulla',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('response', { option: 'rejected' });
                    }
                });
            });

            // Ascolta la risposta dal backend
            $wire.on('notification-response', (event) => {
                if (event.detail.success) {
                    Swal.fire(
                        event.detail.option === 'accepted' ? 'Invito accettato!' : 'Invito rifiutato!',
                        'Operazione completata con successo.',
                        'success'
                    );
                } else {
                    // Gestione specifica per l'errore "già membro"
                    if (event.detail.error === 'ALREADY_MEMBER') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Già membro!',
                            text: event.detail.message || 'Sei già membro di questa collezione!',
                            confirmButtonText: 'Ho capito',
                            confirmButtonColor: '#3B82F6',
                            background: 'rgba(255, 255, 255, 0.95)',
                            backdrop: 'rgba(0, 0, 0, 0.5)'
                        });
                    } else {
                        // Altri errori - mostra errore generico
                        Swal.fire(
                            'Errore!',
                            event.detail.message || event.detail.error || 'Si è verificato un errore.',
                            'error'
                        );
                    }
                }
            });
        </script>

</div>



