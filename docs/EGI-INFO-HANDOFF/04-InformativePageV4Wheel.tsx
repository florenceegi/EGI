/**
 * FlorenceEGI - Pagina Informativa V4 con WheelMenu
 * 
 * Navigazione innovativa con menu circolare animato
 */
import React, { useState, useCallback } from 'react';
import { WheelMenu, WheelMenuItem } from './components/WheelMenu';

// V4 Sections
import HeroV4 from './sections/HeroV4';
import OriginStoryV4 from './sections/OriginStoryV4';
import EgizzareV4 from './sections/EgizzareV4';
import WhatIsEGIV4 from './sections/WhatIsEGIV4';
import TransparencyV4 from './sections/TransparencyV4';
import BlockchainSimpleV4 from './sections/BlockchainSimpleV4';
import ProblemsV4 from './sections/ProblemsV4';
import InvoicesV4 from './sections/InvoicesV4';
import WhoCanUseV4 from './sections/WhoCanUseV4';
import CTAFinalV4 from './sections/CTAFinalV4';

import './styles/base.css';
import './InformativePageV4Wheel.css';

// Menu items configuration
const MENU_ITEMS: WheelMenuItem[] = [
    { id: 'hero', label: 'Home', icon: '🏠', description: 'Introduzione a FlorenceEGI' },
    { id: 'originstory', label: 'La Storia', icon: '📖', description: 'Come nasce FlorenceEGI', emphasized: true },
    { id: 'egizzare', label: 'Egizzare', icon: '✨', description: 'Cosa significa egizzare' },
    { id: 'whatisegi', label: 'Cos\'è un EGI', icon: '💎', description: 'Ecological Goods Invent' },
    { id: 'transparency', label: 'Trasparenza', icon: '📊', description: 'Come si dividono i guadagni' },
    { id: 'blockchain', label: 'Blockchain', icon: '🔗', description: 'Semplice come Amazon' },
    { id: 'problems', label: 'Problemi', icon: '🛡️', description: '8 problemi risolti' },
    { id: 'invoices', label: 'Fatture', icon: '📋', description: 'Documentazione fiscale' },
    { id: 'whocause', label: 'Per chi', icon: '👥', description: 'Creator, Collector, EPP' },
    { id: 'cta', label: 'Inizia', icon: '🚀', description: 'Comincia ora' },
];

// Section components map
const SECTIONS: Record<string, React.FC> = {
    hero: HeroV4,
    originstory: OriginStoryV4,
    egizzare: EgizzareV4,
    whatisegi: WhatIsEGIV4,
    transparency: TransparencyV4,
    blockchain: BlockchainSimpleV4,
    problems: ProblemsV4,
    invoices: InvoicesV4,
    whocause: WhoCanUseV4,
    cta: CTAFinalV4,
};

export default function InformativePageV4Wheel() {
    const [isMenuMinimized, setIsMenuMinimized] = useState(false);
    const [selectedSection, setSelectedSection] = useState<string | null>(null);

    const handleSelectSection = useCallback((item: WheelMenuItem) => {
        setSelectedSection(item.id);
        setIsMenuMinimized(true);
    }, []);

    const handleToggleMenu = useCallback(() => {
        setIsMenuMinimized(prev => !prev);
    }, []);

    // Render selected section
    const renderSection = () => {
        if (!selectedSection) return null;
        const SectionComponent = SECTIONS[selectedSection];
        if (!SectionComponent) return null;
        return <SectionComponent />;
    };

    return (
        <div className="informative-page informative-page--v4-wheel">
            
            {/* WheelMenu */}
            <WheelMenu
                items={MENU_ITEMS}
                onSelect={handleSelectSection}
                isMinimized={isMenuMinimized}
                onToggle={handleToggleMenu}
                selectedId={selectedSection || undefined}
            />

            {/* Selected Section Content */}
            {isMenuMinimized && selectedSection && (
                <div className="v4-wheel__content">
                    {renderSection()}
                    
                    {/* Navigation footer */}
                    <div className="v4-wheel__nav-footer">
                        <button 
                            className="v4-wheel__nav-prev"
                            onClick={() => {
                                const currentIndex = MENU_ITEMS.findIndex(i => i.id === selectedSection);
                                if (currentIndex > 0) {
                                    setSelectedSection(MENU_ITEMS[currentIndex - 1].id);
                                }
                            }}
                            disabled={MENU_ITEMS.findIndex(i => i.id === selectedSection) === 0}
                        >
                            ← Precedente
                        </button>
                        
                        <span className="v4-wheel__nav-indicator">
                            {MENU_ITEMS.findIndex(i => i.id === selectedSection) + 1} / {MENU_ITEMS.length}
                        </span>
                        
                        <button 
                            className="v4-wheel__nav-next"
                            onClick={() => {
                                const currentIndex = MENU_ITEMS.findIndex(i => i.id === selectedSection);
                                if (currentIndex < MENU_ITEMS.length - 1) {
                                    setSelectedSection(MENU_ITEMS[currentIndex + 1].id);
                                }
                            }}
                            disabled={MENU_ITEMS.findIndex(i => i.id === selectedSection) === MENU_ITEMS.length - 1}
                        >
                            Successivo →
                        </button>
                    </div>
                </div>
            )}

        </div>
    );
}
