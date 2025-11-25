import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './ExamplesLight.css';

/**
 * Examples Light
 * Sempre visibile - "Qualsiasi cosa esista può diventare un EGI"
 */
export default function ExamplesLight() {
    const categories = [
        {
            icon: '🎨',
            title: getTranslation('examples.tab_art', 'Arte & Creatività'),
            examples: ['Quadri fisici', 'Sculture', 'Fotografie', 'Murales'],
            color: '#E74C3C'
        },
        {
            icon: '🎵',
            title: getTranslation('examples.tab_music', 'Musica & Show'),
            examples: ['Album', 'Canzoni', 'Ticket concerti', 'Backstage pass'],
            color: '#9B59B6'
        },
        {
            icon: '📚',
            title: getTranslation('examples.tab_books', 'Libri & Content'),
            examples: ['E-book firmati', 'Articoli esclusivi', 'Ricette', 'Podcast'],
            color: '#3498DB'
        },
        {
            icon: '🌱',
            title: getTranslation('examples.tab_eco', 'Ambiente'),
            examples: ['Alberi piantati', 'Kg plastica rimossa', 'Energia rinnovabile'],
            color: '#2E8B57'
        },
        {
            icon: '🏃',
            title: getTranslation('examples.tab_sport', 'Sport & Experience'),
            examples: ['Risultati maratone', 'Percorsi GPS', 'Memorabilia'],
            color: '#F39C12'
        },
        {
            icon: '👗',
            title: getTranslation('examples.tab_fashion', 'Moda'),
            examples: ['Scarpe artigianali', 'Gioielli tracciati', 'Abiti sartoriali'],
            color: '#E91E63'
        },
        {
            icon: '🏛️',
            title: getTranslation('examples.tab_culture', 'Heritage'),
            examples: ['Reperti museali', 'Manoscritti', 'Opere teatrali'],
            color: '#795548'
        }
    ];

    return (
        <section className="examples-light">
            <div className="examples-light__container">
                {/* Header */}
                <header className="examples-light__header">
                    <h2 className="examples-light__title">
                        {getTranslation('examples.title', 'Qualsiasi Cosa Esista, Può Diventare un EGI')}
                    </h2>
                    <p className="examples-light__subtitle">
                        {getTranslation('examples.subtitle', 'Esplora le infinite possibilità')}
                    </p>
                </header>

                {/* Categories Grid */}
                <div className="examples-light__grid">
                    {categories.map((cat, index) => (
                        <div 
                            key={index} 
                            className="examples-light__card"
                            style={{ '--accent-color': cat.color } as React.CSSProperties}
                        >
                            <div className="examples-light__card-icon">{cat.icon}</div>
                            <h3 className="examples-light__card-title">{cat.title}</h3>
                            <ul className="examples-light__card-list">
                                {cat.examples.map((ex, i) => (
                                    <li key={i}>{ex}</li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* Bottom message */}
                <p className="examples-light__message">
                    <span className="examples-light__message-icon">💡</span>
                    E molto altro ancora... Se puoi immaginarlo, puoi EGIZZARLO.
                </p>
            </div>
        </section>
    );
}
