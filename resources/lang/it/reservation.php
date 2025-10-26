<?php

/**
 * Messaggi Prenotazione
 * @package FlorenceEGI
 * @subpackage Traduzioni
 * @language it
 * @version 2.0.0
 */

return [
    // Messaggi di successo
    'success' => 'La tua offerta è stata effettuata con successo! Il certificato è stato generato.',
    'cancel_success' => 'La tua offerta è stata annullata con successo.',
    'success_title' => 'Offerta effettuata!',
    'view_certificate' => 'Visualizza Certificato',
    'close' => 'Chiudi',

    // Messaggi di errore
    'unauthorized' => 'Devi collegare il tuo wallet o effettuare l\'accesso per fare un\'offerta.',
    'validation_failed' => 'Controlla i dati inseriti e riprova.',
    'auth_required' => 'È richiesta l\'autenticazione per visualizzare le tue offerte.',
    'list_failed' => 'Impossibile recuperare le tue offerte. Riprova più tardi.',
    'status_failed' => 'Impossibile recuperare lo stato dell\'offerta. Riprova più tardi.',
    'unauthorized_cancel' => 'Non hai il permesso per annullare questa offerta.',
    'cancel_failed' => 'Impossibile annullare l\'offerta. Riprova più tardi.',

    // Pulsanti UI
    'button' => [
        'reserve' => 'Fai un\'offerta',
        'reserved' => 'Offerta fatta',
        'make_offer' => 'Fai un\'offerta'
    ],

    // Badge
    'badge' => [
        'highest' => 'Offerta Più Alta',
        'superseded' => 'Offerta Superata',
        'has_offers' => 'Con Offerte'
    ],

    // Dettagli offerta
    'already_reserved' => [
        'title' => 'Hai già fatto un\'offerta',
        'text' => 'Hai già un\'offerta attiva per questo EGI.',
        'details' => 'Dettagli della tua offerta:',
        'type' => 'Tipo',
        'amount' => 'Importo',
        'status' => 'Stato',
        'view_certificate' => 'Visualizza Certificato',
        'ok' => 'OK',
        'new_reservation' => 'Nuova Offerta',
        'confirm_new' => 'Vuoi effettuare una nuova offerta?'
    ],

    // Storico offerte
    'history' => [
        'title' => 'Storico Offerte',
        'entries' => 'Voci di Offerta',
        'view_certificate' => 'Visualizza Certificato',
        'no_entries' => 'Nessuna offerta trovata.',
        'be_first' => 'Sii il primo a fare un\'offerta per questo EGI!'
    ],

    // Messaggi di errore
    'errors' => [
        'button_click_error' => 'Si è verificato un errore nell\'elaborazione della tua richiesta.',
        'form_validation' => 'Controlla i dati inseriti e riprova.',
        'api_error' => 'Si è verificato un errore nella comunicazione con il server.',
        'unauthorized' => 'Devi collegare il tuo wallet o effettuare l\'accesso per fare un\'offerta.'
    ],

    // Form
    'form' => [
        'title' => 'Fai un\'offerta per questo EGI',
        'offer_amount_label' => 'La tua Offerta (EUR)',
        'offer_amount_placeholder' => 'Inserisci l\'importo in EUR',
        'algo_equivalent' => 'Circa :amount ALGO',
        'terms_accepted' => 'Accetto i termini e le condizioni per le offerte sugli EGI',
        'contact_info' => 'Informazioni di Contatto Aggiuntive (Opzionale)',
        'submit_button' => 'Conferma Offerta',
        'cancel_button' => 'Annulla'
    ],

    // Tipologia offerta
    'type' => [
        'strong' => 'Offerta Identificata',
        'weak' => 'Offerta Anonima'
    ],

    // Livelli di priorità
    'priority' => [
        'highest' => 'Offerta Attiva',
        'superseded' => 'Superata',
    ],

    // Stato dell'offerta
    'status' => [
        'active' => 'Attiva',
        'pending' => 'In attesa',
        'cancelled' => 'Annullata',
        'expired' => 'Scaduta'
    ],

    // === NUOVA SEZIONE: NOTIFICHE ===
    'notifications' => [
        'reservation_expired' => 'La tua prenotazione di €:amount per :egi_title è scaduta.',
        'superseded' => 'La tua offerta per :egi_title è stata superata. Nuova offerta più alta: €:new_highest_amount',
        'highest' => 'Congratulazioni! La tua offerta di €:amount per :egi_title è ora la più alta!',
        'rank_changed' => 'La tua posizione per :egi_title è cambiata: sei ora in posizione #:new_rank',
        'competitor_withdrew' => 'Un concorrente si è ritirato. Sei salito in posizione #:new_rank per :egi_title',
        'pre_launch_reminder' => 'Il mint on-chain inizierà presto! Conferma la tua prenotazione per :egi_title.',
        'mint_window_open' => 'È il tuo turno! Hai 48 ore per completare il mint di :egi_title.',
        'mint_window_closing' => 'Attenzione! Restano solo :hours_remaining ore per completare il mint di :egi_title.',
        'default' => 'Aggiornamento sulla tua prenotazione per :egi_title',
        'archived_success' => 'Notifica archiviata con successo.'
    ],
];
