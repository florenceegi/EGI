<?php

namespace App\Http\Controllers\Info;

use App\Http\Controllers\Controller;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
class FlorenceEgiController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Versione completa della pagina informativa
     */
    public function index() {
        $translations = $this->getTranslations();
        return view('info.florenceegi', compact('translations'));
    }

    /**
     * Versione Light della pagina informativa
     * - Sezioni essenziali sempre visibili
     * - Sezioni dettagliate collassabili
     */
    public function light() {
        $translations = $this->getTranslations();
        return view('info.florenceegi-light', compact('translations'));
    }

    /**
     * Versione V4 della pagina informativa (Preview)
     * - Nuovi contenuti riscritti per target "casalinga 55 anni"
     * - Focus su: attività gratuita, blockchain trasparente, AI
     */
    public function v4() {
        $translations = $this->getTranslations();
        return view('info.florenceegi-v4', compact('translations'));
    }

    /**
     * Versione V4 Wheel della pagina informativa
     * - Menu circolare innovativo
     * - Navigazione interattiva
     */
    public function v4wheel() {
        $translations = $this->getTranslations();
        return view('info.florenceegi-v4-wheel', compact('translations'));
    }

    /**
     * Recupera tutte le traduzioni per la pagina
     */
    private function getTranslations(): array {
        return [
            'meta' => __('info_florence_egi.meta'),
            'nav' => __('info_florence_egi.nav'),
            'hero' => __('info_florence_egi.hero'),
            'intro' => __('info_florence_egi.intro'),
            'problems' => __('info_florence_egi.problems'),
            'problems_details' => __('info_florence_egi.problems_details'),
            'examples' => __('info_florence_egi.examples'),
            'modal' => __('info_florence_egi.modal'),
            'how' => __('info_florence_egi.how'),
            'ammk' => __('info_florence_egi.ammk'),
            'technology' => __('info_florence_egi.technology'),
            'payments' => __('info_florence_egi.payments'),
            'compliance' => __('info_florence_egi.compliance'),
            'ecosystem' => __('info_florence_egi.ecosystem'),
            'governance' => __('info_florence_egi.governance'),
            'pricing' => __('info_florence_egi.pricing'),
            'cta_final' => __('info_florence_egi.cta_final'),
            'footer' => __('info_florence_egi.footer'),
        ];
    }
}
