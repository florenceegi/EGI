{{-- resources/views/auth/wizard/step1-user-type.blade.php --}}
{{-- 🎯 Step 1: User Type Selection --}}
{{-- ✅ Usa traduzioni da register.php --}}
{{-- ✅ Contrasti corretti --}}

@extends('auth.wizard.layout')

@section('content')
    <div class="mb-6 text-center sm:mb-8">
        <h1 class="font-rinascimento mb-2 text-xl font-bold text-blu-algoritmo sm:mb-3 sm:text-2xl lg:text-3xl">
            {{ __('register.user_type_legend') }}
        </h1>
        <p class="text-sm text-gray-600 sm:text-base">
            {{ __('register.form_subtitle') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register.wizard.step1.store') }}" id="step1-form">
        @csrf

        {{-- User Type Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-3 sm:mb-8 sm:grid-cols-2 sm:gap-4">
            @foreach ($userTypes as $type => $details)
                @php
                    $isDisabled = $details['disabled'] ?? false;
                    $isComingSoon = $details['coming_soon'] ?? false;
                @endphp
                <label class="type-card block p-3 sm:p-4 {{ $isDisabled ? 'opacity-60 cursor-not-allowed' : '' }}" 
                       data-type="{{ $type }}"
                       @if($isDisabled) data-disabled="true" @endif>
                    <input type="radio" name="user_type" value="{{ $type }}" class="sr-only"
                        {{ old('user_type') === $type ? 'checked' : '' }}
                        {{ $isDisabled ? 'disabled' : '' }}>
                    <div class="flex items-start gap-3">
                        {{-- Icon --}}
                        <div
                            class="bg-{{ $details['color'] }} flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full sm:h-12 sm:w-12 {{ $isDisabled ? 'grayscale' : '' }}">
                            <svg class="h-5 w-5 text-white sm:h-6 sm:w-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $details['icon_svg_path'] }}" />
                            </svg>
                        </div>
                        {{-- Content --}}
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-semibold {{ $isDisabled ? 'text-gray-400' : 'text-blu-algoritmo' }} sm:text-base">
                                {{ __('register.user_type_' . $type) }}
                                @if($isComingSoon)
                                    <span class="ml-1.5 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                                        Coming Soon
                                    </span>
                                @endif
                            </h3>
                            <p class="mt-1 line-clamp-2 text-xs {{ $isDisabled ? 'text-gray-400' : 'text-gray-600' }} sm:text-sm">
                                {{ __('register.user_type_' . $type . '_desc') }}
                            </p>
                        </div>
                        {{-- Check indicator --}}
                        <div
                            class="check-indicator flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 transition-all sm:h-6 sm:w-6">
                            <svg class="h-3 w-3 text-white opacity-0 transition-opacity sm:h-4 sm:w-4" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        {{-- Error Message --}}
        @error('user_type')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
                <p class="text-rosso-medici flex items-center text-sm">
                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            </div>
        @enderror

        {{-- Continue Button --}}
        <div class="flex justify-end">
            <button type="submit" id="continue-btn"
                class="btn-primary rounded-xl px-6 py-2.5 text-sm font-semibold sm:px-8 sm:py-3 sm:text-base" disabled>
                {{ __('register.submit_button') == 'Crea Account' ? 'Continua' : 'Continue' }}
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
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.type-card');
            const continueBtn = document.getElementById('continue-btn');

            cards.forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                const checkIndicator = card.querySelector('.check-indicator');
                const checkIcon = checkIndicator.querySelector('svg');
                const isDisabled = card.dataset.disabled === 'true';

                // Check if pre-selected (and not disabled)
                if (radio.checked && !isDisabled) {
                    card.classList.add('selected');
                    checkIndicator.classList.add('bg-verde-rinascita', 'border-verde-rinascita');
                    checkIndicator.classList.remove('border-gray-300');
                    checkIcon.classList.remove('opacity-0');
                    continueBtn.disabled = false;
                }

                card.addEventListener('click', function(e) {
                    // Prevent selection of disabled options
                    if (isDisabled) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Deselect all (except disabled ones)
                    cards.forEach(c => {
                        if (c.dataset.disabled !== 'true') {
                            c.classList.remove('selected');
                            const ci = c.querySelector('.check-indicator');
                            const icon = ci.querySelector('svg');
                            ci.classList.remove('bg-verde-rinascita', 'border-verde-rinascita');
                            ci.classList.add('border-gray-300');
                            icon.classList.add('opacity-0');
                            c.querySelector('input').checked = false;
                        }
                    });

                    // Select this one
                    radio.checked = true;
                    card.classList.add('selected');
                    checkIndicator.classList.add('bg-verde-rinascita', 'border-verde-rinascita');
                    checkIndicator.classList.remove('border-gray-300');
                    checkIcon.classList.remove('opacity-0');
                    continueBtn.disabled = false;
                });
            });
        });
    </script>
@endpush
