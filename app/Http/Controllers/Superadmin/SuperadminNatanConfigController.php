<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - NATAN Configuration Panel)
 * @date 2025-10-27
 * @purpose Superadmin panel for NATAN AI system configuration
 */
class SuperadminNatanConfigController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->middleware('auth');
        $this->middleware('can:manage_system_settings');
    }

    /**
     * Show NATAN configuration panel
     *
     * @return View
     */
    public function index(): View {
        try {
            $this->logger->info('[SuperAdmin NATAN Config] Configuration panel accessed', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            $config = [
                // Claude API limits
                'claude_context_limit' => config('natan.claude_context_limit'),
                'claude_context_limit_minimum' => config('natan.claude_context_limit_minimum'),

                // Token management
                'max_tokens_per_call' => config('natan.max_tokens_per_call'),
                'reserved_tokens_system' => config('natan.reserved_tokens_system'),
                'reserved_tokens_output' => config('natan.reserved_tokens_output'),
                'avg_tokens_per_char' => config('natan.avg_tokens_per_char'),

                // User controls
                'slider_min_acts' => config('natan.slider_min_acts'),
                'slider_max_acts' => config('natan.slider_max_acts'),
                'slider_default_acts' => config('natan.slider_default_acts'),

                // Cost estimation
                'cost_per_chunk' => config('natan.cost_per_chunk'),
                'cost_aggregation' => config('natan.cost_aggregation'),

                // Time estimation
                'time_per_chunk_seconds' => config('natan.time_per_chunk_seconds'),
                'time_aggregation_seconds' => config('natan.time_aggregation_seconds'),

                // Quality controls
                'min_relevance_score' => config('natan.min_relevance_score'),
                'chunking_strategy' => config('natan.chunking_strategy'),

                // System features
                'enable_progress_tracking' => config('natan.enable_progress_tracking'),

                // Rate limiting
                'rate_limit_max_retries' => config('natan.rate_limit_max_retries'),
                'rate_limit_initial_delay_seconds' => config('natan.rate_limit_initial_delay_seconds'),
            ];

            return view('superadmin.natan.config', compact('config'));
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_CONFIG_INDEX_ERROR', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ], $e);

            return view('superadmin.natan.config')->withErrors([
                'error' => __('messages.error_loading_config')
            ]);
        }
    }

    /**
     * Update NATAN configuration
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                // Claude API limits
                'claude_context_limit' => 'required|integer|min:5|max:500',
                'claude_context_limit_minimum' => 'required|integer|min:1|max:50',

                // Token management
                'max_tokens_per_call' => 'required|integer|min:10000|max:200000',
                'reserved_tokens_system' => 'required|integer|min:500|max:10000',
                'reserved_tokens_output' => 'required|integer|min:1000|max:20000',
                'avg_tokens_per_char' => 'required|numeric|min:0.1|max:1.0',

                // User controls
                'slider_min_acts' => 'required|integer|min:10|max:1000',
                'slider_max_acts' => 'required|integer|min:100|max:50000',
                'slider_default_acts' => 'required|integer|min:50|max:10000',

                // Cost estimation
                'cost_per_chunk' => 'required|numeric|min:0.01|max:10.0',
                'cost_aggregation' => 'required|numeric|min:0.01|max:5.0',

                // Time estimation
                'time_per_chunk_seconds' => 'required|integer|min:1|max:120',
                'time_aggregation_seconds' => 'required|integer|min:1|max:60',

                // Quality controls
                'min_relevance_score' => 'required|numeric|min:0.0|max:1.0',
                'chunking_strategy' => 'required|in:token-based,relevance-based,adaptive',

                // System features
                'enable_progress_tracking' => 'required|boolean',

                // Rate limiting
                'rate_limit_max_retries' => 'required|integer|min:1|max:10',
                'rate_limit_initial_delay_seconds' => 'required|integer|min:1|max:60',
            ]);

            $this->logger->info('[SuperAdmin NATAN Config] Configuration update requested', [
                'user_id' => $user->id,
                'changes' => $validated,
            ]);

            // Update .env file
            $this->updateEnvFile($validated);

            // Audit trail
            $this->auditService->logUserAction(
                $user,
                'NATAN configuration updated',
                ['changes' => $validated],
                GdprActivityCategory::SYSTEM_CONFIGURATION
            );

            $this->logger->info('[SuperAdmin NATAN Config] Configuration updated successfully', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('superadmin.natan.config')
                ->with('success', __('natan.config.updated_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_CONFIG_UPDATE_ERROR', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ], $e);

            return redirect()->back()
                ->withErrors(['error' => __('messages.error_updating_config')])
                ->withInput();
        }
    }

    /**
     * Update .env file with new configuration
     *
     * @param array $config
     * @return void
     */
    private function updateEnvFile(array $config): void {
        $envFile = base_path('.env');
        $envContent = \file_get_contents($envFile);

        $envMapping = [
            'claude_context_limit' => 'NATAN_CLAUDE_CONTEXT_LIMIT',
            'claude_context_limit_minimum' => 'NATAN_CLAUDE_CONTEXT_LIMIT_MINIMUM',
            'max_tokens_per_call' => 'NATAN_MAX_TOKENS_PER_CALL',
            'reserved_tokens_system' => 'NATAN_RESERVED_TOKENS_SYSTEM',
            'reserved_tokens_output' => 'NATAN_RESERVED_TOKENS_OUTPUT',
            'avg_tokens_per_char' => 'NATAN_AVG_TOKENS_PER_CHAR',
            'slider_min_acts' => 'NATAN_SLIDER_MIN_ACTS',
            'slider_max_acts' => 'NATAN_SLIDER_MAX_ACTS',
            'slider_default_acts' => 'NATAN_SLIDER_DEFAULT_ACTS',
            'cost_per_chunk' => 'NATAN_COST_PER_CHUNK',
            'cost_aggregation' => 'NATAN_COST_AGGREGATION',
            'time_per_chunk_seconds' => 'NATAN_TIME_PER_CHUNK_SECONDS',
            'time_aggregation_seconds' => 'NATAN_TIME_AGGREGATION_SECONDS',
            'min_relevance_score' => 'NATAN_MIN_RELEVANCE_SCORE',
            'chunking_strategy' => 'NATAN_CHUNKING_STRATEGY',
            'enable_progress_tracking' => 'NATAN_ENABLE_PROGRESS_TRACKING',
            'rate_limit_max_retries' => 'NATAN_RATE_LIMIT_MAX_RETRIES',
            'rate_limit_initial_delay_seconds' => 'NATAN_RATE_LIMIT_INITIAL_DELAY',
        ];

        foreach ($config as $key => $value) {
            if (!isset($envMapping[$key])) {
                continue;
            }

            $envKey = $envMapping[$key];
            $envValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;

            // Check if key exists
            if (\preg_match("/^{$envKey}=/m", $envContent)) {
                // Update existing
                $envContent = \preg_replace(
                    "/^{$envKey}=.*/m",
                    "{$envKey}={$envValue}",
                    $envContent
                );
            } else {
                // Add new
                $envContent .= "\n{$envKey}={$envValue}";
            }
        }

        \file_put_contents($envFile, $envContent);

        // Clear config cache
        \Artisan::call('config:clear');
    }

    /**
     * Reset configuration to defaults
     *
     * @return RedirectResponse
     */
    public function reset(): RedirectResponse {
        try {
            $user = auth()->user();

            $this->logger->info('[SuperAdmin NATAN Config] Reset to defaults requested', [
                'user_id' => $user->id,
            ]);

            $defaults = [
                'claude_context_limit' => 100,
                'claude_context_limit_minimum' => 5,
                'max_tokens_per_call' => 180000,
                'reserved_tokens_system' => 2000,
                'reserved_tokens_output' => 8000,
                'avg_tokens_per_char' => 0.25,
                'slider_min_acts' => 50,
                'slider_max_acts' => 5000,
                'slider_default_acts' => 500,
                'cost_per_chunk' => 0.09,
                'cost_aggregation' => 0.03,
                'time_per_chunk_seconds' => 10,
                'time_aggregation_seconds' => 15,
                'min_relevance_score' => 0.3,
                'chunking_strategy' => 'token-based',
                'enable_progress_tracking' => true,
                'rate_limit_max_retries' => 3,
                'rate_limit_initial_delay_seconds' => 2,
            ];

            $this->updateEnvFile($defaults);

            // Audit trail
            $this->auditService->logUserAction(
                $user,
                'NATAN configuration reset to defaults',
                ['defaults' => $defaults],
                GdprActivityCategory::SYSTEM_CONFIGURATION
            );

            return redirect()->route('superadmin.natan.config')
                ->with('success', __('natan.config.reset_successfully'));
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_CONFIG_RESET_ERROR', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ], $e);

            return redirect()->back()
                ->withErrors(['error' => __('messages.error_resetting_config')]);
        }
    }
}
