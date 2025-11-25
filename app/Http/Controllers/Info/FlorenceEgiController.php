<?php

namespace App\Http\Controllers\Info;

use App\Http\Controllers\Controller;

class FlorenceEgiController extends Controller {
    public function index() {
        $translations = [
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
        ];

        return view('info.florenceegi', compact('translations'));
    }
}
