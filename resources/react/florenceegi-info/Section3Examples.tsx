import React, { useState } from 'react';
import './Section3Examples.css';

interface Example {
  icon: string;
  title: string;
  description: string;
}

interface Category {
  id: string;
  name: string;
  icon: string;
  examples: Example[];
}

export default function Section3Examples() {
  const [activeTab, setActiveTab] = useState('art');
  const t = window.florenceEgiTranslations?.examples;

  const categories: Category[] = [
    {
      id: 'art',
      name: t?.tab_art || 'Arte & Creatività',
      icon: '🎨',
      examples: [
        { icon: '��️', title: t?.art_painting || 'Quadro fisico', description: t?.art_painting_desc || 'EGI + certificato + royalty perpetua' },
        { icon: '📸', title: t?.art_photo || 'Fotografia', description: t?.art_photo_desc || 'Serie limitata tokenizzata' },
        { icon: '🗿', title: t?.art_sculpture || 'Scultura', description: t?.art_sculpture_desc || 'Gemello digitale 3D + proprietà fisica' },
        { icon: '🎭', title: t?.art_mural || 'Murales', description: t?.art_mural_desc || 'Opera georeferenziata + NFT' },
        { icon: '💡', title: t?.art_installation || 'Installazione', description: t?.art_installation_desc || 'Documentazione immersiva + accesso esclusivo' },
      ]
    },
    {
      id: 'music',
      name: t?.tab_music || 'Musica & Show',
      icon: '🎵',
      examples: [
        { icon: '🎶', title: t?.music_song || 'Canzone', description: t?.music_song_desc || 'Music NFT + split royalty con band' },
        { icon: '💿', title: t?.music_album || 'Album', description: t?.music_album_desc || 'Tracce vendibili separatamente' },
        { icon: '🎤', title: t?.music_concert || 'Concerto', description: t?.music_concert_desc || 'Ticket + backstage access token' },
        { icon: '🎙️', title: t?.music_podcast || 'Podcast', description: t?.music_podcast_desc || 'Episodi premium tokenizzati' },
      ]
    },
    {
      id: 'books',
      name: t?.tab_books || 'Libri & Content',
      icon: '📚',
      examples: [
        { icon: '📖', title: t?.books_book || 'Libro', description: t?.books_book_desc || 'Capitoli vendibili singolarmente' },
        { icon: '📱', title: t?.books_ebook || 'E-book', description: t?.books_ebook_desc || 'Edizioni limitate firmate' },
        { icon: '📰', title: t?.books_article || 'Articolo', description: t?.books_article_desc || 'Accesso esclusivo + archivio storico' },
        { icon: '🍳', title: t?.books_recipe || 'Ricetta segreta', description: t?.books_recipe_desc || 'IP protetto + licenze uso' },
      ]
    },
    {
      id: 'eco',
      name: t?.tab_eco || 'Ambiente',
      icon: '🌱',
      examples: [
        { icon: '🌳', title: t?.eco_tree || 'Albero piantato', description: t?.eco_tree_desc || 'Carbon credit certificato' },
        { icon: '🌊', title: t?.eco_ocean || 'Pulizia oceano', description: t?.eco_ocean_desc || 'Kg plastica rimossa tokenizzati' },
        { icon: '🏛️', title: t?.eco_monument || 'Restauro monumento', description: t?.eco_monument_desc || 'Contributo storico certificato' },
        { icon: '⚡', title: t?.eco_energy || 'Energia rinnovabile', description: t?.eco_energy_desc || 'kWh verdi scambiabili' },
      ]
    },
    {
      id: 'sport',
      name: t?.tab_sport || 'Sport & Exp',
      icon: '🏃',
      examples: [
        { icon: '🏅', title: t?.sport_marathon || 'Maratona', description: t?.sport_marathon_desc || 'Risultato certificato + sponsor split' },
        { icon: '🚴', title: t?.sport_cycling || 'Gara ciclismo', description: t?.sport_cycling_desc || 'Percorso GPS + memorabilia' },
        { icon: '🤿', title: t?.sport_diving || 'Immersione subacquea', description: t?.sport_diving_desc || 'Esperienza documentata + video 360°' },
      ]
    },
    {
      id: 'fashion',
      name: t?.tab_fashion || 'Moda',
      icon: '👗',
      examples: [
        { icon: '👟', title: t?.fashion_shoe || 'Scarpa artigianale', description: t?.fashion_shoe_desc || 'Prototipo unico + storia produzione' },
        { icon: '💎', title: t?.fashion_jewel || 'Gioiello', description: t?.fashion_jewel_desc || 'Certificato autenticità + tracciabilità gemme' },
        { icon: '👔', title: t?.fashion_dress || 'Abito sartoriale', description: t?.fashion_dress_desc || 'Design esclusivo + making-of' },
      ]
    },
    {
      id: 'culture',
      name: t?.tab_culture || 'Heritage',
      icon: '🏛️',
      examples: [
        { icon: '🏺', title: t?.culture_artifact || 'Reperto storico', description: t?.culture_artifact_desc || 'Certificato provenienza + 3D scan' },
        { icon: '📜', title: t?.culture_manuscript || 'Manoscritto', description: t?.culture_manuscript_desc || 'Digitalizzazione HD + accesso ricerca' },
        { icon: '🎭', title: t?.culture_theater || 'Opera teatrale', description: t?.culture_theater_desc || 'Replica streaming + royalty cast' },
      ]
    },
  ];

  const activeCategory = categories.find(cat => cat.id === activeTab) || categories[0];

  return (
    <section className="section-examples">
      <div className="container">
        <header className="section-header">
          <h2 className="section-title">{t?.title || 'Qualsiasi Cosa Esista, Può Diventare un EGI'}</h2>
          <p className="section-subtitle">{t?.subtitle || 'Esplora le infinite possibilità'}</p>
        </header>

        <div className="examples-tabs">
          {categories.map(category => (
            <button
              key={category.id}
              className={`tab-button ${activeTab === category.id ? 'active' : ''}`}
              onClick={() => setActiveTab(category.id)}
            >
              <span className="tab-icon">{category.icon}</span>
              <span className="tab-name">{category.name}</span>
            </button>
          ))}
        </div>

        <div className="examples-content">
          <div className="examples-grid">
            {activeCategory.examples.map((example, index) => (
              <div 
                key={index} 
                className="example-card"
                style={{ animationDelay: `${index * 0.1}s` }}
              >
                <div className="example-icon">{example.icon}</div>
                <h3 className="example-title">{example.title}</h3>
                <p className="example-description">{example.description}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
