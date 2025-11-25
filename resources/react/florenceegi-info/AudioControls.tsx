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

  // Se l'audio è disabilitato nella config, non mostrare nulla
  if (!isAudioAvailable) {
    return null;
  }

  const handleMusicToggle = () => {
    playSfx('click');
    setMusicEnabled(!isMusicEnabled);
  };

  const handleSfxToggle = () => {
    setSfxEnabled(!isSfxEnabled);
    if (!isSfxEnabled) {
      // Play a sound to confirm SFX is now on
      setTimeout(() => playSfx('click'), 100);
    }
  };

  const handleNextTrack = () => {
    playSfx('click');
    playNextTrack();
  };

  // Etichetta sorgente audio
  const sourceLabel = audioSource === 'local' ? '📀' : audioSource === 'royalty-free' ? '🌐' : '🤖';

  return (
    <div className="audio-controls">
      {/* Music Toggle */}
      <button
        className={`audio-btn music-btn ${isMusicEnabled ? 'active' : ''}`}
        onClick={handleMusicToggle}
        title={isMusicEnabled ? 'Disattiva Musica' : 'Attiva Musica'}
      >
        {isMusicEnabled ? '🎵' : '🔇'}
      </button>

      {/* Next Track Button (visible when music enabled and playing) */}
      {isMusicEnabled && isPlaying && (
        <button
          className="audio-btn next-btn"
          onClick={handleNextTrack}
          title="Prossimo Brano"
        >
          ⏭️
        </button>
      )}

      {/* Current Track Info (visible when music enabled) */}
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

      {/* Music Volume Slider (visible when music enabled) */}
      {isMusicEnabled && (
        <div className="volume-slider-container music-volume">
          <input
            type="range"
            min="0"
            max="1"
            step="0.1"
            value={musicVolume}
            onChange={(e) => setMusicVolume(parseFloat(e.target.value))}
            className="volume-slider"
            title="Volume Musica"
          />
        </div>
      )}

      {/* SFX Toggle */}
      <button
        className={`audio-btn sfx-btn ${isSfxEnabled ? 'active' : ''}`}
        onClick={handleSfxToggle}
        title={isSfxEnabled ? 'Disattiva Effetti' : 'Attiva Effetti'}
      >
        {isSfxEnabled ? '🔊' : '🔈'}
      </button>

      {/* SFX Volume Slider (visible when SFX enabled) */}
      {isSfxEnabled && (
        <div className="volume-slider-container sfx-volume">
          <input
            type="range"
            min="0"
            max="1"
            step="0.1"
            value={sfxVolume}
            onChange={(e) => setSfxVolume(parseFloat(e.target.value))}
            className="volume-slider"
            title="Volume Effetti"
          />
        </div>
      )}
    </div>
  );
}
