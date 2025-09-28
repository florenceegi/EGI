{{--
    Component: Info Pages Footer
    Versione: 1.0 FlorenceEGI Brand Guidelines
    Data: 28 Settembre 2025
    Descrizione: Footer standardizzato per tutte le pagine info
    Caratteristiche: Brand compliant, responsive, link standardizzati
--}}

<!-- Footer Essenziale -->
<footer class="bg-blu-algoritmo py-8 text-white">
    <div class="golden-ratio-container px-4 sm:px-6">
        <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
            <div class="flex items-center space-x-3">
                <i class="fas fa-infinity text-oro-fiorentino text-xl"></i>
                <div class="text-center md:text-left">
                    <div class="renaissance-title font-bold">FlorenceEGI</div>
                    <div class="font-body text-sm text-blue-200">Il Rinascimento Digitale</div>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-4 font-body text-sm md:gap-6">
                <a href="{{ route('home') }}" class="hover:text-oro-fiorentino transition-colors">Home</a>
                <a href="{{ route('info.florence-egi') }}"
                    class="hover:text-oro-fiorentino transition-colors">FlorenceEGI</a>
                <a href="{{ route('archetypes.patron') }}"
                    class="hover:text-oro-fiorentino transition-colors">Archetipi</a>
                <a href="{{ route('info.epp') }}" class="hover:text-oro-fiorentino transition-colors">EPP</a>
                <a href="/privacy-policy" class="hover:text-oro-fiorentino transition-colors">Privacy</a>
                <a href="/terms" class="hover:text-oro-fiorentino transition-colors">Termini</a>
                <a href="mailto:info@florenceegi.com" class="hover:text-oro-fiorentino transition-colors">Contatti</a>
            </div>
        </div>
    </div>
</footer>
