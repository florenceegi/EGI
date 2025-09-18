{{-- resources/views/egis/partials/sidebar/collection-collaborators-section.blade.php --}}
<div class="mb-8">
    <h3 class="flex items-center gap-2 mb-4 text-lg font-semibold text-white">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
        </svg>
        {{ __('egi.collection_collaborators') }}
    </h3>

    @php
        // Ottieni tutti i collaboratori della collection con i loro ruoli
        $collaborators = $collection->users()->get();

        // Separa il creator/owner dagli altri collaboratori
        $owner = $collaborators->firstWhere('pivot.is_owner', true);
        $otherCollaborators = $collaborators->where('pivot.is_owner', false);

        // Se non c'è un owner nella tabella pivot, usa il creator della collection
        if (!$owner && $collection->creator) {
            $owner = $collection->creator;
            $owner->pivot = (object) ['is_owner' => true, 'role' => 'creator'];
        }
    @endphp

    <div class="space-y-3">
        {{-- Creator/Owner della collection --}}
        @if($owner)
            <div class="p-4 border rounded-lg border-blue-500/30 bg-blue-900/20">
                <div class="flex items-center gap-3">
                    @if($owner->avatar_url)
                        <img src="{{ $owner->avatar_url }}" alt="{{ $owner->name }}"
                             class="object-cover w-10 h-10 border-2 border-blue-400 rounded-full">
                    @else
                        <div class="flex items-center justify-center w-10 h-10 font-bold text-white bg-blue-500 rounded-full">
                            {{ strtoupper(substr($owner->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium text-white">
                                <a href="{{ route('creator.home', $owner->id) }}"
                                   class="transition-colors duration-200 hover:text-blue-300">
                                    {{ $owner->name }}
                                </a>
                            </h4>
                            @if($owner->pivot->is_owner)
                                <span class="px-2 py-1 text-xs text-yellow-300 border rounded-full bg-yellow-500/20 border-yellow-500/30">
                                    <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-rule="evenodd"></path>
                                    </svg>
                                    @if($owner->pivot->role === 'creator')
                                        {{ __('egi.creator.created_by') }}
                                    @else
                                        {{ __('egi.owner') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                        @if($owner->pivot->role)
                            <p class="text-sm text-gray-400">{{ ucfirst($owner->pivot->role) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Altri collaboratori --}}
        @if($otherCollaborators->isNotEmpty())
            @foreach($otherCollaborators as $collaborator)
                <div class="p-3 border rounded-lg border-gray-600/30 bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        @if($collaborator->avatar_url)
                            <img src="{{ $collaborator->avatar_url }}" alt="{{ $collaborator->name }}"
                                 class="object-cover w-8 h-8 border border-gray-500 rounded-full">
                        @else
                            <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-gray-600 rounded-full">
                                {{ strtoupper(substr($collaborator->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="flex-1">
                            <h5 class="text-sm font-medium text-white">
                                <a href="{{ route('creator.home', $collaborator->id) }}"
                                   class="transition-colors duration-200 hover:text-blue-300">
                                    {{ $collaborator->name }}
                                </a>
                            </h5>
                            @if($collaborator->pivot->role)
                                <p class="text-xs text-gray-400">{{ ucfirst($collaborator->pivot->role) }}</p>
                            @endif
                        </div>

                        {{-- Badge del ruolo --}}
                        @if($collaborator->pivot->role)
                            @if($collaborator->pivot->role === 'admin')
                                <span class="px-2 py-1 text-xs text-red-300 border rounded-full bg-red-500/20 border-red-500/30">
                                    Admin
                                </span>
                            @elseif($collaborator->pivot->role === 'editor')
                                <span class="px-2 py-1 text-xs text-green-300 border rounded-full bg-green-500/20 border-green-500/30">
                                    Editor
                                </span>
                            @elseif($collaborator->pivot->role === 'viewer')
                                <span class="px-2 py-1 text-xs text-blue-300 border rounded-full bg-blue-500/20 border-blue-500/30">
                                    Viewer
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs text-gray-300 border rounded-full bg-gray-500/20 border-gray-500/30">
                                    {{ ucfirst($collaborator->pivot->role) }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            {{-- Messaggio quando non ci sono altri collaboratori --}}
            @if(!$owner || $otherCollaborators->isEmpty())
                <div class="p-3 text-sm text-center text-gray-400">
                    {{ __('egi.no_other_collaborators') }}
                </div>
            @endif
        @endif
    </div>
</div>
