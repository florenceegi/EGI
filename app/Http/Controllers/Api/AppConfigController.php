<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FegiAuth;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: Centralized Application Configuration Provider
 * 🎯 Purpose: Provides configuration, translations and error definitions to client
 * 🧱 Core Logic: Aggregates all client-needed data in a single endpoint
 * 🛡️ GDPR: Only exposes necessary configuration data, no personal info
 *
 * @author Padmin D. Curtis
 *
 * @version 1.0.0
 *
 * @date 2025-05-13
 *
 * @core-features
 * 1. Unified configuration endpoint
 * 2. Frontend translations aggregation
 * 3. Error definitions provider
 * 4. Route mapping service
 * 5. Cache-optimized responses
 *
 * @signature [AppConfigController::v1.0] florence-egi-spa-config
 */
class AppConfigController extends Controller {
    /** @var UltraLogManager Structured logging instance */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface Error management interface */
    private ErrorManagerInterface $errorManager;

    /** @var string Logging channel name */
    protected string $channel = 'app_config';

    /**
     * @Oracode Constructor with dependency injection
     * 🎯 Purpose: Initialize controller with required services
     * 📥 Input: Logger and error manager instances
     *
     * @param  UltraLogManager  $logger  Structured logger
     * @param  ErrorManagerInterface  $errorManager  Error handler
     *
     * @oracode-di-pattern Full dependency injection for testability
     *
     * @oracode-ultra-integrated ULM and UEM properly injected
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * @Oracode Get complete application configuration
     * 🎯 Purpose: Provide all client-needed configuration in one call
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with complete app configuration
     * 📡 API: GET /api/app-config
     *
     * @param  Request  $request  Current HTTP request
     * @return \Illuminate\Http\JsonResponse Configuration data
     *
     * @oracode-flow
     * 1. Determine user context
     * 2. Check cache for existing config
     * 3. Build configuration if not cached
     * 4. Return JSON response
     *
     * @cache 300 seconds per user/locale combination
     *
     * @seo-purpose SPA initialization endpoint
     */
    public function getAppConfig(Request $request): \Illuminate\Http\JsonResponse {

        $this->logger->info('Request for app configuration', [
            'ip' => $request->ip(),
            'channel' => $this->channel,
        ]);

        try {
            $user = FegiAuth::user();
            $lang = app()->getLocale();

            // Cache configuration for performance
            $cacheKey = "app_config_{$lang}_" . ($user ? $user->id : 'guest');

            $config = Cache::remember($cacheKey, 300, function () use ($user, $lang) {
                return $this->buildConfiguration($user, $lang);
            });

            $this->logger->info('App configuration served', [
                'user_id' => $user?->id,
                'language' => $lang,
                'cache_key' => $cacheKey,
                'channel' => $this->channel,
            ]);

            return response()->json($config);
        } catch (\Exception $e) {
            $this->logger->error('Failed to serve app configuration', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
                'channel' => $this->channel,
            ]);

            return $this->errorManager->handle('APP_CONFIG_ERROR', [
                'context' => 'getAppConfig',
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * @Oracode Get UEM error definitions
     * 🎯 Purpose: Provide error handling configuration to client
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with error definitions
     * 📡 API: GET /api/error-definitions
     *
     * @param  Request  $request  Current HTTP request
     * @return \Illuminate\Http\JsonResponse Error definitions
     *
     * @error-boundary Returns default config on failure
     *
     * @seo-purpose UEM client initialization endpoint
     */
    public function getErrorDefinitions(Request $request): \Illuminate\Http\JsonResponse {
        try {
            $uemConfig = [
                'errors' => config('error-manager.errors', []),
                'default_display_mode' => config('error-manager.ui.default_display_mode', 'sweet-alert'),
                'error_container_id' => 'error-container',
                'error_message_id' => 'error-message',
            ];

            $this->logger->info('Error definitions served', [
                'ip' => $request->ip(),
                'channel' => $this->channel,
            ]);

            return response()->json($uemConfig);
        } catch (\Exception $e) {
            $this->logger->error('Failed to serve error definitions', [
                'error' => $e->getMessage(),
                'channel' => $this->channel,
            ]);

            return $this->errorManager->handle('ERROR_DEFINITIONS_FAIL', [
                'context' => 'getErrorDefinitions',
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * @Oracode Build complete configuration object
     * 🎯 Purpose: Aggregate all configuration data
     * 📥 Input: User model and locale
     * 📤 Output: Configuration array
     *
     * @param  User|null  $user  Current user or null
     * @param  string  $lang  Current locale
     * @return array Complete configuration
     *
     * @internal Aggregates user data, routes, translations, settings
     *
     * @privacy-safe Only exposes necessary configuration
     */
    private function buildConfiguration($user, string $lang): array {
        $isAuthenticated = (bool) $user;
        $isWeakAuth = $user ? (bool) $user->is_weak_auth : false;
        $userData = $this->getUserData($user);
        $routes = $this->getRoutes();
        $translations = $this->getFrontendTranslations($lang);
        $appSettings = $this->getAppSettings();

        // VERSIONE CORRETTA: Raggruppiamo tutto sotto una chiave "AppConfig"
        return [
            'AppConfig' => [
                'isAuthenticated' => $isAuthenticated, // Nome pulito
                'isWeakAuth' => $isWeakAuth, // Indica se l'utente è in modalità weak auth
                'loggedInUserWallet' => $user?->wallet,
                'initialUserData' => $userData,
                'routes' => $routes,
                'translations' => $translations,
                'appSettings' => $appSettings,
                'locale' => $lang,
                'availableLocales' => config('app.available_locales', ['it', 'en']),
                'csrf_token' => csrf_token(),
                'env' => app()->environment('production') ? 'production' : 'development',
            ],
        ];
    }

    /**
     * @Oracode Get user-specific data
     * 🎯 Purpose: Extract user's current collection info
     * 📥 Input: User model or null
     * 📤 Output: User data array
     *
     * @param  User|null  $user  Current user
     * @return array User data structure
     *
     * @internal Returns default structure for guests
     */
    private function getUserData($user): array {
        if (! $user) {
            return [
                'current_collection_id' => null,
                'current_collection_name' => null,
                'can_edit_current_collection' => false,
            ];
        }

        $currentCollection = $user->currentCollection;

        return [
            'current_collection_id' => $currentCollection?->id,
            'current_collection_name' => $currentCollection?->collection_name,
            'can_edit_current_collection' => $currentCollection ?
                $user->canEditCollection($currentCollection) : false,
        ];
    }

    /**
     * @Oracode Build routes configuration
     * 🎯 Purpose: Provide all client-needed routes
     * 📤 Output: Routes array with placeholders
     *
     * @return array Routes configuration
     *
     * @internal Uses :id placeholder for parametric routes
     */
    private function getRoutes(): array {
        return [
            'baseUrl' => url('/'),
            'walletConnect' => route('wallet.connect'),
            'walletDisconnect' => route('wallet.disconnect'),
            'collectionsCreate' => route('collections.create'),
            'register' => route('register'),
            'logout' => route('custom.logout'),
            'homeCollectionsIndex' => route('home.collections.index'),
            'viewCollectionBase' => route('home.collections.show', [':id']), // Parametro con placeholder
            'editCollectionBase' => route('collections.edit', [':id']), // Parametro con placeholder

            'api' => [
                'baseUrl' => url('/api'),

                // Route API esistenti
                'appConfig' => route('api.app.config'),
                'errorDefinitions' => route('api.error.definitions'),

                // Route di prenotazione e certificati (aggiornate in base alle definizioni reali)
                'egiReservationStatus' => route('api.egis.reservation-status', ['egiId' => ':egiId']),
                'egisReserve' => route('api.egis.reserve', ['egiId' => ':egiId']),
                'egiModalInfo' => route('api.egis.modal-info', ['egiId' => ':egiId']),
                'reservationsCancel' => route('api.reservations.cancel', ['id' => ':id']),
                'myReservations' => route('api.my-reservations'),

                // Route per like/unlike
                'toggleCollectionLike' => route('api.toggle.collection.like', ['collectionId' => ':collection->id']),
                'toggleEgiLike' => route('api.toggle.egi.like', ['egi' => ':egi']),
                'currencyAlgoExchangeRate' => route('api.currency.algo-exchange-rate'),

                // Aggiungi qui altre route API future
            ],
        ];
    }

    /**
     * @Oracode Get guest layout translation keys
     * 🎯 Purpose: Map guest layout translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array Guest layout key mappings (simplified => full)
     */
    private function getGuestLayoutTranslationMappings(): array {
        return [
            // Chiave semplificata => chiave completa Laravel
            'fegi_connect_title' => 'collection.wallet.fegi_connect_title',
            'fegi_modal_subtitle' => 'collection.wallet.fegi_modal_subtitle',
            'padminGreeting' => 'guest_layout.padminGreeting',
            'padminReady' => 'guest_layout.padminReady',
            'errorModalNotFoundConnectWallet' => 'guest_layout.errorModalNotFoundConnectWallet',
            'connecting' => 'guest_layout.connecting',
            'walletAddressRequired' => 'guest_layout.walletAddressRequired',
            'errorConnectionFailed' => 'guest_layout.errorConnectionFailed',
            'errorConnectionGeneric' => 'guest_layout.errorConnectionGeneric',
            'registrationRequiredTitle' => 'guest_layout.registrationRequiredTitle',
            'registrationRequiredTextCollections' => 'guest_layout.registrationRequiredTextCollections',
            'registerNowButton' => 'guest_layout.registerNowButton',
            'laterButton' => 'guest_layout.laterButton',
            'errorEgiFormOpen' => 'guest_layout.errorEgiFormOpen',
            'errorUnexpected' => 'guest_layout.errorUnexpected',
            'walletConnectedTitle' => 'guest_layout.walletConnectedTitle',
            'errorWalletDropdownMissing' => 'guest_layout.errorWalletDropdownMissing',
            'errorNoWalletToCopy' => 'guest_layout.errorNoWalletToCopy',
            'copied' => 'guest_layout.copied',
            'errorCopyAddress' => 'guest_layout.errorCopyAddress',
            'disconnectedTitle' => 'guest_layout.disconnectedTitle',
            'disconnectedTextWeak' => 'guest_layout.disconnectedTextWeak',
            'errorLogoutFormMissing' => 'guest_layout.errorLogoutFormMissing',
            'errorApiDisconnect' => 'guest_layout.errorApiDisconnect',
            'walletDefaultText' => 'guest_layout.walletDefaultText',
            'walletAriaLabelLoggedIn' => 'guest_layout.walletAriaLabelLoggedIn',
            'walletAriaLabelConnected' => 'guest_layout.walletAriaLabelConnected',
            'loggedInStatus' => 'guest_layout.loggedInStatus',
            'connectedStatusWeak' => 'guest_layout.connectedStatusWeak',
            'errorGalleriesListUIDOM' => 'guest_layout.errorGalleriesListUIDOM',
            'errorFetchCollections' => 'guest_layout.errorFetchCollections',
            'errorLoadingGalleries' => 'guest_layout.errorLoadingGalleries',
            'byCreator' => 'guest_layout.byCreator',
            'switchingGallery' => 'guest_layout.switchingGallery',
            'gallerySwitchedTitle' => 'guest_layout.gallerySwitchedTitle',
            'gallerySwitchedText' => 'guest_layout.gallerySwitchedText',
            'pageWillReload' => 'guest_layout.pageWillReload',
            'editCurrentGalleryTitle' => 'guest_layout.editCurrentGalleryTitle',
            'viewCurrentGalleryTitle' => 'guest_layout.viewCurrentGalleryTitle',
            'errorMobileMenuElementsMissing' => 'guest_layout.errorMobileMenuElementsMissing',
            'errorTitle' => 'guest_layout.errorTitle',
            'warningTitle' => 'guest_layout.warningTitle',
            'myGalleries' => 'guest_layout.myGalleries',
            'myGalleriesOwned' => 'guest_layout.myGalleriesOwned',
            'myGalleriesCollaborations' => 'guest_layout.myGalleriesCollaborations',
            'wallet_secret_required' => 'guest_layout.wallet_secret_required',
            'wallet_invalid_secret' => 'guest_layout.wallet_invalid_secret',
            'wallet_existing_connection' => 'guest_layout.wallet_existing_connection',
            'wallet_new_connection' => 'guest_layout.wallet_new_connection',
            'wallet_disconnected_successfully' => 'guest_layout.wallet_disconnected_successfully',
        ];
    }

    /**
     * @Oracode Get collection-specific translation keys
     * 🎯 Purpose: Map collection translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array Collection key mappings (simplified => full)
     */
    private function getCollectionTranslationMappings(): array {
        return [
            // Chiavi principali collection
            'create_new_egi' => 'collection.create_new_egi',
            'create_new_collection' => 'collection.create_new_collection',
            'create_new_gallery' => 'collection.create_new_gallery',
            'invite_collection_member' => 'collection.invite_collection_member',
            'came_back_to_collection' => 'collection.came_back_to_collection',
            'confirm_delete_title' => 'collection.confirm_delete_title',
            'confirm_delete_text' => 'collection.confirm_delete_text',
            'confirm_delete_button' => 'collection.confirm_delete_button',
            'cancel_delete_button' => 'collection.cancel_delete_button',
            'deleted_title' => 'collection.deleted_title',
            'deleted_text' => 'collection.deleted_text',
            'delete_card' => 'collection.delete_card',
            'delete_banner' => 'collection.delete_banner',
            'delete_avatar' => 'collection.delete_avatar',
            'delete_EGI' => 'collection.delete_EGI',
            'save_card' => 'collection.save_card',
            'save_banner' => 'collection.save_banner',
            'save_avatar' => 'collection.save_avatar',
            'save_EGI' => 'collection.save_EGI',
            'create_validation_error' => 'collection.create_validation_error',
            'creation_failed' => 'collection.creation_failed',
            'collection_delete' => 'collection.collection_delete',
            'avatar_image' => 'collection.avatar_image',
            'card_image' => 'collection.card_image',
            'EGI_image' => 'collection.EGI_image',
            'banner_image' => 'collection.banner_image',
            'edit_collection_data' => 'collection.edit_collection_data',
            'modifies_EGI' => 'collection.modifies_EGI',
            'new_collection' => 'collection.new_collection',
            'open_collection' => 'collection.open_collection',
            'collection_members' => 'collection.collection_members',
            'team_members_description' => 'collection.team_members_description',
            'manage_collection' => 'collection.manage_collection',
            'manage_head_images' => 'collection.manage_head_images',
            'collection' => 'collection.collection',
            'collections' => 'collection.collections',
            'collection_data' => 'collection.collection_data',
            'collection_name' => 'collection.collection_name',
            'collection_id' => 'collection.collection_id',
            'collection_description_placeholder' => 'collection.collection_description_placeholder',
            'collection_description_suggest' => 'collection.collection_description_suggest',
            'collection_description' => 'collection.collection_description',
            'collection_image' => 'collection.collection_image',
            'collection_image_alt' => 'collection.collection_image_alt',
            'add_epp' => 'collection.add_epp',
            'add_epp_placeholder' => 'collection.add_epp_placeholder',
            'need_to_associate_epp' => 'collection.need_to_associate_epp',
            'EGI_floor_price' => 'collection.EGI_floor_price',
            'set_base_EcoNFT_price' => 'collection.set_base_EcoNFT_price',
            'collection_site_URL' => 'collection.collection_site_URL',
            'collection_site_URL_suggest' => 'collection.collection_site_URL_suggest',
            'position_for_mor_than_one_collection' => 'collection.position_for_mor_than_one_collection',
            'publish_collection' => 'collection.publish_collection',
            'image_for_EcoNFT_collection' => 'collection.image_for_EcoNFT_collection',
            'image_description' => 'collection.image_description',
            'image_alt' => 'collection.image_alt',
            'image_title' => 'collection.image_title',
            'select_new_EcoNFT_photo' => 'collection.select_new_EcoNFT_photo',
            'switch_collection' => 'collection.switch_collection',
            'goto_collection' => 'collection.goto_collection',
            'type' => 'collection.type',
            'select_content_type' => 'collection.select_content_type',
            'type_image' => 'collection.type_image',
            'type_ebook' => 'collection.type_ebook',
            'type_audio' => 'collection.type_audio',
            'type_video' => 'collection.type_video',
            'EGI_number' => 'collection.EGI_number',
            'position' => 'collection.position',
            'updated_successfully' => 'collection.updated_successfully',
            'save_failed' => 'collection.save_failed',
            'collection_not_found' => 'collection.collection_not_found',
            'tips_to_optimize_your_collection' => 'collection.tips_to_optimize_your_collection',
            'tips_for_your_collection_images' => 'collection.tips_for_your_collection_images',
            'image_section_title' => 'collection.image_section_title',
            'image_section_description' => 'collection.image_section_description',
            'data_section_title' => 'collection.data_section_title',
            'data_section_description' => 'collection.data_section_description',
            'this_is_default_collection_of_the_team' => 'collection.this_is_default_collection_of_the_team',
            'my_first_collection' => 'collection.my_first_collection',
            'default_collection_description' => 'collection.default_collection_description',
            'current_active_collection' => 'collection.current_active_collection',

            // Chiavi wallet (semplificate rimuovendo collection.wallet.)
            'wallet_copy_address' => 'collection.wallet.copy_address',
            'wallet_donation' => 'collection.wallet.donation',
            'wallet_donation_success' => 'collection.wallet.donation_success',
            'wallet_insufficient_quota' => 'collection.wallet.insufficient_quota',
            'wallet_write_royalty_mint' => 'collection.wallet.write_royalty_mint',
            'wallet_write_royalty_rebind' => 'collection.wallet.write_royalty_rebind',
            'wallet_user_role' => 'collection.wallet.user_role',
            'wallet_role_unknown' => 'collection.wallet.role_unknown',
            'wallet_address' => 'collection.wallet.address',
            'wallet_balance' => 'collection.wallet.balance',
            'wallet_name' => 'collection.wallet.name',
            'wallet_status' => 'collection.wallet.status',
            'wallet_royalty' => 'collection.wallet.royalty',
            'wallet_royalty_mint' => 'collection.wallet.royalty_mint',
            'wallet_royalty_rebind' => 'collection.wallet.royalty_rebind',
            'wallet_manage_wallet' => 'collection.wallet.manage_wallet',
            'wallet_remove_photo' => 'collection.wallet.remove_photo',
            'wallet_wallet' => 'collection.wallet.wallet',
            'wallet_cancellation' => 'collection.wallet.cancellation',
            'wallet_select_a_wallet_connect' => 'collection.wallet.select_a_wallet_connect',
            'wallet_button_wallet_connect' => 'collection.wallet.button_wallet_connect',
            'wallet_button_wallet_disconnect' => 'collection.wallet.button_wallet_disconnect',
            'wallet_create_the_wallet' => 'collection.wallet.create_the_wallet',
            'wallet_update_wallet' => 'collection.wallet.update_wallet',
            'wallet_username' => 'collection.wallet.username',
            'wallet_create' => 'collection.wallet.create',
            'wallet_delete_wallet' => 'collection.wallet.delete_wallet',
            'wallet_approver' => 'collection.wallet.approver',
            'wallet_proposer' => 'collection.wallet.proposer',
            'wallet_owner' => 'collection.wallet.owner',
            'wallet_change_rejected' => 'collection.wallet.wallet_change_rejected',
            'wallet_change_approved' => 'collection.wallet.wallet_change_approved',
            'wallet_change_expired' => 'collection.wallet.wallet_change_expired',
            'wallet_save_secret_warning' => 'collection.wallet.save_secret_warning',
            'wallet_secret_lost_warning' => 'collection.wallet.secret_lost_warning',
            'wallet_copy_secret_prompt' => 'collection.wallet.copy_secret_prompt',
            'wallet_connected_successfully' => 'collection.wallet.wallet_connected_successfully',
            'wallet_created_profile' => 'collection.wallet.wallet_created_profile',
            'wallet_disconnected_successfully' => 'collection.wallet.wallet_disconnected_successfully',
            'wallet_secret_required' => 'collection.wallet.wallet_secret_required',
            'wallet_secret_invalid' => 'collection.wallet.wallet_secret_invalid',
            'wallet_secret_save_warning' => 'collection.wallet.wallet_secret_save_warning',
            'wallet_secret_generated' => 'collection.wallet.wallet_secret_generated',
            'wallet_new_connection' => 'collection.wallet.wallet_new_connection',
            'wallet_existing_connection' => 'collection.wallet.wallet_existing_connection',
            'wallet_secret_copy_prompt' => 'collection.wallet.wallet_secret_copy_prompt',
            'wallet_lost_warning' => 'collection.wallet.wallet_lost_warning',
            'wallet_connection_failed' => 'collection.wallet.wallet_connection_failed',
            'wallet_validation_error' => 'collection.wallet.wallet_validation_error',
            'wallet_connect_title' => 'collection.wallet.wallet_connect_title',
            'wallet_connect_subtitle' => 'collection.wallet.wallet_connect_subtitle',
            'wallet_address_label' => 'collection.wallet.wallet_address_label',
            'wallet_address_placeholder' => 'collection.wallet.wallet_address_placeholder',
            'wallet_address_help' => 'collection.wallet.wallet_address_help',
            'wallet_secret_label' => 'collection.wallet.wallet_secret_label',
            'wallet_secret_placeholder' => 'collection.wallet.wallet_secret_placeholder',
            'wallet_secret_help' => 'collection.wallet.wallet_secret_help',
            'wallet_connect_button' => 'collection.wallet.wallet_connect_button',
            'wallet_weak_auth_info' => 'collection.wallet.wallet_weak_auth_info',
            'wallet_register_full' => 'collection.wallet.wallet_register_full',
            'wallet_reason' => 'collection.wallet.reason',
            'wallet_change_request_approved' => 'collection.wallet.wallet_change_request_approved',
            'wallet_creator_does_not_have_enough_quota_to_allocate' => 'collection.wallet.creator_does_not_have_enough_quota_to_allocate',
            'wallet_creator_wallet_not_found' => 'collection.wallet.creator_wallet_not_found',
            'wallet_total_exceeds_the_maximum_allowed_percentage' => 'collection.wallet.total_exceeds_the_maximum_allowed_percentage',
            'wallet_updated_successfully' => 'collection.wallet.wallet_updated_successfully',
            'wallet_modification_has_been_submitted_for_approval' => 'collection.wallet.modification_has_been_submitted_for_approval',
            'wallet_creation_request_success' => 'collection.wallet.creation_request_success',
            'wallet_create_denied' => 'collection.wallet.create_denied',
            'wallet_proposal_rejected' => 'collection.wallet.proposal_rejected',
            'wallet_creation_request' => 'collection.wallet.wallet_creation_request',
            'wallet_update_request' => 'collection.wallet.wallet_update_request',
            'wallet_creation_error' => 'collection.wallet.creation_error',
            'wallet_creation_error_generic' => 'collection.wallet.creation_error_generic',
            'wallet_confirmation_title' => 'collection.wallet.confirmation_title',
            'wallet_confirmation_text' => 'collection.wallet.confirmation_text',
            'wallet_confirm_delete' => 'collection.wallet.confirm_delete',
            'wallet_cancel_delete' => 'collection.wallet.cancel_delete',
            'wallet_deletion_error' => 'collection.wallet.deletion_error',
            'wallet_deletion_error_generic' => 'collection.wallet.deletion_error_generic',
            'wallet_address_placeholder' => 'collection.wallet.address_placeholder',
            'wallet_royalty_mint_placeholder' => 'collection.wallet.royalty_mint_placeholder',
            'wallet_royalty_rebind_placeholder' => 'collection.wallet.royalty_rebind_placeholder',
            'wallet_creation_success' => 'collection.wallet.creation_success',
            'wallet_creation_success_detail' => 'collection.wallet.creation_success_detail',
            'wallet_permission_denied' => 'collection.wallet.permission_denied',
            'wallet_modal_subtitle' => 'collection.wallet.nft_subtitle',

            // Chiavi validation (semplificate rimuovendo collection.wallet.validation.)
            'wallet_validation_check_pending_wallet' => 'collection.wallet.validation.check_pending_wallet',
            'wallet_validation_check_pending_wallet_title' => 'collection.wallet.validation.check_pending_wallet_title',
            'wallet_validation_address_required' => 'collection.wallet.validation.address_required',
            'wallet_validation_mint_invalid' => 'collection.wallet.validation.mint_invalid',
            'wallet_validation_rebind_invalid' => 'collection.wallet.validation.rebind_invalid',
            'wallet_validation_invalid_action' => 'collection.wallet.validation.invalid_action',

            // Chiavi invitation (semplificate rimuovendo collection.invitation.)
            'invitation_proposal_collaboration' => 'collection.invitation.proposal_collaboration',
            'invitation_confirmation_title' => 'collection.invitation.confirmation_title',
            'invitation_confirmation_text' => 'collection.invitation.confirmation_text',
            'invitation_confirm_delete' => 'collection.invitation.confirm_delete',
            'invitation_cancel_delete' => 'collection.invitation.cancel_delete',
            'invitation_deletion_error' => 'collection.invitation.deletion_error',
            'invitation_deletion_error_generic' => 'collection.invitation.deletion_error_generic',
            'invitation_create_invitation' => 'collection.invitation.create_invitation',

            // Chiavi collaborators (semplificate rimuovendo collection.collaborators.)
            'collaborators_add_denied' => 'collection.collaborators.add_denied',
            'collaborators_add' => 'collection.collaborators.add',
            'collaborators_add_placeholder' => 'collection.collaborators.add_placeholder',
            'collaborators_add_button' => 'collection.collaborators.add_button',
            'collaborators_add_success' => 'collection.collaborators.add_success',
            'collaborators_remove_denied' => 'collection.collaborators.remove_denied',
            'collaborators_remove' => 'collection.collaborators.remove',
            'collaborators_remove_button' => 'collection.collaborators.remove_button',
            'collaborators_remove_success' => 'collection.collaborators.remove_success',
            'collaborators_remove_confirm' => 'collection.collaborators.remove_confirm',
            'collaborators_remove_confirm_button' => 'collection.collaborators.remove_confirm_button',
            'collaborators_remove_cancel_button' => 'collection.collaborators.remove_cancel_button',
            'collaborators_remove_error' => 'collection.collaborators.remove_error',
            'collaborators_remove_error_message' => 'collection.collaborators.remove_error_message',
            'collaborators_remove_error_button' => 'collection.collaborators.remove_error_button',
            'collaborators_add_error' => 'collection.collaborators.add_error',
            'collaborators_proposal_rejected' => 'collection.collaborators.proposal_rejected',
            'collaborators_proposal_accepted' => 'collection.collaborators.proposal_accepted',
        ];
    }

    /**
     * @Oracode Get collection-specific translation keys
     * 🎯 Purpose: Map collection translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array Collection key mappings (simplified => full)
     */
    private function getReservationTranslationMappings(): array {
        return [
            'reservation.status.active' => 'reservation.status.active',
            'reservation.status.pending' => 'reservation.status.pending',
            'reservation.status.cancelled' => 'reservation.status.cancelled',
            'reservation.status.expired' => 'reservation.status.expired',
            'reservation.history.view_certificate' => 'reservation.history.view_certificate',
            'reservation.history.entries' => 'reservation.history.entries',
            'reservation.history.title' => 'reservation.history.title',
            'reservation.success' => 'reservation.success',
            'reservation.cancel_success' => 'reservation.cancel_success',
            'reservation.unauthorized' => 'reservation.unauthorized',
            'reservation.validation_failed' => 'reservation.validation_failed',
            'reservation.auth_required' => 'reservation.auth_required',
            'reservation.list_failed' => 'reservation.list_failed',
            'reservation.status_failed' => 'reservation.status_failed',
            'reservation.unauthorized_cancel' => 'reservation.unauthorized_cancel',
            'reservation.cancel_failed' => 'reservation.cancel_failed',

            'reservation.form.title' => 'reservation.form.title',
            'reservation.form.offer_amount_label' => 'reservation.form.offer_amount_label',
            'reservation.form.offer_amount_placeholder' => 'reservation.form.offer_amount_placeholder',
            'reservation.form.algo_equivalent' => 'reservation.form.algo_equivalent',
            'reservation.form.terms_accepted' => 'reservation.form.terms_accepted',
            'reservation.form.contact_info' => 'reservation.form.contact_info',
            'reservation.form.submit_button' => 'reservation.form.submit_button',
            'reservation.form.cancel_button' => 'reservation.form.cancel_button',

            'reservation.button.reserve' => 'reservation.button.reserve',
            'reservation.button.reserved' => 'reservation.button.reserved',
            'reservation.button.make_offer' => 'reservation.button.make_offer',

            'reservation.badge.highest' => 'reservation.badge.highest',
            'reservation.badge.superseded' => 'reservation.badge.superseded',
            'reservation.badge.has_offers' => 'reservation.badge.has_offers',

            'reservation.already_reserved.title' => 'reservation.already_reserved.title',
            'reservation.already_reserved.text' => 'reservation.already_reserved.text',
            'reservation.already_reserved.details' => 'reservation.already_reserved.details',
            'reservation.already_reserved.type' => 'reservation.already_reserved.type',
            'reservation.already_reserved.amount' => 'reservation.already_reserved.amount',
            'reservation.already_reserved.status' => 'reservation.already_reserved.status',
            'reservation.already_reserved.view_certificate' => 'reservation.already_reserved.view_certificate',
            'reservation.already_reserved.ok' => 'reservation.already_reserved.ok',
            'reservation.already_reserved.new_reservation' => 'reservation.already_reserved.new_reservation',
            'reservation.already_reserved.confirm_new' => 'reservation.already_reserved.confirm_new',

            'reservation.success_title' => 'reservation.success_title',
            'reservation.view_certificate' => 'reservation.view_certificate',
            'reservation.close' => 'reservation.close',

            'reservation.type.strong' => 'reservation.type.strong',
            'reservation.type.weak' => 'reservation.type.weak',
            'reservation.priority.highest' => 'reservation.priority.highest',
            'reservation.priority.superseded' => 'reservation.priority.superseded',

            'reservation.errors.button_click_error' => 'reservation.errors.button_click_error',
            'reservation.errors.form_validation' => 'reservation.errors.form_validation',
            'reservation.errors.api_error' => 'reservation.errors.api_error',
            'reservation.errors.unauthorized' => 'reservation.errors.unauthorized',
        ];
    }

    /**
     * @Oracode Get collection-specific translation keys
     * 🎯 Purpose: Map collection translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array Collection key mappings (simplified => full)
     */
    private function getLikeTranslationMappings(): array {
        return [
            'like.auth_required_title' => 'like.auth_required_title',
            'like.auth_required_for_like' => 'like.auth_required_for_like',
            'like.success_title' => 'like.success_title',
            'like.success_message' => 'like.success_message',
            'like.error_title' => 'like.error_title',
            'like.error_message' => 'like.error_message',
            'like.unlike_success_title' => 'like.unlike_success_title',
            'like.unlike_success_message' => 'like.unlike_success_message',
            'like.unlike_error_title' => 'like.unlike_error_title',
            'like.unlike_error_message' => 'like.unlike_error_message',
            
            // Toast notifications mappings
            'like.toast.egi.liked_title' => 'like.toast.egi.liked_title',
            'like.toast.egi.liked_message' => 'like.toast.egi.liked_message',
            'like.toast.egi.unliked_title' => 'like.toast.egi.unliked_title',
            'like.toast.egi.unliked_message' => 'like.toast.egi.unliked_message',
            'like.toast.collection.liked_title' => 'like.toast.collection.liked_title',
            'like.toast.collection.liked_message' => 'like.toast.collection.liked_message',
            'like.toast.collection.unliked_title' => 'like.toast.collection.unliked_title',
            'like.toast.collection.unliked_message' => 'like.toast.collection.unliked_message',
            
            // Button labels
            'like.add_to_favorites' => 'like.add_to_favorites',
            'like.remove_from_favorites' => 'like.remove_from_favorites',
            'like.likes_count' => 'like.likes_count',
        ];
    }

    /**
     * @Oracode Get notification translation keys
     * 🎯 Purpose: Map notification translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array Notification key mappings (simplified => full)
     *
     * @os1-compliance: Full - Centralizes notification translation management
     */
    private function getNotificationTranslationMappings(): array {
        return [
            'notificationLabelAdditionalDetails' => 'notification.label.additional_details',
            'notificationAriaActionsLabel' => 'notification.aria.actions_label',
            'notificationActionsLearnMore' => 'notification.actions.learn_more',
            'notificationActionsDone' => 'notification.actions.done',
        ];
    }

    /**
     * @return array Assistant key mappings (simplified => full)
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     * @version 2.0.0 (FlorenceEGI - Accordion Categories Support)
     * @date 2025-07-07
     */
    private function getAssistantTranslationMappings(): array {
        return [
            // === HEADER E ACCOGLIENZA ===
            'assistant.header.title' => 'assistant.header.title',
            'assistant.header.subtitle' => 'assistant.header.subtitle',
            'assistant.welcome' => 'assistant.welcome',
            'assistant.dismiss' => 'assistant.dismiss',
            'assistant.close_aria' => 'assistant.close_aria',

            // === CATEGORIE PRINCIPALI ===
            'assistant.category_understand' => 'assistant.category_understand',
            'assistant.category_understand_desc' => 'assistant.category_understand_desc',
            'assistant.category_start' => 'assistant.category_start',
            'assistant.category_start_desc' => 'assistant.category_start_desc',
            'assistant.category_explore' => 'assistant.category_explore',
            'assistant.category_explore_desc' => 'assistant.category_explore_desc',
            'assistant.category_identity' => 'assistant.category_identity',
            'assistant.category_identity_desc' => 'assistant.category_identity_desc',
            'assistant.category_lost' => 'assistant.category_lost',
            'assistant.category_lost_desc' => 'assistant.category_lost_desc',

            // === CAPIRE DOVE SONO ===
            'assistant.what_is_florenceegi' => 'assistant.what_is_florenceegi',
            'assistant.what_is_florenceegi_desc' => 'assistant.what_is_florenceegi_desc',
            'assistant.what_are_egis' => 'assistant.what_are_egis',
            'assistant.what_are_egis_desc' => 'assistant.what_are_egis_desc',
            'assistant.why_cant_buy_egis' => 'assistant.why_cant_buy_egis',
            'assistant.why_cant_buy_egis_desc' => 'assistant.why_cant_buy_egis_desc',

            // === INIZIARE SUBITO ===
            'assistant.create_egi' => 'assistant.create_egi',
            'assistant.create_egi_desc' => 'assistant.create_egi_desc',
            'assistant.reserve_egi' => 'assistant.reserve_egi',
            'assistant.reserve_egi_desc' => 'assistant.reserve_egi_desc',
            'assistant.create_collection' => 'assistant.create_collection',
            'assistant.create_collection_desc' => 'assistant.create_collection_desc',

            // === CONOSCERE I PROTAGONISTI ===
            'assistant.discover_archetypes' => 'assistant.discover_archetypes',
            'assistant.discover_archetypes_desc' => 'assistant.discover_archetypes_desc',
            'assistant.become_patron' => 'assistant.become_patron',
            'assistant.become_patron_desc' => 'assistant.become_patron_desc',
            'assistant.become_collector' => 'assistant.become_collector',
            'assistant.become_collector_desc' => 'assistant.become_collector_desc',
            'assistant.become_creator' => 'assistant.become_creator',
            'assistant.become_creator_desc' => 'assistant.become_creator_desc',
            'assistant.become_commissioner' => 'assistant.become_commissioner',
            'assistant.become_commissioner_desc' => 'assistant.become_commissioner_desc',
            'assistant.become_epp' => 'assistant.become_epp',
            'assistant.become_epp_desc' => 'assistant.become_epp_desc',
            'assistant.become_company' => 'assistant.become_company',
            'assistant.become_company_desc' => 'assistant.become_company_desc',
            'assistant.become_trader_pro' => 'assistant.become_trader_pro',
            'assistant.become_trader_pro_desc' => 'assistant.become_trader_pro_desc',

            // === IDENTITÀ NARRATIVA ===
            'assistant.who_is_natan' => 'assistant.who_is_natan',
            'assistant.who_is_natan_desc' => 'assistant.who_is_natan_desc',
            'assistant.florence_story' => 'assistant.florence_story',
            'assistant.florence_story_desc' => 'assistant.florence_story_desc',

            // === MI SENTO PERSO ===
            'assistant.guided_tour' => 'assistant.guided_tour',
            'assistant.guided_tour_desc' => 'assistant.guided_tour_desc',
            'assistant.personal_assistant' => 'assistant.personal_assistant',
            'assistant.personal_assistant_desc' => 'assistant.personal_assistant_desc',

            // === PAGINA "PERCHÉ NON SI POSSONO COMPRARE GLI EGI" ===
            'assistant.why_cant_buy_page_title' => 'assistant.why_cant_buy_page_title',
            'assistant.why_cant_buy_page_description' => 'assistant.why_cant_buy_page_description',
            'assistant.why_cant_buy_hero_title' => 'assistant.why_cant_buy_hero_title',
            'assistant.why_cant_buy_hero_subtitle' => 'assistant.why_cant_buy_hero_subtitle',
            'assistant.why_cant_buy_mvp_section_title' => 'assistant.why_cant_buy_mvp_section_title',
            'assistant.why_cant_buy_mvp_section_text' => 'assistant.why_cant_buy_mvp_section_text',
            'assistant.why_cant_buy_reservations_section_title' => 'assistant.why_cant_buy_reservations_section_title',
            'assistant.why_cant_buy_reservations_section_text' => 'assistant.why_cant_buy_reservations_section_text',
            'assistant.why_cant_buy_roadmap_section_title' => 'assistant.why_cant_buy_roadmap_section_title',
            'assistant.why_cant_buy_roadmap_section_text' => 'assistant.why_cant_buy_roadmap_section_text',
            'assistant.why_cant_buy_cta_title' => 'assistant.why_cant_buy_cta_title',
            'assistant.why_cant_buy_cta_text' => 'assistant.why_cant_buy_cta_text',
            'assistant.why_cant_buy_cta_button' => 'assistant.why_cant_buy_cta_button',
            'assistant.why_cant_buy_back_button' => 'assistant.why_cant_buy_back_button',

            // === OPZIONI LEGACY (mantenute per compatibilità) ===
            'assistant.create_artwork' => 'assistant.create_artwork',
            'assistant.buy_artwork' => 'assistant.buy_artwork',
            'assistant.what_is_egi' => 'assistant.what_is_egi',
            'assistant.custom_egi' => 'assistant.custom_egi',
            'assistant.white_paper' => 'assistant.white_paper',
            'assistant.let_me_guide' => 'assistant.let_me_guide',
            'assistant.explore' => 'assistant.explore',
            'assistant.explore_desc' => 'assistant.explore_desc',
            'assistant.learn' => 'assistant.learn',
            'assistant.learn_desc' => 'assistant.learn_desc',
            'assistant.start' => 'assistant.start',
            'assistant.start_desc' => 'assistant.start_desc',
            'assistant.business' => 'assistant.business',
            'assistant.business_desc' => 'assistant.business_desc',
            'assistant.create_egi_contextual' => 'assistant.create_egi_contextual',

            // === EXTRA E UTILS ===
            'assistant.faq' => 'assistant.faq',
            'assistant.faq_desc' => 'assistant.faq_desc',
            'assistant.roadmap' => 'assistant.roadmap',
            'assistant.roadmap_desc' => 'assistant.roadmap_desc',
            'assistant.equilibrium_model' => 'assistant.equilibrium_model',
            'assistant.equilibrium_model_desc' => 'assistant.equilibrium_model_desc',

            // === MESSAGGI UI ===
            'assistant.category_expanded_aria' => 'assistant.category_expanded_aria',
            'assistant.category_collapsed_aria' => 'assistant.category_collapsed_aria',
            'assistant.expand_category' => 'assistant.expand_category',
            'assistant.collapse_category' => 'assistant.collapse_category',
            'assistant.loading' => 'assistant.loading',
            'assistant.coming_soon' => 'assistant.coming_soon',
            'assistant.under_construction' => 'assistant.under_construction',

            // === BUTLER ===
            'assistant.auto_open_label' => 'assistant.auto_open_label',
            'assistant.auto_open_aria' => 'assistant.auto_open_aria',
            'assistant.auto_open_hint' => 'assistant.auto_open_hint',

        ];
    }

    /**
     * @Oracode Get frontend translations
     * 🎯 Purpose: Provide all frontend-needed translations in simplified format
     * 📥 Input: Locale string
     * 📤 Output: Array of simplified translation keys
     *
     * @param  string  $lang  Current locale
     * @return array Simplified translations (key => translated_value)
     *
     * @os1-compliance: Full - Centralizes frontend translation management
     *
     * @performance: Cached at app-config level for 300 seconds
     */
    private function getFrontendTranslations(string $lang): array {
        // Merge all mappings - PATTERN CORRETTO
        $mappings = array_merge(
            $this->getGuestLayoutTranslationMappings(),
            $this->getCollectionTranslationMappings(),
            $this->getReservationTranslationMappings(),
            $this->getLikeTranslationMappings(),
            $this->getGdprTranslationMappings(),
            $this->getNotificationTranslationMappings(),
            $this->getAssistantTranslationMappings(),
            $this->getHeicTranslationMappings()
        );

        $translations = [];
        foreach ($mappings as $simplifiedKey => $laravelKey) {
            $translations[$simplifiedKey] = __($laravelKey);
        }

        // === LOG TRADUZIONI CARICATE ===
        $this->logger->info('Frontend translations loaded', [
            'language' => $lang,
            'total_mappings' => count($mappings),
            'total_translations' => count($translations),
            'channel' => $this->channel,
        ]);

        return $translations;
    }

    /**
     * @Oracode Get HEIC translation keys
     * 🎯 Purpose: Map HEIC detection translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array HEIC key mappings (simplified => full)
     *
     * @os1-compliance: Full - Centralizes HEIC translation management
     */
    private function getHeicTranslationMappings(): array {
        return [
            'heic_detection_title' => 'heic.heic_detection.title',
            'heic_detection_greeting' => 'heic.heic_detection.greeting',
            'heic_detection_explanation' => 'heic.heic_detection.explanation',
            'heic_detection_solutions_title' => 'heic.heic_detection.solutions_title',
            'heic_detection_solution_ios' => 'heic.heic_detection.solution_ios',
            'heic_detection_solution_share' => 'heic.heic_detection.solution_share',
            'heic_detection_solution_computer' => 'heic.heic_detection.solution_computer',
            'heic_detection_thanks' => 'heic.heic_detection.thanks',
            'heic_detection_understand_button' => 'heic.heic_detection.understand_button',
        ];
    }

    /**
     * @Oracode Get GDPR translation keys
     * 🎯 Purpose: Map GDPR notification translation keys to simplified names
     * 📥 Input: None
     * 📤 Output: Array of key mappings
     *
     * @return array GDPR key mappings (simplified => full)
     *
     * @os1-compliance: Full - Centralizes GDPR translation management
     */
    private function getGdprTranslationMappings(): array {
        return [
            // === AZIONI E STATI GLOBALI ===
            'gdprSuccess' => 'gdpr.success',
            'gdprError' => 'gdpr.error',
            'gdprWarning' => 'gdpr.warning',
            'gdprCancel' => 'gdpr.cancel',
            'gdprContinue' => 'gdpr.continue',
            'gdprSubmit' => 'gdpr.submit',
            'gdprSave' => 'gdpr.save',
            'notificationAcknowledged' => 'gdpr.notifications.acknowledged', // Spostato e centralizzato

            'gdprConsentUpdateSuccess' => 'gdpr.consent.update_success',
            'gdprConsentUpdateError' => 'gdpr.consent.update_error',
            'gdprConsentWithdraw' => 'gdpr.consent.withdraw',
            'gdprConsentWithdrawConfirm' => 'gdpr.consent.withdraw_confirm',

            'gdprStatusGranted' => 'gdpr.status.granted',
            'gdprStatusWithdrawn' => 'gdpr.status.withdrawn',
            'gdprStatusRejected' => 'gdpr.status.rejected',
            'gdprStatusActive' => 'gdpr.status.active',
            'gdprStatusPending' => 'gdpr.status.pending',

            'gdprErrorGeneral' => 'gdpr.errors.general',
            'gdprErrorUnauthorized' => 'gdpr.errors.unauthorized',
            'gdprErrorValidationFailed' => 'gdpr.errors.validation_failed',

            'gdprModalClarificationTitle' => 'gdpr.modal.clarification.title',
            'gdprModalClarificationExplanation' => 'gdpr.modal.clarification.explanation',
            'gdprModalRevokeButtonText' => 'gdpr.modal.revoke_button_text',
            'gdprModalRevokeDescription' => 'gdpr.modal.revoke_description',
            'gdprModalDisavowButtonText' => 'gdpr.modal.disavow_button_text',
            'gdprModalDisavowDescription' => 'gdpr.modal.disavow_description',

            'gdprModalConfirmationTitle' => 'gdpr.modal.confirmation.title',
            'gdprModalConfirmationWarning' => 'gdpr.modal.confirmation.warning',
            'gdprModalConfirmDisavow' => 'gdpr.modal.confirm_disavow',
            'gdprModalFinalWarning' => 'gdpr.modal.final_warning',

            'gdprModalConsequenceConsentRevocation' => 'gdpr.modal.consequences.consent_revocation',
            'gdprModalConsequenceSecurityNotification' => 'gdpr.modal.consequences.security_notification',
            'gdprModalConsequenceAccountReview' => 'gdpr.modal.consequences.account_review',
            'gdprModalConsequenceEmailConfirmation' => 'gdpr.modal.consequences.email_confirmation',
            'gdprModalSecurityTitle' => 'gdpr.modal.security.title',
            'gdprModalSecurityUnderstood' => 'gdpr.modal.security.understood',

        ];
    }

    /**
     * Returns the current upload limits considering both server and application settings.
     *
     * This method compares the server's PHP.ini settings (post_max_size, upload_max_filesize, max_file_uploads)
     * with the application's configured limits (max_total_size, max_file_size, max_files) and returns the most
     * restrictive values. It also logs a warning and notifies the dev team if the server limits are more restrictive.
     *
     * @return \Illuminate\Http\JsonResponse Response with effective upload limits
     */
    public function getUploadLimits() {
        // Limite server (php.ini)
        $serverPostMaxSize = $this->parseSize(ini_get('post_max_size'));
        $serverUploadMaxFilesize = $this->parseSize(ini_get('upload_max_filesize'));
        $serverMaxFileUploads = (int) ini_get('max_file_uploads');

        // Limiti applicazione (config)
        $appMaxTotalSize = $this->parseSize(config('AllowedFileType.collection.post_max_size', ini_get('post_max_size')));
        $appMaxFileSize = $this->parseSize(config('AllowedFileType.collection.upload_max_filesize', ini_get('upload_max_filesize')));
        $appMaxFiles = (int) config('AllowedFileType.collection.max_file_uploads', ini_get('max_file_uploads'));
        $sizeMargin = (float) config('upload-manager.size_margin', 1.1); // Aggiunto

        // Usa il limite più restrittivo tra server e applicazione
        $effectiveTotalSize = min($serverPostMaxSize, $appMaxTotalSize);
        $effectiveFileSize = min($serverUploadMaxFilesize, $appMaxFileSize);
        $effectiveMaxFiles = min($serverMaxFileUploads, $appMaxFiles);

        // Genera warning se i limiti del server sono più restrittivi dell'applicazione
        if (
            $serverPostMaxSize < $appMaxTotalSize ||
            $serverUploadMaxFilesize < $appMaxFileSize ||
            $serverMaxFileUploads < $appMaxFiles
        ) {

            UltraError::handle('SERVER_LIMITS_RESTRICTIVE', [
                'server_post_max_size' => ini_get('post_max_size'),
                'app_max_total_size' => config('upload-manager.max_total_size'),
                'server_upload_max_filesize' => ini_get('upload_max_filesize'),
                'app_max_file_size' => config('upload-manager.max_file_size'),
                'server_max_file_uploads' => $serverMaxFileUploads,
                'app_max_files' => $appMaxFiles,
            ], new Exception(trans('uploadmanager::uploadmanager.dev.server_limits_restrictive')));
        }

        return response()->json([
            // Limiti effettivi (i più restrittivi)
            'max_total_size' => $effectiveTotalSize,
            'max_file_size' => $effectiveFileSize,
            'max_files' => $effectiveMaxFiles,

            // Valori formattati per la visualizzazione
            'max_total_size_formatted' => $this->formatSize($effectiveTotalSize),
            'max_file_size_formatted' => $this->formatSize($effectiveFileSize),

            // Flag per indicare da dove provengono i limiti
            'total_size_limited_by' => ($serverPostMaxSize <= $appMaxTotalSize) ? 'server' : 'app',
            'file_size_limited_by' => ($serverUploadMaxFilesize <= $appMaxFileSize) ? 'server' : 'app',
            'max_files_limited_by' => ($serverMaxFileUploads <= $appMaxFiles) ? 'server' : 'app',

            // Margine di sicurezza
            'size_margin' => $sizeMargin,

            'allowedExtensions' => config('AllowedFileType.collection.allowed_extensions', [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'pdf',
                'doc',
                'docx',
            ]),
            'allowedMimeTypes' => config('AllowedFileType.collection.allowed_mime_types', [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]),
            'envMode' => config('app.env', 'production'),
        ]);
    }

    /**
     * Converts size string (like "8M") to bytes.
     *
     * @param  string  $size  Size string to parse (e.g., "8M", "2G")
     * @return int Size in bytes
     */
    private function parseSize($size) {
        // Extract unit (last character if it's a letter)
        $unit = '';
        $lastChar = substr($size, -1);
        if (ctype_alpha($lastChar)) {
            $unit = $lastChar;
        }

        // Extract numeric part
        $numericSize = floatval($size);

        if ($unit) {
            $multiplier = stripos('KMGTPEZY', strtoupper($unit));
            if ($multiplier !== false) {
                return round($numericSize * pow(1024, $multiplier + 1));
            }
        }

        return round($numericSize);
    }

    /**
     * Formats bytes into human-readable size.
     *
     * @param  int  $bytes  Size in bytes
     * @return string Formatted size (e.g., "8 MB")
     */
    private function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * @Oracode Get application settings
     * 🎯 Purpose: Provide app feature configuration
     * 📤 Output: Settings array
     *
     * @return array Application settings
     *
     * @internal Includes file upload, EGI, and feature flags
     */
    private function getAppSettings(): array {
        return [
            'allowedExtensions' => config('AllowedFileType.collection.allowed_extensions', []),
            'allowedMimeTypes' => config('AllowedFileType.collection.allowed_mime_types', []),
            'maxFileSize' => config('AllowedFileType.collection.max_size', 10 * 1024 * 1024),
            'egiSettings' => [
                'minPrice' => config('egi.min_price', 0),
                'maxPrice' => config('egi.max_price', 999999),
                'commissionRate' => config('egi.commission_rate', 0.025),
            ],
            'features' => [
                'walletSecretEnabled' => config('features.wallet_secret', true),
                'auctionsEnabled' => config('features.auctions', false),
                'reservationsEnabled' => config('features.reservations', true),
            ],
        ];
    }

    /**
     * Get currency system configuration
     * 🎯 Purpose: Provide currency settings to frontend
     * 📤 Output: Currency configuration including supported currencies
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrencyConfig(): \Illuminate\Http\JsonResponse {
        try {
            $currencyConfig = [
                'supported_currencies' => config('app.currency.supported_currencies', ['USD', 'EUR', 'GBP']),
                'default_currency' => config('app.currency.default_currency', 'USD'),
                'api_source' => config('app.currency.api_source', 'coingecko'),
                'cache_ttl_seconds' => config('app.currency.cache_ttl_seconds', 60),
            ];

            return response()->json([
                'success' => true,
                'data' => $currencyConfig,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get currency configuration', [
                'error' => $e->getMessage(),
                'channel' => $this->channel,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load currency configuration',
            ], 500);
        }
    }
}