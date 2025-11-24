import '@vitejs/plugin-react/preamble';
import * as React from 'react';
import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Canvas } from '@react-three/fiber';
import HomeSplashAnimation from './HomeSplashAnimation';

/**
 * HOME SPLASH APP - Splash screen 3D overlay
 *
 * Sequenza di esecuzione:
 * 1. La home si carica normalmente sotto
 * 2. Il canvas Three.js appare come overlay (z-index 9999)
 * 3. Dopo 4.5s inizia il fade out con opacity
 * 4. Dopo 5s rimuove completamente lo splash
 */

function SplashApp() {
  const [opacity, setOpacity] = useState(1);
  const [mounted, setMounted] = useState(true);

  useEffect(() => {
    // Inizia il fade out immediatamente (dissolvenza di 10 secondi)
    const fadeTimer = setTimeout(() => {
      setOpacity(0);
      // Mostra il body quando inizia il fade out
      document.body.style.visibility = 'visible';
    }, 0);

    // Rimuovi completamente dopo 10 secondi
    const removeTimer = setTimeout(() => {
      setMounted(false);
    }, 10000);

    return () => {
      clearTimeout(fadeTimer);
      clearTimeout(removeTimer);
    };
  }, []);

  // Quando lo splash è completato, non renderiamo più nulla
  if (!mounted) {
    return null;
  }

  return (
    <div
      style={{
        position: 'fixed',
        inset: 0,
        zIndex: 9999,
        opacity: opacity,
        transition: 'opacity 10000ms ease-out',
        pointerEvents: opacity > 0 ? 'auto' : 'none',
      }}
    >
      <Canvas
        camera={{ position: [0, 0, 5], fov: 45 }}
        gl={{ antialias: true, alpha: true }}
        dpr={[1, 2]}
        style={{
          width: '100%',
          height: '100%',
        }}
      >
        <HomeSplashAnimation />
      </Canvas>

      {/* Testo sopra l'animazione 3D */}
      <div
        style={{
          position: 'absolute',
          top: '50%',
          left: '50%',
          transform: 'translate(-50%, -50%)',
          zIndex: 10000,
          textAlign: 'center',
          pointerEvents: 'none',
          padding: '0 1rem',
          width: '100%',
          maxWidth: '90vw',
        }}
      >
        <h1
          style={{
            fontSize: 'clamp(1.5rem, 5vw, 3.5rem)',
            fontWeight: 'bold',
            color: '#ffffff',
            textShadow: '0 0 20px rgba(0, 255, 255, 0.8), 0 0 40px rgba(255, 0, 255, 0.6)',
            letterSpacing: '0.05em',
            fontFamily: 'JetBrains Mono, monospace',
            marginBottom: 'clamp(0.5rem, 2vw, 1rem)',
            animation: 'pulse 2s ease-in-out infinite',
            lineHeight: '1.2',
          }}
        >
          Caricamento della magia...
        </h1>
        <p
          style={{
            fontSize: 'clamp(0.875rem, 3vw, 1.2rem)',
            color: '#00ffff',
            fontFamily: 'JetBrains Mono, monospace',
            opacity: 0.8,
            lineHeight: '1.4',
          }}
        >
          ✨ FlorenceEGI si sta preparando per te ✨
        </p>
      </div>

      {/* Animazione CSS per il pulse */}
      <style>{`
        @keyframes pulse {
          0%, 100% {
            opacity: 1;
            transform: scale(1);
          }
          50% {
            opacity: 0.8;
            transform: scale(1.05);
          }
        }
      `}</style>
    </div>
  );
}

// Montaggio dell'applicazione splash
function mountSplash() {
  const container = document.getElementById('home-splash-root');
  if (container) {
    const root = createRoot(container);
    root.render(<SplashApp />);
  } else {
    // Riprova dopo 50ms se il container non è ancora pronto
    setTimeout(mountSplash, 50);
  }
}

// Monta immediatamente se il DOM è già pronto, altrimenti aspetta
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountSplash);
} else {
  mountSplash();
}
