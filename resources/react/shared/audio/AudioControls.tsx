/**
 * FlorenceEGI Audio System - Controls Component
 *
 * UI riutilizzabile per controlli audio.
 * Si adatta automaticamente alla configurazione del provider.
 */

import React, { useState } from 'react';
import { useAudioOptional } from './AudioContext';
import { AudioSource } from './types';
import './AudioControls.css';

interface AudioControlsProps {
  className?: string;
  showToggleButton?: boolean;
  position?: 'bottom-left' | 'bottom-right' | 'top-left' | 'top-right';
  compactMode?: boolean;
  theme?: 'light' | 'dark' | 'glass';
}

const getSourceIcon = (source: AudioSource): string => {
  switch (source) {
    case 'local': return '📀';
    case 'royalty-free': return '🌐';
    case 'ai-generated': return '🤖';
    case 'custom': return '🎵';
    default: return '🔇';
  }
};

const getSourceLabel = (source: AudioSource): string => {
  switch (source) {
    case 'local': return 'Local';
    case 'royalty-free': return 'Royalty Free';
    case 'ai-generated': return 'AI Generated';
    case 'custom': return 'Custom';
    default: return 'Disabled';
  }
};

export const AudioControls: React.FC<AudioControlsProps> = ({
  className = '',
  showToggleButton = true,
  position,
  compactMode,
  theme = 'glass',
}) => {
  const audio = useAudioOptional();
  const [expanded, setExpanded] = useState(false);

  // Se non c'è provider o l'audio è disabilitato, non renderizzare nulla
  if (!audio || audio.config.source === 'disabled' || audio.playlist.length === 0) {
    return null;
  }

  const {
    config,
    isMusicEnabled,
    setMusicEnabled,
    musicVolume,
    setMusicVolume,
    currentTrack,
    isPlaying,
    playNextTrack,
    playPreviousTrack,
    pauseMusic,
    resumeMusic,
    currentTime,
    duration,
  } = audio;

  // Override con props se forniti
  const finalPosition = position || config.position || 'bottom-left';
  const finalCompact = compactMode ?? config.compactMode ?? false;
  const showVolume = config.showVolumeSlider ?? true;
  const showNext = config.showNextButton ?? true;
  const showInfo = config.showTrackInfo ?? true;

  const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  const handleToggle = () => {
    setMusicEnabled(!isMusicEnabled);
  };

  const handlePlayPause = () => {
    if (isPlaying) {
      pauseMusic();
    } else {
      resumeMusic();
    }
  };

  // Compact mode - solo icona toggle
  if (finalCompact && !expanded) {
    return (
      <div className={`egi-audio-controls ${finalPosition} compact ${theme} ${className}`}>
        <button
          className="audio-toggle-btn"
          onClick={() => setExpanded(true)}
          title="Audio Controls"
        >
          {isMusicEnabled && isPlaying ? '🎵' : '🔇'}
        </button>
      </div>
    );
  }

  return (
    <div className={`egi-audio-controls ${finalPosition} ${theme} ${className}`}>
      {/* Close button for compact mode */}
      {finalCompact && expanded && (
        <button
          className="audio-close-btn"
          onClick={() => setExpanded(false)}
          title="Minimize"
        >
          ×
        </button>
      )}

      {/* Main toggle button */}
      {showToggleButton && (
        <button
          className={`audio-main-toggle ${isMusicEnabled ? 'active' : ''}`}
          onClick={handleToggle}
          title={isMusicEnabled ? 'Disable Music' : 'Enable Music'}
        >
          {isMusicEnabled ? '🔊' : '🔇'}
        </button>
      )}

      {/* Track info */}
      {showInfo && currentTrack && isMusicEnabled && (
        <div className="audio-track-info">
          <div className="track-name">{currentTrack.displayName}</div>
          <div className="track-artist">
            {currentTrack.artist}
            <span className="source-badge" title={getSourceLabel(config.source)}>
              {getSourceIcon(config.source)}
            </span>
          </div>
          {duration > 0 && (
            <div className="track-progress">
              {formatTime(currentTime)} / {formatTime(duration)}
            </div>
          )}
        </div>
      )}

      {/* Playback controls */}
      {isMusicEnabled && (
        <div className="audio-playback-controls">
          {showNext && (
            <button
              className="audio-btn prev"
              onClick={playPreviousTrack}
              title="Previous Track"
            >
              ⏮
            </button>
          )}

          <button
            className="audio-btn play-pause"
            onClick={handlePlayPause}
            title={isPlaying ? 'Pause' : 'Play'}
          >
            {isPlaying ? '⏸' : '▶'}
          </button>

          {showNext && (
            <button
              className="audio-btn next"
              onClick={playNextTrack}
              title="Next Track"
            >
              ⏭
            </button>
          )}
        </div>
      )}

      {/* Volume slider */}
      {showVolume && isMusicEnabled && (
        <div className="audio-volume-control">
          <span className="volume-icon">🔉</span>
          <input
            type="range"
            min="0"
            max="1"
            step="0.05"
            value={musicVolume}
            onChange={(e) => setMusicVolume(parseFloat(e.target.value))}
            className="volume-slider"
          />
          <span className="volume-value">{Math.round(musicVolume * 100)}%</span>
        </div>
      )}
    </div>
  );
};

export default AudioControls;
