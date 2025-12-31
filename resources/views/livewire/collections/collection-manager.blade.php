<div class="mx-auto w-full max-w-4xl md:p-4">

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="mb-4 px-4 text-2xl font-bold md:px-0">{{ __('collection.manage_collection') }}</h2>

    <form wire:submit.prevent="save({{ $collectionId }})" class="space-y-6 md:rounded-lg md:bg-white md:p-6 md:shadow-sm">

        <!-- Sezione dei dati della collection -->
        @include('livewire.collection-manager-includes.data_section')

        <div
            class="mt-6 flex items-center justify-center bg-gray-900 p-4 shadow-md transition-shadow duration-300 hover:shadow-lg md:rounded-xl">
            <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                <!-- Bottone per aprire la vista per la gestione delle immagini di testata -->
                <a href="{{ route('collections.head_images', ['id' => $collectionId]) }}"
                    class="btn btn-primary btn-lg w-full">
                    {{ __('collection.collection_image') }}
                </a>

                <!-- Bottone per aprire la vista dei membri della collection -->
                @if (App\Helpers\FegiAuth::can('update_team'))
                    <a href="{{ route('collections.collection_user', ['id' => $collectionId]) }}"
                        class="btn btn-primary btn-lg w-full">
                        {{ __('collection.collection_members') }}
                    </a>
                @endif

                <!-- Bottone per le impostazioni pagamenti -->
                <a href="{{ route('collections.settings.payments.index', ['collection' => $collectionId]) }}"
                    class="btn btn-primary btn-lg w-full">
                    {{ __('Payment Settings') }}
                </a>
                <!-- Bottone per il salvataggio -->
                <div class="flex justify-center md:justify-end">
                    <x-form-button type="submit" style="primary" class="w-full px-6 md:w-auto">
                        {{ __('label.save') }}
                    </x-form-button>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
    console.log('resources/views/livewire/collection-manager.blade.php');
</script>


<script>
    function closeModal() {
        document.querySelector('.fixed').remove();
    }
</script>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Indirizzo copiato negli appunti!');
        }).catch(err => {
            console.error('Errore durante la copia: ', err);
        });
    }
</script>

<script>
    document.addEventListener('livewire:init', () => {
        // Gestisce errori di permessi o appartenenza
        Livewire.on('swal:error', (text) => {
            Swal.fire({
                icon: 'error',
                title: text[0]['title'],
                text: text[0]['text'],
                confirmButtonColor: '#d33',
                confirmButtonText: 'Chiudi'
            });
        });
    });
</script>
