<?php

return [
    // Nome categoria di fallback se un EGI non ha ancora un tratto di categoria
    'default' => 'Art',

    // Mappa Categoria → classi Tailwind (sfondo / testo / bordo opzionale)
    // Palette pensata per coerenza semantica del dominio
    'map' => [
        'Games'        => ['classes' => 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-inner shadow-indigo-900/40'],
        'Business'     => ['classes' => 'bg-gradient-to-r from-slate-600 to-slate-800 text-white ring-1 ring-slate-400/30'],
    // Art: effetto "arcobaleno con base oro" (gold base + overlay multi-hue)
    // Usando layering di gradient + ring + subtle glow (richiede supporto utility Tailwind)
    'Art'          => ['classes' => 'relative overflow-hidden text-white font-medium bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-500 shadow-[0_0_0_1px_rgba(255,255,255,0.15)] ring-1 ring-amber-300/50 before:content-[""] before:absolute before:inset-0 before:bg-[conic-gradient(at_50%_50%,#fef08a_0deg,#fbbf24_60deg,#f472b6_120deg,#8b5cf6_180deg,#0ea5e9_240deg,#10b981_300deg,#fef08a_360deg)] before:opacity-60 before:mix-blend-overlay before:animate-[spin_8s_linear_infinite]'],
        'Music'        => ['classes' => 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white'],
        'Sports'       => ['classes' => 'bg-gradient-to-r from-orange-500 to-amber-600 text-white'],
        'Education'    => ['classes' => 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white'],
    // Science: complesso, techno (multi-layer plasma + grid glow)
    'Science'      => ['classes' => 'relative overflow-hidden text-white font-medium bg-gradient-to-br from-cyan-600 via-sky-500 to-indigo-700 ring-1 ring-cyan-300/40 shadow-[0_0_0_1px_rgba(255,255,255,0.08),0_0_12px_-2px_rgba(14,165,233,0.5)] before:content-[""] before:absolute before:inset-0 before:bg-[radial-gradient(circle_at_30%_40%,rgba(255,255,255,0.25),transparent_60%),conic-gradient(from_0deg_at_70%_60%,#06b6d4_0deg,#6366f1_120deg,#0ea5e9_240deg,#06b6d4_360deg)] before:mix-blend-overlay before:opacity-70 after:content-[""] after:absolute after:-inset-1 after:bg-[repeating-linear-gradient(45deg,rgba(255,255,255,0.08)_0_6px,transparent_6px_12px)] after:opacity-40 after:animate-[pulse_5s_ease-in-out_infinite]'],
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
