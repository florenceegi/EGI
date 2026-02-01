@props(['countries' => []])

@php
    $routeStore = route('user.domains.personal-data.shipping-address.store');
    $routeUpdate = route('user.domains.personal-data.shipping-address.update', ['id' => 'ID_PLACEHOLDER']);
@endphp

<div x-data="{
    isOpen: false,
    mode: 'create', // 'create' or 'edit'
    actionUrl: '',
    address: {
        id: null,
        country: 'IT',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        postal_code: '',
        phone: '',
        is_default: false
    },

    // Store routes in JS variables
    routes: {
        store: '{{ $routeStore }}',
        update: '{{ $routeUpdate }}'
    },

    openCreate() {
        this.mode = 'create';
        this.actionUrl = this.routes.store;
        this.resetForm();
        this.isOpen = true;
    },

    openEdit(data) {
        this.mode = 'edit';
        this.actionUrl = this.routes.update.replace('ID_PLACEHOLDER', data.id);
        this.address = { ...data };
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
    },

    resetForm() {
        this.address = {
            id: null,
            country: 'IT',
            address_line_1: '',
            address_line_2: '',
            city: '',
            state: '',
            postal_code: '',
            phone: '',
            is_default: false
        };
    }
}"
    @open-shipping-address-modal.window="
    if($event.detail.mode === 'create') openCreate();
    else openEdit($event.detail.data);
"
    class="relative z-50" x-cloak>

    {{-- Modal Backdrop --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close()"></div>

    {{-- Modal Panel --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

            <div
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

                <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                    <button type="button" @click="close()"
                        class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900"
                            x-text="mode === 'create' ? '{{ __('user_personal_data.shipping.add_new') }}' : '{{ __('user_personal_data.shipping.edit_address') }}'">
                        </h3>

                        <form :action="actionUrl" method="POST" class="mt-6 space-y-4">
                            @csrf
                            <template x-if="mode === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            {{-- Country --}}
                            <div>
                                <label for="country"
                                    class="block text-sm font-medium leading-6 text-gray-900">Paese</label>
                                <select id="country" name="country" x-model="address.country"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    @foreach ($countries as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Address Line 1 --}}
                            <div>
                                <label for="address_line_1"
                                    class="block text-sm font-medium leading-6 text-gray-900">Indirizzo</label>
                                <input type="text" name="address_line_1" id="address_line_1"
                                    x-model="address.address_line_1" required
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>

                            {{-- Address Line 2 --}}
                            <div>
                                <label for="address_line_2"
                                    class="block text-sm font-medium leading-6 text-gray-900">Indirizzo 2
                                    (Opzionale)</label>
                                <input type="text" name="address_line_2" id="address_line_2"
                                    x-model="address.address_line_2"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- City --}}
                                <div>
                                    <label for="city"
                                        class="block text-sm font-medium leading-6 text-gray-900">Città</label>
                                    <input type="text" name="city" id="city" x-model="address.city" required
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>

                                {{-- Postal Code --}}
                                <div>
                                    <label for="postal_code"
                                        class="block text-sm font-medium leading-6 text-gray-900">CAP</label>
                                    <input type="text" name="postal_code" id="postal_code"
                                        x-model="address.postal_code" required
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- State --}}
                                <div>
                                    <label for="state"
                                        class="block text-sm font-medium leading-6 text-gray-900">Provincia/Stato</label>
                                    <input type="text" name="state" id="state" x-model="address.state"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium leading-6 text-gray-900">Telefono</label>
                                    <input type="text" name="phone" id="phone" x-model="address.phone"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            {{-- Is Default --}}
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="is_default" name="is_default" type="checkbox" value="1"
                                        x-model="address.is_default"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="is_default" class="font-medium text-gray-900">Imposta come
                                        predefinito</label>
                                    <p class="text-gray-500">Questo indirizzo verrà preselezionato al checkout.</p>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                    {{ __('user_personal_data.save_changes') }}
                                </button>
                                <button type="button" @click="close()"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    {{ __('common.cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
