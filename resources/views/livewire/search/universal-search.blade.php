<div x-data="{open:@entangle('open')}" x-init="window.addEventListener('universal-search-open',()=>{open=true; $nextTick(()=>{const i=$el.querySelector('input[type=search]'); if(i){i.focus();}})})" class="relative w-full max-w-xl mx-auto">
    <div class="flex items-center gap-2 p-2 rounded-full bg-gray-800/70 ring-1 ring-gray-700 focus-within:ring-purple-500 transition">
        <span class="material-symbols-outlined text-purple-300">search</span>
        <input wire:model.debounce.300ms="q" type="search" placeholder="Cerca EGIs, Collezioni, Creators..." class="w-full bg-transparent focus:outline-none text-sm text-white placeholder-gray-400" />
        <button type="button" @click="open=!open" class="px-3 py-1 text-xs font-semibold text-white rounded-full bg-purple-600/80 hover:bg-purple-600">Filtri</button>
        <button type="button" wire:click="goToResults" class="px-3 py-1 text-xs font-semibold text-white rounded-full bg-emerald-600/80 hover:bg-emerald-600">Vai</button>
    </div>

    {{-- Suggestions Panel --}}
    @if($q && $suggestions)
    <div class="absolute z-30 w-full mt-2 overflow-hidden text-sm border rounded-xl bg-gray-900/95 backdrop-blur-xl border-gray-700/50">
        <div class="p-3 space-y-3 max-h-96 overflow-y-auto">
            @if(!empty($suggestions['egis']))
                <div>
                    <h4 class="mb-1 text-xs font-semibold tracking-wide text-purple-300 uppercase">EGI</h4>
                    <ul class="space-y-1">
                        @foreach($suggestions['egis'] as $item)
                            <li>
                                <a href="{{ route('egis.show', $item->id) }}" class="block px-2 py-1 rounded hover:bg-gray-700/60">#{{ $item->id }} — {{ \Illuminate\Support\Str::limit($item->title,40) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(!empty($suggestions['collections']))
                <div>
                    <h4 class="mb-1 text-xs font-semibold tracking-wide text-amber-300 uppercase">Collections</h4>
                    <ul class="space-y-1">
                        @foreach($suggestions['collections'] as $item)
                            <li>
                                <a href="{{ route('home.collections.show', $item->id) }}" class="block px-2 py-1 rounded hover:bg-gray-700/60">{{ \Illuminate\Support\Str::limit($item->collection_name,50) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(!empty($suggestions['creators']))
                <div>
                    <h4 class="mb-1 text-xs font-semibold tracking-wide text-cyan-300 uppercase">Creators</h4>
                    <ul class="space-y-1">
                        @foreach($suggestions['creators'] as $item)
                            <li>
                                <a href="{{ route('creator.home', $item->id) }}" class="block px-2 py-1 rounded hover:bg-gray-700/60">{{ $item->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="pt-2 border-t border-gray-700/40">
                <button wire:click="goToResults" class="w-full px-3 py-2 text-xs font-semibold text-white rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">Apri pagina risultati</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters Drawer --}}
    <div x-show="open" x-transition class="absolute z-40 w-full mt-2 overflow-hidden text-sm border rounded-xl shadow-xl bg-gray-900/95 backdrop-blur-xl border-gray-700/50">
        <div class="flex flex-col gap-4 p-4 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">Tipi</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(['egi'=>'EGI','collection'=>'Collections','creator'=>'Creators'] as $tKey=>$tLabel)
                        <button type="button" wire:click="toggleType('{{ $tKey }}')" class="px-3 py-1 text-xs font-medium rounded-full border {{ in_array($tKey,$selectedTypes) ? 'bg-purple-600 text-white border-purple-500' : 'bg-gray-700/50 text-gray-300 border-gray-600 hover:bg-gray-700'}}">{{ $tLabel }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="mt-2 mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">User Types</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(config('search.user_types') as $ut)
                        <button type="button" wire:click="if(in_array('$ut',$selectedUserTypes)){$selectedUserTypes = array_values(array_diff($selectedUserTypes,['$ut']));}else{$selectedUserTypes[]='$ut';}" class="px-3 py-1 text-[10px] font-medium rounded-full border {{ in_array($ut,$selectedUserTypes) ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-gray-700/50 text-gray-300 border-gray-600 hover:bg-gray-700'}}">{{ ucfirst($ut) }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">Traits (faccette)</h4>
                <div class="space-y-3">
                    @foreach($facets as $typeName => $values)
                        <div>
                            <details class="group" {{ $loop->first ? 'open' : '' }}>
                                <summary class="flex items-center justify-between cursor-pointer select-none">
                                    <span class="text-[11px] font-semibold text-purple-300 group-open:text-purple-200">{{ $typeName }}</span>
                                    <span class="text-[10px] text-gray-500">{{ count($values) }}</span>
                                </summary>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    @foreach($values as $val => $count)
                                        <button type="button" wire:click="toggleTrait('{{ $val }}')" class="px-2 py-1 text-[10px] rounded-md border {{ in_array($val,$selectedTraits) ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-gray-700/40 text-gray-300 border-gray-600 hover:bg-gray-700' }}" title="{{ $val }} ({{ $count }})">{{ \Illuminate\Support\Str::limit($val,14) }} <span class="text-[9px] opacity-70">{{ $count }}</span></button>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-700/40">
                <button type="button" @click="open=false" class="px-3 py-1 text-xs font-medium text-gray-300 rounded-lg bg-gray-700/60 hover:bg-gray-700">Chiudi</button>
                <button type="button" wire:click="goToResults" class="px-4 py-1 text-xs font-semibold text-white rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">Risultati</button>
            </div>
        </div>
    </div>
</div>
