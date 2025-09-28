{{--
    Component: Info Pages Footer
    Versione: 1.0 FlorenceEGI Brand Guidelines
    Data: 28 Settembre 2025
    Descrizione: Footer standardizzato per tutte le pagine info
    Caratteristiche: Brand compliant, responsive, link standardizzati
--}}

<!-- Footer Essenziale -->
<footer class="py-8 text-white bg-blu-algoritmo">
    <div class="px-4 golden-ratio-container sm:px-6">
        <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
            <div class="flex items-center space-x-3">
                <i class="text-xl fas fa-infinity text-oro-fiorentino"></i>
                <div class="text-center md:text-left">
                    <div class="font-bold renaissance-title">FlorenceEGI</div>
                    <div class="text-sm text-blue-200 font-body">Il Rinascimento Digitale</div>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-4 text-sm font-body md:gap-6">
                <a href="{{ route('home') }}" class="transition-colors hover:text-oro-fiorentino">Home</a>
                <a href="{{ route('info.florence-egi') }}"
                    class="transition-colors hover:text-oro-fiorentino">FlorenceEGI</a>
                <a href="{{ route('archetypes.patron') }}"
                    class="transition-colors hover:text-oro-fiorentino">Archetipi</a>
                <a href="{{ route('info.epp') }}" class="transition-colors hover:text-oro-fiorentino">EPP</a>
                <a href="{{ route('gdpr.privacy-policy') }}" class="transition-colors hover:text-oro-fiorentino">Privacy</a>
                <a href="{{ route('legal.terms') }}" class="transition-colors hover:text-oro-fiorentino">Termini</a>
                <a href="mailto:info@florenceegi.com" class="transition-colors hover:text-oro-fiorentino">Contatti</a>
            </div>
        </div>
    </div>
</footer>
