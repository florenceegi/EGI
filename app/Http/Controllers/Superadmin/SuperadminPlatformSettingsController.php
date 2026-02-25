<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Platform Settings CRUD)
 * @date 2026-02-25
 * @purpose Gestione impostazioni di piattaforma dal DB (platform_settings).
 *          Index con tutti i setting raggruppati + edit inline per valore.
 */
class SuperadminPlatformSettingsController extends Controller
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * Lista tutti i setting raggruppati per group.
     */
    public function index(): View | RedirectResponse
    {
        try {
            $settings = PlatformSetting::orderBy('group')->orderBy('key')->get()
                ->groupBy('group');

            return view('superadmin.settings.index', compact('settings'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('PLATFORM_SETTINGS_INDEX_FAILED', [], $e);
        }
    }

    /**
     * Aggiorna un singolo setting (chiamata da form inline).
     */
    public function update(Request $request, PlatformSetting $setting): RedirectResponse
    {
        try {
            $request->validate([
                'value' => 'required|string|max:1000',
            ]);

            $oldValue = $setting->value;

            $setting->update(['value' => $request->value]);
            PlatformSetting::invalidateCache();

            $this->logger->info('Platform setting updated', [
                'group'     => $setting->group,
                'key'       => $setting->key,
                'old_value' => $oldValue,
                'new_value' => $request->value,
                'log_category' => 'PLATFORM_SETTINGS_UPDATE',
            ]);

            return redirect()
                ->route('superadmin.platform-settings.index')
                ->with('success', "Setting [{$setting->group}.{$setting->key}] aggiornato.");

        } catch (\Exception $e) {
            return $this->errorManager->handle('PLATFORM_SETTINGS_UPDATE_FAILED', [
                'setting_id' => $setting->id,
            ], $e);
        }
    }

    /**
     * Aggiornamento bulk di tutti i setting di un gruppo via form.
     */
    public function updateGroup(Request $request, string $group): RedirectResponse
    {
        try {
            $request->validate([
                'settings'   => 'required|array',
                'settings.*' => 'nullable|string|max:1000',
            ]);

            $updated = 0;
            foreach ($request->settings as $id => $value) {
                $setting = PlatformSetting::find((int) $id);
                if ($setting && $setting->group === $group && $setting->is_editable) {
                    $setting->update(['value' => (string) $value]);
                    $updated++;
                }
            }

            PlatformSetting::invalidateCache();

            $this->logger->info('Platform settings group updated', [
                'group'   => $group,
                'updated' => $updated,
                'log_category' => 'PLATFORM_SETTINGS_GROUP_UPDATE',
            ]);

            return redirect()
                ->route('superadmin.platform-settings.index')
                ->with('success', "{$updated} setting del gruppo [{$group}] aggiornati.");

        } catch (\Exception $e) {
            return $this->errorManager->handle('PLATFORM_SETTINGS_GROUP_UPDATE_FAILED', [
                'group' => $group,
            ], $e);
        }
    }
}
