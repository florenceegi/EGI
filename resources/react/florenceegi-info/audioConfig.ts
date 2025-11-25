/**
 * Audio Configuration for FlorenceEGI
 * 
 * Scegli la sorgente audio modificando AUDIO_SOURCE:
 * - 'local': Usa i brani nella cartella /public/audio/ (richiede licenza SIAE per produzione)
 * - 'royalty-free': Usa brani royalty-free da fonti esterne (gratuiti, legali)
 * - 'ai-generated': Per futuro uso con brani generati da AI
 * - 'disabled': Disabilita completamente l'audio
 */

export type AudioSource = 'local' | 'royalty-free' | 'ai-generated' | 'disabled';

// ========================================
// CONFIGURAZIONE - MODIFICA QUI
// ========================================
export const AUDIO_SOURCE: AudioSource = 'royalty-free'; // Cambia questo valore

// Volume default (0.0 - 1.0)
export const DEFAULT_MUSIC_VOLUME = 0.3;
export const DEFAULT_SFX_VOLUME = 0.5;

// Auto-play al caricamento? (sconsigliato per UX, browser lo bloccano)
export const AUTO_PLAY_ON_LOAD = false;

// ========================================
// BRANI LOCALI (cartella /public/audio/)
// ========================================
export const LOCAL_TRACKS = [
    {
        id: 'aria-quarta-corda',
        filename: "Eternity - L'album più rilassante del mondo - 01 - Aria Sulla Quarta Corda.mp3",
        displayName: 'Aria Sulla Quarta Corda',
        artist: 'Eternity Collection',
    },
    {
        id: 'adiemus',
        filename: "Eternity - L'album più rilassante del mondo - 05 - Adiemus.mp3",
        displayName: 'Adiemus',
        artist: 'Eternity Collection',
    },
    {
        id: 'sogno-damore',
        filename: "Eternity - L'album più rilassante del mondo - 06 - Sogno D'amore.mp3",
        displayName: "Sogno D'amore",
        artist: 'Eternity Collection',
    },
    {
        id: 'gymnopedie',
        filename: "Eternity - L'album più rilassante del mondo - 08 - Gymnopedie N. 1.mp3",
        displayName: 'Gymnopédie N. 1',
        artist: 'Erik Satie',
    },
    {
        id: 'lago-cigni',
        filename: "Eternity - L'album più rilassante del mondo - 11 - Il Lago Dei Cigni.mp3",
        displayName: 'Il Lago Dei Cigni',
        artist: 'Tchaikovsky',
    },
    {
        id: 'adagio-archi',
        filename: "Eternity - L'album più rilassante del mondo - 13 - Adagio Per Archi.mp3",
        displayName: 'Adagio Per Archi',
        artist: 'Samuel Barber',
    },
    {
        id: 'piano-concerto-21',
        filename: "Eternity - L'album più rilassante del mondo - 14 - Piano Concerto 21 - Andante.mp3",
        displayName: 'Piano Concerto 21 - Andante',
        artist: 'Mozart',
    },
];

// ========================================
// BRANI ROYALTY-FREE (URL esterni)
// Fonti: Internet Archive (archive.org) - Musica classica di pubblico dominio
// Tutti gratuiti per uso commerciale, hotlinking permesso
// ========================================
export const ROYALTY_FREE_TRACKS = [
    // Musica classica di pubblico dominio da Internet Archive
    {
        id: 'debussy-clair-de-lune',
        url: 'https://archive.org/download/DebussyClairDeLune/Debussy-ClairDeLune.mp3',
        displayName: 'Clair de Lune',
        artist: 'Claude Debussy',
        license: 'Public Domain',
    },
    {
        id: 'satie-gymnopedie-1',
        url: 'https://archive.org/download/ErikSatieGymnopedies/01.Gymnop%C3%A9dieNo.1-Lent.mp3',
        displayName: 'Gymnopédie No. 1',
        artist: 'Erik Satie',
        license: 'Public Domain',
    },
    {
        id: 'bach-air-g-string',
        url: 'https://archive.org/download/AirOnTheGString_201905/Bach-Air_on_the_G_String.mp3',
        displayName: 'Air on the G String',
        artist: 'J.S. Bach',
        license: 'Public Domain',
    },
    {
        id: 'chopin-nocturne-op9',
        url: 'https://archive.org/download/ChopinNocturneOp.9No.2/NocturneOp.9No.2.mp3',
        displayName: 'Nocturne Op. 9 No. 2',
        artist: 'Frédéric Chopin',
        license: 'Public Domain',
    },
    {
        id: 'beethoven-moonlight',
        url: 'https://archive.org/download/Beethoven_Moonlight_Sonata/Beethoven_-_Moonlight_Sonata_1st_movement.mp3',
        displayName: 'Moonlight Sonata (1st mov.)',
        artist: 'Ludwig van Beethoven',
        license: 'Public Domain',
    },
    {
        id: 'pachelbel-canon',
        url: 'https://archive.org/download/PachelbelCanonInDMajor/Pachelbel-CanonInDMajor.mp3',
        displayName: 'Canon in D Major',
        artist: 'Johann Pachelbel',
        license: 'Public Domain',
    },
    {
        id: 'vivaldi-spring',
        url: 'https://archive.org/download/VivaldiTheSpringRv269/01Allegro.mp3',
        displayName: 'La Primavera - Allegro',
        artist: 'Antonio Vivaldi',
        license: 'Public Domain',
    },
];

// ========================================
// BRANI AI-GENERATED (placeholder per futuro)
// ========================================
export const AI_GENERATED_TRACKS = [
    // Placeholder - da popolare quando generi brani con Suno/AIVA
    // {
    //   id: 'florenceegi-theme',
    //   filename: 'florenceegi-theme.mp3',
    //   displayName: 'FlorenceEGI Theme',
    //   artist: 'AI Generated',
    // },
];

// ========================================
// EFFETTI SONORI (opzionali)
// ========================================
export const SFX_LIBRARY: Record<string, string> = {
    // Puoi aggiungere SFX royalty-free qui
    // 'click': 'https://cdn.pixabay.com/audio/...',
    // 'hover': '/audio/sfx/hover.mp3',
    // 'success': '/audio/sfx/success.mp3',
};

// ========================================
// HELPER FUNCTIONS
// ========================================

export interface AudioTrack {
    id: string;
    url?: string;
    filename?: string;
    displayName: string;
    artist: string;
    license?: string;
}

/**
 * Ottiene la lista dei brani in base alla configurazione
 */
export function getActiveTrackList(): AudioTrack[] {
    switch (AUDIO_SOURCE) {
        case 'local':
            return LOCAL_TRACKS.map(track => ({
                ...track,
                url: `/audio/${encodeURIComponent(track.filename)}`,
            }));

        case 'royalty-free':
            return ROYALTY_FREE_TRACKS;

        case 'ai-generated':
            return AI_GENERATED_TRACKS.map(track => ({
                ...track,
                url: track.filename ? `/audio/ai/${encodeURIComponent(track.filename)}` : undefined,
            }));

        case 'disabled':
        default:
            return [];
    }
}

/**
 * Controlla se l'audio è abilitato
 */
export function isAudioEnabled(): boolean {
    return AUDIO_SOURCE !== 'disabled' && getActiveTrackList().length > 0;
}
