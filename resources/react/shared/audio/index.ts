/**
 * FlorenceEGI Audio System
 * 
 * Sistema audio condiviso per tutta l'applicazione EGI.
 * 
 * USAGE:
 * 
 * ```tsx
 * import { AudioProvider, AudioControls, useAudio, AUDIO_PRESET_TECH } from '@/shared/audio';
 * 
 * // Con preset
 * <AudioProvider config={AUDIO_PRESET_TECH}>
 *   <App />
 *   <AudioControls />
 * </AudioProvider>
 * 
 * // Con config custom
 * <AudioProvider config={{
 *   source: 'royalty-free',
 *   tracks: myCustomTracks,
 *   autoPlay: false,
 *   shuffle: true,
 * }}>
 *   <App />
 *   <AudioControls position="bottom-right" theme="dark" />
 * </AudioProvider>
 * 
 * // Hook per controllo programmatico
 * const { playTrack, pauseMusic, currentTrack } = useAudio();
 * ```
 */

// Types
export type {
    AudioSource,
    AudioTrack,
    AudioConfig,
    AudioContextType,
} from './types';

export { DEFAULT_AUDIO_CONFIG } from './types';

// Context & Provider
export { AudioProvider, useAudio, useAudioOptional } from './AudioContext';

// UI Components
export { AudioControls } from './AudioControls';

// Presets & Track Libraries
export {
    // Track libraries
    PUBLIC_DOMAIN_CLASSICAL,
    ROYALTY_FREE_AMBIENT,
    ROYALTY_FREE_ELECTRONIC,
    ROYALTY_FREE_TECH,
    ALL_ROYALTY_FREE_TRACKS,
    // Config presets
    AUDIO_PRESET_AMBIENT,
    AUDIO_PRESET_CLASSICAL,
    AUDIO_PRESET_TECH,
    AUDIO_PRESET_LOFI,
} from './presets';

// Default export
export { AudioProvider as default } from './AudioContext';
