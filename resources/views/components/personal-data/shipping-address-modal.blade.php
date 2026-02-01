@props(['countries' => []])

<div id="shipping-address-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">

    {{-- Modal Backdrop --}}
    <div id="shipping-modal-backdrop" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

            <div
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

                {{-- Close Button (X) --}}
                <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                    <button type="button" onclick="window.closeShippingModal()"
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
                        <h3 id="shipping-modal-title" class="text-base font-semibold leading-6 text-gray-900">
                            {{-- Title via JS --}}
                        </h3>

                        <form id="shipping-address-form" method="POST" class="mt-6 space-y-4">
                            @csrf
                            {{-- Method spoofing handled by JS --}}
                            <input type="hidden" name="id" value="">

                            {{-- Country --}}
                            <div>
                                <label for="sa_country"
                                    class="block text-sm font-medium leading-6 text-gray-900">Paese</label>
                                <select id="sa_country" name="country"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    @foreach ($countries as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Address Line 1 --}}
                            <div>
                                <label for="sa_address_line_1"
                                    class="block text-sm font-medium leading-6 text-gray-900">Indirizzo</label>
                                <input type="text" name="address_line_1" id="sa_address_line_1" required
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>

                            {{-- Address Line 2 --}}
                            <div>
                                <label for="sa_address_line_2"
                                    class="block text-sm font-medium leading-6 text-gray-900">Indirizzo 2
                                    (Opzionale)</label>
                                <input type="text" name="address_line_2" id="sa_address_line_2"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- City --}}
                                <div>
                                    <label for="sa_city"
                                        class="block text-sm font-medium leading-6 text-gray-900">Città</label>
                                    <input type="text" name="city" id="sa_city" required
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>

                                {{-- Postal Code --}}
                                <div>
                                    <label for="sa_postal_code"
                                        class="block text-sm font-medium leading-6 text-gray-900">CAP</label>
                                    <input type="text" name="postal_code" id="sa_postal_code" required
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- State --}}
                                <div>
                                    <label for="sa_state"
                                        class="block text-sm font-medium leading-6 text-gray-900">Provincia/Stato</label>
                                    <input type="text" name="state" id="sa_state"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label for="sa_phone"
                                        class="block text-sm font-medium leading-6 text-gray-900">Telefono</label>
                                    <input type="text" name="phone" id="sa_phone"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            {{-- Is Default --}}
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="sa_is_default" name="is_default" type="checkbox" value="1"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="sa_is_default" class="font-medium text-gray-900">Imposta come
                                        predefinito</label>
                                    <p class="text-gray-500">Questo indirizzo verrà preselezionato al checkout.</p>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="button" onclick="window.saveShippingAddress(event)"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                    Salva Indirizzo
                                </button>
                                <button type="button" onclick="window.closeShippingModal()"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    Annulla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
