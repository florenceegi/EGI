{{--
    CoA Sidebar Section for EGI Show
    🎯 Purpose: Integrate CoA functionality into EGI sidebar
    🛡️ Privacy: GDPR-compliant CoA display
    📍 Location: Include in egis/show.blade.php sidebar sections

    Variables available from parent:
    - $egi: The EGI model instance
    - $isCreator: Boolean indicating if current user is the creator
    - $canUpdateEgi: Boolean for update permissions
    - $canDeleteEgi: Boolean for delete permissions
--}}

{{-- CoA (Certificate of Authenticity) Section --}}
@php
    $existingCoa = $egi->coa()->first();
    $hasActiveCoa = $existingCoa && $existingCoa->status === 'valid';
@endphp

@if($hasActiveCoa || $isCreator)
    {{-- Box completo: se CoA presente O se creator (per traits management) --}}
    <div class="bg-gradient-to-br from-amber-900/20 to-yellow-900/20 rounded-xl border border-amber-500/30 backdrop-blur-sm p-4 space-y-3">
        @include('components.coa.sidebar-section', [
            'egi' => $egi,
            'isCreator' => $isCreator
        ])
    </div>
@else
    {{-- Solo badge compatto se CoA assente e non creator --}}
    @include('components.coa.sidebar-section', [
        'egi' => $egi,
        'isCreator' => $isCreator
    ])
@endif

{{-- Include CoA Annex Modal if user can manage --}}
@if($isCreator && $egi->coa && $egi->coa->status === 'active')
    @include('components.coa.annex-modal', [
        'coa' => $egi->coa
    ])
@endif

{{-- Include Vocabulary Modal for traits management --}}
@if($isCreator)
    @include('components.coa.vocabulary-modal')
@endif
