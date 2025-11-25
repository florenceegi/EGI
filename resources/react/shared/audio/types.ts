/**
 * FlorenceEGI Audio System - Types
 * 
 * Tipi condivisi per il sistema audio riutilizzabile
 */

export type AudioSource = 'local' | 'royalty-free' | 'ai-generated' | 'custom' | 'disabled';

export interface AudioTrack {
    id: string;
    url?: string;
    filename?: string;
    displayName: string;
    artist: string;
    album?: string;
    duration?: number; // in seconds
    license?: string;
    genre?: string;
    tags?: string[];
}

export interface AudioConfig {
    // Sorgente audio
    source: AudioSource;

    // Lista brani
    tracks: AudioTrack[];

    // Effetti sonori (opzionale)
    sfx?: Record<string, string>;

    // Impostazioni default
    defaultMusicVolume?: number;
    defaultSfxVolume?: number;

    // Comportamento
    autoPlay?: boolean;
    shuffle?: boolean;
    loop?: boolean;
    crossfadeDuration?: number; // ms

    // UI
    showTrackInfo?: boolean;
    showVolumeSlider?: boolean;
    showNextButton?: boolean;
    compactMode?: boolean;

    // Posizione UI
    position?: 'bottom-left' | 'bottom-right' | 'top-left' | 'top-right';
}

export interface AudioContextType {
    // Config
    config: AudioConfig;

    // Music controls
    isMusicEnabled: boolean;
    setMusicEnabled: (enabled: boolean) => void;
    musicVolume: number;
    setMusicVolume: (volume: number) => void;

    // SFX controls
    isSfxEnabled: boolean;
    setSfxEnabled: (enabled: boolean) => void;
    sfxVolume: number;
    setSfxVolume: (volume: number) => void;

    // Playback
    playTrack: (trackId: string) => void;
    playRandomTrack: () => void;
    playNextTrack: () => void;
    playPreviousTrack: () => void;
    stopMusic: () => void;
    pauseMusic: () => void;
    resumeMusic: () => void;
    playSfx: (sfxId: string) => void;

    // Current state
    currentTrack: AudioTrack | null;
    isPlaying: boolean;
    currentTime: number;
    duration: number;

    // Playlist
    playlist: AudioTrack[];
    playlistIndex: number;

    // Seek
    seekTo: (time: number) => void;
}

// Default config
export const DEFAULT_AUDIO_CONFIG: AudioConfig = {
    source: 'disabled',
    tracks: [],
    sfx: {},
    defaultMusicVolume: 0.3,
    defaultSfxVolume: 0.5,
    autoPlay: false,
    shuffle: true,
    loop: true,
    crossfadeDuration: 1000,
    showTrackInfo: true,
    showVolumeSlider: true,
    showNextButton: true,
    compactMode: false,
    position: 'bottom-left',
};
