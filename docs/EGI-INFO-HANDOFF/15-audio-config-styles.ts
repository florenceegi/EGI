/**
 * EGI-INFO HANDOFF - Audio Config & Styles
 * 
 * File: 
 * - src/audio/audioConfig.ts
 * - src/audio/AudioControls.css
 */

// ========================================
// FILE: src/audio/audioConfig.ts
// ========================================

export type AudioSource = 'local' | 'royalty-free' | 'ai-generated' | 'disabled';

// CONFIGURAZIONE - Modifica qui
export const AUDIO_SOURCE: AudioSource = 'royalty-free';

// Volume default (0.0 - 1.0)
export const DEFAULT_MUSIC_VOLUME = 0.3;
export const DEFAULT_SFX_VOLUME = 0.5;
export const AUTO_PLAY_ON_LOAD = false;

// Brani locali (richiede licenza SIAE per produzione)
export const LOCAL_TRACKS = [
    { id: 'aria-quarta-corda', filename: "Aria Sulla Quarta Corda.mp3", displayName: 'Aria Sulla Quarta Corda', artist: 'Eternity Collection' },
    { id: 'adiemus', filename: "Adiemus.mp3", displayName: 'Adiemus', artist: 'Eternity Collection' },
    { id: 'sogno-damore', filename: "Sogno D'amore.mp3", displayName: "Sogno D'amore", artist: 'Eternity Collection' },
    { id: 'gymnopedie', filename: "Gymnopedie N. 1.mp3", displayName: 'Gymnopédie N. 1', artist: 'Erik Satie' },
    { id: 'lago-cigni', filename: "Il Lago Dei Cigni.mp3", displayName: 'Il Lago Dei Cigni', artist: 'Tchaikovsky' },
];

// Brani royalty-free (Internet Archive - Public Domain)
export const ROYALTY_FREE_TRACKS = [
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

export const AI_GENERATED_TRACKS: AudioTrack[] = [];

export const SFX_LIBRARY: Record<string, string> = {
    // 'click': '/audio/sfx/click.mp3',
};

export interface AudioTrack {
    id: string;
    url?: string;
    filename?: string;
    displayName: string;
    artist: string;
    license?: string;
}

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
            return AI_GENERATED_TRACKS;
        case 'disabled':
        default:
            return [];
    }
}

export function isAudioEnabled(): boolean {
    return AUDIO_SOURCE !== 'disabled' && getActiveTrackList().length > 0;
}

/* ========================================
   FILE: src/audio/AudioControls.css
   ======================================== */

/*
.audio-controls {
    position: fixed;
    bottom: 20px;
    left: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 1000;
    background: rgba(10, 10, 20, 0.8);
    padding: 12px;
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.audio-controls:hover {
    background: rgba(10, 10, 20, 0.95);
    border-color: rgba(0, 255, 200, 0.3);
}

.audio-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: rgba(20, 20, 30, 0.9);
    color: #fff;
    font-size: 1.3rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.audio-btn:hover {
    transform: scale(1.1);
    border-color: rgba(0, 255, 200, 0.5);
    box-shadow: 0 0 15px rgba(0, 255, 200, 0.3);
}

.audio-btn.active {
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.3), rgba(0, 255, 200, 0.3));
    border-color: rgba(0, 255, 200, 0.6);
}

.music-btn.active {
    animation: musicPulse 2s ease-in-out infinite;
}

@keyframes musicPulse {
    0%, 100% { box-shadow: 0 0 10px rgba(0, 255, 200, 0.3); }
    50% { box-shadow: 0 0 20px rgba(0, 255, 200, 0.6); }
}

.volume-slider-container {
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    transition: all 0.3s ease;
}

.audio-controls:hover .volume-slider-container {
    max-height: 100px;
    opacity: 1;
    margin-top: 8px;
}

.volume-slider {
    width: 44px;
    height: 80px;
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
    writing-mode: vertical-lr;
    direction: rtl;
}

.volume-slider::-webkit-slider-track {
    width: 6px;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00ffc8, #0096ff);
    cursor: pointer;
    box-shadow: 0 0 10px rgba(0, 255, 200, 0.5);
}

.current-track-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    max-width: 200px;
}

.track-icon {
    font-size: 1rem;
    color: #00ffc8;
    animation: noteFloat 2s ease-in-out infinite;
}

@keyframes noteFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

.track-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow: hidden;
    flex: 1;
}

.track-name {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.95);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
}

.track-artist {
    font-size: 0.65rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

@media (max-width: 768px) {
    .audio-controls {
        bottom: 10px;
        left: 10px;
        padding: 8px;
        flex-direction: row;
    }
    .audio-btn { width: 36px; height: 36px; font-size: 1.1rem; }
    .volume-slider-container { display: none; }
}
*/
