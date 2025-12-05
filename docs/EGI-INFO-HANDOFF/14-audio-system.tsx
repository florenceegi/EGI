/**
 * EGI-INFO HANDOFF - Audio System
 * 
 * File: src/audio/
 * - AudioContext.tsx
 * - AudioControls.tsx
 * - audioConfig.ts
 */

// ========================================
// FILE: src/audio/AudioContext.tsx
// ========================================

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

// Fisher-Yates shuffle
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
  const trackList = getActiveTrackList();
  const audioAvailable = isAudioEnabled();

  const [isMusicEnabled, setMusicEnabled] = useState(false);
  const [isSfxEnabled, setSfxEnabled] = useState(true);
  const [musicVolume, setMusicVolume] = useState(DEFAULT_MUSIC_VOLUME);
  const [sfxVolume, setSfxVolume] = useState(DEFAULT_SFX_VOLUME);
  const [currentPhase, setCurrentPhase] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTrackName, setCurrentTrackName] = useState('');
  const [currentArtist, setCurrentArtist] = useState('');

  const musicRef = useRef<HTMLAudioElement | null>(null);
  const sfxPoolRef = useRef<HTMLAudioElement[]>([]);
  const playlistRef = useRef<AudioTrack[]>([]);
  const currentIndexRef = useRef(0);

  // Initialize audio
  useEffect(() => {
    if (!audioAvailable) return;

    musicRef.current = new Audio();
    musicRef.current.volume = musicVolume;

    playlistRef.current = shuffleArray(trackList);
    currentIndexRef.current = 0;

    sfxPoolRef.current = Array.from({ length: 5 }, () => {
      const audio = new Audio();
      audio.volume = sfxVolume;
      return audio;
    });

    return () => {
      if (musicRef.current) {
        musicRef.current.pause();
        musicRef.current = null;
      }
      sfxPoolRef.current.forEach(audio => audio.pause());
      sfxPoolRef.current = [];
    };
  }, [audioAvailable]);

  // Handle track end
  useEffect(() => {
    const audio = musicRef.current;
    if (!audio || !audioAvailable) return;

    const handleTrackEnd = () => {
      if (isMusicEnabled) {
        currentIndexRef.current++;
        if (currentIndexRef.current >= playlistRef.current.length) {
          playlistRef.current = shuffleArray(trackList);
          currentIndexRef.current = 0;
        }
        const track = playlistRef.current[currentIndexRef.current];
        playTrackInternal(track);
      }
    };

    audio.addEventListener('ended', handleTrackEnd);
    return () => audio.removeEventListener('ended', handleTrackEnd);
  }, [isMusicEnabled, audioAvailable, trackList]);

  useEffect(() => {
    if (musicRef.current) musicRef.current.volume = musicVolume;
  }, [musicVolume]);

  useEffect(() => {
    sfxPoolRef.current.forEach(audio => { audio.volume = sfxVolume; });
  }, [sfxVolume]);

  const playTrackInternal = (track: AudioTrack) => {
    if (!musicRef.current || !track.url) return;

    musicRef.current.src = track.url;
    musicRef.current.volume = 0;
    setCurrentTrackName(track.displayName);
    setCurrentArtist(track.artist);

    musicRef.current.play().then(() => {
      setIsPlaying(true);
      const fadeInterval = setInterval(() => {
        if (!musicRef.current) { clearInterval(fadeInterval); return; }
        if (musicRef.current.volume < musicVolume) {
          musicRef.current.volume = Math.min(musicVolume, musicRef.current.volume + 0.02);
        } else {
          clearInterval(fadeInterval);
        }
      }, 50);
    }).catch(err => {
      console.log('Audio autoplay blocked:', err);
      setIsPlaying(false);
    });
  };

  const playTrack = useCallback((track: AudioTrack) => {
    if (!musicRef.current || !audioAvailable) return;

    if (isPlaying && musicRef.current.volume > 0) {
      const fadeOutInterval = setInterval(() => {
        if (!musicRef.current) { clearInterval(fadeOutInterval); return; }
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

  const playRandomTrack = useCallback(() => {
    if (!isMusicEnabled || !audioAvailable || trackList.length === 0) return;
    if (currentIndexRef.current >= playlistRef.current.length) {
      playlistRef.current = shuffleArray(trackList);
      currentIndexRef.current = 0;
    }
    const track = playlistRef.current[currentIndexRef.current];
    currentIndexRef.current++;
    playTrack(track);
  }, [isMusicEnabled, playTrack, audioAvailable, trackList]);

  const playNextTrack = useCallback(() => {
    if (!isMusicEnabled || !audioAvailable || trackList.length === 0) return;
    currentIndexRef.current++;
    if (currentIndexRef.current >= playlistRef.current.length) {
      playlistRef.current = shuffleArray(trackList);
      currentIndexRef.current = 0;
    }
    const track = playlistRef.current[currentIndexRef.current];
    playTrack(track);
  }, [isMusicEnabled, playTrack, audioAvailable, trackList]);

  const stopMusic = useCallback(() => {
    if (!musicRef.current) return;
    const fadeOutInterval = setInterval(() => {
      if (!musicRef.current) { clearInterval(fadeOutInterval); return; }
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

  const pauseMusic = useCallback(() => {
    if (!musicRef.current) return;
    musicRef.current.pause();
    setIsPlaying(false);
  }, []);

  const resumeMusic = useCallback(() => {
    if (!musicRef.current || !isMusicEnabled) return;
    musicRef.current.play().then(() => setIsPlaying(true)).catch(console.log);
  }, [isMusicEnabled]);

  const playSfx = useCallback((sfxId: string) => {
    if (!isSfxEnabled) return;
    const sfxUrl = SFX_LIBRARY[sfxId];
    if (!sfxUrl) return;
    const availableAudio = sfxPoolRef.current.find(a => a.paused || a.ended) || sfxPoolRef.current[0];
    if (availableAudio) {
      availableAudio.src = sfxUrl;
      availableAudio.currentTime = 0;
      availableAudio.play().catch(() => {});
    }
  }, [isSfxEnabled]);

  const handleSetMusicEnabled = useCallback((enabled: boolean) => {
    if (!audioAvailable) return;
    setMusicEnabled(enabled);
    if (!enabled) {
      stopMusic();
    } else {
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

// ========================================
// FILE: src/audio/AudioControls.tsx
// ========================================

/*
import React from 'react';
import { useAudio } from './AudioContext';
import './AudioControls.css';

export default function AudioControls() {
  const {
    isMusicEnabled,
    setMusicEnabled,
    musicVolume,
    setMusicVolume,
    isSfxEnabled,
    setSfxEnabled,
    sfxVolume,
    setSfxVolume,
    playSfx,
    currentTrackName,
    currentArtist,
    isPlaying,
    playNextTrack,
    isAudioAvailable,
    audioSource,
  } = useAudio();

  if (!isAudioAvailable) return null;

  const handleMusicToggle = () => {
    playSfx('click');
    setMusicEnabled(!isMusicEnabled);
  };

  const handleSfxToggle = () => {
    setSfxEnabled(!isSfxEnabled);
    if (!isSfxEnabled) setTimeout(() => playSfx('click'), 100);
  };

  const handleNextTrack = () => {
    playSfx('click');
    playNextTrack();
  };

  const sourceLabel = audioSource === 'local' ? '📀' : audioSource === 'royalty-free' ? '🌐' : '🤖';

  return (
    <div className="audio-controls">
      <button
        className={`audio-btn music-btn ${isMusicEnabled ? 'active' : ''}`}
        onClick={handleMusicToggle}
        title={isMusicEnabled ? 'Disattiva Musica' : 'Attiva Musica'}
      >
        {isMusicEnabled ? '🎵' : '🔇'}
      </button>

      {isMusicEnabled && isPlaying && (
        <button className="audio-btn next-btn" onClick={handleNextTrack} title="Prossimo Brano">
          ⏭️
        </button>
      )}

      {isMusicEnabled && currentTrackName && (
        <div className="current-track-info" title={`${currentTrackName} - ${currentArtist}`}>
          <span className="track-icon">♫</span>
          <div className="track-details">
            <span className="track-name">{currentTrackName}</span>
            {currentArtist && <span className="track-artist">{currentArtist}</span>}
          </div>
          <span className="source-badge" title={`Sorgente: ${audioSource}`}>{sourceLabel}</span>
        </div>
      )}

      {isMusicEnabled && (
        <div className="volume-slider-container music-volume">
          <input
            type="range" min="0" max="1" step="0.1" value={musicVolume}
            onChange={(e) => setMusicVolume(parseFloat(e.target.value))}
            className="volume-slider" title="Volume Musica"
          />
        </div>
      )}

      <button
        className={`audio-btn sfx-btn ${isSfxEnabled ? 'active' : ''}`}
        onClick={handleSfxToggle}
        title={isSfxEnabled ? 'Disattiva Effetti' : 'Attiva Effetti'}
      >
        {isSfxEnabled ? '🔊' : '🔈'}
      </button>

      {isSfxEnabled && (
        <div className="volume-slider-container sfx-volume">
          <input
            type="range" min="0" max="1" step="0.1" value={sfxVolume}
            onChange={(e) => setSfxVolume(parseFloat(e.target.value))}
            className="volume-slider" title="Volume Effetti"
          />
        </div>
      )}
    </div>
  );
}
*/
