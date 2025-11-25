/**
 * FlorenceEGI Audio System - Preset Tracklist
 *
 * Playlist predefinite royalty-free e configurabili
 * Tutte le tracce sono LEGALI per uso commerciale
 *
 * NOTA: Usiamo Internet Archive (archive.org) perché permette hotlinking diretto.
 * Pixabay e simili bloccano l'accesso diretto (403 Forbidden).
 */

import { AudioTrack, AudioConfig } from './types';

// =============================================================================
// TRACCE ROYALTY-FREE - Musica Classica di Pubblico Dominio
// Fonte: Internet Archive (archive.org) - Hotlinking permesso
// =============================================================================

export const PUBLIC_DOMAIN_CLASSICAL: AudioTrack[] = [
    {
        id: 'pd-debussy-clair-de-lune',
        displayName: 'Clair de Lune',
        artist: 'Claude Debussy',
        url: 'https://archive.org/download/DebussyClairDeLune/Debussy-ClairDeLune.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['calm', 'relaxing', 'piano', 'impressionist'],
    },
    {
        id: 'pd-satie-gymnopedie',
        displayName: 'Gymnopédie No. 1',
        artist: 'Erik Satie',
        url: 'https://archive.org/download/ErikSatieGymnopedies/01.Gymnop%C3%A9dieNo.1-Lent.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['calm', 'minimalist', 'piano'],
    },
    {
        id: 'pd-bach-air',
        displayName: 'Air on the G String',
        artist: 'J.S. Bach',
        url: 'https://archive.org/download/AirOnTheGString_201905/Bach-Air_on_the_G_String.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'strings', 'elegant'],
    },
    {
        id: 'pd-chopin-nocturne',
        displayName: 'Nocturne Op. 9 No. 2',
        artist: 'Frédéric Chopin',
        url: 'https://archive.org/download/ChopinNocturneOp.9No.2/NocturneOp.9No.2.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['romantic', 'piano', 'nocturne'],
    },
    {
        id: 'pd-beethoven-moonlight',
        displayName: 'Moonlight Sonata (1st mov.)',
        artist: 'Ludwig van Beethoven',
        url: 'https://archive.org/download/Beethoven_Moonlight_Sonata/Beethoven_-_Moonlight_Sonata_1st_movement.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['romantic', 'piano', 'famous'],
    },
    {
        id: 'pd-pachelbel-canon',
        displayName: 'Canon in D Major',
        artist: 'Johann Pachelbel',
        url: 'https://archive.org/download/PachelbelCanonInDMajor/Pachelbel-CanonInDMajor.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'famous', 'wedding'],
    },
    {
        id: 'pd-vivaldi-spring',
        displayName: 'La Primavera - Allegro',
        artist: 'Antonio Vivaldi',
        url: 'https://archive.org/download/VivaldiTheSpringRv269/01Allegro.mp3',
        license: 'Public Domain',
        genre: 'classical',
        tags: ['baroque', 'energetic', 'seasons'],
    },
];

// Alias per compatibilità
export const ROYALTY_FREE_AMBIENT: AudioTrack[] = PUBLIC_DOMAIN_CLASSICAL.filter(
    t => t.tags?.includes('calm') || t.tags?.includes('relaxing')
);

export const ROYALTY_FREE_ELECTRONIC: AudioTrack[] = []; // Non abbiamo URL funzionanti per electronic

export const ROYALTY_FREE_TECH: AudioTrack[] = []; // Non abbiamo URL funzionanti per tech

// =============================================================================
// CONFIG PRESETS
// =============================================================================

export const AUDIO_PRESET_AMBIENT: AudioConfig = {
    source: 'royalty-free',
    tracks: ROYALTY_FREE_AMBIENT,
    defaultMusicVolume: 0.25,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 2000,
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    position: 'bottom-left',
};

export const AUDIO_PRESET_CLASSICAL: AudioConfig = {
    source: 'royalty-free',
    tracks: PUBLIC_DOMAIN_CLASSICAL,
    defaultMusicVolume: 0.25,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 2000,
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    position: 'bottom-left',
};

// Backward compatible - usa classical invece di tech (non abbiamo URL funzionanti per tech)
export const AUDIO_PRESET_TECH: AudioConfig = {
    source: 'royalty-free',
    tracks: PUBLIC_DOMAIN_CLASSICAL,
    defaultMusicVolume: 0.2,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 1500,
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    position: 'bottom-left',
};

export const AUDIO_PRESET_LOFI: AudioConfig = {
    source: 'royalty-free',
    tracks: ROYALTY_FREE_AMBIENT, // Usa ambient calm tracks
    defaultMusicVolume: 0.3,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 1000,
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    compactMode: true,
    position: 'bottom-right',
};

// All tracks combined
export const ALL_ROYALTY_FREE_TRACKS: AudioTrack[] = PUBLIC_DOMAIN_CLASSICAL;
