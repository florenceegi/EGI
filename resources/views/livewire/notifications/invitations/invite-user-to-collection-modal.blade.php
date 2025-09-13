<div>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-gray-800 shadow-lg rounded-2xl">
            <h3 class="mb-4 text-2xl font-bold text-white">{{ __('Invite Collection Member') }}</h3>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }}</label>
                <input type="email" id="email" wire:model="email"
                    class="w-full text-white bg-gray-700 input input-bordered input-primary"
                    placeholder="{{ __('Enter user email') }}">
                @error('email')
                    <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-300">{{ __('Role') }}</label>
                <select id="role" wire:model="role"
                    class="w-full text-white bg-gray-700 select select-bordered select-primary">
                    <option value="" disabled>{{ __('Select a role') }}</option>
                    @foreach ($rolesForInvite as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Errori generali -->
            @error('invitation')
                <div class="p-3 mb-4 bg-red-900 border border-red-600 rounded-md">
                    <span class="text-sm text-red-300">{{ $message }}</span>
                </div>
            @enderror

            <!-- Azioni -->
            <div class="flex justify-end mt-6 space-x-4">
                <button wire:click="closeModal" class="btn btn-secondary">{{ __('Cancel') }}</button>
                <button wire:click="invite" class="btn btn-primary">{{ __('Spedisi invito') }}</button>
            </div>
        </div>
    </div>

</div>
