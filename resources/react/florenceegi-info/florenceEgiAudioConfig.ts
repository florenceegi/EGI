/**
 * FlorenceEGI Page - Audio Configuration
 *
 * Configurazione specifica per la pagina FlorenceEGI
 * che utilizza il sistema audio condiviso.
 *
 * Per cambiare sorgente audio, modifica FLORENCEEGI_AUDIO_SOURCE
 */

import { AudioConfig, AudioTrack, AudioSource } from '../shared/audio';

// ========================================
// CONFIGURAZIONE PRINCIPALE
// ========================================

/**
 * Sorgente audio per FlorenceEGI:
 * - 'local': Brani in /public/audio/ (richiede SIAE per produzione)
 * - 'royalty-free': Brani da Internet Archive (gratuiti, legali)
 * - 'custom': Tracklist personalizzata
 * - 'disabled': Audio disabilitato
 * 
 * NOTA: 'royalty-free' non funziona perché la maggior parte dei servizi
 * blocca l'hotlinking. Per ora usiamo 'local' per testing.
 * Per produzione, scaricare brani royalty-free in /public/audio/royalty-free/
 */
export const FLORENCEEGI_AUDIO_SOURCE: AudioSource = 'local';

// ========================================
// BRANI LOCALI (Eternity Collection)
// ATTENZIONE: Richiede licenza SIAE per uso pubblico!
// ========================================

const LOCAL_TRACKS: AudioTrack[] = [
    {
        id: 'aria-quarta-corda',
        filename: "Eternity - L'album più rilassante del mondo - 01 - Aria Sulla Quarta Corda.mp3",
        displayName: 'Aria Sulla Quarta Corda',
        artist: 'Eternity Collection',
        genre: 'classical',
    },
    {
        id: 'adiemus',
        filename: "Eternity - L'album più rilassante del mondo - 05 - Adiemus.mp3",
        displayName: 'Adiemus',
        artist: 'Eternity Collection',
        genre: 'new-age',
    },
    {
        id: 'sogno-damore',
        filename: "Eternity - L'album più rilassante del mondo - 06 - Sogno D'amore.mp3",
        displayName: "Sogno D'amore",
        artist: 'Eternity Collection',
        genre: 'classical',
    },
    {
        id: 'gymnopedie',
        filename: "Eternity - L'album più rilassante del mondo - 08 - Gymnopedie N. 1.mp3",
        displayName: 'Gymnopédie N. 1',
        artist: 'Erik Satie',
        genre: 'classical',
    },
    {
        id: 'lago-cigni',
        filename: "Eternity - L'album più rilassante del mondo - 11 - Il Lago Dei Cigni.mp3",
        displayName: 'Il Lago Dei Cigni',
        artist: 'Tchaikovsky',
        genre: 'classical',
    },
    {
        id: 'adagio-archi',
        filename: "Eternity - L'album più rilassante del mondo - 13 - Adagio Per Archi.mp3",
        displayName: 'Adagio Per Archi',
        artist: 'Samuel Barber',
        genre: 'classical',
    },
    {
        id: 'piano-concerto-21',
        filename: "Eternity - L'album più rilassante del mondo - 14 - Piano Concerto 21 - Andante.mp3",
        displayName: 'Piano Concerto 21 - Andante',
        artist: 'Mozart',
        genre: 'classical',
    },
];

// ========================================
// BRANI ROYALTY-FREE (Internet Archive - Pubblico Dominio)
// Tutti gratuiti per uso commerciale - Hotlinking permesso
// ========================================

const ROYALTY_FREE_TRACKS: AudioTrack[] = [
    {
        id: 'debussy-clair-de-lune',
        url: 'https://archive.org/download/DebussyClairDeLune/Debussy-ClairDeLune.mp3',
        displayName: 'Clair de Lune',
        artist: 'Claude Debussy',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['relaxing', 'calm', 'piano'],
    },
    {
        id: 'satie-gymnopedie-1',
        url: 'https://archive.org/download/ErikSatieGymnopedies/01.Gymnop%C3%A9dieNo.1-Lent.mp3',
        displayName: 'Gymnopédie No. 1',
        artist: 'Erik Satie',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['minimalist', 'calm', 'piano'],
    },
    {
        id: 'bach-air-g-string',
        url: 'https://archive.org/download/AirOnTheGString_201905/Bach-Air_on_the_G_String.mp3',
        displayName: 'Air on the G String',
        artist: 'J.S. Bach',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'strings', 'elegant'],
    },
    {
        id: 'chopin-nocturne-op9',
        url: 'https://archive.org/download/ChopinNocturneOp.9No.2/NocturneOp.9No.2.mp3',
        displayName: 'Nocturne Op. 9 No. 2',
        artist: 'Frédéric Chopin',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['romantic', 'piano', 'nocturne'],
    },
    {
        id: 'beethoven-moonlight',
        url: 'https://archive.org/download/Beethoven_Moonlight_Sonata/Beethoven_-_Moonlight_Sonata_1st_movement.mp3',
        displayName: 'Moonlight Sonata (1st mov.)',
        artist: 'Ludwig van Beethoven',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['romantic', 'piano', 'famous'],
    },
    {
        id: 'pachelbel-canon',
        url: 'https://archive.org/download/PachelbelCanonInDMajor/Pachelbel-CanonInDMajor.mp3',
        displayName: 'Canon in D Major',
        artist: 'Johann Pachelbel',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'famous', 'elegant'],
    },
    {
        id: 'vivaldi-spring',
        url: 'https://archive.org/download/VivaldiTheSpringRv269/01Allegro.mp3',
        displayName: 'La Primavera - Allegro',
        artist: 'Antonio Vivaldi',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'energetic', 'famous'],
    },
];

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Converte brani locali in formato con URL
 */
function prepareLocalTracks(): AudioTrack[] {
    return LOCAL_TRACKS.map(track => ({
        ...track,
        url: `/audio/${encodeURIComponent(track.filename || '')}`,
    }));
}

/**
 * Ottiene la lista tracce in base alla configurazione
 */
function getTrackList(): AudioTrack[] {
    switch (FLORENCEEGI_AUDIO_SOURCE) {
        case 'local':
            return prepareLocalTracks();
        case 'royalty-free':
            return ROYALTY_FREE_TRACKS;
        case 'disabled':
        default:
            return [];
    }
}

// ========================================
// CONFIGURAZIONE FINALE
// ========================================

export const FLORENCEEGI_AUDIO_CONFIG: AudioConfig = {
    source: FLORENCEEGI_AUDIO_SOURCE,
    tracks: getTrackList(),

    // Impostazioni
    defaultMusicVolume: 0.3,
    defaultSfxVolume: 0.5,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 1500,

    // UI
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    compactMode: false,
    position: 'bottom-left',
};

// Export per compatibilità
export { LOCAL_TRACKS, ROYALTY_FREE_TRACKS };
