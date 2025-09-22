<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VocabularyTermSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Svuota tabella se esistente
        DB::table('vocabulary_terms')->truncate();

        $terms = [];

        // ========================================
        // TECHNIQUES - TUTTE LE TECNICHE COMPLETE DAI FILE MD
        // ========================================

        // TECHNIQUE - Pittura
        $terms = array_merge($terms, [
            ['slug' => 'painting-oil', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 10],
            ['slug' => 'painting-acrylic', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 20],
            ['slug' => 'painting-watercolor', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 30],
            ['slug' => 'painting-tempera', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 40],
            ['slug' => 'painting-gouache', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 50],
            ['slug' => 'painting-encaustic', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 60],
            ['slug' => 'painting-fresco', 'category' => 'technique', 'ui_group' => 'Pittura', 'sort_order' => 70],
        ]);

        // TECHNIQUE - Disegno
        $terms = array_merge($terms, [
            ['slug' => 'drawing-graphite', 'category' => 'technique', 'ui_group' => 'Disegno', 'sort_order' => 110],
            ['slug' => 'drawing-charcoal', 'category' => 'technique', 'ui_group' => 'Disegno', 'sort_order' => 120],
            ['slug' => 'drawing-ink', 'category' => 'technique', 'ui_group' => 'Disegno', 'sort_order' => 130],
            ['slug' => 'drawing-colored-pencil', 'category' => 'technique', 'ui_group' => 'Disegno', 'sort_order' => 140],
            ['slug' => 'pastel', 'category' => 'technique', 'ui_group' => 'Disegno', 'sort_order' => 150],
        ]);

        // TECHNIQUE - Contemporaneo
        $terms = array_merge($terms, [
            ['slug' => 'spray-painting', 'category' => 'technique', 'ui_group' => 'Contemporaneo', 'sort_order' => 210],
            ['slug' => 'mixed-media', 'category' => 'technique', 'ui_group' => 'Contemporaneo', 'sort_order' => 220],
            ['slug' => 'collage', 'category' => 'technique', 'ui_group' => 'Contemporaneo', 'sort_order' => 230],
            ['slug' => 'assemblage', 'category' => 'technique', 'ui_group' => 'Contemporaneo', 'sort_order' => 240],
        ]);

        // TECHNIQUE - Incisione COMPLETA
        $terms = array_merge($terms, [
            ['slug' => 'printmaking-etching', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 310],
            ['slug' => 'printmaking-engraving', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 320],
            ['slug' => 'printmaking-drypoint', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 330],
            ['slug' => 'printmaking-mezzotint', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 340],
            ['slug' => 'printmaking-aquatint', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 350],
            ['slug' => 'printmaking-lithography', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 360],
            ['slug' => 'printmaking-screenprint', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 370],
            ['slug' => 'printmaking-woodcut', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 380],
            ['slug' => 'printmaking-linocut', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 390],
            ['slug' => 'printmaking-monotype', 'category' => 'technique', 'ui_group' => 'Incisione', 'sort_order' => 400],
        ]);

        // TECHNIQUE - Scultura
        $terms = array_merge($terms, [
            ['slug' => 'sculpture-carving', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 410],
            ['slug' => 'sculpture-modeling', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 420],
            ['slug' => 'sculpture-casting', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 430],
            ['slug' => 'sculpture-lost-wax', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 440],
            ['slug' => 'sculpture-welding', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 450],
            ['slug' => 'sculpture-3dprint', 'category' => 'technique', 'ui_group' => 'Scultura', 'sort_order' => 460],
        ]);

        // TECHNIQUE - Fotografia
        $terms = array_merge($terms, [
            ['slug' => 'photography-gelatin-silver', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 510],
            ['slug' => 'photography-albumen', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 520],
            ['slug' => 'photography-cyanotype', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 530],
            ['slug' => 'photography-platinum', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 540],
            ['slug' => 'photography-chromogenic', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 550],
            ['slug' => 'photography-inkjet', 'category' => 'technique', 'ui_group' => 'Fotografia', 'sort_order' => 560],
        ]);

        // TECHNIQUE - Digitale
        $terms = array_merge($terms, [
            ['slug' => 'digital-print', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 610],
            ['slug' => 'digital-generative', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 620],
            ['slug' => 'digital-3d-render', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 630],
            ['slug' => 'video-single-channel', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 640],
            ['slug' => 'installation-interactive', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 650],
            ['slug' => 'ar-vr', 'category' => 'technique', 'ui_group' => 'Digitale', 'sort_order' => 660],
        ]);

        // TECHNIQUE - Tessile
        $terms = array_merge($terms, [
            ['slug' => 'textile-weaving', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 710],
            ['slug' => 'textile-embroidery', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 720],
            ['slug' => 'textile-tapestry', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 730],
            ['slug' => 'textile-felting', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 740],
            ['slug' => 'textile-batik', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 750],
            ['slug' => 'textile-shibori', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 760],
            ['slug' => 'textile-knitting', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 770],
            ['slug' => 'textile-crochet', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 780],
            ['slug' => 'textile-needle-felting', 'category' => 'technique', 'ui_group' => 'Tessile', 'sort_order' => 790],
        ]);

        // TECHNIQUE - Ceramica
        $terms = array_merge($terms, [
            ['slug' => 'ceramic-wheel-thrown', 'category' => 'technique', 'ui_group' => 'Ceramica', 'sort_order' => 810],
            ['slug' => 'ceramic-handbuilt', 'category' => 'technique', 'ui_group' => 'Ceramica', 'sort_order' => 820],
            ['slug' => 'ceramic-slip-cast', 'category' => 'technique', 'ui_group' => 'Ceramica', 'sort_order' => 830],
            ['slug' => 'ceramic-raku', 'category' => 'technique', 'ui_group' => 'Ceramica', 'sort_order' => 840],
        ]);

        // TECHNIQUE - Vetro
        $terms = array_merge($terms, [
            ['slug' => 'glass-blown', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 910],
            ['slug' => 'glass-kilnformed', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 920],
            ['slug' => 'glass-lampworking', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 930],
            ['slug' => 'glass-stained', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 940],
            ['slug' => 'glass-pate-de-verre', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 950],
            ['slug' => 'glass-coldworking', 'category' => 'technique', 'ui_group' => 'Vetro', 'sort_order' => 960],
        ]);

        // TECHNIQUE - Legno
        $terms = array_merge($terms, [
            ['slug' => 'wood-marquetry', 'category' => 'technique', 'ui_group' => 'Legno', 'sort_order' => 1010],
            ['slug' => 'wood-turning', 'category' => 'technique', 'ui_group' => 'Legno', 'sort_order' => 1020],
            ['slug' => 'wood-pyrography', 'category' => 'technique', 'ui_group' => 'Legno', 'sort_order' => 1030],
            ['slug' => 'wood-lacquer', 'category' => 'technique', 'ui_group' => 'Legno', 'sort_order' => 1040],
        ]);

        // TECHNIQUE - Cuoio
        $terms = array_merge($terms, [
            ['slug' => 'leather-tooling', 'category' => 'technique', 'ui_group' => 'Cuoio', 'sort_order' => 1110],
            ['slug' => 'leather-dyeing', 'category' => 'technique', 'ui_group' => 'Cuoio', 'sort_order' => 1120],
        ]);

        // TECHNIQUE - Gioielleria COMPLETA
        $terms = array_merge($terms, [
            ['slug' => 'jewelry-fabrication', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1210],
            ['slug' => 'jewelry-forging', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1220],
            ['slug' => 'jewelry-repousse', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1230],
            ['slug' => 'jewelry-chasing', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1240],
            ['slug' => 'jewelry-filigree', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1250],
            ['slug' => 'jewelry-granulation', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1260],
            ['slug' => 'jewelry-stone-setting-bezel', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1270],
            ['slug' => 'jewelry-stone-setting-prong', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1280],
            ['slug' => 'jewelry-stone-setting-pave', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1290],
            ['slug' => 'jewelry-stone-setting-channel', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1300],
            ['slug' => 'jewelry-enameling-cloisonne', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1310],
            ['slug' => 'jewelry-enameling-champleve', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1320],
            ['slug' => 'jewelry-enameling-plique-a-jour', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1330],
            ['slug' => 'jewelry-mokume-gane', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1340],
            ['slug' => 'jewelry-niello', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1350],
            ['slug' => 'jewelry-damascening', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1360],
            ['slug' => 'jewelry-hand-engraving', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1370],
            ['slug' => 'jewelry-laser-welding', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1380],
            ['slug' => 'jewelry-electroforming', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1390],
            ['slug' => 'jewelry-wax-carving', 'category' => 'technique', 'ui_group' => 'Gioielleria', 'sort_order' => 1400],
        ]);

        // TECHNIQUE - Mosaico
        $terms = array_merge($terms, [
            ['slug' => 'mosaic', 'category' => 'technique', 'ui_group' => 'Mosaico', 'sort_order' => 1510],
        ]);

        // =======================================
        // MATERIALS - TUTTI I MATERIALI COMPLETI DAI FILE MD
        // =======================================

        // MATERIALS - Colori e Pittura
        $terms = array_merge($terms, [
            ['slug' => 'material-paint-oil', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2010],
            ['slug' => 'material-paint-acrylic', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2020],
            ['slug' => 'material-paint-watercolor', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2030],
            ['slug' => 'material-paint-tempera', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2040],
            ['slug' => 'material-paint-gouache', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2050],
            ['slug' => 'material-wax-encaustic', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2060],
            ['slug' => 'material-pigment', 'category' => 'materials', 'ui_group' => 'Colori', 'sort_order' => 2070],
        ]);

        // MATERIALS - Strumenti da Disegno
        $terms = array_merge($terms, [
            ['slug' => 'material-graphite', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2110],
            ['slug' => 'material-charcoal', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2120],
            ['slug' => 'material-pastel-soft', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2130],
            ['slug' => 'material-pastel-oil', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2140],
            ['slug' => 'material-ink', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2150],
            ['slug' => 'material-spray-paint', 'category' => 'materials', 'ui_group' => 'Strumenti', 'sort_order' => 2160],
        ]);

        // MATERIALS - Finiture e Preparazioni
        $terms = array_merge($terms, [
            ['slug' => 'material-gold-leaf', 'category' => 'materials', 'ui_group' => 'Finiture', 'sort_order' => 2210],
            ['slug' => 'material-silver-leaf', 'category' => 'materials', 'ui_group' => 'Finiture', 'sort_order' => 2220],
            ['slug' => 'material-gesso', 'category' => 'materials', 'ui_group' => 'Finiture', 'sort_order' => 2230],
        ]);

        // MATERIALS - Resine e Compositi
        $terms = array_merge($terms, [
            ['slug' => 'material-resin-epoxy', 'category' => 'materials', 'ui_group' => 'Resine', 'sort_order' => 2310],
            ['slug' => 'material-fiberglass', 'category' => 'materials', 'ui_group' => 'Resine', 'sort_order' => 2320],
        ]);

        // MATERIALS - Metalli Generici
        $terms = array_merge($terms, [
            ['slug' => 'material-metal-bronze', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2410],
            ['slug' => 'material-metal-brass', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2420],
            ['slug' => 'material-metal-aluminum', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2430],
            ['slug' => 'material-metal-steel', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2440],
            ['slug' => 'material-metal-iron', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2450],
            ['slug' => 'material-metal-copper', 'category' => 'materials', 'ui_group' => 'Metalli', 'sort_order' => 2460],
        ]);

        // MATERIALS - Metalli Preziosi
        $terms = array_merge($terms, [
            ['slug' => 'material-metal-gold', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2510],
            ['slug' => 'material-metal-silver-sterling', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2520],
            ['slug' => 'material-metal-silver-fine', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2530],
            ['slug' => 'material-metal-platinum', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2540],
            ['slug' => 'material-metal-palladium', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2550],
            ['slug' => 'material-metal-titanium', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2560],
            ['slug' => 'material-metal-tantalum', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2570],
            ['slug' => 'material-metal-white-gold', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2580],
            ['slug' => 'material-metal-rose-gold', 'category' => 'materials', 'ui_group' => 'Metalli Preziosi', 'sort_order' => 2590],
        ]);

        // MATERIALS - Materiali Naturali
        $terms = array_merge($terms, [
            ['slug' => 'material-wood', 'category' => 'materials', 'ui_group' => 'Naturali', 'sort_order' => 2610],
            ['slug' => 'material-stone-marble', 'category' => 'materials', 'ui_group' => 'Naturali', 'sort_order' => 2620],
            ['slug' => 'material-stone-limestone', 'category' => 'materials', 'ui_group' => 'Naturali', 'sort_order' => 2630],
            ['slug' => 'material-stone-granite', 'category' => 'materials', 'ui_group' => 'Naturali', 'sort_order' => 2640],
        ]);

        // MATERIALS - Ceramica
        $terms = array_merge($terms, [
            ['slug' => 'material-clay', 'category' => 'materials', 'ui_group' => 'Ceramica', 'sort_order' => 2710],
            ['slug' => 'material-porcelain', 'category' => 'materials', 'ui_group' => 'Ceramica', 'sort_order' => 2720],
            ['slug' => 'material-stoneware', 'category' => 'materials', 'ui_group' => 'Ceramica', 'sort_order' => 2730],
            ['slug' => 'material-earthenware', 'category' => 'materials', 'ui_group' => 'Ceramica', 'sort_order' => 2740],
        ]);

        // MATERIALS - Vetro
        $terms = array_merge($terms, [
            ['slug' => 'material-glass', 'category' => 'materials', 'ui_group' => 'Vetro', 'sort_order' => 2810],
        ]);

        // MATERIALS - Carte Speciali
        $terms = array_merge($terms, [
            ['slug' => 'material-paper-rag', 'category' => 'materials', 'ui_group' => 'Carte', 'sort_order' => 2910],
            ['slug' => 'material-paper-baryta', 'category' => 'materials', 'ui_group' => 'Carte', 'sort_order' => 2920],
            ['slug' => 'material-paper-rc', 'category' => 'materials', 'ui_group' => 'Carte', 'sort_order' => 2930],
            ['slug' => 'material-paper-handmade', 'category' => 'materials', 'ui_group' => 'Carte', 'sort_order' => 2940],
        ]);

        // MATERIALS - Supporti come Materiali (quando la tela è IL materiale usato)
        $terms = array_merge($terms, [
            ['slug' => 'material-canvas-cotton', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3010],
            ['slug' => 'material-canvas-linen', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3020],
            ['slug' => 'material-panel-wood', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3030],
            ['slug' => 'material-panel-mdf', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3040],
            ['slug' => 'material-panel-dibond', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3050],
            ['slug' => 'material-acrylic-sheet', 'category' => 'materials', 'ui_group' => 'Supporti', 'sort_order' => 3060],
        ]);

        // MATERIALS - Tessuti
        $terms = array_merge($terms, [
            ['slug' => 'material-fabric-silk', 'category' => 'materials', 'ui_group' => 'Tessuti', 'sort_order' => 3110],
            ['slug' => 'material-fabric-wool', 'category' => 'materials', 'ui_group' => 'Tessuti', 'sort_order' => 3120],
            ['slug' => 'material-fabric-cotton', 'category' => 'materials', 'ui_group' => 'Tessuti', 'sort_order' => 3130],
            ['slug' => 'material-fabric-linen', 'category' => 'materials', 'ui_group' => 'Tessuti', 'sort_order' => 3140],
        ]);

        // MATERIALS - Pietre e Gemme COMPLETE
        $terms = array_merge($terms, [
            ['slug' => 'material-gem-diamond', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3210],
            ['slug' => 'material-gem-corundum', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3220],
            ['slug' => 'material-gem-emerald', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3230],
            ['slug' => 'material-gem-quartz', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3240],
            ['slug' => 'material-gem-onyx', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3250],
            ['slug' => 'material-gem-opal', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3260],
            ['slug' => 'material-gem-turquoise', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3270],
            ['slug' => 'material-gem-amber', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3280],
            ['slug' => 'material-gem-coral', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3290],
            ['slug' => 'material-gem-pearl', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3300],
            ['slug' => 'material-gem-synthetic', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3310],
        ]);

        // MATERIALS — Gemme (estensioni)
        $terms = array_merge($terms, [
            // Corindone (famiglia) + varianti commerciali
            ['slug' => 'material-gem-ruby',            'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3320],
            ['slug' => 'material-gem-sapphire',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3330], // include blu; colori specifici sotto
            ['slug' => 'material-gem-sapphire-pink',   'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3340],
            ['slug' => 'material-gem-sapphire-yellow', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3350],
            ['slug' => 'material-gem-sapphire-green',  'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3360],
            ['slug' => 'material-gem-sapphire-padparadscha', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3370],

            // Berillo (famiglia) + varietà
            ['slug' => 'material-gem-beryl',           'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3380],
            ['slug' => 'material-gem-aquamarine',      'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3390],
            ['slug' => 'material-gem-morganite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3400],
            ['slug' => 'material-gem-heliodor',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3410],
            ['slug' => 'material-gem-goshenite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3420],

            // Crisoberillo
            ['slug' => 'material-gem-chrysoberyl',     'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3430],
            ['slug' => 'material-gem-alexandrite',     'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3440],

            // Granato (famiglia) + varietà principali
            ['slug' => 'material-gem-garnet',          'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3450],
            ['slug' => 'material-gem-garnet-almandine', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3460],
            ['slug' => 'material-gem-garnet-pyrope',   'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3470],
            ['slug' => 'material-gem-garnet-spessartine', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3480],
            ['slug' => 'material-gem-garnet-grossular', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3490],
            ['slug' => 'material-gem-tsavorite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3500], // grossular Cr
            ['slug' => 'material-gem-garnet-andradite', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3510],
            ['slug' => 'material-gem-demantoid',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3520], // andradite V

            // Tormalina (famiglia) + varianti
            ['slug' => 'material-gem-tourmaline',      'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3530],
            ['slug' => 'material-gem-rubellite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3540],
            ['slug' => 'material-gem-indicolite',      'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3550],
            ['slug' => 'material-gem-paraiba',         'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3560], // Cu-bearing

            // Altre "classiche"
            ['slug' => 'material-gem-topaz',           'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3570],
            ['slug' => 'material-gem-tanzanite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3580], // zoisite
            ['slug' => 'material-gem-zircon',          'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3590], // naturale (non CZ)
            ['slug' => 'material-gem-peridot',         'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3600], // olivina
            ['slug' => 'material-gem-jadeite',         'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3610],
            ['slug' => 'material-gem-nephrite',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3620],
            ['slug' => 'material-gem-spinel',          'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3630],
            ['slug' => 'material-gem-apatite',         'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3640],
            ['slug' => 'material-gem-kunzite',         'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3650], // spodumene
            ['slug' => 'material-gem-hiddenite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3660], // spodumene verde

            // Feldspati ornamentali
            ['slug' => 'material-gem-moonstone',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3670],
            ['slug' => 'material-gem-labradorite',     'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3680],
            ['slug' => 'material-gem-sunstone',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3690],

            // Calcedoni & quarzi varietali (se vuoi granularità)
            ['slug' => 'material-gem-chalcedony',      'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3700],
            ['slug' => 'material-gem-agate',           'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3710],
            ['slug' => 'material-gem-carnelian',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3720],
            ['slug' => 'material-gem-jasper',          'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3730],
            ['slug' => 'material-gem-chrysoprase',     'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3740],

            // Ornamentali "da banco"
            ['slug' => 'material-gem-lapis-lazuli',    'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3750],
            ['slug' => 'material-gem-malachite',       'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3760],
            ['slug' => 'material-gem-hematite',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3770],
            ['slug' => 'material-gem-obsidian',        'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3780],
            ['slug' => 'material-gem-jet',             'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3790],

            // Sintetiche / simulanti (richiestissime)
            ['slug' => 'material-gem-cubic-zirconia',  'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3800], // CZ
            ['slug' => 'material-gem-moissanite',      'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3810],
            ['slug' => 'material-gem-lab-grown-diamond', 'category' => 'materials', 'ui_group' => 'Gemme', 'sort_order' => 3820], // diamante sintetico
        ]);

        // MATERIALS - Altri Materiali
        $terms = array_merge($terms, [
            ['slug' => 'material-leather', 'category' => 'materials', 'ui_group' => 'Altri', 'sort_order' => 3910],
            ['slug' => 'material-found-objects', 'category' => 'materials', 'ui_group' => 'Altri', 'sort_order' => 3920],
        ]);

        // MATERIALS - Elettronica
        $terms = array_merge($terms, [
            ['slug' => 'material-electronics-led', 'category' => 'materials', 'ui_group' => 'Elettronica', 'sort_order' => 4010],
            ['slug' => 'material-electronics-general', 'category' => 'materials', 'ui_group' => 'Elettronica', 'sort_order' => 4020],
        ]);

        // MATERIALS - Componenti Gioielleria
        $terms = array_merge($terms, [
            ['slug' => 'material-metal-wire', 'category' => 'materials', 'ui_group' => 'Componenti', 'sort_order' => 4110],
            ['slug' => 'material-metal-sheet', 'category' => 'materials', 'ui_group' => 'Componenti', 'sort_order' => 4120],
            ['slug' => 'material-solder-jewelry', 'category' => 'materials', 'ui_group' => 'Componenti', 'sort_order' => 4130],
        ]);

        // MATERIALS - Smalti e Trattamenti
        $terms = array_merge($terms, [
            ['slug' => 'material-enamel-vitreous', 'category' => 'materials', 'ui_group' => 'Smalti', 'sort_order' => 4210],
            ['slug' => 'material-patina-oxidation', 'category' => 'materials', 'ui_group' => 'Smalti', 'sort_order' => 4220],
        ]);

        // ======================================
        // SUPPORTS - TUTTI I SUPPORTI COMPLETI DAI FILE MD
        // ======================================

        // SUPPORTS - Tele e Tessuti (quando la tela è IL supporto dell'opera)
        $terms = array_merge($terms, [
            ['slug' => 'support-canvas-cotton', 'category' => 'support', 'ui_group' => 'Tele', 'sort_order' => 4010],
            ['slug' => 'support-canvas-linen', 'category' => 'support', 'ui_group' => 'Tele', 'sort_order' => 4020],
            ['slug' => 'support-canvas-jute', 'category' => 'support', 'ui_group' => 'Tele', 'sort_order' => 4030],
            ['slug' => 'support-canvas-hemp', 'category' => 'support', 'ui_group' => 'Tele', 'sort_order' => 4040],
        ]);

        // SUPPORTS - Pannelli
        $terms = array_merge($terms, [
            ['slug' => 'support-panel-wood', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4110],
            ['slug' => 'support-panel-mdf', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4120],
            ['slug' => 'support-panel-hardboard', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4130],
            ['slug' => 'support-panel-plywood', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4140],
            ['slug' => 'support-panel-dibond', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4150],
            ['slug' => 'support-acrylic-sheet', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4160],
            // Pannelli compositi e materiali specifici
            ['slug' => 'support-panel-foamcore', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4156],
            ['slug' => 'support-panel-gatorfoam', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4157],
            ['slug' => 'support-panel-pvc-forex', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4158],
            ['slug' => 'support-panel-polycarbonate', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4159],
            ['slug' => 'support-panel-coroplast', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4161],
            ['slug' => 'support-panel-honeycomb-aluminum', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4162],
            // Legni specifici
            ['slug' => 'support-panel-poplar', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4165],
            ['slug' => 'support-panel-birch', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4166],
            ['slug' => 'support-panel-oak', 'category' => 'support', 'ui_group' => 'Pannelli', 'sort_order' => 4167],
        ]);

        // SUPPORTS - Carte COMPLETE
        $terms = array_merge($terms, [
            ['slug' => 'support-paper-watercolor', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4210],
            ['slug' => 'support-paper-drawing', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4220],
            ['slug' => 'support-paper-pastel', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4230],
            ['slug' => 'support-paper-newsprint', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4240],
            ['slug' => 'support-paper-rice', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4250],
            ['slug' => 'support-paper-handmade', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4260],
            ['slug' => 'support-parchment', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4270],
            ['slug' => 'support-vellum', 'category' => 'support', 'ui_group' => 'Carte', 'sort_order' => 4280],
        ]);

        // SUPPORTS - Carte per Stampa
        $terms = array_merge($terms, [
            ['slug' => 'support-paper-print-rag', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4310],
            ['slug' => 'support-paper-print-baryta', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4320],
            ['slug' => 'support-paper-print-rc', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4330],
            // Blocchi per xylografia e tecniche di stampa
            ['slug' => 'support-block-wood', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4335],
            ['slug' => 'support-block-linoleum', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4336],
            ['slug' => 'support-plate-photopolymer', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4337],
            ['slug' => 'support-mount-acrylic-face', 'category' => 'support', 'ui_group' => 'Stampa', 'sort_order' => 4338],
        ]);

        // SUPPORTS - Pietre COMPLETE
        $terms = array_merge($terms, [
            ['slug' => 'support-stone-marble', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4410],
            ['slug' => 'support-stone-limestone', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4420],
            ['slug' => 'support-stone-granite', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4430],
            ['slug' => 'support-stone-travertine', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4440],
            ['slug' => 'support-stone-sandstone', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4450],
            // Pietre aggiuntive
            ['slug' => 'support-stone-alabaster', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4460],
            ['slug' => 'support-stone-slate', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4470],
            ['slug' => 'support-stone-basalt', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4480],
            ['slug' => 'support-stone-onyx', 'category' => 'support', 'ui_group' => 'Pietre', 'sort_order' => 4490],
        ]);

        // SUPPORTS - Metalli
        $terms = array_merge($terms, [
            ['slug' => 'support-metal-copper', 'category' => 'support', 'ui_group' => 'Metalli', 'sort_order' => 4510],
            ['slug' => 'support-metal-zinc', 'category' => 'support', 'ui_group' => 'Metalli', 'sort_order' => 4520],
            ['slug' => 'support-metal-aluminum', 'category' => 'support', 'ui_group' => 'Metalli', 'sort_order' => 4530],
            ['slug' => 'support-metal-steel', 'category' => 'support', 'ui_group' => 'Metalli', 'sort_order' => 4540],
        ]);

        // SUPPORTS - Gioielleria (metalli preziosi e supporti specialistici)
        $terms = array_merge($terms, [
            // Lamine, fili, tubi (precious & special)
            ['slug' => 'support-metal-gold-sheet', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4910],
            ['slug' => 'support-metal-gold-wire', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4920],
            ['slug' => 'support-metal-gold-tube', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4930],

            ['slug' => 'support-metal-silver-sterling-sheet', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4940],
            ['slug' => 'support-metal-silver-sterling-wire', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4950],
            ['slug' => 'support-metal-silver-sterling-tube', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4960],

            ['slug' => 'support-metal-platinum-sheet', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4970],
            ['slug' => 'support-metal-platinum-wire', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4980],

            ['slug' => 'support-metal-titanium-sheet', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4990],
            ['slug' => 'support-metal-titanium-wire', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 4995],

            // Modelli e grezzi
            ['slug' => 'support-wax-blank', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 5000],
            ['slug' => 'support-cast-blank', 'category' => 'support', 'ui_group' => 'Gioielleria', 'sort_order' => 5010],
        ]);

        // SUPPORTS - Altri Materiali
        $terms = array_merge($terms, [
            ['slug' => 'support-glass', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4610],
            ['slug' => 'support-ceramic', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4620],
            ['slug' => 'support-leather-hide', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4630],
            ['slug' => 'support-fabric-silk', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4640],
            ['slug' => 'support-fabric-cotton', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4650],
            // Tessili e vetri aggiuntivi
            ['slug' => 'support-fabric-linen', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4655],
            ['slug' => 'support-fabric-wool', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4656],
            ['slug' => 'support-glass-tempered', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4660],
            ['slug' => 'support-glass-laminated', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4665],
            ['slug' => 'support-leather-veg', 'category' => 'support', 'ui_group' => 'Altri', 'sort_order' => 4635],
        ]);

        // SUPPORTS - Muro e Affreschi
        $terms = array_merge($terms, [
            ['slug' => 'support-wall', 'category' => 'support', 'ui_group' => 'Affreschi', 'sort_order' => 4710],
            ['slug' => 'support-plaster', 'category' => 'support', 'ui_group' => 'Affreschi', 'sort_order' => 4720],
            ['slug' => 'support-canvas-marouflage', 'category' => 'support', 'ui_group' => 'Affreschi', 'sort_order' => 4730],
        ]);

        // SUPPORTS - Mosaico
        $terms = array_merge($terms, [
            ['slug' => 'support-mosaic-mesh', 'category' => 'support', 'ui_group' => 'Mosaico', 'sort_order' => 4750],
            ['slug' => 'support-mosaic-cement-board', 'category' => 'support', 'ui_group' => 'Mosaico', 'sort_order' => 4760],
            ['slug' => 'support-mosaic-mortar-bed', 'category' => 'support', 'ui_group' => 'Mosaico', 'sort_order' => 4770],
        ]);

        // SUPPORTS - Scultura (armature e supporti strutturali)
        $terms = array_merge($terms, [
            ['slug' => 'support-armature-steel-wire', 'category' => 'support', 'ui_group' => 'Scultura', 'sort_order' => 4850],
            ['slug' => 'support-armature-aluminum', 'category' => 'support', 'ui_group' => 'Scultura', 'sort_order' => 4860],
        ]);

        // SUPPORTS - Digitale
        $terms = array_merge($terms, [
            ['slug' => 'support-digital-file', 'category' => 'support', 'ui_group' => 'Digitale', 'sort_order' => 4810],
        ]);

        // Aggiungi timestamps
        $now = now();
        foreach ($terms as &$term) {
            $term['is_active'] = true;
            $term['created_at'] = $now;
            $term['updated_at'] = $now;
        }

        // Bulk insert
        DB::table('vocabulary_terms')->insert($terms);

        $this->command->info('✅ Vocabulary terms seeded: ' . count($terms) . ' terms');
        $this->command->info('📊 Techniques: ' . collect($terms)->where('category', 'technique')->count());
        $this->command->info('📊 Materials: ' . collect($terms)->where('category', 'materials')->count());
        $this->command->info('📊 Supports: ' . collect($terms)->where('category', 'support')->count());
    }
}
