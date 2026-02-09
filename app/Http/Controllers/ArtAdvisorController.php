<?php

namespace App\Http\Controllers;

use App\Services\ArtAdvisorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Art Advisor Controller - AI Chat Interface
 *
 * Handles AI Art Advisor chat requests with SSE streaming responses.
 * Minimal, stateless helper for quick creative guidance.
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Art Advisor)
 * @date 2025-10-29
 * @purpose AI assistant API for creators and collectors
 */
class ArtAdvisorController extends Controller {
    protected ArtAdvisorService $artAdvisor;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        ArtAdvisorService $artAdvisor,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->artAdvisor = $artAdvisor;
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        $this->middleware('auth');
    }

    /**
     * Handle AI chat request with SSE streaming
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function chat(Request $request): StreamedResponse {
        // Validate request
        $validated = $request->validate([
            'expert' => 'required|string|in:creative,platform',
            'message' => 'required|string|max:1000',
            'context' => 'sometimes|array',
            'use_vision' => 'sometimes|boolean',
        ]);

        $expertId = $validated['expert'];
        $userMessage = $validated['message'];
        $context = $validated['context'] ?? [];
        $useVision = $validated['use_vision'] ?? false;

        // Auto-detect vision need if not explicitly set
        if (!$useVision && !empty($context['image_url'])) {
            $useVision = $this->artAdvisor->shouldUseVision($userMessage);
        }

        $this->logger->info('[ArtAdvisorController] Chat request received', [
            'expert' => $expertId,
            'message_length' => strlen($userMessage),
            'has_context' => !empty($context),
            'use_vision' => $useVision,
            'user_id' => Auth::id(),
        ]);

        // Return SSE stream
        return response()->stream(function () use ($expertId, $userMessage, $context, $useVision) {
            // Disable output buffering
            if (ob_get_level()) ob_end_clean();

            // Set SSE headers (already set by StreamedResponse, but ensure)
            header('X-Accel-Buffering: no');

            try {
                // Send start event
                $this->sendSSE('start', [
                    'expert' => $expertId,
                    'expert_name' => ArtAdvisorService::getAvailableExperts()[$expertId] ?? $expertId,
                    'use_vision' => $useVision,
                ]);

                // Get AI response
                $response = $this->artAdvisor->getResponse(
                    $expertId,
                    $userMessage,
                    $context,
                    $useVision
                );

                // Send response chunk by chunk (simulate streaming for better UX)
                $this->streamResponse($response['message']);

                // Send completion event
                $this->sendSSE('complete', [
                    'model' => $response['model'] ?? 'unknown',
                    'usage' => $response['usage'] ?? null,
                ]);
            } catch (\Exception $e) {
                $this->logger->error('[ArtAdvisorController] Chat error', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]);

                // Send error event
                $this->sendSSE('error', [
                    'message' => __('art_advisor.error_occurred'),
                    'code' => 'AI_RESPONSE_FAILED',
                ]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Stream response in chunks for better UX
     *
     * @param string $message Complete message to stream
     * @return void
     */
    private function streamResponse(string $message): void {
        // Split message into sentences for natural streaming
        $sentences = preg_split('/(?<=[.!?])\s+/', $message, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($sentences as $sentence) {
            $this->sendSSE('chunk', ['content' => $sentence . ' ']);
            flush();

            // Small delay for natural feel (10ms per sentence)
            usleep(10000);
        }
    }

    /**
     * Send SSE event
     *
     * @param string $event Event name
     * @param array $data Event data
     * @return void
     */
    private function sendSSE(string $event, array $data): void {
        echo "event: {$event}\n";
        echo 'data: ' . json_encode($data) . "\n\n";
        flush();
    }

    /**
     * Test endpoint - verify controller is accessible
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(): \Illuminate\Http\JsonResponse {
        return response()->json([
            'status' => 'ok',
            'service' => 'AI Art Advisor',
            'available_experts' => ArtAdvisorService::getAvailableExperts(),
            'user_id' => Auth::id(),
        ]);
    }
}
