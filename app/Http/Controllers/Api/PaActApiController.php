<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\PaBatchJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: PA Act API
 * 🎯 Purpose: Handle NATAN agent API requests for PA act metadata processing
 * 🛡️ Privacy: GDPR-compliant, validates auth via middleware, no file storage
 * 🧱 Core Logic: Receive metadata JSON, create PaBatchJob, process via handler
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose API endpoints for NATAN agent metadata submission
 */
class PaActApiController extends Controller
{
    /**
     * Ultra Log Manager instance.
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error Manager instance.
     *
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with dependency injection.
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

        // Apply NATAN agent authentication middleware
        $this->middleware('natan.agent');
    }

    /**
     * Store PA act metadata from agent.
     *
     * Receives metadata-only JSON payload (no file upload).
     * Creates PaBatchJob and processes via existing PaActUploadHandler.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @privacy-safe No file storage, only metadata processing
     */
    public function storeMetadata(Request $request): JsonResponse
    {
        $user = auth()->user();

        // 1. ULM: Log request
        $this->logger->info('NATAN_API_METADATA_RECEIVED', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        // 2. Validate request
        $validator = Validator::make($request->all(), [
            'file_hash' => 'required|string|size:64', // SHA256 = 64 hex chars
            'file_name' => 'required|string|max:255',
            'file_size' => 'required|integer|min:1',
            'file_path' => 'nullable|string', // PA-side path (reference only)
            'extracted_text' => 'required|string',
            'signature_valid' => 'required|boolean',
            'signature_date' => 'nullable|date',
            'signature_subject' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            $this->logger->warning('NATAN_API_VALIDATION_FAILED', [
                'user_id' => $user->id,
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // 3. Check duplicate (by hash)
        $existingEgi = Egi::where('file_hash', $validated['file_hash'])
            ->where('owner_id', $user->id)
            ->first();

        if ($existingEgi) {
            $this->logger->info('NATAN_API_DUPLICATE_DETECTED', [
                'user_id' => $user->id,
                'file_hash' => $validated['file_hash'],
                'existing_egi_id' => $existingEgi->id,
            ]);

            // Create job as duplicate
            PaBatchJob::create([
                'user_id' => $user->id,
                'source_id' => 1, // TODO: Get from request or default
                'egi_id' => $existingEgi->id,
                'file_name' => $validated['file_name'],
                'file_path' => $validated['file_path'] ?? null,
                'file_hash' => $validated['file_hash'],
                'file_size' => $validated['file_size'],
                'status' => 'duplicate',
                'agent_metadata' => $validated['metadata'] ?? null,
            ]);

            return response()->json([
                'status' => 'duplicate',
                'message' => __('pa_batch.api.duplicate'),
                'egi_id' => $existingEgi->id,
                'verification_code' => $existingEgi->pa_public_code,
            ], 200);
        }

        // 4. Create PaBatchJob (pending)
        try {
            $job = PaBatchJob::create([
                'user_id' => $user->id,
                'source_id' => 1, // TODO: Get from request or default source
                'file_name' => $validated['file_name'],
                'file_path' => $validated['file_path'] ?? null,
                'file_hash' => $validated['file_hash'],
                'file_size' => $validated['file_size'],
                'status' => 'pending',
                'agent_metadata' => array_merge($validated['metadata'] ?? [], [
                    'signature_valid' => $validated['signature_valid'],
                    'signature_date' => $validated['signature_date'] ?? null,
                    'signature_subject' => $validated['signature_subject'] ?? null,
                ]),
            ]);

            $this->logger->info('NATAN_API_JOB_CREATED', [
                'user_id' => $user->id,
                'job_id' => $job->id,
                'file_hash' => $validated['file_hash'],
            ]);
        } catch (\Exception $e) {
            $this->logger->error('NATAN_API_JOB_CREATE_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Internal error',
                'message' => __('pa_batch.api.job_create_failed'),
            ], 500);
        }

        // 5. Process metadata (create EGI record)
        try {
            // Extract document info from text (AI/regex)
            $docInfo = $this->extractDocumentInfo($validated['extracted_text']);

            // Create EGI record
            $egi = Egi::create([
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'title' => $docInfo['title'] ?? $validated['file_name'],
                'description' => $docInfo['description'] ?? null,
                'file_hash' => $validated['file_hash'],
                'file_size' => $validated['file_size'],
                'pa_file_path' => $validated['file_path'] ?? null,
                'pa_act_type' => $docInfo['act_type'] ?? 'atto',
                'pa_protocol_number' => $docInfo['protocol'] ?? null,
                'pa_protocol_date' => $docInfo['protocol_date'] ?? null,
                'pa_signature_valid' => $validated['signature_valid'],
                'pa_signature_date' => $validated['signature_date'] ?? null,
                'pa_public_code' => $this->generateVerificationCode(),
                'status' => 'published',
                'is_published' => true,
                'jsonMetadata' => [
                    'pa_act' => [
                        'extracted_text' => substr($validated['extracted_text'], 0, 5000), // Limit for storage
                        'signature_subject' => $validated['signature_subject'] ?? null,
                        'agent_metadata' => $validated['metadata'] ?? [],
                    ],
                ],
            ]);

            // Update job with EGI ID
            $job->update([
                'egi_id' => $egi->id,
                'status' => 'completed',
                'processing_completed_at' => now(),
            ]);

            // Dispatch blockchain tokenization
            \App\Jobs\TokenizePaActJob::dispatch($egi)->onQueue('pa_blockchain');

            $this->logger->info('NATAN_API_SUCCESS', [
                'user_id' => $user->id,
                'job_id' => $job->id,
                'egi_id' => $egi->id,
            ]);

            return response()->json([
                'status' => 'accepted',
                'message' => __('pa_batch.api.success'),
                'egi_id' => $egi->id,
                'job_id' => $job->id,
                'verification_code' => $egi->pa_public_code,
            ], 201);
        } catch (\Exception $e) {
            // Mark job as failed
            $job->update([
                'status' => 'failed',
                'last_error' => 'Processing error: ' . $e->getMessage(),
                'attempts' => 1,
            ]);

            $this->logger->error('NATAN_API_PROCESSING_FAILED', [
                'user_id' => $user->id,
                'job_id' => $job->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Processing failed',
                'message' => __('pa_batch.api.processing_failed'),
                'job_id' => $job->id,
            ], 500);
        }
    }

    /**
     * Extract document info from text (simplified version).
     *
     * TODO: Move to dedicated service (PaBatchMetadataService)
     *
     * @param string $text
     * @return array
     */
    private function extractDocumentInfo(string $text): array
    {
        $info = [];

        // Extract act type (basic regex)
        if (preg_match('/DELIBERA(?:ZIONE)?/i', $text)) {
            $info['act_type'] = 'delibera';
        } elseif (preg_match('/DETERMINA(?:ZIONE)?/i', $text)) {
            $info['act_type'] = 'determina';
        } elseif (preg_match('/ORDINANZA/i', $text)) {
            $info['act_type'] = 'ordinanza';
        } elseif (preg_match('/DECRETO/i', $text)) {
            $info['act_type'] = 'decreto';
        } else {
            $info['act_type'] = 'atto';
        }

        // Extract protocol number (pattern: N. XXX/YYYY or n. XXX del YYYY)
        if (preg_match('/(?:N\.|n\.)\s*(\d+)(?:\/|del\s+)(\d{4})/i', $text, $matches)) {
            $info['protocol'] = $matches[1] . '/' . $matches[2];
            $info['protocol_date'] = $matches[2] . '-01-01'; // Default to year start
        }

        // Extract title (OGGETTO: ...)
        if (preg_match('/OGGETTO:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $info['title'] = trim($matches[1]);
            $info['description'] = $info['title']; // Use as description too
        }

        return $info;
    }

    /**
     * Generate unique verification code.
     *
     * Format: VER-XXXXXXXXXX (10 random uppercase alphanum)
     *
     * @return string
     */
    private function generateVerificationCode(): string
    {
        do {
            $code = 'VER-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        } while (Egi::where('pa_public_code', $code)->exists());

        return $code;
    }

    /**
     * Get job status.
     *
     * @param Request $request
     * @param int $jobId
     * @return JsonResponse
     */
    public function getJobStatus(Request $request, int $jobId): JsonResponse
    {
        $user = auth()->user();

        $job = PaBatchJob::where('id', $jobId)
            ->where('user_id', $user->id)
            ->first();

        if (!$job) {
            return response()->json([
                'error' => 'Not found',
                'message' => __('pa_batch.api.job_not_found'),
            ], 404);
        }

        return response()->json([
            'job_id' => $job->id,
            'status' => $job->status,
            'egi_id' => $job->egi_id,
            'file_name' => $job->file_name,
            'file_hash' => $job->file_hash,
            'attempts' => $job->attempts,
            'last_error' => $job->last_error,
            'created_at' => $job->created_at,
            'completed_at' => $job->processing_completed_at,
        ], 200);
    }
}
