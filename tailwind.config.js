import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import daisyui from 'daisyui'; // Import corretto di daisyui

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Aggiungi qui altri percorsi se necessario, es. per i componenti Ultra
        './vendor/ultra/**/*.blade.php',
        './vendor/fabio-cherici/**/*.blade.php', // Se hai pacchetti custom
    ],

    theme: {
        extend: {
            fontFamily: {
                // Font per il corpo testo (sans-serif)
                sans: ['"Source Sans Pro"', ...defaultTheme.fontFamily.sans],
                // Font per titoli e display
                display: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                // Font per codice
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
                // Alias per coerenza con le guidelines (opzionale, ma utile)
                body: ['"Source Sans Pro"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // --- FLORENCEEGI BRAND PALETTE ---
                'florence-gold': {
                    light: '#EBC08A',      // Per hover leggeri, focus rings
                    DEFAULT: '#D4A574',    // Oro Fiorentino Principale
                    dark: '#B88A5F',       // Per hover scuri, active states
                    text: '#1F2937',       // Testo scuro su sfondo Oro (es. per bottoni oro)
                },
                'verde-rinascita': {
                    light: '#4A7031',
                    DEFAULT: '#2D5016',
                    dark: '#1E350F',
                    text: '#F9FAFB',       // Testo chiaro su sfondo Verde
                },
                'blu-algoritmo': {
                    light: '#3C5A88',
                    DEFAULT: '#1B365D',
                    dark: '#0E1C30',       // Blu molto scuro, potenziale sfondo pagina
                    text: '#F9FAFB',       // Testo chiaro su sfondo Blu
                },
                'grigio-pietra': {
                    extralight: '#F3F4F6', // gray-100 Tailwind, per sfondi card chiare su tema scuro
                    light: '#D1D5DB',      // gray-300 Tailwind, per testi chiari secondari
                    DEFAULT: '#6B7280',    // gray-500 Tailwind (Originale #6B6B6B è simile a gray-500/600)
                    dark: '#374151',       // gray-700 Tailwind, per testi scuri o bordi
                    extradark: '#1F2937',  // gray-800 Tailwind, per sfondi card scure
                },
                'rosso-committente': {
                    extralight: '#FBE9E9',  // Una tonalità molto chiara, quasi rosa, per sfondi leggeri
                    light: '#D97F81',       // Un rosso più chiaro per hover o elementi meno prominenti
                    DEFAULT: '#A12C2F',     // Il colore principale del Commissioner
                    dark: '#802225',        // Un rosso più profondo per stati attivi o bordi
                    extradark: '#5E1A1C',   // Un rosso molto scuro, quasi marrone, per testi o elementi scuri
                    text: '#FFFFFF',        // Colore del testo ad alto contrasto da usare su sfondi rosso-committente
                },
                // Colori Secondari / Di Stato (Brand Guidelines)
                'rosso-urgenza': {
                    DEFAULT: '#C13120', // (Era #C13120) Simile a Tailwind red-600/700
                    text: '#FFFFFF',
                },
                'arancio-energia': {
                    DEFAULT: '#E67E22', // (Era #E67E22) Simile a Tailwind orange-500/600
                    text: '#FFFFFF',
                },
                'viola-innovazione': {
                    DEFAULT: '#8E44AD', // (Era #8E44AD) Simile a Tailwind purple-600
                    text: '#FFFFFF',
                },
                'verde-trading': '#10B981', // Emerald-500
                'verde-trading-dark': '#059669', // Emerald-600
               

                // --- COLORI DI STATO STANDARD (per DaisyUI e utility Tailwind) ---
                // Questi dovrebbero avere buon contrasto sul nostro "base-100" scuro
                'info': {
                    DEFAULT: '#22D3EE', // Tailwind cyan-400
                    content: '#064e3b', // Testo scuro per leggibilità
                },
                'success': {
                    DEFAULT: '#4ADE80', // Tailwind green-400
                    content: '#052e16', // Testo scuro
                },
                'warning': {
                    DEFAULT: '#FBBF24', // Tailwind amber-400
                    content: '#78350f', // Testo scuro
                },
                'error': {
                    DEFAULT: '#F87171', // Tailwind red-400
                    content: '#7f1d1d', // Testo scuro
                },
            },
            // Qui puoi aggiungere altre estensioni come spacing, borderRadius, ecc.
            // Esempio per bordi arrotondati rinascimentali (molto leggeri)
            // borderRadius: {
            //     'rinascimento-sm': '2px',
            //     'rinascimento-md': '4px',
            // }
        },
    },

    daisyui: {
        styled: true, // Applica stili di default ai componenti DaisyUI
        themes: [{
            florenceegi: { // Il nostro tema custom per DaisyUI
                "primary": "#D4A574",         // Oro Fiorentino (florence-gold.DEFAULT)
                "primary-content": "#1F2937", // Testo scuro su primario (florence-gold.text)

                "secondary": "#1B365D",       // Blu Algoritmo (blu-algoritmo.DEFAULT)
                "secondary-content": "#F9FAFB",// Testo chiaro su secondario (blu-algoritmo.text)

                "accent": "#2D5016",          // Verde Rinascita (verde-rinascita.DEFAULT)
                "accent-content": "#F9FAFB",  // Testo chiaro su accento (verde-rinascita.text)

                "neutral": "#374151",         // Grigio Pietra Scuro (grigio-pietra.dark / Tailwind gray-700)
                "neutral-content": "#D1D5DB", // Testo chiaro su neutro (grigio-pietra.light)

                "base-100": "#0E1C30",        // SFONDO PAGINA PRINCIPALE: Blu Algoritmo Molto Scuro (blu-algoritmo.dark)
                                              // Alternativa: "#111827" (Tailwind gray-900)
                "base-200": "#1F2937",        // Sfondo per card leggermente più chiaro (grigio-pietra.extradark / Tailwind gray-800)
                "base-300": "#374151",        // Sfondo per elementi ancora più chiari o bordi (grigio-pietra.dark / Tailwind gray-700)
                "base-content": "#D1D5DB",    // Colore testo di default su base-100 (grigio-pietra.light / Tailwind gray-300)

                "info": "#22D3EE",            // info.DEFAULT
                "info-content": "#064e3b",    // info.content

                "success": "#4ADE80",         // success.DEFAULT
                "success-content": "#052e16", // success.content

                "warning": "#FBBF24",         // warning.DEFAULT
                "warning-content": "#78350f", // warning.content

                "error": "#F87171",           // error.DEFAULT
                "error-content": "#7f1d1d",   // error.content

                // Aggiustamenti specifici DaisyUI
                "--rounded-box": "0.5rem", // Esempio: default 0.5rem per card, etc. (era 1rem)
                "--rounded-btn": "0.375rem", // Esempio: default 0.375rem per bottoni (era 0.5rem)
                // "--btn-text-case": "none", // Se non vuoi i bottoni in uppercase di default
            },
        }],
        base: true,        // Applica stili di base (come normalize)
        utils: true,       // Applica classi utility di DaisyUI
        logs: false,       // Riduce i log in console
        rtl: false,
        prefix: "",        // Nessun prefisso per le classi DaisyUI (es. usa `btn` non `du-btn`)
    },

    plugins: [
        typography,
        forms,
        daisyui // Usa la variabile importata
    ],

    corePlugins: {
        preflight: true, // Abilita il Preflight di Tailwind (modern-normalize)
    },

    // Il safelist è utile se generi classi dinamicamente e vuoi che Tailwind le includa sempre
    // Valuta se queste sono ancora necessarie con DaisyUI e i nuovi colori.
    // Potrebbe essere meglio safelistare nomi di classi specifici che usi (es. bg-florence-gold)
    // se hai problemi di purging.
    safelist: [
        // Esempi basati sui colori DaisyUI (se usi input con colori specifici)
        // 'input-primary', 'input-secondary', 'input-accent',
        // 'select-primary', 'select-secondary', 'select-accent',
        // Classi specifiche della tua palette che potrebbero essere purgate:
        'bg-florence-gold', 'text-florence-gold',
        'bg-verde-rinascita', 'text-verde-rinascita',
        'bg-blu-algoritmo', 'text-blu-algoritmo',
        'bg-rosso-committente', 'text-rosso-committente',
    // --- Safelist badge categorie (config/egi_category_badges.php) ---
    'bg-gradient-to-r', 'bg-gradient-to-br',
    'from-amber-400','via-amber-300','to-yellow-500',
    'from-indigo-500','to-purple-600','from-slate-600','to-slate-800',
    'from-emerald-500','to-teal-500','from-orange-500','to-amber-600',
    'from-blue-500','to-cyan-500','from-cyan-600','via-sky-500','to-indigo-700',
    'from-fuchsia-500','to-violet-600','from-yellow-500','to-amber-500',
    'from-purple-600','to-indigo-600','from-stone-500','to-stone-700',
    'from-green-500','to-lime-500','from-rose-500','to-pink-500','from-red-500','to-orange-500','from-sky-500','to-indigo-500',
    'ring-amber-300/50','ring-cyan-300/40',
    'shadow-[0_0_0_1px_rgba(255,255,255,0.15)]','shadow-[0_0_0_1px_rgba(255,255,255,0.08),0_0_12px_-2px_rgba(14,165,233,0.5)]',
    'before:content-[""]','before:absolute','before:inset-0',
    'before:bg-[conic-gradient(at_50%_50%,#fef08a_0deg,#fbbf24_60deg,#f472b6_120deg,#8b5cf6_180deg,#0ea5e9_240deg,#10b981_300deg,#fef08a_360deg)]',
    'before:bg-[radial-gradient(circle_at_30%_40%,rgba(255,255,255,0.25),transparent_60%),conic-gradient(from_0deg_at_70%_60%,#06b6d4_0deg,#6366f1_120deg,#0ea5e9_240deg,#06b6d4_360deg)]',
    'before:mix-blend-overlay','before:opacity-60','before:opacity-70',
    'before:animate-[spin_8s_linear_infinite]',
    'after:content-[""]','after:absolute','after:-inset-1',
    'after:bg-[repeating-linear-gradient(45deg,rgba(255,255,255,0.08)_0_6px,transparent_6px_12px)]','after:opacity-40','after:animate-[pulse_5s_ease-in-out_infinite]',
        // ... etc. per gli altri colori e le loro varianti (light, dark, text) se usate dinamicamente
        // Pattern per i colori di stato (se non coperti da DaisyUI)
        {
            pattern: /bg-(info|success|warning|error)(-(DEFAULT|content))?/,
        },
        {
            pattern: /text-(info|success|warning|error)(-(DEFAULT|content))?/,
        },
    ],
};
