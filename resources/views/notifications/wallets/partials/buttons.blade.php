<!-- Contenitore dei Bottoni -->
@if (isset($notification->model) &&
        ($notification->model->type === App\Enums\NotificationStatus::UPDATE->value ||
            $notification->model->type === App\Enums\NotificationStatus::CREATION->value))
    <div class="notification-item mb-3 flex space-x-3" data-notification-id="{{ $notification->id }}"
        data-payload="{{ App\Enums\NotificationHandlerType::WALLET->value }}"
        data-payload-id="{{ $notification->model->id }}"
        aria-label="Azioni per la notifica di creazione del wallet">
        <div class="notification-actions flex space-x-3">
            @if (
                $notification->outcome === App\Enums\NotificationStatus::PENDING_UPDATE->value ||
                    $notification->outcome === App\Enums\NotificationStatus::PENDING_CREATE->value ||
                    $notification->outcome === App\Enums\NotificationStatus::PENDING->value)
                <button
                    class="response-btn flex flex-1 items-center justify-center rounded-lg bg-[#10B981] px-4 py-2 text-white transition-colors duration-200 hover:bg-green-600"
                    data-notification-id="{{ $notification->id }}"
                    @if ($notification->model->status === App\Enums\NotificationStatus::PENDING_CREATE->value) data-action="{{ App\Enums\NotificationStatus::ACCEPTED->value }}"
                    @else
                        data-action="{{ App\Enums\NotificationStatus::UPDATE->value }}" @endif
                    {{-- data-action="{{ $dataAction }}" --}} aria-label="Accetta la notifica di creazione del wallet">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('label.accept') }}
                </button>

                <button id="reject-btn-{{ $notification->id }}"
                    class="reject-btn flex flex-1 items-center justify-center rounded-lg bg-[#E53E3E] px-4 py-2 text-white transition-colors duration-200 hover:bg-red-600"
                    data-notification-id="{{ $notification->id }}"
                    data-action={{ App\Enums\NotificationStatus::REJECTED->value }}
                    aria-label="Rifiuta la notifica di creazione del wallet">
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
        @include('notifications.wallets.partials.button-archived')

    </div>
@endif
