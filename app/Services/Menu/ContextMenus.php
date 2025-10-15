<?php

namespace App\Services\Menu;

use App\Services\Menu\Items\BackToDashboardMenu;
use App\Services\Menu\Items\EgiUploadPageMenu;
use App\Services\Menu\Items\PermissionsRolesMenu;
use App\Services\Menu\Items\AssignRolesMenu;
use App\Services\Menu\Items\AssignPermissionsMenu;
use App\Services\Menu\Items\OpenCollectionMenu;
use App\Services\Menu\Items\NewCollectionMenu;
// Nuovi item per i menu contestuali
use App\Services\Menu\Items\StatisticsMenu;
use App\Services\Menu\Items\WalletMenu;
use App\Services\Menu\Items\AccountDataMenu;
use App\Services\Menu\Items\ActivityLogMenu;
use App\Services\Menu\Items\BioProfileMenu;
use App\Services\Menu\Items\BreachReportMenu;
use App\Services\Menu\Items\ConsentMenu;
use App\Services\Menu\Items\DeleteAccountMenu;
use App\Services\Menu\Items\DocumentationMenu;
use App\Services\Menu\Items\EditPersonalDataMenu;
use App\Services\Menu\Items\ExportDataMenu;
use App\Services\Menu\Items\LimitProcessingMenu;
use App\Services\Menu\Items\MyDocumentsMenu;
use App\Services\Menu\Items\MyInvoicePreferencesMenu;
use App\Services\Menu\Items\MyOrganizationMenu;
use App\Services\Menu\Items\MyPersonalDataMenu;
use App\Services\Menu\Items\MyProfileMenu;
use App\Services\Menu\Items\PrivacyPolicyMenu;
use App\Services\Menu\Items\TermOfServiceMenu;
use App\Services\Menu\Items\BiographyMenu;
use App\Services\Menu\Items\ViewBiographyMenu;
// PA Enterprise Menu Items
use App\Services\Menu\Items\PADashboardMenu;
use App\Services\Menu\Items\PAHeritageMenu;
use App\Services\Menu\Items\PACoAMenu;
use App\Services\Menu\Items\PAInspectorsMenu;
use App\Services\Menu\Items\PAActsMenu;
use App\Services\Menu\Items\PAStatisticsMenu;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Service: Context-aware Menu Provider
 * 🎯 Purpose: Provides appropriate menus based on application context
 * 🧱 Core Logic: Factory method for context-specific menu groups
 *
 * @package App\Services\Menu
 * @version 2.0
 */
class ContextMenus
{
    /**
     * Get menu groups for specific application context
     *
     * @param string $context The current application context
     * @return array Array of MenuGroup objects for the context
     */
    public static function getMenusForContext(string $context): array
    {
        $menus = [];

        Log::channel('upload')->info('🔍 CONTEXT MENUS - PA CONTEXT DETECTED', [
            'context' => $context,
        ]);

        switch ($context) {
            case 'dashboard':
                // Dashboard main menu
                $collectionsMenu = new MenuGroup(__('menu.collections'), 'folder_collection', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                ]);
                $menus[] = $collectionsMenu;

                // Statistics menu
                $statsMenu = new MenuGroup(__('menu.statistics'), 'chart-bar', [
                    new StatisticsMenu(),
                ]);
                $menus[] = $statsMenu;

                // Personal data menu
                $personalDataMenu = new MenuGroup(__('menu.personal_data'), 'user-cog', [

                    // new MyProfileMenu(),            // permission: 'edit_own_profile_data'
                    new MyPersonalDataMenu(),       // permission: 'edit_own_personal_data'
                    new BiographyMenu(),
                    new ViewBiographyMenu(),
                    // new MyOrganizationMenu(),       // permission: 'edit_own_organization_data'
                    // new MyDocumentsMenu(),          // permission: 'manage_own_documents'
                    // new MyInvoicePreferencesMenu(), // permission: 'manage_own_invoice_preferences'
                    // new BioProfileMenu(),
                ]);
                $menus[] = $personalDataMenu;

                // Biography menu
                // $biographyMenu = new MenuGroup(__('menu.biography'), 'book', [
                //     new BiographyMenu(),
                //     new ViewBiographyMenu(),
                // ]);
                // $menus[] = $biographyMenu;

                // Wallet menu
                $walletMenu = new MenuGroup(__('menu.wallet'), 'wallet', [
                    new WalletMenu(),
                ]);
                $menus[] = $walletMenu;

                // GDPR menu
                $gdprMenu = new MenuGroup(__('menu.gdpr_privacy'), 'shield', [
                    new ConsentMenu(),
                    new AccountDataMenu(),
                    new ExportDataMenu(),
                    new EditPersonalDataMenu(),
                    new LimitProcessingMenu(),
                    new DeleteAccountMenu(),
                    new ActivityLogMenu(),
                    new BreachReportMenu(),
                    new PrivacyPolicyMenu(),
                    new TermOfServiceMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $gdprMenu;

                // Documentation menu
                $docsMenu = new MenuGroup(__('menu.documentation'), 'book', [
                    new DocumentationMenu(),
                ]);
                $menus[] = $docsMenu;

                // Admin tools menu (only for admin users)
                $adminMenu = new MenuGroup(__('menu.admin_tools'), 'tools', [
                    new PermissionsRolesMenu(),
                    new AssignRolesMenu(),
                    new AssignPermissionsMenu(),
                ]);
                $menus[] = $adminMenu;
                break;

            case 'collections':
                // Collections context menu
                $collectionsMenu = new MenuGroup(__('menu.collections'), 'folder_collection', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $collectionsMenu;
                break;

            case 'consents':

                $gdprPrivacyMenu = new MenuGroup(__('menu.gdpr_privacy'), 'shield', [
                    new ConsentMenu(),
                    new ExportDataMenu(),
                    new EditPersonalDataMenu(),
                    new LimitProcessingMenu(),
                    new DeleteAccountMenu(),
                    new ActivityLogMenu(),
                    new BreachReportMenu(),
                    new PrivacyPolicyMenu(),
                    // new TermOfServiceMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $gdprPrivacyMenu;

                break;

            case 'statistics':

                $statisticsMenu = new MenuGroup(__('menu.statistics'), 'chart-bar', [
                    new StatisticsMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $statisticsMenu;
                break;

            case 'pa.acts':
            case 'pa.natan.chat':
                // PA Acts Tokenization Context (route: pa.acts.index, pa.acts.upload, pa.acts.show)
                Log::channel('upload')->info('🔍 CONTEXT MENUS - PA ACTS CONTEXT', [
                    'context' => $context,
                ]);

                $paActsMenu = new MenuGroup(__('menu.pa_acts_management'), 'pa-acts', [

                    new MenuItem(
                        translationKey: 'menu.pa_acts_list',
                        route: 'pa.acts.index',
                        icon: 'pa-acts-list',
                        permission: 'access_pa_dashboard'
                    ),
                    new MenuItem(
                        translationKey: 'menu.pa_acts_upload',
                        route: 'pa.acts.upload',
                        icon: 'pa-acts-upload',
                        permission: 'access_pa_dashboard'
                    ),
                ]);
                $menus[] = $paActsMenu;

                break;

            // case 'egis':
            //     // PA Heritage Context (route: egis.index, egis.create, egis.edit, egis.show)
            //     Log::channel('upload')->info('🔍 CONTEXT MENUS - HERITAGE (EGIS) CONTEXT', [
            //         'context' => $context,
            //     ]);

            //     $heritageMenu = new MenuGroup(__('menu.heritage_management'), 'pa-heritage', [
            //         new MenuItem(
            //             translationKey: 'menu.heritage_list',
            //             route: 'egis.index',
            //             icon: 'pa-heritage-list',
            //             permission: 'manage_institutional_collections'
            //         ),
            //         new MenuItem(
            //             translationKey: 'menu.heritage_create',
            //             route: 'egis.create',
            //             icon: 'pa-heritage-create',
            //             permission: 'manage_institutional_collections'
            //         ),
            //     ]);
            //     $menus[] = $heritageMenu;

            //     // Navigation menu PA generale
            //     $paNavMenu = new MenuGroup(__('menu.pa_navigation'), 'pa-navigation', [
            //         new PAActsMenu(),
            //         new PACoAMenu(),
            //         new PAInspectorsMenu(),
            //     ]);
            //     $menus[] = $paNavMenu;
            //     break;

            case 'pa.egis':
            case 'pa.dashboard':
            case 'pa.heritage':
            case 'pa.coa':
            case 'pa.inspectors':
            case 'pa':
                // PA Enterprise Context (Dashboard, Heritage, CoA, Inspectors, generic)
                Log::channel('upload')->info('🔍 CONTEXT MENUS - PA DASHBOARD/GENERIC CONTEXT', [
                    'context' => $context,
                ]);

                $paMainMenu = new MenuGroup(__('menu.pa_management'), 'pa-building', [
                    new PAHeritageMenu(),
                    new PAActsMenu(),
                    new PAStatisticsMenu(),
                    new PACoAMenu(),
                    new PAInspectorsMenu(),
                ]);
                $menus[] = $paMainMenu;
                break;
        }

        return $menus;
    }
}
