import React, { useState } from 'react';
import './Collapsible.css';

interface CollapsibleProps {
    /** Testo del bottone quando chiuso */
    triggerTextClosed: string;
    /** Testo del bottone quando aperto */
    triggerTextOpen: string;
    /** Contenuto collapsible */
    children: React.ReactNode;
    /** Stato iniziale (default: chiuso) */
    defaultExpanded?: boolean;
    /** Classe CSS aggiuntiva */
    className?: string;
}

/**
 * Componente Collapsible riutilizzabile
 * Mostra/nasconde contenuto con animazione smooth
 */
export default function Collapsible({
    triggerTextClosed,
    triggerTextOpen,
    children,
    defaultExpanded = false,
    className = ''
}: CollapsibleProps) {
    const [isExpanded, setIsExpanded] = useState(defaultExpanded);

    const toggleExpanded = () => {
        setIsExpanded(!isExpanded);
    };

    return (
        <div className={`collapsible-component ${className}`}>
            <button
                className={`collapsible-component__trigger ${isExpanded ? 'collapsible-component__trigger--expanded' : ''}`}
                onClick={toggleExpanded}
                aria-expanded={isExpanded}
            >
                <span>{isExpanded ? triggerTextOpen : triggerTextClosed}</span>
                <svg 
                    className="collapsible-component__icon" 
                    width="16" 
                    height="16" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    strokeWidth="2"
                >
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <div 
                className={`collapsible-component__content ${isExpanded ? 'collapsible-component__content--expanded' : ''}`}
                aria-hidden={!isExpanded}
            >
                {children}
            </div>
        </div>
    );
}
