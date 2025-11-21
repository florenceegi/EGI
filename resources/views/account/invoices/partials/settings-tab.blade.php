{{-- Invoice Settings Tab --}}
<div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
    
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('invoices.settings.title') }}
        </h3>
    </div>

    <form method="POST" action="{{ route('account.invoices.settings.update') }}" class="space-y-6">
        @csrf

        {{-- Invoicing Mode --}}
        <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
            <label class="mb-3 block text-sm font-medium text-gray-900 dark:text-white">
                {{ __('invoices.settings.invoicing_mode') }}
            </label>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <input type="radio" name="invoicing_mode" value="platform_managed" 
                           id="mode_platform"
                           {{ ($preferences->invoicing_mode ?? 'platform_managed') === 'platform_managed' ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="mode_platform" class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            🏛️ {{ __('invoices.settings.platform_managed') }}
                        </span>
                        <span class="block text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.info.platform_managed_info') }}
                        </span>
                    </label>
                </div>
                
                <div class="flex items-start">
                    <input type="radio" name="invoicing_mode" value="user_managed" 
                           id="mode_user"
                           {{ ($preferences->invoicing_mode ?? 'platform_managed') === 'user_managed' ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="mode_user" class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            💼 {{ __('invoices.settings.user_managed') }}
                        </span>
                        <span class="block text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.info.user_managed_info') }}
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- External System Settings (visible only if user_managed) --}}
        <div id="external-system-settings" 
             class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
             style="display: {{ ($preferences->invoicing_mode ?? 'platform_managed') === 'user_managed' ? 'block' : 'none' }}">
            
            <h4 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                {{ __('invoices.settings.external_system_name') }}
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label for="external_system_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('invoices.settings.external_system_name') }}
                    </label>
                    <input type="text" name="external_system_name" id="external_system_name"
                           value="{{ old('external_system_name', $preferences->external_system_name ?? '') }}"
                           placeholder="Es: TeamSystem, SAP, Zucchetti..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label for="external_system_notes" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('invoices.settings.external_system_notes') }}
                    </label>
                    <textarea name="external_system_notes" id="external_system_notes" rows="3"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                              placeholder="{{ __('common.notes_optional') }}">{{ old('external_system_notes', $preferences->external_system_notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Invoice Frequency --}}
        <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
            <label class="mb-3 block text-sm font-medium text-gray-900 dark:text-white">
                {{ __('invoices.settings.invoice_frequency') }}
            </label>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <input type="radio" name="invoice_frequency" value="monthly" 
                           id="freq_monthly"
                           {{ ($preferences->invoice_frequency ?? 'monthly') === 'monthly' ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="freq_monthly" class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            📅 {{ __('invoices.settings.frequency.monthly') }}
                        </span>
                        <span class="block text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.info.monthly_aggregation_info') }}
                        </span>
                    </label>
                </div>
                
                <div class="flex items-start">
                    <input type="radio" name="invoice_frequency" value="instant" 
                           id="freq_instant"
                           {{ ($preferences->invoice_frequency ?? 'monthly') === 'instant' ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="freq_instant" class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            ⚡ {{ __('invoices.settings.frequency.instant') }}
                        </span>
                        <span class="block text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.info.instant_invoicing_info') }}
                        </span>
                    </label>
                </div>
                
                <div class="flex items-start">
                    <input type="radio" name="invoice_frequency" value="manual" 
                           id="freq_manual"
                           {{ ($preferences->invoice_frequency ?? 'monthly') === 'manual' ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="freq_manual" class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            ✋ {{ __('invoices.settings.frequency.manual') }}
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Notification Settings --}}
        <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
            <h4 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                {{ __('common.notifications') }}
            </h4>
            
            <div class="space-y-3">
                <div class="flex items-center">
                    <input type="hidden" name="auto_generate_monthly" value="0">
                    <input type="checkbox" name="auto_generate_monthly" value="1" 
                           id="auto_generate"
                           {{ ($preferences->auto_generate_monthly ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="auto_generate" class="ml-3 text-sm text-gray-900 dark:text-white">
                        {{ __('invoices.settings.auto_generate_monthly') }}
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="hidden" name="notify_on_invoice_generated" value="0">
                    <input type="checkbox" name="notify_on_invoice_generated" value="1" 
                           id="notify_generated"
                           {{ ($preferences->notify_on_invoice_generated ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="notify_generated" class="ml-3 text-sm text-gray-900 dark:text-white">
                        {{ __('invoices.settings.notify_on_invoice_generated') }}
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="hidden" name="notify_buyer_on_invoice" value="0">
                    <input type="checkbox" name="notify_buyer_on_invoice" value="1" 
                           id="notify_buyer"
                           {{ ($preferences->notify_buyer_on_invoice ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="notify_buyer" class="ml-3 text-sm text-gray-900 dark:text-white">
                        {{ __('invoices.settings.notify_buyer_on_invoice') }}
                    </label>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end">
            <button type="submit" 
                    class="rounded-lg bg-purple-600 px-6 py-3 font-medium text-white hover:bg-purple-700">
                {{ __('common.save_settings') }}
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    // Show/hide external system settings based on invoicing mode
    document.querySelectorAll('input[name="invoicing_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const externalSettings = document.getElementById('external-system-settings');
            if (this.value === 'user_managed') {
                externalSettings.style.display = 'block';
            } else {
                externalSettings.style.display = 'none';
            }
        });
    });
</script>
@endpush

