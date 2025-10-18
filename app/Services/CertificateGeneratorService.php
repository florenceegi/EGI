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
            // Validate blockchain record has required data
            if (!$egiBlockchain->asa_id || !$egiBlockchain->blockchain_tx_id) {
                throw new \Exception('Blockchain record missing required data (asa_id or blockchain_tx_id)');
            }

            // Generate certificate UUID
            $certificateUuid = (string) Str::uuid();

            // Create certificate record in egi_reservation_certificates table
            $certificate = EgiReservationCertificate::create([
                'certificate_type' => 'mint',
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egi->id,
                'user_id' => $egiBlockchain->buyer_user_id,
                'wallet_address' => $egiBlockchain->buyer_wallet ?? 'Treasury Custody',
                'reservation_type' => 'strong', // Mint is always strong ownership
                'offer_amount_fiat' => $egiBlockchain->paid_amount,
                'offer_amount_algo' => 0, // Not used for mint certificates
                'certificate_uuid' => $certificateUuid,
                'signature_hash' => hash('sha256', $certificateUuid . '|' . $egiBlockchain->asa_id . '|' . $egiBlockchain->blockchain_tx_id),
                'is_superseded' => false,
                'is_current_highest' => true,
            ]);

            // Generate certificate path
            $certificateFileName = "egi_blockchain_certificate_{$certificate->certificate_uuid}.pdf";
            $certificatePath = "certificates/blockchain/{$certificateFileName}";

            // Get buyer information
            $buyer = $egiBlockchain->buyer;
            $buyerName = $buyer ? $buyer->name : 'Anonymous Buyer';

            // Prepare certificate data (ensure all values are strings for PDF generation)
            $certificateData = [
                'certificate_uuid' => (string) $certificate->certificate_uuid,
                'egi_id' => (string) $egi->id,
                'egi_title' => (string) ($egi->title ?? 'Unknown EGI'),
                'buyer_name' => (string) $buyerName,
                'buyer_wallet' => (string) ($egiBlockchain->buyer_wallet ?? 'Treasury Custody'),
                'asa_id' => (string) $egiBlockchain->asa_id,
                'blockchain_tx_id' => (string) $egiBlockchain->blockchain_tx_id,
                'purchase_amount' => (float) $egiBlockchain->paid_amount,
                'purchase_currency' => (string) ($egiBlockchain->paid_currency ?? 'EUR'),
                'minted_at' => (string) ($egiBlockchain->minted_at ? $egiBlockchain->minted_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s')),
                'ownership_type' => (string) ($egiBlockchain->ownership_type ?? 'Unknown'),
                'verification_url' => (string) route('egi-certificates.show', $certificate->certificate_uuid)
            ];

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
                'trace' => $e->getTraceAsString()
            ]);

            throw UltraError::handle('BLOCKCHAIN_CERTIFICATE_GENERATION_FAILED', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Generate blockchain certificate PDF content
     *
     * @param array $certificateData Certificate data array
     * @return string PDF content as string
     */
    /**
     * Generate blockchain certificate PDF with DomPDF
     *
     * @param array $certificateData Certificate data
     * @return string Binary PDF content
     */
    private function generateBlockchainCertificatePdf(array $certificateData): string {
        // Build HTML for certificate with Brand Guidelines
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @page {
                    margin: 25mm 20mm;
                    size: A4 portrait;
                }
                @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap");

                body {
                    font-family: "Source Sans Pro", "DejaVu Sans", sans-serif;
                    color: #1a202c;
                    line-height: 1.4;
                    font-size: 10pt;
                }

                /* HEADER - Eleganza Rinascimentale */
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    padding: 15px 0;
                    border-top: 3px solid #D4A574;
                    border-bottom: 3px solid #D4A574;
                    background: linear-gradient(to bottom, #ffffff 0%, #faf8f5 100%);
                }
                .header h1 {
                    font-family: "Playfair Display", serif;
                    color: #1B365D;
                    font-size: 20pt;
                    margin: 5px 0;
                    font-weight: 700;
                    letter-spacing: 0.5px;
                }
                .header .subtitle {
                    color: #D4A574;
                    font-size: 11pt;
                    font-weight: 600;
                    font-style: italic;
                }

                /* LAYOUT A 2 COLONNE */
                .content-wrapper {
                    display: table;
                    width: 100%;
                    margin-top: 15px;
                }
                .column {
                    display: table-cell;
                    vertical-align: top;
                    width: 48%;
                    padding: 0 1%;
                }

                /* SEZIONI - Oro Fiorentino */
                .section {
                    margin-bottom: 12px;
                    padding: 10px;
                    background: linear-gradient(135deg, #fdfbf7 0%, #f8f5ef 100%);
                    border-left: 3px solid #D4A574;
                    border-radius: 3px;
                }
                .section-title {
                    font-family: "Playfair Display", serif;
                    color: #1B365D;
                    font-size: 11pt;
                    font-weight: 700;
                    margin-bottom: 8px;
                    padding-bottom: 4px;
                    border-bottom: 1px solid #D4A574;
                }

                /* INFO ROWS */
                .info-row {
                    margin-bottom: 5px;
                    font-size: 9pt;
                }
                .info-label {
                    color: #6B6B6B;
                    font-weight: 600;
                    display: inline-block;
                    width: 35%;
                }
                .info-value {
                    color: #1a202c;
                    display: inline-block;
                    width: 63%;
                    font-family: "DejaVu Sans Mono", monospace;
                    font-size: 8.5pt;
                    word-wrap: break-word;
                }

                /* BLOCKCHAIN BOX - Blu Algoritmo */
                .blockchain-box {
                    background: linear-gradient(135deg, #1B365D 0%, #2a4a7a 100%);
                    color: white;
                    padding: 12px;
                    margin: 15px 0;
                    border-radius: 5px;
                    box-shadow: 0 2px 8px rgba(27, 54, 93, 0.3);
                }
                .blockchain-box .section-title {
                    color: #D4A574;
                    border-bottom-color: #D4A574;
                }
                .blockchain-box .info-label {
                    color: #D4A574;
                }
                .blockchain-box .info-value {
                    color: white;
                    font-weight: 600;
                }

                /* VERIFICATION BOX */
                .verification-box {
                    background: linear-gradient(135deg, #fff9e6 0%, #fef5d4 100%);
                    border: 2px solid #D4A574;
                    padding: 10px;
                    margin: 12px 0;
                    border-radius: 3px;
                    text-align: center;
                }
                .verification-box .section-title {
                    color: #1B365D;
                    font-size: 10pt;
                    margin-bottom: 5px;
                    border: none;
                }
                .verification-box p {
                    margin: 3px 0;
                    font-size: 8pt;
                    line-height: 1.3;
                }
                .verification-url {
                    font-family: "DejaVu Sans Mono", monospace;
                    font-size: 7pt;
                    color: #1B365D;
                    word-break: break-all;
                }

                /* FOOTER - Elegante */
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    padding-top: 10px;
                    border-top: 2px solid #D4A574;
                    font-size: 8pt;
                    color: #6B6B6B;
                    line-height: 1.3;
                }
                .footer strong {
                    color: #1B365D;
                    font-family: "Playfair Display", serif;
                }
            </style>
        </head>
        <body>
            <!-- HEADER -->
            <div class="header">
                <h1>🏛️ CERTIFICATO DI PROPRIETÀ BLOCKCHAIN</h1>
                <div class="subtitle">FlorenceEGI - Il Rinascimento Digitale</div>
            </div>

            <!-- CONTENT A 2 COLONNE -->
            <div class="content-wrapper">
                <!-- COLONNA SINISTRA -->
                <div class="column">
                    <!-- Certificato Info -->
                    <div class="section">
                        <div class="section-title">📜 Certificato</div>
                        <div class="info-row">
                            <span class="info-label">UUID:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['certificate_uuid']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Emissione:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['minted_at']) . '</span>
                        </div>
                    </div>

                    <!-- EGI Info -->
                    <div class="section">
                        <div class="section-title">🎨 Dettagli EGI</div>
                        <div class="info-row">
                            <span class="info-label">Titolo:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['egi_title']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">EGI ID:</span>
                            <span class="info-value">#' . htmlspecialchars($certificateData['egi_id']) . '</span>
                        </div>
                    </div>
                </div>

                <!-- COLONNA DESTRA -->
                <div class="column">
                    <!-- Proprietario -->
                    <div class="section">
                        <div class="section-title">👤 Proprietario</div>
                        <div class="info-row">
                            <span class="info-label">Nome:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['buyer_name']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Wallet:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['buyer_wallet']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tipo:</span>
                            <span class="info-value">' . htmlspecialchars($certificateData['ownership_type']) . '</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOCKCHAIN DATA - Full Width -->
            <div class="blockchain-box">
                <div class="section-title">⛓️ DATI BLOCKCHAIN (Algorand)</div>
                <div class="info-row">
                    <span class="info-label">Asset ID (ASA):</span>
                    <span class="info-value" style="font-size: 10pt; font-weight: 700;">' . htmlspecialchars($certificateData['asa_id']) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaction ID:</span>
                    <span class="info-value">' . htmlspecialchars($certificateData['blockchain_tx_id']) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Importo Pagato:</span>
                    <span class="info-value">' . number_format($certificateData['purchase_amount'], 2, ',', '.') . ' ' . htmlspecialchars($certificateData['purchase_currency']) . '</span>
                </div>
            </div>

            <!-- VERIFICATION -->
            <div class="verification-box">
                <div class="section-title">🔐 VERIFICA AUTENTICITÀ</div>
                <p>Questo certificato può essere verificato pubblicamente:</p>
                <p class="verification-url">' . htmlspecialchars($certificateData['verification_url']) . '</p>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <p><strong>FlorenceEGI</strong> - Arte, Tecnologia e Rigenerazione Planetaria</p>
                <p style="font-size: 7.5pt;">Il primo marketplace che unisce Qualità Artistica + Liquidità Massima + Impatto Ambientale Reale</p>
                <p style="margin-top: 5px;">Certificato generato il ' . now()->format('d/m/Y H:i:s') . ' | © ' . date('Y') . ' FlorenceEGI - Tutti i diritti riservati</p>
            </div>
        </body>
        </html>
        ';

        // Generate PDF with DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->output();
    }
}