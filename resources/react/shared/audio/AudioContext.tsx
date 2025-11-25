/**
 * FlorenceEGI Audio System - Context Provider
 *
 * Provider riutilizzabile per gestire audio in tutta l'app.
 * Inietta la configurazione via props per massima flessibilità.
 */

import React, { createContext, useContext, useState, useRef, useCallback, useEffect } from 'react';
import { AudioConfig, AudioContextType, AudioTrack, DEFAULT_AUDIO_CONFIG } from './types';

const EgiAudioContext = createContext<AudioContextType | null>(null);

export const useAudio = (): AudioContextType => {
  const context = useContext(EgiAudioContext);
  if (!context) {
    throw new Error('useAudio must be used within an AudioProvider');
  }
  return context;
};

// Hook opzionale che non lancia errore se usato fuori dal provider
export const useAudioOptional = (): AudioContextType | null => {
  return useContext(EgiAudioContext);
};

interface AudioProviderProps {
  children: React.ReactNode;
  config?: Partial<AudioConfig>;
  onTrackChange?: (track: AudioTrack | null) => void;
  onPlayStateChange?: (isPlaying: boolean) => void;
}

// Fisher-Yates shuffle
const shuffleArray = <T,>(array: T[]): T[] => {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
};

export const AudioProvider: React.FC<AudioProviderProps> = ({
  children,
  config: providedConfig,
  onTrackChange,
  onPlayStateChange,
}) => {
  // Merge config with defaults
  const config: AudioConfig = {
    ...DEFAULT_AUDIO_CONFIG,
    ...providedConfig,
  };

  // State
  const [isMusicEnabled, setMusicEnabled] = useState(config.autoPlay ?? false);
  const [musicVolume, setMusicVolume] = useState(config.defaultMusicVolume ?? 0.3);
  const [isSfxEnabled, setSfxEnabled] = useState(true);
  const [sfxVolume, setSfxVolume] = useState(config.defaultSfxVolume ?? 0.5);
  const [currentTrack, setCurrentTrack] = useState<AudioTrack | null>(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);
  const [playlist, setPlaylist] = useState<AudioTrack[]>([]);
  const [playlistIndex, setPlaylistIndex] = useState(-1);

  // Refs
  const audioRef = useRef<HTMLAudioElement | null>(null);
  const sfxCacheRef = useRef<Map<string, HTMLAudioElement>>(new Map());
  const isInitializedRef = useRef(false);
  const fadeIntervalRef = useRef<NodeJS.Timeout | null>(null);

  // Initialize playlist on mount
  useEffect(() => {
    if (config.source === 'disabled' || !config.tracks.length) {
      return;
    }

    const tracks = config.shuffle ? shuffleArray(config.tracks) : [...config.tracks];
    setPlaylist(tracks);

    if (tracks.length > 0) {
      setPlaylistIndex(0);
      setCurrentTrack(tracks[0]);
    }
  }, [config.tracks, config.shuffle, config.source]);

  // Create/update audio element
  useEffect(() => {
    if (!audioRef.current) {
      audioRef.current = new Audio();
    }

    const audio = audioRef.current;

    const handleEnded = () => {
      if (config.loop && playlist.length > 0) {
        playNextTrack();
      } else {
        setIsPlaying(false);
        onPlayStateChange?.(false);
      }
    };

    const handleTimeUpdate = () => {
      setCurrentTime(audio.currentTime);
    };

    const handleLoadedMetadata = () => {
      setDuration(audio.duration);
    };

    const handleCanPlay = () => {
      if (isMusicEnabled && !isInitializedRef.current) {
        isInitializedRef.current = true;
        fadeIn();
      }
    };

    audio.addEventListener('ended', handleEnded);
    audio.addEventListener('timeupdate', handleTimeUpdate);
    audio.addEventListener('loadedmetadata', handleLoadedMetadata);
    audio.addEventListener('canplay', handleCanPlay);

    return () => {
      audio.removeEventListener('ended', handleEnded);
      audio.removeEventListener('timeupdate', handleTimeUpdate);
      audio.removeEventListener('loadedmetadata', handleLoadedMetadata);
      audio.removeEventListener('canplay', handleCanPlay);
    };
  }, [playlist, isMusicEnabled, config.loop]);

  // Load current track
  useEffect(() => {
    if (!currentTrack || !audioRef.current) return;

    const url = currentTrack.url || (currentTrack.filename ? `/audio/${currentTrack.filename}` : null);
    if (!url) return;

    audioRef.current.src = url;
    audioRef.current.load();

    onTrackChange?.(currentTrack);
  }, [currentTrack, onTrackChange]);

  // Handle volume changes
  useEffect(() => {
    if (audioRef.current) {
      audioRef.current.volume = musicVolume;
    }
  }, [musicVolume]);

  // Handle music enable/disable
  useEffect(() => {
    if (!audioRef.current || !currentTrack) return;

    if (isMusicEnabled) {
      fadeIn();
    } else {
      fadeOut();
    }
  }, [isMusicEnabled, currentTrack]);

  // Fade in
  const fadeIn = useCallback(() => {
    if (!audioRef.current) return;

    const audio = audioRef.current;
    const targetVolume = musicVolume;
    const duration = config.crossfadeDuration ?? 1000;
    const steps = 20;
    const stepTime = duration / steps;
    const volumeStep = targetVolume / steps;

    audio.volume = 0;
    audio.play().catch(console.warn);
    setIsPlaying(true);
    onPlayStateChange?.(true);

    let currentStep = 0;

    if (fadeIntervalRef.current) {
      clearInterval(fadeIntervalRef.current);
    }

    fadeIntervalRef.current = setInterval(() => {
      currentStep++;
      if (currentStep >= steps) {
        audio.volume = targetVolume;
        if (fadeIntervalRef.current) {
          clearInterval(fadeIntervalRef.current);
          fadeIntervalRef.current = null;
        }
      } else {
        audio.volume = volumeStep * currentStep;
      }
    }, stepTime);
  }, [musicVolume, config.crossfadeDuration, onPlayStateChange]);

  // Fade out
  const fadeOut = useCallback(() => {
    if (!audioRef.current) return;

    const audio = audioRef.current;
    const duration = config.crossfadeDuration ?? 1000;
    const steps = 20;
    const stepTime = duration / steps;
    const volumeStep = audio.volume / steps;

    let currentStep = 0;

    if (fadeIntervalRef.current) {
      clearInterval(fadeIntervalRef.current);
    }

    fadeIntervalRef.current = setInterval(() => {
      currentStep++;
      if (currentStep >= steps) {
        audio.volume = 0;
        audio.pause();
        setIsPlaying(false);
        onPlayStateChange?.(false);
        if (fadeIntervalRef.current) {
          clearInterval(fadeIntervalRef.current);
          fadeIntervalRef.current = null;
        }
      } else {
        audio.volume = Math.max(0, volumeStep * (steps - currentStep));
      }
    }, stepTime);
  }, [config.crossfadeDuration, onPlayStateChange]);

  // Play a specific track by ID
  const playTrack = useCallback((trackId: string) => {
    const index = playlist.findIndex(t => t.id === trackId);
    if (index >= 0) {
      setPlaylistIndex(index);
      setCurrentTrack(playlist[index]);
      setMusicEnabled(true);
    }
  }, [playlist]);

  // Play random track
  const playRandomTrack = useCallback(() => {
    if (playlist.length === 0) return;

    const randomIndex = Math.floor(Math.random() * playlist.length);
    setPlaylistIndex(randomIndex);
    setCurrentTrack(playlist[randomIndex]);
    setMusicEnabled(true);
  }, [playlist]);

  // Play next track
  const playNextTrack = useCallback(() => {
    if (playlist.length === 0) return;

    const nextIndex = (playlistIndex + 1) % playlist.length;
    setPlaylistIndex(nextIndex);
    setCurrentTrack(playlist[nextIndex]);

    if (isMusicEnabled && audioRef.current) {
      // Small delay to let the track load
      setTimeout(() => {
        audioRef.current?.play().catch(console.warn);
      }, 100);
    }
  }, [playlist, playlistIndex, isMusicEnabled]);

  // Play previous track
  const playPreviousTrack = useCallback(() => {
    if (playlist.length === 0) return;

    const prevIndex = (playlistIndex - 1 + playlist.length) % playlist.length;
    setPlaylistIndex(prevIndex);
    setCurrentTrack(playlist[prevIndex]);

    if (isMusicEnabled && audioRef.current) {
      setTimeout(() => {
        audioRef.current?.play().catch(console.warn);
      }, 100);
    }
  }, [playlist, playlistIndex, isMusicEnabled]);

  // Stop music
  const stopMusic = useCallback(() => {
    if (audioRef.current) {
      fadeOut();
    }
  }, [fadeOut]);

  // Pause music
  const pauseMusic = useCallback(() => {
    if (audioRef.current) {
      audioRef.current.pause();
      setIsPlaying(false);
      onPlayStateChange?.(false);
    }
  }, [onPlayStateChange]);

  // Resume music
  const resumeMusic = useCallback(() => {
    if (audioRef.current && currentTrack) {
      audioRef.current.play().catch(console.warn);
      setIsPlaying(true);
      onPlayStateChange?.(true);
    }
  }, [currentTrack, onPlayStateChange]);

  // Play SFX
  const playSfx = useCallback((sfxId: string) => {
    if (!isSfxEnabled || !config.sfx) return;

    const sfxUrl = config.sfx[sfxId];
    if (!sfxUrl) return;

    // Use cached audio element or create new one
    let sfxAudio = sfxCacheRef.current.get(sfxId);
    if (!sfxAudio) {
      sfxAudio = new Audio(sfxUrl);
      sfxCacheRef.current.set(sfxId, sfxAudio);
    }

    sfxAudio.volume = sfxVolume;
    sfxAudio.currentTime = 0;
    sfxAudio.play().catch(console.warn);
  }, [isSfxEnabled, sfxVolume, config.sfx]);

  // Seek
  const seekTo = useCallback((time: number) => {
    if (audioRef.current) {
      audioRef.current.currentTime = time;
    }
  }, []);

  // Context value
  const contextValue: AudioContextType = {
    config,
    isMusicEnabled,
    setMusicEnabled,
    musicVolume,
    setMusicVolume,
    isSfxEnabled,
    setSfxEnabled,
    sfxVolume,
    setSfxVolume,
    playTrack,
    playRandomTrack,
    playNextTrack,
    playPreviousTrack,
    stopMusic,
    pauseMusic,
    resumeMusic,
    playSfx,
    currentTrack,
    isPlaying,
    currentTime,
    duration,
    playlist,
    playlistIndex,
    seekTo,
  };

  return (
    <EgiAudioContext.Provider value={contextValue}>
      {children}
    </EgiAudioContext.Provider>
  );
};

export default AudioProvider;
