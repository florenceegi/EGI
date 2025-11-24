import * as React from 'react';
import { useState, useEffect, useRef } from 'react';

/**
 * IMAGE RAIN ANIMATION - FLORENCEEGI
 *
 * Pioggia di immagini NFT che cade dopo lo splash screen
 * - Immagini cadono dritte con scia luminosa
 * - Velocità lenta e costante
 * - Dissolvenza dopo alcuni secondi
 */

interface ImageParticle {
  id: number;
  imageUrl: string;
  x: number;
  y: number;
  speed: number;
  delay: number;
  size: number;
}

interface ImageRainProps {
  onComplete?: () => void;
  duration?: number; // Durata totale in ms
}

const ImageRain: React.FC<ImageRainProps> = ({ onComplete, duration = 8000 }) => {
  const [particles, setParticles] = useState<ImageParticle[]>([]);
  const [opacity, setOpacity] = useState(1);
  const [mounted, setMounted] = useState(true);
  const animationFrameRef = useRef<number>();

  // Fetch immagini dagli utenti
  useEffect(() => {
    const fetchUserImages = async () => {
      const isMobile = window.innerWidth < 768;

      try {
        // Trova tutte le EGI card visibili nella home
        const egiCards = document.querySelectorAll('.egi-card');
        console.log('🎴 Found EGI cards:', egiCards.length);

        if (egiCards.length === 0) {
          throw new Error('No EGI cards found in home');
        }

        // MOBILE: 5 colonne sovrapposte, più piccole, più veloci
        if (isMobile) {
          const response = await fetch('/api/random-egi-images?limit=15');
          const data = await response.json();
          const imageUrls = data.images || [];

          const columns = 5;
          const mobileParticles: ImageParticle[] = Array.from({ length: 15 }, (_, i) => {
            const columnWidth = 100 / columns;
            const column = i % columns;
            // Sovrapposizione: aggiungi offset casuale ±5%
            const baseX = (column * columnWidth) + (columnWidth / 2);
            const xPos = baseX + (Math.random() * 10 - 5);

            return {
              id: i,
              imageUrl: imageUrls[i] || '/images/logo/logo_1.webp',
              x: xPos,
              y: -10 - (Math.floor(i / columns) * 40), // Più vicine verticalmente
              speed: 0.3 + (Math.random() * 0.1), // Più veloci
              delay: (i % columns) * 300, // Delay più breve
              size: 100, // Molto più piccole
            };
          });

          setParticles(mobileParticles);
          return;
        }

        // DESKTOP: come prima, allineate alle card
        const cardData = Array.from(egiCards).slice(0, 15).map((card, index) => {
          const img = card.querySelector('img');
          const rect = card.getBoundingClientRect();
          const imageUrl = img?.src || '/images/logo/logo_1.webp';

          // Calcola posizione X in percentuale rispetto al viewport
          const xPercent = ((rect.left + rect.width / 2) / window.innerWidth) * 100;

          return {
            id: index,
            imageUrl: imageUrl,
            x: xPercent, // Posizione X allineata alla card
            y: -10 - (Math.random() * 50), // Piccola variazione casuale in verticale
            speed: 0.15 + (Math.random() * 0.05), // Velocità molto lenta (13s per attraversare schermo)
            delay: Math.random() * 500, // Delay casuale molto breve (max 500ms)
            size: 280, // Stessa dimensione delle card
          };
        });

        console.log('🌧️ Rain particles aligned to cards:', cardData.length);
        setParticles(cardData);

      } catch (error) {
        console.error('Error fetching card positions, using API fallback:', error);

        // Fallback: usa API per le immagini
        try {
          const response = await fetch('/api/random-egi-images?limit=15');
          if (!response.ok) throw new Error('API failed');

          const data = await response.json();
          const imageUrls = data.images || [];

          // Sistema a colonne
          const columns = isMobile ? 5 : 4;
          const fallbackParticles: ImageParticle[] = Array.from({ length: 15 }, (_, i) => {
            const columnWidth = 100 / columns;
            const column = i % columns;
            const baseX = (column * columnWidth) + (columnWidth / 2) - 10;
            const xPos = isMobile ? baseX + (Math.random() * 10 - 5) : baseX;

            return {
              id: i,
              imageUrl: imageUrls[i] || '/images/logo/logo_1.webp',
              x: xPos,
              y: -10 - (Math.floor(i / columns) * (isMobile ? 40 : 80)),
              speed: isMobile ? 0.3 + (Math.random() * 0.1) : 0.4,
              delay: (i % columns) * (isMobile ? 300 : 500),
              size: isMobile ? 100 : 280,
            };
          });

          setParticles(fallbackParticles);
        } catch (apiError) {
          console.error('API fallback also failed:', apiError);
        }
      }
    };

    fetchUserImages();
  }, []);

  // Animazione della caduta
  useEffect(() => {
    if (particles.length === 0) return;

    const animate = () => {
      setParticles(prev =>
        prev.map(particle => ({
          ...particle,
          y: particle.y + particle.speed,
        }))
      );
      animationFrameRef.current = requestAnimationFrame(animate);
    };

    const startTimeout = setTimeout(() => {
      animationFrameRef.current = requestAnimationFrame(animate);
    }, 100);

    return () => {
      clearTimeout(startTimeout);
      if (animationFrameRef.current) {
        cancelAnimationFrame(animationFrameRef.current);
      }
    };
  }, [particles.length]);

  // Gestione dissolvenza e cleanup
  useEffect(() => {
    // Inizia fade out a 6 secondi
    const fadeTimer = setTimeout(() => {
      setOpacity(0);
    }, duration - 2000);

    // Rimuovi componente dopo duration
    const removeTimer = setTimeout(() => {
      setMounted(false);
      if (onComplete) {
        onComplete();
      }
      // Rimuovi fisicamente il container
      const container = document.getElementById('image-rain-root');
      if (container) {
        container.remove();
      }
    }, duration);

    return () => {
      clearTimeout(fadeTimer);
      clearTimeout(removeTimer);
    };
  }, [duration, onComplete]);

  if (!mounted) {
    return null;
  }

  return (
    <div
      style={{
        position: 'fixed',
        inset: 0,
        zIndex: opacity > 0.01 ? 9998 : -1,
        opacity: opacity,
        transition: 'opacity 2000ms ease-out',
        pointerEvents: 'none',
        overflow: 'hidden',
      }}
    >
      {particles.map(particle => (
        <div
          key={particle.id}
          style={{
            position: 'absolute',
            left: `${particle.x}%`,
            top: `${particle.y}%`,
            width: `${particle.size}px`,
            height: `${particle.size}px`,
            opacity: particle.y > -10 ? 1 : 0,
            transition: `opacity ${particle.delay}ms ease-in`,
          }}
        >
          {/* Scia luminosa - larga quanto l'immagine */}
          <div
            style={{
              position: 'absolute',
              top: '-150px',
              left: '0',
              width: '100%',
              height: '150px',
              background: 'linear-gradient(to bottom, rgba(0, 255, 255, 0), rgba(0, 255, 255, 0.6), rgba(255, 0, 255, 0.3))',
              opacity: 0.7,
              borderRadius: '12px 12px 0 0',
            }}
          />

          {/* Immagine */}
          <img
            src={particle.imageUrl}
            alt="NFT"
            style={{
              width: '100%',
              height: '100%',
              objectFit: 'cover',
              borderRadius: '12px',
              border: '2px solid rgba(0, 255, 255, 0.5)',
              boxShadow: '0 0 30px rgba(0, 255, 255, 0.4), 0 0 60px rgba(255, 0, 255, 0.3)',
            }}
            loading="eager"
          />
        </div>
      ))}
    </div>
  );
};

export default ImageRain;
