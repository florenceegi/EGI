import React, { createContext, useContext, useState, useRef, useEffect, useCallback } from 'react';
import {
  getActiveTrackList,
  isAudioEnabled,
  AudioTrack,
  DEFAULT_MUSIC_VOLUME,
  DEFAULT_SFX_VOLUME,
  SFX_LIBRARY,
  AUDIO_SOURCE,
} from './audioConfig';

interface AudioContextType {
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
  playRandomTrack: () => void;
  playNextTrack: () => void;
  stopMusic: () => void;
  pauseMusic: () => void;
  resumeMusic: () => void;
  playSfx: (sfxId: string) => void;

  // Current track info
  currentTrackName: string;
  currentArtist: string;
  isPlaying: boolean;

  // Audio source info
  audioSource: string;
  isAudioAvailable: boolean;

  // Phase-based audio
  currentPhase: number;
  setCurrentPhase: (phase: number) => void;
}

const AudioContext = createContext<AudioContextType | null>(null);

export function useAudio() {
  const context = useContext(AudioContext);
  if (!context) {
    throw new Error('useAudio must be used within AudioProvider');
  }
  return context;
}

// Optional hook that doesn't throw if outside provider
export function useAudioOptional() {
  return useContext(AudioContext);
}

// Funzione per mescolare array (Fisher-Yates shuffle)
function shuffleArray<T>(array: T[]): T[] {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
}

interface AudioProviderProps {
  children: React.ReactNode;
}

export function AudioProvider({ children }: AudioProviderProps) {
  // Get track list from config
  const trackList = getActiveTrackList();
  const audioAvailable = isAudioEnabled();

  // State
  const [isMusicEnabled, setMusicEnabled] = useState(false); // Default off per UX
  const [isSfxEnabled, setSfxEnabled] = useState(true);
  const [musicVolume, setMusicVolume] = useState(DEFAULT_MUSIC_VOLUME);
  const [sfxVolume, setSfxVolume] = useState(DEFAULT_SFX_VOLUME);
  const [currentPhase, setCurrentPhase] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTrackName, setCurrentTrackName] = useState('');
  const [currentArtist, setCurrentArtist] = useState('');

  // Refs
  const musicRef = useRef<HTMLAudioElement | null>(null);
  const sfxPoolRef = useRef<HTMLAudioElement[]>([]);
  const playlistRef = useRef<AudioTrack[]>([]);
  const currentIndexRef = useRef(0);

  // Initialize audio elements and shuffle playlist
  useEffect(() => {
    if (!audioAvailable) return;

    // Create music audio element
    musicRef.current = new Audio();
    musicRef.current.volume = musicVolume;

    // Shuffle playlist on mount
    playlistRef.current = shuffleArray(trackList);
    currentIndexRef.current = 0;

    // Create SFX pool (multiple audio elements for overlapping sounds)
    sfxPoolRef.current = Array.from({ length: 5 }, () => {
      const audio = new Audio();
      audio.volume = sfxVolume;
      return audio;
    });

    return () => {
      // Cleanup
      if (musicRef.current) {
        musicRef.current.pause();
        musicRef.current = null;
      }
      sfxPoolRef.current.forEach(audio => {
        audio.pause();
      });
      sfxPoolRef.current = [];
    };
  }, [audioAvailable]);

  // Handle track end - play next random track
  useEffect(() => {
    const audio = musicRef.current;
    if (!audio || !audioAvailable) return;

    const handleTrackEnd = () => {
      if (isMusicEnabled) {
        // Avanza al prossimo brano
        currentIndexRef.current++;

        // Se playlist esaurita, rimescola
        if (currentIndexRef.current >= playlistRef.current.length) {
          playlistRef.current = shuffleArray(trackList);
          currentIndexRef.current = 0;
        }

        const track = playlistRef.current[currentIndexRef.current];
        playTrackInternal(track);
      }
    };

    audio.addEventListener('ended', handleTrackEnd);

    return () => {
      audio.removeEventListener('ended', handleTrackEnd);
    };
  }, [isMusicEnabled, audioAvailable, trackList]);

  // Update music volume when changed
  useEffect(() => {
    if (musicRef.current) {
      musicRef.current.volume = musicVolume;
    }
  }, [musicVolume]);

  // Update SFX volume when changed
  useEffect(() => {
    sfxPoolRef.current.forEach(audio => {
      audio.volume = sfxVolume;
    });
  }, [sfxVolume]);

  // Internal function to play a track
  const playTrackInternal = (track: AudioTrack) => {
    if (!musicRef.current || !track.url) return;

    musicRef.current.src = track.url;
    musicRef.current.volume = 0;
    setCurrentTrackName(track.displayName);
    setCurrentArtist(track.artist);

    musicRef.current.play().then(() => {
      setIsPlaying(true);
      // Fade in
      const fadeInterval = setInterval(() => {
        if (!musicRef.current) {
          clearInterval(fadeInterval);
          return;
        }
        if (musicRef.current.volume < musicVolume) {
          musicRef.current.volume = Math.min(musicVolume, musicRef.current.volume + 0.02);
        } else {
          clearInterval(fadeInterval);
        }
      }, 50);
    }).catch(err => {
      console.log('Audio autoplay blocked - user must interact first:', err);
      setIsPlaying(false);
    });
  };

  // Play a specific track with fade-in/fade-out
  const playTrack = useCallback((track: AudioTrack) => {
    if (!musicRef.current || !audioAvailable) return;

    if (isPlaying && musicRef.current.volume > 0) {
      // Fade out first
      const fadeOutInterval = setInterval(() => {
        if (!musicRef.current) {
          clearInterval(fadeOutInterval);
          return;
        }
        if (musicRef.current.volume > 0.02) {
          musicRef.current.volume = Math.max(0, musicRef.current.volume - 0.02);
        } else {
          clearInterval(fadeOutInterval);
          musicRef.current.pause();
          playTrackInternal(track);
        }
      }, 30);
    } else {
      playTrackInternal(track);
    }
  }, [isPlaying, musicVolume, audioAvailable]);

  // Play random track from playlist
  const playRandomTrack = useCallback(() => {
    if (!isMusicEnabled || !audioAvailable || trackList.length === 0) return;

    // Se playlist esaurita, rimescola
    if (currentIndexRef.current >= playlistRef.current.length) {
      playlistRef.current = shuffleArray(trackList);
      currentIndexRef.current = 0;
    }

    const track = playlistRef.current[currentIndexRef.current];
    currentIndexRef.current++;
    playTrack(track);
  }, [isMusicEnabled, playTrack, audioAvailable, trackList]);

  // Play next track in shuffled playlist
  const playNextTrack = useCallback(() => {
    if (!isMusicEnabled || !audioAvailable || trackList.length === 0) return;

    currentIndexRef.current++;

    // Se playlist esaurita, rimescola
    if (currentIndexRef.current >= playlistRef.current.length) {
      playlistRef.current = shuffleArray(trackList);
      currentIndexRef.current = 0;
    }

    const track = playlistRef.current[currentIndexRef.current];
    playTrack(track);
  }, [isMusicEnabled, playTrack, audioAvailable, trackList]);

  // Stop music
  const stopMusic = useCallback(() => {
    if (!musicRef.current) return;

    // Fade out
    const fadeOutInterval = setInterval(() => {
      if (!musicRef.current) {
        clearInterval(fadeOutInterval);
        return;
      }
      if (musicRef.current.volume > 0.02) {
        musicRef.current.volume = Math.max(0, musicRef.current.volume - 0.03);
      } else {
        clearInterval(fadeOutInterval);
        musicRef.current.pause();
        musicRef.current.currentTime = 0;
        setIsPlaying(false);
        setCurrentTrackName('');
        setCurrentArtist('');
      }
    }, 30);
  }, []);

  // Pause music
  const pauseMusic = useCallback(() => {
    if (!musicRef.current) return;
    musicRef.current.pause();
    setIsPlaying(false);
  }, []);

  // Resume music
  const resumeMusic = useCallback(() => {
    if (!musicRef.current || !isMusicEnabled) return;
    musicRef.current.play().then(() => {
      setIsPlaying(true);
    }).catch(err => {
      console.log('Audio resume blocked:', err);
    });
  }, [isMusicEnabled]);

  // Play SFX
  const playSfx = useCallback((sfxId: string) => {
    if (!isSfxEnabled) return;

    const sfxUrl = SFX_LIBRARY[sfxId];
    if (!sfxUrl) {
      // SFX file not found - that's ok, sfx are optional
      return;
    }

    // Find available audio element from pool
    const availableAudio = sfxPoolRef.current.find(
      audio => audio.paused || audio.ended
    ) || sfxPoolRef.current[0];

    if (availableAudio) {
      availableAudio.src = sfxUrl;
      availableAudio.currentTime = 0;
      availableAudio.play().catch(err => {
        // SFX play failed - not critical
      });
    }
  }, [isSfxEnabled]);

  // Handle music enable/disable
  const handleSetMusicEnabled = useCallback((enabled: boolean) => {
    if (!audioAvailable) return;

    setMusicEnabled(enabled);
    if (!enabled) {
      stopMusic();
    } else {
      // Start playing random track when enabled
      // Rimescola per varietà
      playlistRef.current = shuffleArray(trackList);
      currentIndexRef.current = 0;
      const track = playlistRef.current[currentIndexRef.current];
      currentIndexRef.current++;
      playTrack(track);
    }
  }, [stopMusic, playTrack, audioAvailable, trackList]);

  const value: AudioContextType = {
    isMusicEnabled,
    setMusicEnabled: handleSetMusicEnabled,
    musicVolume,
    setMusicVolume,
    isSfxEnabled,
    setSfxEnabled,
    sfxVolume,
    setSfxVolume,
    playRandomTrack,
    playNextTrack,
    stopMusic,
    pauseMusic,
    resumeMusic,
    playSfx,
    currentTrackName,
    currentArtist,
    isPlaying,
    audioSource: AUDIO_SOURCE,
    isAudioAvailable: audioAvailable,
    currentPhase,
    setCurrentPhase,
  };

  return (
    <AudioContext.Provider value={value}>
      {children}
    </AudioContext.Provider>
  );
}
