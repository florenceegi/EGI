<?php

/**
 * AI Art Advisor - Traduzioni IT
 * @package FlorenceEGI
 * @subpackage Traduzioni
 * @language it
 * @version 1.0.0
 */

return [
    // General
    'open_advisor' => 'Apri AI Art Advisor - Assistente Creativo',
    
    // Modal UI
    'title' => 'AI Art Advisor',
    'subtitle' => 'Il tuo assistente creativo intelligente',
    'close' => 'Chiudi',

    // Experts
    'experts' => [
        'creative' => 'Creative Advisor',
        'creative_description' => 'Esperto in arte, NFT e marketing',
        'platform' => 'Platform Assistant',
        'platform_description' => 'Guida all\'uso di FlorenceEGI',
    ],

    // Chat Interface
    'chat' => [
        'placeholder' => 'Chiedi qualcosa all\'AI...',
        'send' => 'Invia',
        'thinking' => 'AI sta pensando...',
        'analyzing_image' => 'Analizzando l\'immagine...',
        'select_expert' => 'Scegli un esperto',
    ],

    // Context Display
    'context' => [
        'title' => 'Contesto EGI',
        'egi_number' => 'EGI',
        'collection' => 'Collezione',
        'price' => 'Prezzo',
        'traits' => 'Traits esistenti',
        'status' => 'Stato',
        'minted' => 'Mintato on-chain',
        'pre_mint' => 'Pre-Mint (modificabile)',
    ],

    // Action Buttons
    'actions' => [
        'copy' => 'Copia',
        'apply' => 'Applica al Form',
        'copy_success' => 'Copiato negli appunti!',
        'apply_success' => 'Applicato al form!',
        'vision_mode' => 'Analizza Immagine',
        'vision_active' => 'Modalità Vision attiva',
    ],

    // Quick Prompts / Examples
    'examples' => [
        'title' => 'Esempi di domande',
        'creative' => [
            'Suggerisci 5 traits per questa opera',
            'Migliora la descrizione per collezionisti luxury',
            'Analizza i colori e suggerisci mood',
            'Che prezzo consigli per questa opera?',
        ],
        'platform' => [
            'Come faccio a mintare questo EGI?',
            'Differenza tra EGI Classico e Vivente?',
            'Come funzionano le royalties?',
            'Come promuovo le mie opere?',
        ],
    ],

    // Welcome Messages (based on mode)
    'welcome' => [
        'general' => 'Ciao! Sono il tuo AI Art Advisor. Come posso aiutarti oggi?',
        'generate_description' => 'Perfetto! Creiamo una descrizione efficace per la tua opera. Dimmi:

1. **Che emozione** vuoi trasmettere? (calma, energia, mistero, gioia...)
2. **A chi è rivolta?** (collezionisti luxury, giovani creator, corporate/PA...)
3. **Preferisci enfatizzare**: tecnica artistica o concept/storytelling?
4. Vuoi che **analizzi visivamente l\'immagine** per dettagli precisi?',
        
        'suggest_traits' => 'Analizziamo la tua opera per suggerire traits ottimali. 

Vuoi che esamini l\'immagine visivamente oppure preferisci descrivermi tu le caratteristiche principali?',
        
        'pricing_advice' => 'Ti aiuto a definire il pricing strategico. Dimmi:

1. Sei **emerging artist** o hai già un portfolio/track record?
2. Preferisci **prezzo fisso** o vuoi testare il mercato con **asta/offerte**?
3. Quanto tempo hai impiegato per creare quest\'opera?',
    ],

    // Errors
    'error_occurred' => 'Si è verificato un errore. Riprova tra poco.',
    'error_vision_failed' => 'Analisi visiva fallita. Riprova in modalità testo.',
    'error_rate_limit' => 'Troppe richieste. Aspetta un momento prima di riprovare.',
    'error_invalid_expert' => 'Esperto non valido selezionato.',

    // Status Messages
    'status' => [
        'connecting' => 'Connessione all\'AI...',
        'connected' => 'Connesso',
        'streaming' => 'Ricevendo risposta...',
        'complete' => 'Completato',
        'error' => 'Errore',
    ],

    // Tips & Hints
    'tips' => [
        'vision_tip' => 'Tip: Chiedi all\'AI di "guardare l\'immagine" per analisi visiva dettagliata',
        'apply_tip' => 'Puoi applicare i suggerimenti direttamente al form con il bottone "Applica"',
        'copy_tip' => 'Clicca "Copia" per copiare il testo negli appunti',
    ],

    // Mode-specific labels
    'modes' => [
        'generate_description' => 'Generazione Descrizione',
        'suggest_traits' => 'Suggerimento Traits',
        'pricing_advice' => 'Consulenza Pricing',
        'general' => 'Assistenza Generale',
    ],
];

