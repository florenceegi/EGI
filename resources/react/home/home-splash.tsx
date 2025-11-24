import '@vitejs/plugin-react/preamble';
import * as React from 'react';
import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Canvas } from '@react-three/fiber';
import HomeSplashAnimation from './HomeSplashAnimation';
import ImageRain from './ImageRain';

/**
 * HOME SPLASH APP - Splash screen 3D overlay + Image Rain
 *
 * Sequenza di esecuzione:
 * 1. La home si carica normalmente sotto
 * 2. Il canvas Three.js appare come overlay (z-index 9999)
 * 3. Dopo 10s lo splash si dissolve
 * 4. Parte la pioggia di immagini NFT
 * 5. Dopo altri 8s la pioggia si dissolve e appare la home
 */

function SplashApp({ onComplete }: { onComplete?: () => void }) {
  const [opacity, setOpacity] = useState(1);
  const [mounted, setMounted] = useState(true);

  useEffect(() => {
    // Inizia il fade out immediatamente (dissolvenza di 8 secondi)
    const fadeTimer = setTimeout(() => {
      setOpacity(0);
    }, 0);

    // Dopo 6 secondi (mentre sta finendo) rimuovi splash e notifica completamento
    const removeTimer = setTimeout(() => {
      setMounted(false);
      if (onComplete) {
        onComplete();
      }
    }, 6000);

    return () => {
      clearTimeout(fadeTimer);
      clearTimeout(removeTimer);
    };
  }, [onComplete]);

  // Quando lo splash è completato, non renderiamo più nulla
  if (!mounted) {
    return null;
  }

  return (
    <div
      style={{
        position: 'fixed',
        inset: 0,
        zIndex: opacity > 0.01 ? 9999 : -1, // Abbassa z-index quando quasi trasparente
        opacity: opacity,
        transition: 'opacity 8000ms ease-out',
        pointerEvents: 'none', // Sempre none per permettere click-through
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

// Overlay che copre la home durante splash e pioggia
function HomeOverlay({ opacity }: { opacity: number }) {
  useEffect(() => {
    // Crea un overlay separato fuori da #home-splash-root
    const overlay = document.createElement('div');
    overlay.id = 'home-black-overlay';
    overlay.style.cssText = `
      position: fixed;
      inset: 0;
      background-color: #0a0a0a;
      opacity: ${opacity};
      transition: opacity 13000ms ease-in;
      z-index: 9990;
      pointer-events: none;
    `;
    document.body.appendChild(overlay);

    return () => {
      overlay.remove();
    };
  }, []);

  // Aggiorna opacity quando cambia
  useEffect(() => {
    const overlay = document.getElementById('home-black-overlay');
    if (overlay) {
      overlay.style.opacity = opacity.toString();
    }
  }, [opacity]);

  return null; // Non renderizza nulla in React, usa DOM diretto
}

// Componente principale che gestisce splash + pioggia
function MainApp() {
  const [showRain, setShowRain] = useState(false);
  const [overlayOpacity, setOverlayOpacity] = useState(1); // Overlay nero che copre tutto

  useEffect(() => {
    // Rimuovi il loading iniziale appena React si monta
    const initialOverlay = document.getElementById('home-initial-overlay');
    if (initialOverlay) {
      initialOverlay.remove();
    }

    // Aggiungi classe al body per prevenire scroll
    document.body.classList.add('splash-active');
  }, []);

  const handleSplashComplete = () => {
    setShowRain(true);

    // Inizia a far schiarire l'overlay dopo 6.5 secondi (metà percorso della pioggia)
    setTimeout(() => {
      setOverlayOpacity(0);
    }, 6500);
  };

  const handleRainComplete = () => {
    // Rimuovi la classe dal body
    document.body.classList.remove('splash-active');

    // Rimuovi il container della pioggia
    const rainContainer = document.getElementById('image-rain-root');
    if (rainContainer) {
      rainContainer.remove();
    }

    // Rimuovi l'overlay nero immediatamente
    const blackOverlay = document.getElementById('home-black-overlay');
    if (blackOverlay) {
      blackOverlay.remove();
    }

    // Rimuovi anche il container principale dello splash
    const splashRoot = document.getElementById('home-splash-root');
    if (splashRoot) {
      splashRoot.remove();
    }
  };

  return (
    <>
      <HomeOverlay opacity={overlayOpacity} />
      <SplashApp onComplete={handleSplashComplete} />
      {showRain && (
        <div id="image-rain-root">
          <ImageRain onComplete={handleRainComplete} duration={13000} />
        </div>
      )}
    </>
  );
}

// Montaggio dell'applicazione splash
function mountSplash() {
  const container = document.getElementById('home-splash-root');
  if (container) {
    const root = createRoot(container);
    root.render(<MainApp />);
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
