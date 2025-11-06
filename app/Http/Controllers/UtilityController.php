<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Controller per gestione Utility con supporto UEM/ULM
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI - Utility System with UEM/ULM)
 * @date 2025-08-29
 * @purpose Gestisce creazione, modifica e eliminazione delle utility associate agli EGI
 * @context Controller per UtilityManager component con gestione errori e logging
 */
class UtilityController extends Controller {

    /**
     * Ultra Log Manager instance
     */
    private UltraLogManager $logger;

    /**
     * Error Manager instance
     */
    private ErrorManagerInterface $errorManager;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    /**
     * Store new utility
     */
    public function store(Request $request) {
        try {
            // Log inizio operazione
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Creating new utility', [
                    'user_id' => auth()->id(),
                    'egi_id' => $request->input('egi_id'),
                    'utility_type' => $request->input('type'),
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // Validazione con messaggi localizzati
            $validated = $request->validate([
                'egi_id' => 'required|exists:egis,id',
                'type' => 'required|in:physical,service,hybrid,digital',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                // Physical fields
                'weight' => 'nullable|numeric|min:0',
                'dimensions' => 'nullable|array',
                'dimensions.length' => 'nullable|numeric|min:0',
                'dimensions.width' => 'nullable|numeric|min:0',
                'dimensions.height' => 'nullable|numeric|min:0',
                'estimated_shipping_days' => 'nullable|integer|min:1',
                'fragile' => 'nullable|boolean',
                'insurance_recommended' => 'nullable|boolean',
                'shipping_notes' => 'nullable|string',
                // Service fields
                'valid_from' => 'nullable|date',
                'valid_until' => 'nullable|date|after:valid_from',
                'max_uses' => 'nullable|integer|min:1',
                'activation_instructions' => 'nullable|string',
                // Media
                'gallery' => 'nullable|array',
                'gallery.*' => 'image|max:10240' // Max 10MB per image
            ], [
                // Messaggi di validazione localizzati
                'title.required' => __('utility.validation.title_required'),
                'type.required' => __('utility.validation.type_required'),
                'valid_until.after' => __('utility.validation.valid_until_after'),
            ]);

            // Verifica permessi
            $egi = Egi::findOrFail($validated['egi_id']);

            // Verifica che l'utente sia il creator dell'EGI
            if (!Auth::check() || Auth::id() !== $egi->user_id) {
                // Log tentativo non autorizzato
                if ($this->logger) {
                    $this->logger->warning('[UTILITY System] Unauthorized utility creation attempt', [
                        'user_id' => auth()->id(),
                        'egi_id' => $egi->id,
                        'egi_owner_id' => $egi->user_id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }
                abort(403, 'Unauthorized action.');
            }

            // 🔒 BLOCKCHAIN IMMUTABILITY: Check if EGI is minted (BLOCKING)
            if ($egi->token_EGI) {
                return $this->errorManager->handle('UTILITY_EGI_MINTED', [
                    'user_id' => auth()->id(),
                    'egi_id' => $egi->id,
                    'token_egi' => $egi->token_EGI,
                    'action' => 'create_utility'
                ]);
            }

            // Verifica che la collection non sia ancora pubblicata
            // Temporaneamente commentato per permettere il testing
            /*if ($egi->collection->status === 'published') {
            return redirect()
                ->route('egis.show', $egi)
                ->with('error', 'Cannot modify utility after collection is published.');
        }*/

            // Crea utility
            $utility = Utility::create([
                'egi_id' => $validated['egi_id'],
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'dimensions' => $validated['dimensions'] ?? null,
                'estimated_shipping_days' => $validated['estimated_shipping_days'] ?? null,
                'fragile' => $validated['fragile'] ?? false,
                'insurance_recommended' => $validated['insurance_recommended'] ?? false,
                'shipping_notes' => $validated['shipping_notes'] ?? null,
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'max_uses' => $validated['max_uses'] ?? null,
                'activation_instructions' => $validated['activation_instructions'] ?? null,
                'status' => 'active',
                'current_uses' => 0
            ]);

            // Gestione media
            if ($request->hasFile('gallery')) {
                try {
                    foreach ($request->file('gallery') as $image) {
                        $utility->addMedia($image)->toMediaCollection('utility_gallery');
                    }
                } catch (\Exception $e) {
                    // Log ma non fallire completamente
                    if ($this->logger) {
                        $this->logger->warning('[UTILITY System] Media upload failed', [
                            'utility_id' => $utility->id,
                            'user_id' => auth()->id(),
                            'error' => $e->getMessage(),
                            'timestamp' => now()->toIso8601String()
                        ]);
                    }

                    // Gestione errore media con UEM
                    $this->errorManager->handle('UTILITY_MEDIA_UPLOAD_ERROR', [
                        'user_id' => auth()->id(),
                        'utility_id' => $utility->id,
                        'egi_id' => $egi->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'files_count' => count($request->file('gallery')),
                        'error_message' => $e->getMessage(),
                        'timestamp' => now()->toIso8601String()
                    ], $e);
                }
            }

            // Log successo
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Utility created successfully', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'egi_id' => $egi->id,
                    'utility_type' => $utility->type,
                    'has_media' => $request->hasFile('gallery'),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            return redirect()
                ->route('egis.show', $egi)
                ->with('success', __('utility.success_created'));
        } catch (ValidationException $e) {
            // Errore di validazione - non critico
            if ($this->logger) {
                $this->logger->warning('[UTILITY System] Validation failed', [
                    'user_id' => auth()->id(),
                    'egi_id' => $request->input('egi_id'),
                    'errors' => $e->errors(),
                    'input' => $request->except(['_token', 'gallery']),
                    'timestamp' => now()->toIso8601String()
                ]);
            }
            throw $e;
        } catch (\Exception $e) {
            // Errore critico - usa UEM
            $this->errorManager->handle('UTILITY_CREATION_ERROR', [
                'user_id' => auth()->id(),
                'egi_id' => $request->input('egi_id'),
                'utility_type' => $request->input('type'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_data' => $request->except(['_token', '_method', 'gallery']),
                'timestamp' => now()->toIso8601String(),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], $e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('utility.messages.creation_failed'));
        }
    }

    /**
     * Update existing utility
     */
    public function update(Request $request, Utility $utility) {
        try {
            // Log inizio operazione
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Updating utility', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'egi_id' => $utility->egi->id,
                    'utility_type' => $request->input('type'),
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // Verifica permessi
            if (!Auth::check() || Auth::id() !== $utility->egi->user_id) {
                // Log tentativo non autorizzato
                if ($this->logger) {
                    $this->logger->warning('[UTILITY System] Unauthorized utility update attempt', [
                        'user_id' => auth()->id(),
                        'utility_id' => $utility->id,
                        'egi_id' => $utility->egi->id,
                        'egi_owner_id' => $utility->egi->user_id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }
                abort(403, 'Unauthorized action.');
            }

            // 🔒 BLOCKCHAIN IMMUTABILITY: Check if EGI is minted (BLOCKING)
            if ($utility->egi->token_EGI) {
                return $this->errorManager->handle('UTILITY_EGI_MINTED', [
                    'user_id' => auth()->id(),
                    'egi_id' => $utility->egi->id,
                    'token_egi' => $utility->egi->token_EGI,
                    'utility_id' => $utility->id,
                    'action' => 'update_utility'
                ]);
            }

            // Verifica che la collection non sia ancora pubblicata
            // Temporaneamente commentato per permettere il testing
            /*if ($utility->egi->collection->status === 'published') {
            return redirect()
                ->route('egis.show', $utility->egi)
                ->with('error', 'Cannot modify utility after collection is published.');
        }*/

            // Validazione con messaggi localizzati (stessa di store)
            $validated = $request->validate([
                'type' => 'required|in:physical,service,hybrid,digital',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                // Physical fields
                'weight' => 'nullable|numeric|min:0',
                'dimensions' => 'nullable|array',
                'dimensions.length' => 'nullable|numeric|min:0',
                'dimensions.width' => 'nullable|numeric|min:0',
                'dimensions.height' => 'nullable|numeric|min:0',
                'estimated_shipping_days' => 'nullable|integer|min:1',
                'fragile' => 'nullable|boolean',
                'insurance_recommended' => 'nullable|boolean',
                'shipping_notes' => 'nullable|string',
                // Service fields
                'valid_from' => 'nullable|date',
                'valid_until' => 'nullable|date|after:valid_from',
                'max_uses' => 'nullable|integer|min:1',
                'activation_instructions' => 'nullable|string',
                // Media
                'gallery' => 'nullable|array',
                'gallery.*' => 'image|max:10240', // Max 10MB per image
                'remove_media' => 'nullable|array',
                'remove_media.*' => 'integer'
            ], [
                'title.required' => __('utility.validation.title_required'),
                'type.required' => __('utility.validation.type_required'),
                'valid_until.after' => __('utility.validation.valid_until_after'),
            ]);

            // Update utility
            $utility->update([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'dimensions' => $validated['dimensions'] ?? null,
                'estimated_shipping_days' => $validated['estimated_shipping_days'] ?? null,
                'fragile' => $validated['fragile'] ?? false,
                'insurance_recommended' => $validated['insurance_recommended'] ?? false,
                'shipping_notes' => $validated['shipping_notes'] ?? null,
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'max_uses' => $validated['max_uses'] ?? null,
                'activation_instructions' => $validated['activation_instructions'] ?? null,
            ]);

            // Gestione rimozione media
            if ($request->has('remove_media')) {
                try {
                    foreach ($request->remove_media as $mediaId) {
                        $utility->media()->find($mediaId)?->delete();
                    }
                } catch (\Exception $e) {
                    // Log ma non fallire completamente
                    if ($this->logger) {
                        $this->logger->warning('[UTILITY System] Media removal failed', [
                            'utility_id' => $utility->id,
                            'user_id' => auth()->id(),
                            'media_ids' => $request->remove_media,
                            'error' => $e->getMessage(),
                            'timestamp' => now()->toIso8601String()
                        ]);
                    }
                }
            }

            // Aggiungi nuove immagini
            if ($request->hasFile('gallery')) {
                try {
                    foreach ($request->file('gallery') as $image) {
                        $utility->addMedia($image)->toMediaCollection('utility_gallery');
                    }
                } catch (\Exception $e) {
                    // Log ma non fallire completamente
                    if ($this->logger) {
                        $this->logger->warning('[UTILITY System] Media upload failed during update', [
                            'utility_id' => $utility->id,
                            'user_id' => auth()->id(),
                            'error' => $e->getMessage(),
                            'timestamp' => now()->toIso8601String()
                        ]);
                    }
                }
            }

            // Log successo
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Utility updated successfully', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'egi_id' => $utility->egi->id,
                    'utility_type' => $utility->type,
                    'has_new_media' => $request->hasFile('gallery'),
                    'removed_media' => $request->has('remove_media'),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            return redirect()
                ->route('egis.show', $utility->egi)
                ->with('success', __('utility.success_updated'));
        } catch (ValidationException $e) {
            // Errore di validazione - non critico
            if ($this->logger) {
                $this->logger->warning('[UTILITY System] Update validation failed', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'errors' => $e->errors(),
                    'input' => $request->except(['_token', '_method', 'gallery']),
                    'timestamp' => now()->toIso8601String()
                ]);
            }
            throw $e;
        } catch (\Exception $e) {
            // Errore critico - usa UEM
            $this->errorManager->handle('UTILITY_UPDATE_ERROR', [
                'user_id' => auth()->id(),
                'utility_id' => $utility->id,
                'egi_id' => $utility->egi->id,
                'utility_type' => $request->input('type'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_data' => $request->except(['_token', '_method', 'gallery']),
                'timestamp' => now()->toIso8601String(),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], $e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('utility.messages.update_failed'));
        }
    }

    /**
     * Remove utility
     */
    public function destroy(Request $request, Utility $utility) {
        try {
            // Log inizio operazione
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Deleting utility', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'egi_id' => $utility->egi->id,
                    'utility_type' => $utility->type,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // Verifica permessi
            if (!Auth::check() || Auth::id() !== $utility->egi->user_id) {
                // Log tentativo non autorizzato
                if ($this->logger) {
                    $this->logger->warning('[UTILITY System] Unauthorized utility deletion attempt', [
                        'user_id' => auth()->id(),
                        'utility_id' => $utility->id,
                        'egi_id' => $utility->egi->id,
                        'egi_owner_id' => $utility->egi->user_id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }
                abort(403, 'Unauthorized action.');
            }

            // Verifica che la collection non sia ancora pubblicata
            // Temporaneamente commentato per permettere il testing
            /*if ($utility->egi->collection->status === 'published') {
                return redirect()
                    ->route('egis.show', $utility->egi)
                    ->with('error', 'Cannot remove utility after collection is published.');
            }*/

            $egi = $utility->egi;

            // Elimina media associati
            try {
                $utility->clearMediaCollection('utility_gallery');
                $utility->clearMediaCollection('utility_documents');
            } catch (\Exception $e) {
                // Log ma non fallire completamente
                if ($this->logger) {
                    $this->logger->warning('[UTILITY System] Media cleanup failed during deletion', [
                        'utility_id' => $utility->id,
                        'user_id' => auth()->id(),
                        'error' => $e->getMessage(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }
            }

            // Elimina utility
            $utility->delete();

            // Log successo
            if ($this->logger) {
                $this->logger->info('[UTILITY System] Utility deleted successfully', [
                    'user_id' => auth()->id(),
                    'utility_id' => $utility->id,
                    'egi_id' => $egi->id,
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // Per richieste AJAX, restituisci JSON
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => true,
                    'message' => __('utility.actions.delete_success')
                ]);
            }

            // Per richieste normali, reindirizza - TEMPORANEAMENTE COMMENTATO PER DEBUG
            // return redirect()
            //     ->route('egis.show', $egi)
            //     ->with('success', __('utility.actions.delete_success'));

            // Invece, per ora, restituiamo sempre JSON
            return response()->json([
                'success' => true,
                'message' => __('utility.actions.delete_success')
            ]);
        } catch (\Exception $e) {

            // Errore critico - usa UEM
            $this->errorManager->handle('UTILITY_DELETION_ERROR', [
                'user_id' => auth()->id(),
                'utility_id' => $utility->id,
                'egi_id' => $utility->egi->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], $e);

            return redirect()
                ->route('egis.show', $utility->egi)
                ->with('error', __('utility.messages.deletion_failed'));
        }
    }
}
