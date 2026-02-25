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

<div id="egi-ecosystem-back" style="display:none; align-items:center;">
    <a id="egi-ecosystem-back-link" href="#"
        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-400 transition-all duration-200 hover:bg-emerald-500/20"
        aria-label="Torna all'ecosistema FlorenceEGI">
        <span aria-hidden="true">←</span>
        <span id="egi-ecosystem-back-name"></span>
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var REFS = {
            hub: {
                name: 'Florence EGI',
                url: 'https://florenceegi.com'
            },
            art: {
                name: 'Florence Art EGI',
                url: 'https://art.florenceegi.com'
            },
            natan: {
                name: 'NATAN-LOC',
                url: 'https://natan-loc.florenceegi.com'
            },
        };

        var ref = new URLSearchParams(window.location.search).get('ref');
        if (ref && REFS[ref]) {
            var wrap = document.getElementById('egi-ecosystem-back');
            var link = document.getElementById('egi-ecosystem-back-link');
            var name = document.getElementById('egi-ecosystem-back-name');
            if (wrap && link && name) {
                link.href = REFS[ref].url;
                name.textContent = REFS[ref].name;
                wrap.style.display = 'flex';
            }
        }
    });
</script>
