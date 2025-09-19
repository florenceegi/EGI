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
<div class="bg-gradient-to-br from-amber-900/20 to-yellow-900/20 rounded-xl border border-amber-500/30 backdrop-blur-sm p-4 space-y-3">
    @include('components.coa.sidebar-section', [
        'egi' => $egi,
        'isCreator' => $isCreator
    ])
</div>

{{-- CoA Vocabulary Traits Management Section --}}
@if($isCreator)
<div class="bg-gradient-to-br from-purple-900/20 to-blue-900/20 rounded-xl border border-purple-500/30 backdrop-blur-sm p-4 space-y-3">
    @include('components.coa.traits-management', [
        'egi' => $egi
    ])
</div>
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
