/**
 * EGI-INFO HANDOFF - AnimationContext
 * 
 * File: src/context/AnimationContext.tsx
 * 
 * Context per gestire le animazioni globalmente.
 * Permette di mettere in pausa o disabilitare completamente
 * le animazioni CSS per utenti che preferiscono ridurre il movimento.
 */

import React, { createContext, useContext, useState, useCallback } from 'react';

interface AnimationContextType {
  isPaused: boolean;
  isDisabled: boolean;
  togglePause: () => void;
  toggleDisabled: () => void;
  setPaused: (paused: boolean) => void;
  setDisabled: (disabled: boolean) => void;
}

const AnimationContext = createContext<AnimationContextType>({
  isPaused: false,
  isDisabled: false,
  togglePause: () => {},
  toggleDisabled: () => {},
  setPaused: () => {},
  setDisabled: () => {},
});

export function useAnimation() {
  return useContext(AnimationContext);
}

export function AnimationProvider({ children }: { children: React.ReactNode }) {
  const [isPaused, setIsPaused] = useState(false);
  const [isDisabled, setIsDisabled] = useState(false);

  const togglePause = useCallback(() => {
    setIsPaused(prev => !prev);
  }, []);

  const toggleDisabled = useCallback(() => {
    setIsDisabled(prev => !prev);
  }, []);

  const setPaused = useCallback((paused: boolean) => {
    setIsPaused(paused);
  }, []);

  const setDisabled = useCallback((disabled: boolean) => {
    setIsDisabled(disabled);
  }, []);

  return (
    <AnimationContext.Provider value={{
      isPaused,
      isDisabled,
      togglePause,
      toggleDisabled,
      setPaused,
      setDisabled
    }}>
      {children}
    </AnimationContext.Provider>
  );
}

export default AnimationContext;

// ========================================
// USAGE EXAMPLE
// ========================================
/*

// In App.tsx
import { AnimationProvider } from './context/AnimationContext';

function App() {
  return (
    <AnimationProvider>
      <YourComponents />
    </AnimationProvider>
  );
}

// In a component that uses animations
import { useAnimation } from './context/AnimationContext';

function AnimatedComponent() {
  const { isPaused, isDisabled, togglePause } = useAnimation();
  
  return (
    <div 
      className={`animated ${isPaused ? 'paused' : ''} ${isDisabled ? 'no-animation' : ''}`}
    >
      <button onClick={togglePause}>
        {isPaused ? 'Resume' : 'Pause'} Animations
      </button>
    </div>
  );
}

// CSS to support animation states
.animated {
  animation: fadeIn 1s ease;
}

.animated.paused {
  animation-play-state: paused;
}

.animated.no-animation,
.no-animation * {
  animation: none !important;
  transition: none !important;
}

*/
