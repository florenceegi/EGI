<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use App\Models\EgiBlockchain;
use App\Contracts\PaymentServiceInterface;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\DataTransferObjects\Payment\PaymentResult;
use App\Services\EgiMintingService;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @Oracode Service: EGI Purchase Workflow Orchestrator
 * 🎯 Purpose: Orchestrate complete EGI purchase workflow from payment to minted certificate
 * 🧱 Core Logic: Transaction management between DB and blockchain operations
 * 🛡️ MiCA-SAFE: FIAT payments → blockchain minting → certificate generation
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Complete EGI purchase orchestration with atomic transaction management
 */
class EgiPurchaseWorkflowService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private EgiMintingService $mintingService;


    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @param EgiMintingService $mintingService EGI blockchain minting service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        EgiMintingService $mintingService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->mintingService = $mintingService;
    }

    /**
     * Process direct EGI purchase - Payment → Mint → Certificate
     *
     * @param Egi $egi EGI to purchase
     * @param User $user User making the purchase
     * @param PaymentServiceInterface $paymentService Payment service provider (Stripe/PayPal)
     * @param PaymentRequest $paymentRequest Payment request data
     * @return array Purchase result with blockchain and certificate data
     * @throws \Exception Purchase process failed
     * @gdpr-compliant Full audit trail for financial and blockchain operations
     */
    public function processDirectPurchase(
        Egi $egi,
        User $user,
        PaymentServiceInterface $paymentService,
        PaymentRequest $paymentRequest
    ): array {
        // Start transaction for atomicity
        return DB::transaction(function () use ($egi, $user, $paymentService, $paymentRequest) {
            try {
                // 1. ULM: Log workflow start
                $this->logger->info('Direct EGI purchase workflow initiated', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'payment_amount' => $paymentRequest->amount,
                    'payment_currency' => $paymentRequest->currency,
                    'payment_provider' => $paymentService->getProviderName(),
                    'log_category' => 'EGI_PURCHASE_WORKFLOW_START'
                ]);

                // 2. GDPR: Check comprehensive consent
                $this->validateUserConsents($user);

                // 3. Validate EGI availability
                $this->validateEgiAvailability($egi);

                // 4. Process payment
                $paymentResult = $this->processPayment($paymentService, $paymentRequest, $user);

                // 5. Create blockchain record placeholder
                $egiBlockchain = $this->createBlockchainPlaceholder($egi, $user, $paymentResult);

                // 6. Mint EGI on blockchain (AREA 2.2.3: with payment distribution)
                $mintResult = $this->mintEgiOnBlockchain($egi, $user, $egiBlockchain);
                $mintedEgi = $mintResult['blockchain'];
                $distributions = $mintResult['distributions'];

                // 7. Generate certificate
                $certificatePath = $this->generateCertificate($mintedEgi, $user);

                // 8. Update EGI ownership
                $this->updateEgiOwnership($egi, $user, $mintedEgi);

                // 9. Final audit trail
                $this->auditService->logActivity(
                    $user,
                    GdprActivityCategory::BLOCKCHAIN_PURCHASE,
                    'Direct EGI purchase completed successfully',
                    [
                        'egi_id' => $egi->id,
                        'egi_title' => $egi->title,
                        'payment_id' => $paymentResult->paymentId,
                        'payment_amount' => $paymentResult->amount,
                        'payment_currency' => $paymentResult->currency,
                        'asa_id' => $mintedEgi->asa_id,
                        'blockchain_tx_id' => $mintedEgi->blockchain_tx_id,
                        'certificate_uuid' => $mintedEgi->certificate_uuid,
                        'certificate_path' => $certificatePath,
                        'payment_provider' => $paymentService->getProviderName(),
                        'distributions_count' => count($distributions) // AREA 2.2.3
                    ]
                );

                // 10. ULM: Log success
                $this->logger->info('Direct EGI purchase workflow completed successfully', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'payment_id' => $paymentResult->paymentId,
                    'asa_id' => $mintedEgi->asa_id,
                    'blockchain_tx_id' => $mintedEgi->blockchain_tx_id,
                    'certificate_uuid' => $mintedEgi->certificate_uuid,
                    'distributions_count' => count($distributions), // AREA 2.2.3
                    'log_category' => 'EGI_PURCHASE_WORKFLOW_SUCCESS'
                ]);

                return [
                    'success' => true,
                    'egi' => $egi,
                    'payment_result' => $paymentResult,
                    'blockchain_record' => $mintedEgi,
                    'distributions' => $distributions, // AREA 2.2.3
                    'certificate_path' => $certificatePath,
                    'verification_url' => url("/verify/{$mintedEgi->certificate_uuid}"),
                    'workflow_id' => $this->generateWorkflowId(),
                    'completed_at' => now()
                ];
            } catch (\Exception $e) {
                // Rollback handled automatically by DB::transaction
                $this->handleWorkflowFailure($egi, $user, $paymentService->getProviderName(), $e);
                throw $e;
            }
        });
    }

    /**
     * Process reservation-based purchase - Existing Reservation → Payment → Mint → Certificate
     *
     * @param Reservation $reservation Existing reservation to convert to purchase
     * @param PaymentServiceInterface $paymentService Payment service provider
     * @param PaymentRequest $paymentRequest Payment request data
     * @return array Purchase result with blockchain and certificate data
     * @throws \Exception Purchase process failed
     * @gdpr-compliant Full audit trail linking reservation to blockchain purchase
     */
    public function processReservationPayment(
        Reservation $reservation,
        PaymentServiceInterface $paymentService,
        PaymentRequest $paymentRequest
    ): array {
        // Start transaction for atomicity
        return DB::transaction(function () use ($reservation, $paymentService, $paymentRequest) {
            try {
                $egi = $reservation->egi;
                $user = $reservation->user;

                // 1. ULM: Log workflow start
                $this->logger->info('Reservation-based EGI purchase workflow initiated', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'reservation_id' => $reservation->id,
                    'reservation_amount_eur' => $reservation->amount_eur,
                    'payment_amount' => $paymentRequest->amount,
                    'payment_currency' => $paymentRequest->currency,
                    'payment_provider' => $paymentService->getProviderName(),
                    'log_category' => 'EGI_RESERVATION_PURCHASE_START'
                ]);

                // 2. GDPR: Check comprehensive consent
                $this->validateUserConsents($user);

                // 3. Validate reservation status
                $this->validateReservationForPayment($reservation);

                // 4. Process payment
                $paymentResult = $this->processPayment($paymentService, $paymentRequest, $user);

                // 5. Create blockchain record with reservation link
                $egiBlockchain = $this->createBlockchainWithReservation($egi, $user, $reservation, $paymentResult);

                // 6. Mint EGI on blockchain (AREA 2.2.3: with payment distribution)
                $mintResult = $this->mintEgiOnBlockchain($egi, $user, $egiBlockchain);
                $mintedEgi = $mintResult['blockchain'];
                $distributions = $mintResult['distributions'];

                // 7. Generate certificate
                $certificatePath = $this->generateCertificate($mintedEgi, $user);

                // 8. Update reservation status to completed
                $this->completeReservation($reservation, $mintedEgi);

                // 9. Update EGI ownership
                $this->updateEgiOwnership($egi, $user, $mintedEgi);

                // 10. Final audit trail
                $this->auditService->logActivity(
                    $user,
                    GdprActivityCategory::BLOCKCHAIN_PURCHASE,
                    'Reservation-based EGI purchase completed successfully',
                    [
                        'egi_id' => $egi->id,
                        'egi_title' => $egi->title,
                        'reservation_id' => $reservation->id,
                        'reservation_amount_eur' => $reservation->amount_eur,
                        'payment_id' => $paymentResult->paymentId,
                        'payment_amount' => $paymentResult->amount,
                        'payment_currency' => $paymentResult->currency,
                        'asa_id' => $mintedEgi->asa_id,
                        'blockchain_tx_id' => $mintedEgi->blockchain_tx_id,
                        'certificate_uuid' => $mintedEgi->certificate_uuid,
                        'certificate_path' => $certificatePath,
                        'payment_provider' => $paymentService->getProviderName(),
                        'distributions_count' => count($distributions) // AREA 2.2.3
                    ]
                );

                // 11. ULM: Log success
                $this->logger->info('Reservation-based EGI purchase workflow completed successfully', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'reservation_id' => $reservation->id,
                    'payment_id' => $paymentResult->paymentId,
                    'asa_id' => $mintedEgi->asa_id,
                    'blockchain_tx_id' => $mintedEgi->blockchain_tx_id,
                    'certificate_uuid' => $mintedEgi->certificate_uuid,
                    'distributions_count' => count($distributions), // AREA 2.2.3
                    'log_category' => 'EGI_RESERVATION_PURCHASE_SUCCESS'
                ]);

                return [
                    'success' => true,
                    'egi' => $egi,
                    'reservation' => $reservation->fresh(),
                    'payment_result' => $paymentResult,
                    'blockchain_record' => $mintedEgi,
                    'distributions' => $distributions, // AREA 2.2.3
                    'certificate_path' => $certificatePath,
                    'verification_url' => url("/verify/{$mintedEgi->certificate_uuid}"),
                    'workflow_id' => $this->generateWorkflowId(),
                    'completed_at' => now()
                ];
            } catch (\Exception $e) {
                // Rollback handled automatically by DB::transaction
                $this->handleReservationWorkflowFailure($reservation, $paymentService->getProviderName(), $e);
                throw $e;
            }
        });
    }

    /**
     * Validate user has all required consents for purchase workflow
     *
     * @param User $user User to validate
     * @throws \Exception Missing required consents
     * @privacy-safe Only validates consent status, no personal data
     */
    private function validateUserConsents(User $user): void {
        $requiredConsents = [
            'allow-personal-data-processing',
            'allow-payment-processing',
            'allow-blockchain-operations'
        ];

        foreach ($requiredConsents as $consentType) {
            if (!$this->consentService->hasConsent($user, $consentType)) {
                throw new \Exception("Missing required consent: {$consentType}");
            }
        }
    }

    /**
     * Validate EGI is available for purchase
     *
     * @param Egi $egi EGI to validate
     * @throws \Exception EGI not available
     * @business-logic Ensure EGI can be purchased (not already minted)
     */
    private function validateEgiAvailability(Egi $egi): void {
        // Check if EGI is already minted
        if ($egi->egiBlockchain && $egi->egiBlockchain->mint_status === 'minted') {
            throw new \Exception("EGI '{$egi->title}' is already minted and owned");
        }

        // Check if EGI is published and available
        if (!$egi->is_published) {
            throw new \Exception("EGI '{$egi->title}' is not available for purchase");
        }
    }

    /**
     * Validate reservation can be converted to purchase
     *
     * @param Reservation $reservation Reservation to validate
     * @throws \Exception Reservation not valid for payment
     * @business-logic Ensure reservation is active and can be paid
     */
    private function validateReservationForPayment(Reservation $reservation): void {
        if ($reservation->status !== Reservation::STATUS_ACTIVE) {
            throw new \Exception("Reservation is not active (status: {$reservation->status})");
        }

        if (!$reservation->is_current) {
            throw new \Exception("Reservation is not current (superseded)");
        }

        if ($reservation->sub_status !== 'highest') {
            throw new \Exception("Only the highest reservation can be converted to purchase");
        }
    }

    /**
     * Process payment through selected payment service
     *
     * @param PaymentServiceInterface $paymentService Payment service provider
     * @param PaymentRequest $paymentRequest Payment request data
     * @param User $user User making payment
     * @return PaymentResult Payment processing result
     * @throws \Exception Payment processing failed
     */
    private function processPayment(
        PaymentServiceInterface $paymentService,
        PaymentRequest $paymentRequest,
        User $user
    ): PaymentResult {
        // Validate payment service supports currency
        if (!$paymentService->supportsCurrency($paymentRequest->currency)) {
            throw new \Exception(
                "Payment provider {$paymentService->getProviderName()} " .
                    "does not support currency {$paymentRequest->currency}"
            );
        }

        $paymentResult = $paymentService->processPayment($paymentRequest);

        if (!$paymentResult->success) {
            throw new \Exception(
                "Payment failed: {$paymentResult->errorMessage} (Code: {$paymentResult->errorCode})"
            );
        }

        // Log successful payment
        $this->auditService->logActivity(
            $user,
            GdprActivityCategory::PAYMENT_PROCESSING,
            'EGI purchase payment processed successfully',
            [
                'payment_id' => $paymentResult->paymentId,
                'amount' => $paymentResult->amount,
                'currency' => $paymentResult->currency,
                'provider' => $paymentService->getProviderName(),
                'egi_id' => $paymentRequest->egiId
            ]
        );

        return $paymentResult;
    }

    /**
     * Create blockchain placeholder record for direct purchase
     *
     * @param Egi $egi EGI being purchased
     * @param User $user Buyer user
     * @param PaymentResult $paymentResult Payment result
     * @return EgiBlockchain Created blockchain record
     */
    private function createBlockchainPlaceholder(Egi $egi, User $user, PaymentResult $paymentResult): EgiBlockchain {
        return EgiBlockchain::create([
            'egi_id' => $egi->id,
            'buyer_user_id' => $user->id,
            'ownership_type' => 'purchased',
            'payment_method' => 'direct_purchase',
            'psp_provider' => $paymentResult->metadata['provider'] ?? 'unknown',
            'payment_reference' => $paymentResult->paymentId,
            'paid_amount' => $paymentResult->amount,
            'paid_currency' => $paymentResult->currency,
            'mint_status' => 'minting_queued',
            'certificate_uuid' => \Illuminate\Support\Str::uuid()
        ]);
    }

    /**
     * Create blockchain record with reservation link
     *
     * @param Egi $egi EGI being purchased
     * @param User $user Buyer user
     * @param Reservation $reservation Source reservation
     * @param PaymentResult $paymentResult Payment result
     * @return EgiBlockchain Created blockchain record
     */
    private function createBlockchainWithReservation(
        Egi $egi,
        User $user,
        Reservation $reservation,
        PaymentResult $paymentResult
    ): EgiBlockchain {
        return EgiBlockchain::create([
            'egi_id' => $egi->id,
            'buyer_user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'ownership_type' => 'purchased_from_reservation',
            'payment_method' => 'reservation_payment',
            'psp_provider' => $paymentResult->metadata['provider'] ?? 'unknown',
            'payment_reference' => $paymentResult->paymentId,
            'paid_amount' => $paymentResult->amount,
            'paid_currency' => $paymentResult->currency,
            'mint_status' => 'minting_queued',
            'certificate_uuid' => \Illuminate\Support\Str::uuid()
        ]);
    }

    /**
     * Mint EGI on blockchain with automatic payment distribution (AREA 2.2.3)
     * Integrates mintEgiWithPayment() from EgiMintingService for dual tracking
     *
     * @param Egi $egi EGI to mint
     * @param User $user User requesting mint
     * @param EgiBlockchain $egiBlockchain Blockchain record to update
     * @return array ['blockchain' => EgiBlockchain, 'distributions' => array]
     * @throws \Exception Minting failed
     */
    private function mintEgiOnBlockchain(Egi $egi, User $user, EgiBlockchain $egiBlockchain): array {
        try {
            $egiBlockchain->update(['mint_status' => 'minting']);

            // AREA 2.2.3: Use mintEgiWithPayment() for automatic payment distribution
            $paymentData = [
                'paid_amount' => $egiBlockchain->paid_amount,
                'paid_currency' => $egiBlockchain->paid_currency ?? 'EUR',
                'payment_method' => $egiBlockchain->payment_method ?? 'fiat'
            ];

            $metadata = [
                'purchase_type' => $egiBlockchain->ownership_type,
                'payment_reference' => $egiBlockchain->payment_reference,
                'payment_amount' => $egiBlockchain->paid_amount,
                'payment_currency' => $egiBlockchain->paid_currency
            ];

            // Call integrated mint + distribution service (AREA 2.2.2)
            $mintResult = $this->mintingService->mintEgiWithPayment($egi, $user, $paymentData, $metadata);

            $this->logger->info('EGI minted with payment distribution', [
                'egi_id' => $egi->id,
                'blockchain_record_id' => $mintResult['blockchain']->id,
                'distributions_count' => count($mintResult['distributions']),
                'payment_distributed' => $mintResult['payment_distributed'],
                'log_category' => 'PURCHASE_WORKFLOW_MINT_DISTRIBUTION'
            ]);

            return $mintResult;
        } catch (\Exception $e) {
            $egiBlockchain->update([
                'mint_status' => 'failed',
                'mint_error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate certificate for minted EGI
     *
     * @param EgiBlockchain $egiBlockchain Minted EGI blockchain record
     * @param User $user Certificate owner
     * @return string Certificate file path
     * @throws \Exception Certificate generation failed
     */
    private function generateCertificate(EgiBlockchain $egiBlockchain, User $user): string {
        try {
            // For blockchain purchases, we generate a direct certificate
            // Unlike reservation certificates, this is for confirmed blockchain ownership

            // Generate certificate path directly
            $certificateFileName = "egi_blockchain_certificate_{$egiBlockchain->certificate_uuid}.pdf";
            $certificatePath = "certificates/blockchain/{$certificateFileName}";

            // Create certificate data for blockchain purchase
            $certificateData = [
                'egi_id' => $egiBlockchain->egi_id,
                'egi_title' => $egiBlockchain->egi->title ?? 'Unknown EGI',
                'buyer_name' => $user->name,
                'buyer_wallet' => $egiBlockchain->buyer_wallet ?? 'N/A',
                'asa_id' => $egiBlockchain->asa_id,
                'blockchain_tx_id' => $egiBlockchain->blockchain_tx_id,
                'certificate_uuid' => $egiBlockchain->certificate_uuid,
                'purchase_amount' => $egiBlockchain->paid_amount,
                'purchase_currency' => $egiBlockchain->paid_currency,
                'minted_at' => $egiBlockchain->minted_at,
                'ownership_type' => $egiBlockchain->ownership_type,
                'verification_url' => url("/verify/{$egiBlockchain->certificate_uuid}")
            ];

            // Generate PDF content for blockchain certificate
            $pdfContent = $this->generateBlockchainCertificatePdf($certificateData);

            // Store certificate file
            \Storage::put($certificatePath, $pdfContent);

            // Update blockchain record with certificate path
            $egiBlockchain->update([
                'certificate_path' => $certificatePath,
                'verification_url' => url("/verify/{$egiBlockchain->certificate_uuid}")
            ]);

            $this->logger->info('Blockchain certificate generated successfully', [
                'user_id' => $user->id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'certificate_path' => $certificatePath,
                'certificate_uuid' => $egiBlockchain->certificate_uuid
            ]);

            return $certificatePath;
        } catch (\Exception $e) {
            // UEM: Error handling (P1 compliance)
            $this->errorManager->handle('CERTIFICATE_GENERATION_FAILED', [
                'user_id' => $user->id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'certificate_uuid' => $egiBlockchain->certificate_uuid,
                'error_message' => $e->getMessage()
            ], $e);
            throw $e;
        }
    }

    /**
     * Generate PDF content for blockchain certificate
     *
     * @param array $certificateData Certificate data
     * @return string PDF content as binary string
     */
    private function generateBlockchainCertificatePdf(array $certificateData): string {
        // For MVP, generate a simple text-based certificate
        // In production, this would use a proper PDF library like TCPDF or DomPDF

        $content = "========================================\n";
        $content .= "    FLORENCE EGI BLOCKCHAIN CERTIFICATE\n";
        $content .= "========================================\n\n";
        $content .= "Certificate UUID: {$certificateData['certificate_uuid']}\n";
        $content .= "EGI Title: {$certificateData['egi_title']}\n";
        $content .= "Owner: {$certificateData['buyer_name']}\n";
        $content .= "Blockchain Asset ID: {$certificateData['asa_id']}\n";
        $content .= "Transaction ID: {$certificateData['blockchain_tx_id']}\n";
        $content .= "Purchase Amount: {$certificateData['purchase_amount']} {$certificateData['purchase_currency']}\n";
        $content .= "Minted At: {$certificateData['minted_at']}\n";
        $content .= "Ownership Type: {$certificateData['ownership_type']}\n\n";
        $content .= "Verification URL: {$certificateData['verification_url']}\n\n";
        $content .= "This certificate proves blockchain ownership of the EGI asset.\n";
        $content .= "Generated by FlorenceEGI Platform - " . now()->toDateTimeString() . "\n";
        $content .= "========================================\n";

        return $content;
    }

    /**
     * Update EGI ownership after successful purchase
     *
     * @param Egi $egi EGI being purchased
     * @param User $user New owner
     * @param EgiBlockchain $egiBlockchain Blockchain record
     */
    private function updateEgiOwnership(Egi $egi, User $user, EgiBlockchain $egiBlockchain): void {
        // Note: In EGI system, ownership is tracked through blockchain record
        // EGI model doesn't have owner_id field updated for blockchain purchases
        // This is intentional - blockchain record is source of truth for ownership

        $this->auditService->logActivity(
            $user,
            GdprActivityCategory::OWNERSHIP_TRANSFER,
            'EGI ownership transferred via blockchain purchase',
            [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'new_owner_id' => $user->id,
                'blockchain_record_id' => $egiBlockchain->id,
                'asa_id' => $egiBlockchain->asa_id,
                'blockchain_tx_id' => $egiBlockchain->blockchain_tx_id
            ]
        );
    }

    /**
     * Complete reservation after successful payment and minting
     *
     * @param Reservation $reservation Reservation to complete
     * @param EgiBlockchain $egiBlockchain Associated blockchain record
     */
    private function completeReservation(Reservation $reservation, EgiBlockchain $egiBlockchain): void {
        $reservation->update([
            'status' => Reservation::STATUS_COMPLETED,
            'sub_status' => 'minted'
        ]);

        $this->auditService->logActivity(
            $reservation->user,
            GdprActivityCategory::RESERVATION_MANAGEMENT,
            'Reservation completed via blockchain purchase',
            [
                'reservation_id' => $reservation->id,
                'egi_id' => $reservation->egi_id,
                'blockchain_record_id' => $egiBlockchain->id,
                'asa_id' => $egiBlockchain->asa_id
            ]
        );
    }

    /**
     * Handle workflow failure with comprehensive logging
     *
     * @param Egi $egi EGI involved in failed workflow
     * @param User $user User involved in failed workflow
     * @param string $paymentProvider Payment provider name
     * @param \Exception $exception Failure exception
     */
    private function handleWorkflowFailure(Egi $egi, User $user, string $paymentProvider, \Exception $exception): void {
        // UEM handles error logging automatically (P1 compliance)
        $this->errorManager->handle('EGI_PURCHASE_WORKFLOW_FAILED', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'payment_provider' => $paymentProvider,
            'error_message' => $exception->getMessage(),
            'error_trace' => $exception->getTraceAsString()
        ], $exception);
    }

    /**
     * Handle reservation workflow failure with comprehensive logging
     *
     * @param Reservation $reservation Reservation involved in failed workflow
     * @param string $paymentProvider Payment provider name
     * @param \Exception $exception Failure exception
     */
    private function handleReservationWorkflowFailure(
        Reservation $reservation,
        string $paymentProvider,
        \Exception $exception
    ): void {
        // UEM handles error logging automatically (P1 compliance)
        $this->errorManager->handle('EGI_RESERVATION_PURCHASE_WORKFLOW_FAILED', [
            'user_id' => $reservation->user_id,
            'egi_id' => $reservation->egi_id,
            'reservation_id' => $reservation->id,
            'payment_provider' => $paymentProvider,
            'error_message' => $exception->getMessage(),
            'error_trace' => $exception->getTraceAsString()
        ], $exception);
    }

    /**
     * Generate unique workflow ID for tracking
     *
     * @return string Unique workflow identifier
     */
    private function generateWorkflowId(): string {
        return 'workflow_' . time() . '_' . \Illuminate\Support\Str::random(8);
    }
}