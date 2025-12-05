/**
 * WheelMenu - Menu circolare innovativo
 * 
 * UX Solutions:
 * - Mobile: Layout adattivo (cerchio più piccolo o lista)
 * - Accessibilità: Navigazione tastiera + aria labels
 * - Prima visita vs ritorno: LocalStorage per skip animazione
 */
import React, { useState, useEffect, useCallback } from 'react';
import './WheelMenu.css';

export interface WheelMenuItem {
    id: string;
    label: string;
    icon: string;
    description?: string;
    emphasized?: boolean; // Voce enfatizzata (più grande nel menu)
}

interface WheelMenuProps {
    items: WheelMenuItem[];
    onSelect: (item: WheelMenuItem) => void;
    isMinimized: boolean;
    onToggle: () => void;
    selectedId?: string;
}

const WheelMenu: React.FC<WheelMenuProps> = ({
    items,
    onSelect,
    isMinimized,
    onToggle,
    selectedId
}) => {
    const [isSpinning, setIsSpinning] = useState(true);
    const [spinAngle, setSpinAngle] = useState(0);
    const [hasVisitedBefore, setHasVisitedBefore] = useState(false);
    const [focusedIndex, setFocusedIndex] = useState(0);
    const [isMobile, setIsMobile] = useState(false);

    // Check if mobile
    useEffect(() => {
        const checkMobile = () => setIsMobile(window.innerWidth < 768);
        checkMobile();
        window.addEventListener('resize', checkMobile);
        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    // Check localStorage for previous visit
    useEffect(() => {
        const visited = localStorage.getItem('florenceegi_wheel_visited');
        if (visited) {
            setHasVisitedBefore(true);
            setIsSpinning(false);
        }
    }, []);

    // Spin animation on first visit
    useEffect(() => {
        if (hasVisitedBefore || isMinimized) return;

        let animationFrame: number;
        let startTime: number;
        const duration = 3000; // 3 secondi di spin
        const totalRotation = 720 + Math.random() * 360; // 2-3 giri

        const animate = (timestamp: number) => {
            if (!startTime) startTime = timestamp;
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing out cubic per rallentamento naturale
            const easeOut = 1 - Math.pow(1 - progress, 3);
            setSpinAngle(totalRotation * easeOut);

            if (progress < 1) {
                animationFrame = requestAnimationFrame(animate);
            } else {
                setIsSpinning(false);
                localStorage.setItem('florenceegi_wheel_visited', 'true');
            }
        };

        animationFrame = requestAnimationFrame(animate);
        return () => cancelAnimationFrame(animationFrame);
    }, [hasVisitedBefore, isMinimized]);

    // Keyboard navigation
    const handleKeyDown = useCallback((e: React.KeyboardEvent) => {
        if (isMinimized) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                onToggle();
            }
            return;
        }

        switch (e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                setFocusedIndex(prev => (prev + 1) % items.length);
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                setFocusedIndex(prev => (prev - 1 + items.length) % items.length);
                break;
            case 'Enter':
            case ' ':
                e.preventDefault();
                onSelect(items[focusedIndex]);
                break;
            case 'Escape':
                if (!isMinimized) onToggle();
                break;
        }
    }, [isMinimized, items, focusedIndex, onSelect, onToggle]);

    const handleItemClick = (item: WheelMenuItem) => {
        if (isSpinning) return;
        onSelect(item);
    };

    // Calculate item position on wheel
    const getItemStyle = (index: number, total: number) => {
        const angle = (360 / total) * index - 90 + spinAngle; // -90 per partire dall'alto
        const radius = isMobile ? 120 : 180; // Raggio più piccolo su mobile
        const radian = (angle * Math.PI) / 180;
        const x = Math.cos(radian) * radius;
        const y = Math.sin(radian) * radius;

        return {
            transform: `translate(${x}px, ${y}px)`,
            '--item-angle': `${-angle}deg` // Counter-rotate per testo leggibile
        } as React.CSSProperties;
    };

    // Minimized state - icona in alto a destra
    if (isMinimized) {
        return (
            <button
                className="wheel-menu-minimized"
                onClick={onToggle}
                onKeyDown={handleKeyDown}
                aria-label="Apri menu di navigazione"
                aria-expanded="false"
            >
                <div className="wheel-menu-minimized__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v8M8 12h8" />
                    </svg>
                </div>
                <span className="wheel-menu-minimized__label">Menu</span>
            </button>
        );
    }

    // Mobile: lista verticale invece di ruota
    if (isMobile) {
        return (
            <div 
                className="wheel-menu wheel-menu--mobile"
                role="navigation"
                aria-label="Menu principale"
            >
                <div className="wheel-menu__backdrop" onClick={onToggle} />
                
                <div className="wheel-menu__mobile-container">
                    <h2 className="wheel-menu__mobile-title">Esplora FlorenceEGI</h2>
                    
                    <ul className="wheel-menu__mobile-list" role="menu">
                        {items.map((item, index) => (
                            <li key={item.id} role="none">
                                <button
                                    className={`wheel-menu__mobile-item ${selectedId === item.id ? 'wheel-menu__mobile-item--selected' : ''} ${item.emphasized ? 'wheel-menu__mobile-item--emphasized' : ''}`}
                                    onClick={() => handleItemClick(item)}
                                    onKeyDown={handleKeyDown}
                                    role="menuitem"
                                    tabIndex={focusedIndex === index ? 0 : -1}
                                    aria-current={selectedId === item.id ? 'page' : undefined}
                                >
                                    <span className="wheel-menu__mobile-item-icon">{item.icon}</span>
                                    <span className="wheel-menu__mobile-item-label">{item.label}</span>
                                </button>
                            </li>
                        ))}
                    </ul>

                    <button 
                        className="wheel-menu__mobile-close"
                        onClick={onToggle}
                        aria-label="Chiudi menu"
                    >
                        Chiudi
                    </button>
                </div>
            </div>
        );
    }

    // Desktop: ruota animata
    return (
        <div 
            className={`wheel-menu ${isSpinning ? 'wheel-menu--spinning' : ''}`}
            role="navigation"
            aria-label="Menu principale"
            onKeyDown={handleKeyDown}
        >
            <div className="wheel-menu__backdrop" onClick={onToggle} />

            {/* Centro della ruota */}
            <div className="wheel-menu__center">
                <div className="wheel-menu__logo">
                    <span className="wheel-menu__logo-florence">Florence</span>
                    <span className="wheel-menu__logo-egi">EGI</span>
                </div>
                <p className="wheel-menu__hint">
                    {isSpinning ? 'Attendere...' : 'Scegli una sezione'}
                </p>
            </div>

            {/* Cerchio esterno decorativo */}
            <div 
                className="wheel-menu__ring"
                style={{ transform: `rotate(${spinAngle}deg)` }}
            />

            {/* Items sulla ruota */}
            <ul className="wheel-menu__items" role="menu">
                {items.map((item, index) => (
                    <li
                        key={item.id}
                        className={`wheel-menu__item ${focusedIndex === index ? 'wheel-menu__item--focused' : ''} ${selectedId === item.id ? 'wheel-menu__item--selected' : ''} ${item.emphasized ? 'wheel-menu__item--emphasized' : ''}`}
                        style={getItemStyle(index, items.length)}
                        role="none"
                    >
                        <button
                            className="wheel-menu__item-button"
                            onClick={() => handleItemClick(item)}
                            disabled={isSpinning}
                            role="menuitem"
                            tabIndex={focusedIndex === index ? 0 : -1}
                            aria-label={`${item.label}${item.description ? ': ' + item.description : ''}`}
                            aria-current={selectedId === item.id ? 'page' : undefined}
                        >
                            <span className="wheel-menu__item-icon">{item.icon}</span>
                            <span 
                                className="wheel-menu__item-label"
                                style={{ transform: `rotate(var(--item-angle))` }}
                            >
                                {item.label}
                            </span>
                        </button>
                    </li>
                ))}
            </ul>

            {/* Skip button per accessibilità */}
            <button 
                className="wheel-menu__skip"
                onClick={() => {
                    setIsSpinning(false);
                    localStorage.setItem('florenceegi_wheel_visited', 'true');
                }}
                aria-label="Salta animazione"
            >
                {isSpinning ? 'Salta' : 'Chiudi'}
            </button>
        </div>
    );
};

export default WheelMenu;
