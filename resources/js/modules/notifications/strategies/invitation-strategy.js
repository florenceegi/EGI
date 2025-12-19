/**
 * @fileoverview Strategia per la gestione delle notifiche di tipo "invitation"
 * @version 1.0.0
 * @package EGI Florence
 * @module InvitationStrategy
 *
 * @description
 * Gestisce le azioni sulle notifiche di tipo "invitation" (accettazione, rifiuto, archiviazione),
 * mappando gli stati delle notifiche tramite `getEnum` e integrandosi con il sistema di viste di NotificationDetailsController.
 *
 * @requires NotificationActionRequest
 * @requires notification-api/sendAction
 * @requires utils/displayError
 */
import { NotificationActionRequest } from '../dto/notification-action-request.js';
import { sendAction } from '../services/notification-api.js';
import { displayError } from '../services/utils.js';
import { getEnum } from '../../../utils/enums.js';

export class InvitationStrategy {
    constructor(notificationInstance) {
        this.notificationInstance = notificationInstance;
        this.payload = '/invitation';
        this.statuses = {
            ACCEPTED: null,
            REJECTED: null,
            ARCHIVED: null
        };
    }

    async initialize() {
        await this.initStatuses();
        return this;
    }

    async initStatuses() {
        this.statuses.ACCEPTED = await getEnum("NotificationStatus", "ACCEPTED");
        this.statuses.REJECTED = await getEnum("NotificationStatus", "REJECTED");
        this.statuses.ARCHIVED = await getEnum("NotificationStatus", "ARCHIVED");
    }

    async handleAction(actionRequest, baseUrl) {
        const fullBaseUrl = baseUrl + this.payload;
        console.log(`🚀 handleAction parametri per la notifica ${JSON.stringify(actionRequest)} e per il percorso: ${fullBaseUrl}`);
        
        // DEBUG: Log what values we're comparing
        console.log(`🔍 DEBUG: actionRequest.action = "${actionRequest.action}" (type: ${typeof actionRequest.action})`);
        console.log(`🔍 DEBUG: this.statuses.ACCEPTED = "${this.statuses.ACCEPTED}" (type: ${typeof this.statuses.ACCEPTED})`);
        console.log(`🔍 DEBUG: this.statuses.REJECTED = "${this.statuses.REJECTED}"`);
        console.log(`🔍 DEBUG: this.statuses.ARCHIVED = "${this.statuses.ARCHIVED}"`);
        console.log(`🔍 DEBUG: Match ACCEPTED: ${actionRequest.action === this.statuses.ACCEPTED}`);

        const actions = {
            [this.statuses.ACCEPTED]: () => this.accept(actionRequest, fullBaseUrl),
            [this.statuses.REJECTED]: () => this.openRejectModal(actionRequest, fullBaseUrl),
            [this.statuses.ARCHIVED]: () => this.archive(actionRequest, fullBaseUrl)
        };

        const actionFn = actions[actionRequest.action];
        if (actionFn) {
            await actionFn();
            console.log(`✅ Azione ${actionRequest.action} completata per invito ${actionRequest.notificationId}`);
        } else {
            console.warn(`Azione non gestita: ${actionRequest.action}`);
            console.warn(`🔍 DEBUG: Available actions: ${Object.keys(actions).join(', ')}`);
        }
    }

    async accept(actionRequest, baseUrl) {
        try {
            await sendAction.call(this, actionRequest, baseUrl);
            this.notificationInstance.showProgressMessage('✅ Invito accettato!', actionRequest.notificationId, '#10B981');
        } catch (error) {
            // Controlla se è l'errore specifico "già membro"
            if (error.message && error.message.includes('ALREADY_MEMBER:')) {
                const message = error.message.replace('ALREADY_MEMBER:', '');

                await Swal.fire({
                    icon: 'info',
                    title: 'Già membro!',
                    text: message,
                    confirmButtonText: 'Ho capito',
                    confirmButtonColor: '#3B82F6',
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: 'rgba(0, 0, 0, 0.5)'
                });

                // Rimuovi la notifica dal DOM dato che l'utente è già membro
                this.notificationInstance.removeNotificationFromDOM(actionRequest.notificationId);

            } else {
                // Altri errori - mostra errore generico
                await Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Si è verificato un errore durante l\'accettazione dell\'invito.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#EF4444'
                });
            }

            console.error('Errore nell\'accettazione invito:', error);
            throw error;
        }
    }

    async openRejectModal(actionRequest, baseUrl) {
        const { value: reason } = await Swal.fire({
            title: 'Motivo del rifiuto',
            input: 'text',
            inputPlaceholder: 'Inserisci il motivo del rifiuto',
            showCancelButton: true,
            cancelButtonText: 'Annulla',
            confirmButtonText: 'Invia Rifiuto',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage(`Per favore, inserisci un motivo per il rifiuto`);
                }
                return reason;
            }
        });
        if (reason) {
            try {
                actionRequest.reason = reason;
                await this.reject(actionRequest, baseUrl);
            } catch (error) {
                console.error(`Errore nel rifiuto dell'invito ${actionRequest.notificationId}:`, error);
                displayError.call(this, error.message);
            }
        }
    }

    async reject(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.notificationInstance.showProgressMessage('❌ Invito rifiutato!', actionRequest.notificationId, '#E53E3E');
    }

    async archive(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.notificationInstance.showProgressMessage('📦 Invito archiviato!', actionRequest.notificationId, '#6B7280');
    }
}
