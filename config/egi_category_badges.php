<?php

return [
    // Nome categoria di fallback se un EGI non ha ancora un tratto di categoria
    'default' => 'Art',

    // Mappa Categoria → classi Tailwind (sfondo / testo / bordo opzionale)
    // Palette pensata per coerenza semantica del dominio
    'map' => [
        'Games'        => ['classes' => 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-inner shadow-indigo-900/40'],
        'Business'     => ['classes' => 'bg-gradient-to-r from-slate-600 to-slate-800 text-white ring-1 ring-slate-400/30'],
        'Art'          => ['classes' => 'bg-gradient-to-r from-pink-500 to-rose-500 text-white'],
        'Music'        => ['classes' => 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white'],
        'Sports'       => ['classes' => 'bg-gradient-to-r from-orange-500 to-amber-600 text-white'],
        'Education'    => ['classes' => 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white'],
        'Science'      => ['classes' => 'bg-gradient-to-r from-cyan-500 to-sky-500 text-white'],
        'Technology'   => ['classes' => 'bg-gradient-to-r from-fuchsia-500 to-violet-600 text-white'],
        'Collectibles' => ['classes' => 'bg-gradient-to-r from-yellow-500 to-amber-500 text-gray-900 font-semibold'],
        'Fantasy'      => ['classes' => 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white'],
        'History'      => ['classes' => 'bg-gradient-to-r from-stone-500 to-stone-700 text-white'],
        'Nature'       => ['classes' => 'bg-gradient-to-r from-green-500 to-lime-500 text-white'],
        'Fashion'      => ['classes' => 'bg-gradient-to-r from-rose-500 to-pink-500 text-white'],
        'Food'         => ['classes' => 'bg-gradient-to-r from-red-500 to-orange-500 text-white'],
        'Travel'       => ['classes' => 'bg-gradient-to-r from-sky-500 to-indigo-500 text-white'],
    ],
];
