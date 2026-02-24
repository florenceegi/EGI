{{--
    EcosystemBackButton
    Compare SOLO quando l'utente arriva da un altro sottodominio dell'ecosistema
    FlorenceEGI tramite ?ref=<key> nell'URL.

    Siti supportati:
      ?ref=hub   → florenceegi.com
      ?ref=art   → art.florenceegi.com (questo sito)
      ?ref=natan → natan-loc.florenceegi.com

    Implementazione vanilla JS — zero dipendenze, P0-0 compliant.
--}}

<div id="egi-ecosystem-back" class="hidden items-center">
    <a
        id="egi-ecosystem-back-link"
        href="#"
        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-emerald-500/40 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-all duration-200"
        aria-label="Torna all'ecosistema FlorenceEGI"
    >
        <span aria-hidden="true">←</span>
        <span id="egi-ecosystem-back-name"></span>
    </a>
</div>

<script>
(function () {
    var REFS = {
        hub:   { name: 'Florence EGI',      url: 'https://florenceegi.com' },
        art:   { name: 'Florence Art EGI',  url: 'https://art.florenceegi.com' },
        natan: { name: 'NATAN-LOC',         url: 'https://natan-loc.florenceegi.com' },
    };

    var ref = new URLSearchParams(window.location.search).get('ref');
    if (ref && REFS[ref]) {
        var wrap = document.getElementById('egi-ecosystem-back');
        var link = document.getElementById('egi-ecosystem-back-link');
        var name = document.getElementById('egi-ecosystem-back-name');
        if (wrap && link && name) {
            link.href = REFS[ref].url;
            name.textContent = REFS[ref].name;
            wrap.classList.remove('hidden');
            wrap.classList.add('flex');
        }
    }
})();
</script>
