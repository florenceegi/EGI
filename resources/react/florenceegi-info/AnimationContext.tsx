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
