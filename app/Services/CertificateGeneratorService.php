<?php

namespace App\Services;

use App\Models\EgiReservationCertificate;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: CertificateGeneratorService
 * 🎯 Purpose: Generates and manages certificates for EGI reservations
 * 🧱 Core Logic: Creates certificates, signatures, and PDF documents
 * 🛡️ GDPR: Ensures minimal PII in certificates
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class CertificateGeneratorService {
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Generate a certificate for a reservation
     *
     * @param Reservation $reservation The reservation
     * @param array $additionalData Additional data for the certificate
     * @return EgiReservationCertificate The generated certificate
     * @throws \Exception If certificate generation fails
     *
     * @privacy-safe Creates certificates with minimal PII
     */
    public function generateCertificate(Reservation $reservation, array $additionalData = []): EgiReservationCertificate {
        try {
            // Create the certificate record
            $certificate = $reservation->createCertificate($additionalData);

            // Generate and store PDF
            $this->generatePdf($certificate);

            $this->logger->info('Certificate generated successfully', [
                'certificate_uuid' => $certificate->certificate_uuid,
                'reservation_id' => $reservation->id,
                'egi_id' => $reservation->egi_id
            ]);

            return $certificate;
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate certificate', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
                'trace' => $e->getTraceAsString()
            ]);

            throw UltraError::handle('RESERVATION_CERTIFICATE_GENERATION_FAILED', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Generate a PDF for a certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return bool Whether PDF generation was successful
     */
    public function generatePdf(EgiReservationCertificate $certificate): bool {
        try {
            // Create PDF filename
            $filename = 'certificate_' . $certificate->certificate_uuid . '.pdf';
            $path = 'certificates/' . Str::substr($certificate->certificate_uuid, 0, 2) . '/' . $filename;

            // We'll use a simple PDF generation approach for MVP
            // In a real implementation, you might want to use a more robust PDF library
            $pdf = $this->createPdfContent($certificate);

            // Store the PDF
            Storage::put($path, $pdf);

            // Update the certificate with the path
            $certificate->pdf_path = $path;
            $certificate->save();

            $this->logger->info('PDF generated for certificate', [
                'certificate_uuid' => $certificate->certificate_uuid,
                'path' => $path
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate PDF for certificate', [
                'error' => $e->getMessage(),
                'certificate_uuid' => $certificate->certificate_uuid,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Create PDF content for a certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return string The PDF content
     */
    private function createPdfContent(EgiReservationCertificate $certificate): string {
        // For MVP, we'll use a simple approach with TCPDF
        // In a real implementation, you might want to use a template with more styling
        // This is a placeholder for the actual PDF generation logic

        // You'll need to add the TCPDF library to your project
        // composer require tecnickcom/tcpdf

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('FlorenceEGI');
        $pdf->SetAuthor('FlorenceEGI Certificate Authority');
        $pdf->SetTitle('EGI Reservation Certificate');
        $pdf->SetSubject('Reservation Certificate for EGI #' . $certificate->egi_id);

        // Set default header data
        $pdf->SetHeaderData('', 0, 'FlorenceEGI Reservation Certificate', 'Certificate UUID: ' . $certificate->certificate_uuid);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // Add a page
        $pdf->AddPage();

        // Create certificate content
        $html = $this->generateHtmlForCertificate($certificate);

        // Print content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generate QR code with verification URL
        $style = [
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => [255, 255, 255],
            'module_width' => 1,
            'module_height' => 1
        ];

        $verificationUrl = route('egi-certificates.verify', $certificate->certificate_uuid);
        $pdf->write2DBarcode($verificationUrl, 'QRCODE,L', 150, 190, 40, 40, $style, 'N');

        // Close and return PDF content
        return $pdf->Output('', 'S');
    }

    /**
     * Generate HTML content for the certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return string HTML content
     */
    private function generateHtmlForCertificate(EgiReservationCertificate $certificate): string {
        // Load certificate details
        $egi = $certificate->egi;
        $reservation = $certificate->reservation;

        // Build HTML content
        $html = '
        <style>
            .certificate {
                font-family: Arial, sans-serif;
                color: #333;
            }
            .header {
                font-size: 24px;
                font-weight: bold;
                color: #4a5568;
                text-align: center;
                margin-bottom: 20px;
            }
            .section {
                margin-bottom: 15px;
            }
            .label {
                font-weight: bold;
            }
            .value {
                margin-left: 10px;
            }
            .signature {
                margin-top: 30px;
                font-size: 12px;
                color: #718096;
            }
            .footer {
                text-align: center;
                font-size: 10px;
                color: #a0aec0;
                margin-top: 30px;
            }
            .badge {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
            }
            .badge-strong {
                background-color: #4299e1;
            }
            .badge-weak {
                background-color: #ed8936;
            }
        </style>

        <div class="certificate">
            <div class="header">EGI Reservation Certificate</div>

            <div class="section">
                <span class="badge badge-' . $certificate->reservation_type . '">' .
            ucfirst($certificate->reservation_type) . ' Reservation
                </span>
            </div>

            <div class="section">
                <span class="label">EGI Title:</span>
                <span class="value">' . htmlspecialchars($egi->title ?? 'Unknown EGI') . '</span>
            </div>

            <div class="section">
                <span class="label">Collection:</span>
                <span class="value">' . htmlspecialchars($egi->collection->collection_name ?? 'Unknown Collection') . '</span>
            </div>

            <div class="section">
                <span class="label">Wallet Address:</span>
                <span class="value">' . htmlspecialchars($certificate->wallet_address) . '</span>
            </div>

            <div class="section">
                <span class="label">Offer Amount:</span>
                <span class="value">€' . number_format($certificate->offer_amount_fiat, 2) . ' (' . number_format($certificate->offer_amount_algo, 8) . ' ALGO)</span>
            </div>

            <div class="section">
                <span class="label">Reservation Date:</span>
                <span class="value">' . $certificate->created_at->format('d M Y H:i:s') . '</span>
            </div>

            <div class="section">
                <span class="label">Certificate UUID:</span>
                <span class="value">' . $certificate->certificate_uuid . '</span>
            </div>

            <div class="signature">
                <p>This certificate is digitally signed by FlorenceEGI.</p>
                <p>Signature Hash: ' . $certificate->signature_hash . '</p>
                <p>To verify this certificate, visit:</p>
                <p>' . route('egi-certificates.verify', $certificate->certificate_uuid) . '</p>
            </div>

            <div class="footer">
                <p>This certificate confirms a reservation on the FlorenceEGI platform, a bridge between digital and ecological value.</p>
                <p>© ' . date('Y') . ' FlorenceEGI - All Rights Reserved</p>
            </div>
        </div>';

        return $html;
    }

    /**
     * Generate blockchain certificate for minted EGI
     *
     * @param \App\Models\EgiBlockchain $egiBlockchain Minted EGI blockchain record
     * @return string Certificate file path
     * @throws \Exception Certificate generation failed
     *
     * @privacy-safe Minimal PII in certificate, GDPR compliant
     */
    /**
     * Generate post-mint blockchain certificate and create record
     *
     * @param \App\Models\Egi $egi The minted EGI
     * @param \App\Models\EgiBlockchain $egiBlockchain The blockchain record
     * @return EgiReservationCertificate The generated certificate model
     * @throws \Exception If certificate generation fails
     *
     * @purpose Creates blockchain certificate after successful mint
     */
    public function generateBlockchainCertificate(\App\Models\Egi $egi, \App\Models\EgiBlockchain $egiBlockchain): EgiReservationCertificate {
        try {
            // ✅ VALIDAZIONE BLOCKING - Certificato SOLO con dati completi
            // Un certificato senza ASA ID, TX ID o importo NON CERTIFICA NULLA
            if (empty($egiBlockchain->asa_id)) {
                throw new \Exception('Cannot generate certificate: ASA ID missing (mint not completed)');
            }

            if (empty($egiBlockchain->blockchain_tx_id)) {
                throw new \Exception('Cannot generate certificate: Transaction ID missing (mint not completed)');
            }

            // CREATOR SELF-MINT: paid_amount può essere NULL (mint gratuito per owner)
            // BUYER MINT: paid_amount deve essere > 0
            // FALLBACK: Se paid_amount è NULL, usa 0 (certificato owner self-mint gratuito)
            $paidAmount = $egiBlockchain->paid_amount ?? 0;
            
            if ($paidAmount < 0) {
                throw new \Exception('Cannot generate certificate: Payment amount is negative');
            }
            
            $this->logger->info('Payment amount validation passed', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'paid_amount_raw' => $egiBlockchain->paid_amount,
                'paid_amount_used' => $paidAmount,
                'is_free_owner_mint' => is_null($egiBlockchain->paid_amount)
            ]);

            // Generate certificate UUID
            $certificateUuid = (string) Str::uuid();

            // Create timestamp for signature
            $createdAt = now()->toIso8601String();

            // Generate signature hash using blockchain-specific fields
            // MUST match generateVerificationData() in EgiReservationCertificate model
            $signatureData = implode('|', [
                $certificateUuid,
                $egi->id,
                $egiBlockchain->id,
                $egiBlockchain->asa_id ?? '',
                $egiBlockchain->blockchain_tx_id ?? '',
                $paidAmount, // Usa $paidAmount con fallback (0 se NULL)
                $createdAt
            ]);

            // FALLBACK: Se buyer_user_id è null (owner self-mint), usa egi.user_id (creator)
            $buyerUserId = $egiBlockchain->buyer_user_id ?? $egi->user_id;
            
            $this->logger->info('Creating blockchain certificate record', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egi->id,
                'buyer_user_id_raw' => $egiBlockchain->buyer_user_id,
                'buyer_user_id_fallback' => $buyerUserId,
                'certificate_uuid' => $certificateUuid
            ]);
            
            // Create certificate record in egi_reservation_certificates table
            $certificate = EgiReservationCertificate::create([
                'certificate_type' => 'mint',
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egi->id,
                'reservation_id' => $egiBlockchain->reservation_id, // Can be null for direct mint
                'user_id' => $buyerUserId,
                'wallet_address' => $egiBlockchain->buyer_wallet ?? 'Treasury Custody',
                'reservation_type' => 'strong', // Mint is always strong ownership
                'offer_amount_fiat' => $paidAmount, // Usa $paidAmount con fallback (0 se owner self-mint)
                'offer_amount_algo' => 0, // Not used for mint certificates
                'certificate_uuid' => $certificateUuid,
                'signature_hash' => hash('sha256', $signatureData),
                'is_superseded' => false,
                'is_current_highest' => true,
                'created_at' => $createdAt,
            ]);

            // Generate certificate path
            $certificateFileName = "egi_blockchain_certificate_{$certificate->certificate_uuid}.pdf";
            $certificatePath = "certificates/blockchain/{$certificateFileName}";

            // Get buyer information
            // FALLBACK: Se buyer relation è null, usa creator name
            $buyer = $egiBlockchain->buyer ?? $egi->user;
            $buyerName = $buyer ? $buyer->name : 'Anonymous Buyer';

            // Prepare certificate data (ensure all values are strings for PDF generation)
            $certificateData = [
                'certificate_uuid' => (string) $certificate->certificate_uuid,
                'egi_id' => (string) $egi->id,
                'egi_title' => (string) ($egi->title ?? 'Unknown EGI'),
                'buyer_name' => (string) $buyerName,
                'buyer_wallet' => (string) ($egiBlockchain->buyer_wallet ?? 'Treasury Custody'),
                'asa_id' => $egiBlockchain->asa_id ?? '',
                'blockchain_tx_id' => $egiBlockchain->blockchain_tx_id ?? '',
                'purchase_amount' => (float) $paidAmount, // Usa $paidAmount con fallback (0 se owner self-mint)
                'purchase_currency' => (string) ($egiBlockchain->paid_currency ?? 'EUR'),
                'minted_at' => (string) ($egiBlockchain->minted_at ? $egiBlockchain->minted_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s')),
                'ownership_type' => (string) ($egiBlockchain->ownership_type ?? 'Full Ownership'),
                'verification_url' => (string) route('egi-certificates.show', $certificate->certificate_uuid)
            ];

            // 🔍 DEBUG: Log certificate data before PDF generation
            $this->logger->debug('Certificate data prepared for PDF', [
                'egi_id' => $egi->id,
                'blockchain_id' => $egiBlockchain->id,
                'asa_id' => $egiBlockchain->asa_id,
                'tx_id' => $egiBlockchain->blockchain_tx_id,
                'paid_amount_raw' => $egiBlockchain->paid_amount,
                'paid_amount_used' => $paidAmount,
                'egi_price' => $egi->price,
                'final_purchase_amount' => $certificateData['purchase_amount'],
                'certificate_data' => $certificateData,
            ]);

            // Generate PDF content
            $pdfContent = $this->generateBlockchainCertificatePdf($certificateData);

            // Store certificate file
            Storage::put($certificatePath, $pdfContent);

            // Update certificate with PDF path and public URL
            $certificate->update([
                'pdf_path' => $certificatePath,
                'public_url' => route('egi-certificates.show', $certificate->certificate_uuid)
            ]);

            // Update blockchain record with certificate reference
            $egiBlockchain->update([
                'certificate_path' => $certificatePath,
                'verification_url' => route('egi-certificates.show', $certificate->certificate_uuid)
            ]);

            $this->logger->info('Blockchain certificate generated successfully', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egi->id,
                'certificate_uuid' => $certificate->certificate_uuid,
                'certificate_path' => $certificatePath
            ]);

            return $certificate;
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate blockchain certificate', [
                'error' => $e->getMessage(),
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egi->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw per permettere al controller di gestire con ErrorManager
            throw new \Exception('Blockchain certificate generation failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate blockchain certificate PDF content
     *
     * @param array $certificateData Certificate data array
     * @return string PDF content as string
     */
    /**
     * Generate blockchain certificate PDF - ULTRA ECCELLENZA PA-READY v2
     * NO EMOJI (DomPDF non li supporta), layout professionale, singola pagina
     *
     * @param array $certificateData Certificate data
     * @return string Binary PDF content
     */
    private function generateBlockchainCertificatePdf(array $certificateData): string {
        // Dati DEVONO essere presenti (validati in generateBlockchainCertificate)
        // Se arriviamo qui, ASA ID, TX ID e paid_amount sono garantiti NON NULL
        $asaId = htmlspecialchars($certificateData['asa_id']);
        $txId = htmlspecialchars($certificateData['blockchain_tx_id']);
        $amount = number_format($certificateData['purchase_amount'], 2, ',', '.') . ' ' . htmlspecialchars($certificateData['purchase_currency'] ?? 'EUR');

        // Blockchain explorer URLs (network-aware)
        $network = config('algorand.algorand.network', 'testnet');
        $explorerBaseUrl = config("algorand.algorand.{$network}.explorer_url", 'https://testnet.explorer.perawallet.app');
        $asaExplorerUrl = "{$explorerBaseUrl}/asset/{$asaId}";
        $txExplorerUrl = "{$explorerBaseUrl}/tx/{$txId}";

        // Dati certificato escapati
        $certificateUuid = htmlspecialchars($certificateData['certificate_uuid']);
        $mintedAt = htmlspecialchars($certificateData['minted_at']);
        $egiId = htmlspecialchars($certificateData['egi_id']);
        $egiTitle = htmlspecialchars($certificateData['egi_title']);
        $buyerName = htmlspecialchars($certificateData['buyer_name']);
        $buyerWallet = htmlspecialchars($certificateData['buyer_wallet']);
        $ownershipType = htmlspecialchars(ucfirst($certificateData['ownership_type']));
        $verificationUrl = htmlspecialchars($certificateData['verification_url']);
        $generatedAt = now()->format('d/m/Y H:i:s');
        $currentYear = date('Y');

        // TRADUZIONI (NO TESTO HARDCODED MAI PIÙ!)
        $t = [
            'header_title' => __('certificate_pdf.header_title'),
            'header_subtitle' => __('certificate_pdf.header_subtitle'),
            'section_certificate' => __('certificate_pdf.section_certificate'),
            'label_uuid' => __('certificate_pdf.label_uuid'),
            'label_issue_date' => __('certificate_pdf.label_issue_date'),
            'section_artwork' => __('certificate_pdf.section_artwork'),
            'label_title' => __('certificate_pdf.label_title'),
            'label_artwork_id' => __('certificate_pdf.label_artwork_id'),
            'section_owner' => __('certificate_pdf.section_owner'),
            'label_name' => __('certificate_pdf.label_name'),
            'label_wallet' => __('certificate_pdf.label_wallet'),
            'label_custody' => __('certificate_pdf.label_custody'),
            'section_blockchain' => __('certificate_pdf.section_blockchain'),
            'label_asset_id' => __('certificate_pdf.label_asset_id'),
            'label_transaction_id' => __('certificate_pdf.label_transaction_id'),
            'label_amount_paid' => __('certificate_pdf.label_amount_paid'),
            'section_verification' => __('certificate_pdf.section_verification'),
            'verification_text' => __('certificate_pdf.verification_text'),
            'footer_brand' => __('certificate_pdf.footer_brand'),
            'footer_tagline' => __('certificate_pdf.footer_tagline'),
            'footer_description' => __('certificate_pdf.footer_description'),
            'footer_generated' => __('certificate_pdf.footer_generated', [
                'date' => $generatedAt,
                'year' => $currentYear
            ]),
        ];

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @page {
                    margin: 12mm;
                    size: A4 portrait;
                }

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: "DejaVu Sans", sans-serif;
                    color: #1B365D;
                    line-height: 1.3;
                    font-size: 9pt;
                }

                /* HEADER */
                .header {
                    text-align: center;
                    padding: 15px 0;
                    margin-bottom: 15px;
                    background: linear-gradient(to bottom, #fdfbf7 0%, #f8f5ef 100%);
                    border-top: 4px solid #D4A574;
                    border-bottom: 4px solid #D4A574;
                }
                .header h1 {
                    color: #1B365D;
                    font-size: 18pt;
                    font-weight: 700;
                    margin-bottom: 5px;
                    letter-spacing: 1px;
                }
                .header .subtitle {
                    color: #D4A574;
                    font-size: 10pt;
                    font-weight: 600;
                    font-style: italic;
                }

                /* GRID 2 COLONNE */
                .grid {
                    display: table;
                    width: 100%;
                    margin-bottom: 10px;
                    border-spacing: 8px 0;
                }
                .col {
                    display: table-cell;
                    width: 50%;
                    vertical-align: top;
                }

                /* BOX */
                .box {
                    background: #fdfbf7;
                    border: 2px solid #D4A574;
                    border-radius: 5px;
                    padding: 10px;
                    margin-bottom: 10px;
                }
                .box-title {
                    color: #1B365D;
                    font-size: 11pt;
                    font-weight: 700;
                    margin-bottom: 8px;
                    padding-bottom: 5px;
                    border-bottom: 2px solid #D4A574;
                }

                /* INFO ROWS */
                .info-row {
                    margin-bottom: 6px;
                    padding: 4px 0;
                    border-bottom: 1px solid #f0ebe0;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                .label {
                    display: inline-block;
                    width: 38%;
                    color: #6B6B6B;
                    font-weight: 600;
                    font-size: 8.5pt;
                }
                .value {
                    display: inline-block;
                    width: 60%;
                    color: #1B365D;
                    font-weight: 600;
                    font-family: "DejaVu Sans Mono", monospace;
                    font-size: 8pt;
                    word-break: break-all;
                }
                .value a {
                    color: #1B365D;
                    text-decoration: underline;
                    font-weight: 600;
                }
                .value a:hover {
                    color: #D4A574;
                }

                /* BLOCKCHAIN BOX */
                .blockchain {
                    background: linear-gradient(to bottom, #1B365D 0%, #2a4a7a 100%);
                    color: white;
                    padding: 12px;
                    margin: 10px 0;
                    border-radius: 5px;
                    border: 3px solid #D4A574;
                }
                .blockchain .box-title {
                    color: #D4A574;
                    border-bottom-color: rgba(212, 165, 116, 0.4);
                    font-size: 12pt;
                    text-align: center;
                }
                .blockchain .label {
                    color: #D4A574;
                    font-weight: 700;
                }
                .blockchain .value {
                    color: white;
                    font-weight: 700;
                }
                .blockchain .highlight {
                    background: rgba(255, 255, 255, 0.1);
                    padding: 8px;
                    border-radius: 4px;
                    margin: 8px 0;
                    text-align: center;
                    border: 2px solid rgba(212, 165, 116, 0.3);
                }
                .blockchain .highlight .value {
                    font-size: 12pt;
                    color: #D4A574;
                }

                /* VERIFICATION */
                .verify {
                    background: linear-gradient(to bottom, #FFF9E6 0%, #FEF5D4 100%);
                    border: 2px solid #D4A574;
                    padding: 10px;
                    margin: 10px 0;
                    border-radius: 5px;
                    text-align: center;
                }
                .verify .title {
                    color: #1B365D;
                    font-size: 10pt;
                    font-weight: 700;
                    margin-bottom: 6px;
                }
                .verify p {
                    margin: 4px 0;
                    font-size: 8pt;
                    color: #6B6B6B;
                }
                .verify .url {
                    font-family: "DejaVu Sans Mono", monospace;
                    font-size: 7pt;
                    color: #1B365D;
                    word-break: break-all;
                    background: white;
                    padding: 6px;
                    border-radius: 3px;
                    margin-top: 6px;
                    border: 1px solid #D4A574;
                }

                /* FOOTER */
                .footer {
                    text-align: center;
                    margin-top: 12px;
                    padding-top: 10px;
                    border-top: 3px solid #D4A574;
                    font-size: 7.5pt;
                    color: #6B6B6B;
                    line-height: 1.3;
                }
                .footer .brand {
                    color: #1B365D;
                    font-weight: 700;
                    font-size: 9pt;
                }
                .footer .tagline {
                    color: #D4A574;
                    font-style: italic;
                    margin: 3px 0;
                }
            </style>
        </head>
        <body>
            <!-- HEADER -->
            <div class="header">
                <h1>{$t['header_title']}</h1>
                <div class="subtitle">{$t['header_subtitle']}</div>
            </div>

            <!-- GRID 2 COLONNE -->
            <div class="grid">
                <div class="col">
                    <!-- Certificato -->
                    <div class="box">
                        <div class="box-title">{$t['section_certificate']}</div>
                        <div class="info-row">
                            <span class="label">{$t['label_uuid']}</span>
                            <span class="value">{$certificateUuid}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">{$t['label_issue_date']}</span>
                            <span class="value">{$mintedAt}</span>
                        </div>
                    </div>

                    <!-- Opera Digitale -->
                    <div class="box">
                        <div class="box-title">{$t['section_artwork']}</div>
                        <div class="info-row">
                            <span class="label">{$t['label_title']}</span>
                            <span class="value">{$egiTitle}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">{$t['label_artwork_id']}</span>
                            <span class="value">#{$egiId}</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <!-- Proprietario -->
                    <div class="box">
                        <div class="box-title">{$t['section_owner']}</div>
                        <div class="info-row">
                            <span class="label">{$t['label_name']}</span>
                            <span class="value">{$buyerName}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">{$t['label_wallet']}</span>
                            <span class="value">{$buyerWallet}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">{$t['label_custody']}</span>
                            <span class="value">{$ownershipType}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOCKCHAIN DATA -->
            <div class="blockchain">
                <div class="box-title">{$t['section_blockchain']}</div>

                <div class="highlight">
                    <div class="info-row">
                        <span class="label" style="width: 30%; text-align: right; padding-right: 8px; color: #6B6B6B; font-weight: 600;">{$t['label_asset_id']}</span>
                        <span class="value" style="width: 68%; color: #1B365D; font-weight: 600;"><a href="{$asaExplorerUrl}" target="_blank" style="color: #1B365D; text-decoration: underline;">{$asaId}</a></span>
                    </div>
                </div>

                <div class="info-row">
                    <span class="label" style="color: #6B6B6B; font-weight: 600;">{$t['label_transaction_id']}</span>
                    <span class="value" style="color: #1B365D; font-weight: 600;"><a href="{$txExplorerUrl}" target="_blank" style="color: #1B365D; text-decoration: underline;">{$txId}</a></span>
                </div>
                <div class="info-row">
                    <span class="label" style="color: #6B6B6B; font-weight: 600;">{$t['label_amount_paid']}</span>
                    <span class="value" style="color: #1B365D; font-weight: 600;">{$amount}</span>
                </div>
            </div>

            <!-- VERIFICATION -->
            <div class="verify">
                <div class="title">{$t['section_verification']}</div>
                <p>{$t['verification_text']}</p>
                <div class="url">{$verificationUrl}</div>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <p class="brand">{$t['footer_brand']}</p>
                <p class="tagline">{$t['footer_tagline']}</p>
                <p style="font-size: 7pt; margin-top: 6px;">
                    {$t['footer_description']}
                </p>
                <p style="margin-top: 6px; font-size: 7pt;">
                    {$t['footer_generated']}
                </p>
            </div>
        </body>
        </html>
        HTML;

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->output();
    }
}
