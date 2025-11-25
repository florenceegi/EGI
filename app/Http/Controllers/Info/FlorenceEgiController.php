<?php

namespace App\Http\Controllers\Info;

use App\Http\Controllers\Controller;

class FlorenceEgiController extends Controller {
    
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
