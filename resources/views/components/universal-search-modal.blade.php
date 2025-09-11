{{-- Blade Component: Universal Search Modal (vanilla JS) --}}
<div id="universal-search-modal" class="fixed inset-0 z-[1600] hidden" data-us-modal aria-hidden="true" role="dialog" aria-modal="true" aria-label="Universal search dialog">
    <div data-us-overlay class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-150"></div>
    <div class="relative flex flex-col max-h-full w-full max-w-5xl mx-auto mt-16 px-4 md:mt-24">
        <div class="relative rounded-2xl bg-gray-900/95 border border-gray-700/60 shadow-2xl overflow-hidden backdrop-blur-xl ring-1 ring-black/40">
            {{-- Top Bar --}}
            <div class="flex items-center gap-2 p-3 border-b border-gray-700/50 bg-gradient-to-r from-gray-900/80 to-gray-800/60">
                <span class="material-symbols-outlined text-purple-300 text-xl">search</span>
                <input id="universal-search-input" type="search" placeholder="Cerca EGIs, Collezioni, Creators..." autocomplete="off" class="flex-1 bg-transparent focus:outline-none text-sm md:text-base text-white placeholder-gray-500" />
                <button type="button" data-us-filters class="px-3 py-1.5 text-xs font-semibold text-white rounded-full bg-purple-600/80 hover:bg-purple-600 transition">Filtri</button>
                <button type="button" data-us-go class="px-3 py-1.5 text-xs font-semibold text-white rounded-full bg-emerald-600/80 hover:bg-emerald-600 transition">Vai</button>
                <button type="button" data-us-close class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition" aria-label="Chiudi ricerca">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            {{-- Suggestions --}}
            <div id="universal-search-suggestions" class="hidden max-h-80 overflow-y-auto"></div>
            {{-- Facets Drawer --}}
            <div id="universal-search-facets" class="hidden border-t border-gray-800"></div>
            {{-- Footer --}}
            <div class="p-2 text-[10px] tracking-wide text-gray-500 flex justify-between items-center border-t border-gray-800 bg-gray-900/60">
                <div>ESC per chiudere • Invio per risultati • ⌘/Ctrl+K apre la ricerca</div>
                <div class="flex gap-1">
                    <kbd class="px-1.5 py-0.5 rounded bg-gray-800 text-gray-300">esc</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-gray-800 text-gray-300">↩</kbd>
                </div>
            </div>
        </div>
    </div>
</div>
