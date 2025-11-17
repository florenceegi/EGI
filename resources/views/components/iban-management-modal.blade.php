{{-- IBAN Management Modal - Lightweight popup for IBAN input --}}
<div id="ibanManagementModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
            onclick="closeIbanModal()"></div>

        {{-- Center modal --}}
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1 mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            {{ __('user_personal_data.iban_management') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ __('user_personal_data.iban_description') }}
                            </p>
                        </div>

                        {{-- IBAN Form --}}
                        <form id="ibanManagementForm" class="mt-4">
                            @csrf
                            <div>
                                <label for="iban_input" class="block text-sm font-medium text-gray-700">IBAN</label>
                                <input type="text" id="iban_input" name="iban"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                    placeholder="IT00 A000 0000 0000 0000 0000 000" maxlength="34" autocomplete="off"
                                    required>
                                <p id="iban_error" class="hidden mt-1 text-sm text-red-600"></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="submit" form="ibanManagementForm"
                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('common.save') }}
                </button>
                <button type="button" onclick="closeIbanModal()"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                    {{ __('common.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let ibanModalContext = ''; // 'personal' or 'organization'
    const ibanEndpoints = {
        personal: '{{ route('user.domains.personal-data.iban') }}',
        organization: '{{ route('user.organization.iban') }}'
    };

    function openIbanModal(context) {
        ibanModalContext = context;
        document.getElementById('ibanManagementModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('iban_input').value = '';
        document.getElementById('iban_error').classList.add('hidden');
    }

    function closeIbanModal() {
        document.getElementById('ibanManagementModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ibanManagementForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const iban = document.getElementById('iban_input').value.trim();
                const errorEl = document.getElementById('iban_error');

                // Client-side validation
                if (!iban) {
                    errorEl.textContent =
                    '{{ __('validation.required', ['attribute' => 'IBAN']) }}';
                    errorEl.classList.remove('hidden');
                    return;
                }

                // Determine endpoint based on context
                const endpoint = ibanEndpoints[ibanModalContext] || ibanEndpoints.personal;

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            iban: iban
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        closeIbanModal();

                        // Show success message
                        if (typeof window.showNotification === 'function') {
                            window.showNotification('{{ __('common.saved_successfully') }}',
                                'success');
                        } else {
                            alert('{{ __('common.saved_successfully') }}');
                        }

                        // Reload page to show updated data
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        errorEl.textContent = data.message || '{{ __('common.error_occurred') }}';
                        errorEl.classList.remove('hidden');
                    }
                } catch (error) {
                    errorEl.textContent = '{{ __('common.connection_error') }}';
                    errorEl.classList.remove('hidden');
                }
            });
        }
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('ibanManagementModal').classList.contains(
            'hidden')) {
            closeIbanModal();
        }
    });
</script>
