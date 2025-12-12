{{-- resources/views/egis/partials/sidebar/master-tools-section.blade.php --}}
@if ($isCreator && is_null($egi->token_EGI) && is_null($egi->parent_id))
    <div class="rounded-xl border border-indigo-500/30 bg-indigo-900/10 p-4 transition-all hover:bg-indigo-900/20">
        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-indigo-300">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            {{ __('Master System') }}
        </h3>

        <div class="space-y-4">
            {{-- Toggle Master Template --}}
            <div class="flex items-center justify-between">
                <div class="text-xs text-indigo-200">
                    <span class="block font-bold">{{ __('Template Mode') }}</span>
                    <span class="text-indigo-400">{{ __('Use as Master for cloning') }}</span>
                </div>
                
                <button type="button" 
                    id="btn-toggle-master"
                    onclick="toggleMasterTemplate({{ $egi->id }})"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $egi->is_template ? 'bg-indigo-600' : 'bg-gray-700' }}"
                    role="switch" aria-checked="{{ $egi->is_template ? 'true' : 'false' }}">
                    <span class="sr-only">{{ __('Toggle Master Settings') }}</span>
                    <span aria-hidden="true" 
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $egi->is_template ? 'translate-x-5' : 'translate-x-0' }}">
                    </span>
                </button>
            </div>

            {{-- 2. Buyer Cloning Switch (Visible only if Master) --}}
            <div id="buyer-clone-container" class="{{ $egi->is_template ? '' : 'hidden' }} flex items-center justify-between border-t border-indigo-500/30 pt-4">
                 <div class="text-xs text-indigo-200">
                    <span class="block font-bold">{{ __('Buyer Cloning') }}</span>
                    <span class="text-indigo-400">{{ __('Allow buyers to purchase clones') }}</span>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" id="buyer-clone-toggle" class="peer sr-only" {{ $egi->allow_buyer_clone ? 'checked' : '' }} onchange="toggleBuyerCloning({{ $egi->id }})">
                    <div class="peer h-6 w-11 rounded-full bg-gray-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-cyan-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-800"></div>
                </label>
            </div>

            {{-- Clone Action --}}
            <div id="clone-section" class="{{ $egi->is_template ? '' : 'hidden' }} mt-4 border-t border-indigo-500/30 pt-4">
                <button type="button"
                    onclick="cloneEgiFromMaster({{ $egi->id }})"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-all hover:bg-indigo-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 01-2-2V5" />
                    </svg>
                    {{ __('Generate Clone') }}
                </button>
                <p class="mt-2 text-center text-xs text-indigo-400">
                    {{ __('Generate a new Child EGI from this Master.') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        function toggleMasterTemplate(egiId) {
            const btn = document.getElementById('btn-toggle-master');
            const toggleDot = btn.querySelector('span[aria-hidden="true"]');
            const cloneSection = document.getElementById('clone-section');
            
            // Optimistic UI update
            const isCurrentlyChecked = btn.getAttribute('aria-checked') === 'true';
            const newState = !isCurrentlyChecked;
            
            // Disable button during request
            btn.disabled = true;

            fetch(`/egis/${egiId}/master/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI state based on server response
                    const isTemplate = data.is_template;
                    
                    btn.setAttribute('aria-checked', isTemplate);
                    if (data.is_template) {
                        // document.getElementById('master-tools-container').classList.remove('hidden'); // Element doesn't exist
                        document.getElementById('buyer-clone-container').classList.remove('hidden');
                    } else {
                        // document.getElementById('master-tools-container').classList.add('hidden'); // Element doesn't exist
                        document.getElementById('buyer-clone-container').classList.add('hidden');
                        // Also uncheck it safely? No, backend handles logic.
                    }
                    if (isTemplate) {
                        btn.classList.remove('bg-gray-700');
                        btn.classList.add('bg-indigo-600');
                        toggleDot.classList.remove('translate-x-0');
                        toggleDot.classList.add('translate-x-5');
                        cloneSection.classList.remove('hidden');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Master Mode Active',
                            text: 'This EGI is now a Master Template.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        btn.classList.remove('bg-indigo-600');
                        btn.classList.add('bg-gray-700');
                        toggleDot.classList.remove('translate-x-5');
                        toggleDot.classList.add('translate-x-0');
                        cloneSection.classList.add('hidden');
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Master Mode Disabled',
                            text: 'This EGI is no longer a Master Template.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                } else {
                    throw new Error(data.message || 'Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        function toggleBuyerCloning(egiId) {
             const toggle = document.getElementById('buyer-clone-toggle');
             // Optimistic
             // toggle.disabled = true; 

             fetch(`/egis/${egiId}/master/toggle-buyer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.allow_buyer_clone !== undefined) {
                    toggle.checked = data.allow_buyer_clone;
                    Swal.fire({
                        icon: 'success',
                        title: data.allow_buyer_clone ? 'Buyer Cloning Enabled' : 'Buyer Cloning Disabled',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    throw new Error(data.error || 'Error');
                }
            })
            .catch(error => {
                console.error(error);
                toggle.checked = !toggle.checked; // Revert
                Swal.fire('Error', 'Could not update status', 'error');
            });
        }

        function cloneEgiFromMaster(egiId) {
            Swal.fire({
                title: 'Generate Clone?',
                text: "This will create a new Child EGI and mint it on Algorand.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate clone!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Show loading
                    Swal.fire({
                        title: 'Generating Clone...',
                        html: 'Cloning traits, utility, CoA and minting on blockchain.<br>This may take a few seconds.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/egis/${egiId}/master/clone`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Clone Generated!',
                                text: `Serial: ${data.serial_number}`,
                                confirmButtonText: 'View Child EGI'
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            throw new Error(data.message || 'Cloning failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Cloning Failed',
                            text: error.message
                        });
                    });
                }
            });
        }
    </script>
@endif
@if ($egi->is_template)
    <div class="mb-4 rounded-lg bg-indigo-900/50 p-2 text-center border border-indigo-500/50">
        <p class="text-xs font-bold uppercase tracking-widest text-indigo-300">
            {{ __('Official Master Template') }}
        </p>
    </div>
@endif
@if ($egi->parent_id)
    <div class="mb-4 rounded-lg bg-emerald-900/50 p-2 text-center border border-emerald-500/50 flex flex-col items-center justify-center">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">
            {{ __('Authentic Child EGI') }}
        </p>
        <p class="text-xs text-emerald-400/80">
            Serial: #{{ $egi->serial_number }}
        </p>
        <a href="{{ route('egis.show', $egi->parent_id) }}" class="mt-1 text-[10px] text-emerald-200 underline hover:text-white">
            {{ __('View Master Parent') }}
        </a>
    </div>
@endif
