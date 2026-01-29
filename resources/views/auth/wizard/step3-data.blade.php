{{-- resources/views/auth/wizard/step3-data.blade.php --}}
{{-- 🎯 Step 3: User Data Input --}}
{{-- ✅ Usa traduzioni da register.php --}}
{{-- ✅ Contrasti corretti --}}
{{-- ✅ Campo org_name per company (verificato su DB) --}}

@extends('auth.wizard.layout')

@section('content')
    <div class="mb-6 text-center sm:mb-8">
        <h1 class="font-rinascimento mb-2 text-xl font-bold text-blu-algoritmo sm:mb-3 sm:text-2xl lg:text-3xl">
            {{ __('register.form_title') }}
        </h1>
        <p class="text-sm text-gray-600 sm:text-base">
            Inserisci i tuoi dati per creare l'account
        </p>
    </div>

    <form method="POST" action="{{ route('register.wizard.step3.store') }}" class="space-y-4 sm:space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-800">
                {{ __('register.label_name') }} <span class="text-rosso-medici">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $wizardData['data']['name'] ?? '') }}"
                class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 @error('name') border-rosso-medici @enderror w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:px-4 sm:py-3 sm:text-base"
                placeholder="Mario Rossi" required autofocus>
            <p class="mt-1 text-xs text-gray-500">{{ __('register.name_help') }}</p>
            @error('name')
                <p class="text-rosso-medici mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nickname --}}
        <div>
            <label for="nick_name" class="mb-1.5 block text-sm font-semibold text-gray-800">
                {{ __('register.label_nick_name') }}
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 transform text-gray-400 sm:left-4">@</span>
                <input type="text" id="nick_name" name="nick_name"
                    value="{{ old('nick_name', $wizardData['data']['nick_name'] ?? '') }}"
                    class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 @error('nick_name') border-rosso-medici @enderror w-full rounded-xl border-2 border-gray-300 py-2.5 pl-8 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:py-3 sm:pl-10 sm:pr-4 sm:text-base"
                    placeholder="mariorossi">
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ __('register.nick_name_help') }}</p>
            @error('nick_name')
                <p class="text-rosso-medici mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-800">
                {{ __('register.label_email') }} <span class="text-rosso-medici">*</span>
            </label>
            <input type="email" id="email" name="email"
                value="{{ old('email', $wizardData['data']['email'] ?? '') }}"
                class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 @error('email') border-rosso-medici @enderror w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:px-4 sm:py-3 sm:text-base"
                placeholder="mario@esempio.it" required>
            <p class="mt-1 text-xs text-gray-500">{{ __('register.email_help') }}</p>
            @error('email')
                <p class="text-rosso-medici mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-800">
                {{ __('register.label_password') }} <span class="text-rosso-medici">*</span>
            </label>
            <div class="relative">
                <input type="password" id="password" name="password"
                    class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 @error('password') border-rosso-medici @enderror w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:px-4 sm:py-3 sm:pr-12 sm:text-base"
                    placeholder="••••••••" required>
                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600 sm:right-4">
                    <svg class="eye-closed h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-open hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-rosso-medici mt-1 text-sm">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                {{ __('register.password_help') }}
            </p>
        </div>

        {{-- Password Confirmation --}}
        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-800">
                {{ __('register.label_password_confirmation') }} <span class="text-rosso-medici">*</span>
            </label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:px-4 sm:py-3 sm:pr-12 sm:text-base"
                    placeholder="••••••••" required>
                <button type="button" onclick="togglePassword('password_confirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600 sm:right-4">
                    <svg class="eye-closed h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-open hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ __('register.password_confirmation_help') }}</p>
        </div>

        {{-- Organization Name (shown only if user_type = company) --}}
        @if ($userType === 'company')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <label for="org_name" class="mb-1.5 block text-sm font-semibold text-gray-800">
                    <svg class="mr-1 inline-block h-4 w-4 text-blu-algoritmo" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Nome Organizzazione
                </label>
                <input type="text" id="org_name" name="org_name"
                    value="{{ old('org_name', $wizardData['data']['org_name'] ?? '') }}"
                    class="focus:border-oro-fiorentino focus:ring-oro-fiorentino/20 w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 sm:px-4 sm:py-3 sm:text-base"
                    placeholder="Acme S.r.l.">
                <p class="mt-1 text-xs text-gray-500">Opzionale. Potrai completare il profilo aziendale dopo la
                    registrazione.</p>
            </div>
        @endif

        {{-- Navigation Buttons --}}
        <div class="flex flex-col justify-between gap-3 pt-2 sm:flex-row sm:gap-0 sm:pt-4">
            <a href="{{ route('register.wizard.step2') }}"
                class="btn-secondary inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold sm:px-6 sm:py-3 sm:text-base">
                <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Indietro
            </a>
            <button type="submit"
                class="btn-primary rounded-xl px-6 py-2.5 text-sm font-semibold sm:px-8 sm:py-3 sm:text-base">
                Continua
                <svg class="ml-2 inline-block h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeClosed = button.querySelector('.eye-closed');
            const eyeOpen = button.querySelector('.eye-open');

            if (input.type === 'password') {
                input.type = 'text';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeClosed.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            }
        }
    </script>
@endpush
