<?php

namespace App\Services\Invoicing;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceAggregation;
use App\Models\User;
use App\Models\PaymentDistribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * @package App\Services\Invoicing
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Invoicing System)
 * @date 2025-11-21
 * @purpose Service for managing invoices, aggregations, and invoicing operations
 */
class InvoiceService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get user invoices (sales or purchases)
     *
     * @param User $user
     * @param string $type 'sales' or 'purchases'
     * @param array $filters
     * @return Collection
     */
    public function getUserInvoices(User $user, string $type = 'sales', array $filters = []): Collection {
        $this->logger->info('InvoiceService: Getting user invoices', [
            'user_id' => $user->id,
            'type' => $type,
            'filters' => $filters,
        ]);

        $query = $type === 'sales'
            ? Invoice::forSeller($user->id)
            : Invoice::forBuyer($user->id);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->withStatus($filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->inDateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['invoice_type'])) {
            $query->ofType($filters['invoice_type']);
        }

        return $query->with(['seller', 'buyer', 'items'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    /**
     * Get user monthly aggregations
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getUserAggregations(User $user, array $filters = []): Collection {
        $this->logger->info('InvoiceService: Getting user aggregations', [
            'user_id' => $user->id,
            'filters' => $filters,
        ]);

        $query = InvoiceAggregation::forUser($user->id);

        if (!empty($filters['status'])) {
            $query->withStatus($filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->inPeriod($filters['start_date'], $filters['end_date']);
        }

        return $query->with(['invoice'])
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Create monthly aggregation for user
     *
     * @param User $user
     * @param string $periodStart
     * @param string $periodEnd
     * @return InvoiceAggregation
     * @throws \Exception
     */
    public function createMonthlyAggregation(User $user, string $periodStart, string $periodEnd): InvoiceAggregation {
        try {
            $this->logger->info('InvoiceService: Creating monthly aggregation', [
                'user_id' => $user->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]);

            // Get all payment distributions where user received money in this period
            $distributions = PaymentDistribution::where('user_id', $user->id)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get();

            $totalSales = $distributions->sum('amount_eur');
            $totalItems = $distributions->count();
            $totalBuyers = $distributions->pluck('payer_user_id')->unique()->count();

            $aggregation = InvoiceAggregation::create([
                'user_id' => $user->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_sales_eur' => $totalSales,
                'total_items' => $totalItems,
                'total_buyers' => $totalBuyers,
                'status' => 'pending',
                'metadata' => [
                    'distribution_ids' => $distributions->pluck('id')->toArray(),
                ],
            ]);

            $this->logger->info('InvoiceService: Monthly aggregation created', [
                'aggregation_id' => $aggregation->id,
                'total_sales_eur' => $totalSales,
            ]);

            return $aggregation;
        } catch (\Exception $e) {
            $this->logger->error('InvoiceService: Failed to create aggregation', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate invoice from aggregation
     *
     * @param InvoiceAggregation $aggregation
     * @return Invoice
     * @throws \Exception
     */
    public function generateInvoiceFromAggregation(InvoiceAggregation $aggregation): Invoice {
        try {
            $this->logger->info('InvoiceService: Generating invoice from aggregation', [
                'aggregation_id' => $aggregation->id,
                'user_id' => $aggregation->user_id,
            ]);

            return DB::transaction(function () use ($aggregation) {
                $user = $aggregation->user;

                // Get user invoice preferences
                $preferences = $user->invoicePreferences;

                // Generate invoice code
                $invoiceNumber = $this->getNextInvoiceNumber($user->id);
                $invoiceCode = $this->generateInvoiceCode($user->id, $invoiceNumber);

                // Calculate tax (default 22% IVA)
                $taxRate = 22.00;
                $subtotal = $aggregation->total_sales_eur;
                $taxAmount = round($subtotal * $taxRate / 100, 2);
                $total = $subtotal + $taxAmount;

                // Create invoice
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'invoice_code' => $invoiceCode,
                    'invoice_type' => 'sales',
                    'invoice_status' => 'draft',
                    'seller_user_id' => $user->id,
                    'buyer_user_id' => null, // Multiple buyers in aggregation
                    'issue_date' => now(),
                    'subtotal_eur' => $subtotal,
                    'tax_amount_eur' => $taxAmount,
                    'total_eur' => $total,
                    'currency' => 'EUR',
                    'managed_by' => $preferences->invoicing_mode === 'user_managed' ? 'user_external' : 'platform',
                    'metadata' => [
                        'aggregation_id' => $aggregation->id,
                        'period_start' => $aggregation->period_start,
                        'period_end' => $aggregation->period_end,
                    ],
                ]);

                // Get payment distributions from aggregation metadata
                $distributionIds = $aggregation->metadata['distribution_ids'] ?? [];
                $distributions = PaymentDistribution::whereIn('id', $distributionIds)->get();

                // Create invoice items from distributions
                foreach ($distributions as $distribution) {
                    $itemSubtotal = $distribution->amount_eur;
                    $itemTax = round($itemSubtotal * $taxRate / 100, 2);

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'code' => $distribution->egi_id ? 'EGI-' . str_pad($distribution->egi_id, 7, '0', STR_PAD_LEFT) : null,
                        'description' => $this->generateItemDescription($distribution),
                        'quantity' => 1,
                        'unit_price_eur' => $itemSubtotal,
                        'tax_rate' => $taxRate,
                        'tax_amount_eur' => $itemTax,
                        'subtotal_eur' => $itemSubtotal,
                        'total_eur' => $itemSubtotal + $itemTax,
                        'egi_id' => $distribution->egi_id,
                        'payment_distribution_id' => $distribution->id,
                    ]);
                }

                // Mark aggregation as invoiced
                $aggregation->markAsInvoiced($invoice->id);
                
                // Generate PDF
                $invoice->load(['seller.invoicePreferences', 'buyer.invoicePreferences', 'items']);
                $pdfPath = $this->generateInvoicePdf($invoice);
                
                // Update invoice with PDF path
                $invoice->update(['pdf_path' => $pdfPath]);

                $this->logger->info('InvoiceService: Invoice generated from aggregation', [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoiceCode,
                    'total_eur' => $total,
                    'pdf_path' => $pdfPath,
                ]);

                return $invoice;
            });
        } catch (\Exception $e) {
            $this->logger->error('InvoiceService: Failed to generate invoice', [
                'aggregation_id' => $aggregation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate PDF for invoice
     *
     * @param Invoice $invoice
     * @return string Path to generated PDF
     * @throws \Exception
     */
    protected function generateInvoicePdf(Invoice $invoice): string {
        try {
            $this->logger->info('InvoiceService: Generating PDF', [
                'invoice_id' => $invoice->id,
                'invoice_code' => $invoice->invoice_code,
            ]);
            
            // Generate PDF from view
            $pdf = Pdf::loadView('account.invoices.pdf.invoice', [
                'invoice' => $invoice,
            ]);
            
            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');
            
            // Generate filename
            $filename = sprintf(
                'invoices/%s/%s.pdf',
                $invoice->seller_user_id,
                $invoice->invoice_code
            );
            
            // Save PDF to storage
            Storage::put($filename, $pdf->output());
            
            $this->logger->info('InvoiceService: PDF generated successfully', [
                'invoice_id' => $invoice->id,
                'pdf_path' => $filename,
            ]);
            
            return $filename;
        } catch (\Exception $e) {
            $this->logger->error('InvoiceService: PDF generation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Export aggregation data for external system
     *
     * @param InvoiceAggregation $aggregation
     * @param string $format 'csv', 'excel', 'json'
     * @return string Path to exported file
     * @throws \Exception
     */
    public function exportAggregation(InvoiceAggregation $aggregation, string $format = 'csv'): string {
        try {
            $this->logger->info('InvoiceService: Exporting aggregation', [
                'aggregation_id' => $aggregation->id,
                'format' => $format,
            ]);

            // Get payment distributions with all necessary data
            $distributionIds = $aggregation->metadata['distribution_ids'] ?? [];
            $distributions = PaymentDistribution::whereIn('id', $distributionIds)
                ->with([
                    'egi.blockchain.buyer.billingInfo',
                    'egi.collection',
                    'user' // seller
                ])
                ->get();

            // Prepare export data with full invoice fields
            $exportData = $distributions->map(function ($distribution) {
                $buyer = $distribution->egi && $distribution->egi->blockchain 
                    ? $distribution->egi->blockchain->buyer 
                    : null;
                
                $billingInfo = $buyer?->billingInfo;
                $seller = $distribution->user;
                $egi = $distribution->egi;
                
                return [
                    // Transaction info
                    'transaction_id' => $distribution->id,
                    'transaction_date' => $distribution->created_at->format('Y-m-d H:i:s'),
                    'sale_date' => $distribution->created_at->format('Y-m-d'),
                    
                    // Seller (chi emette la fattura)
                    'seller_name' => $seller?->name ?? 'N/A',
                    'seller_email' => $seller?->email ?? 'N/A',
                    'seller_vat' => $seller?->billingInfo?->vat_number ?? 'N/A',
                    'seller_fiscal_code' => $seller?->billingInfo?->fiscal_code ?? 'N/A',
                    'seller_address' => $seller?->billingInfo?->address ?? 'N/A',
                    'seller_city' => $seller?->billingInfo?->city ?? 'N/A',
                    'seller_postal_code' => $seller?->billingInfo?->postal_code ?? 'N/A',
                    'seller_country' => $seller?->billingInfo?->country ?? 'IT',
                    
                    // Buyer (cliente)
                    'buyer_name' => $buyer?->name ?? 'N/A',
                    'buyer_email' => $buyer?->email ?? 'N/A',
                    'buyer_vat' => $billingInfo?->vat_number ?? 'N/A',
                    'buyer_fiscal_code' => $billingInfo?->fiscal_code ?? 'N/A',
                    'buyer_address' => $billingInfo?->address ?? 'N/A',
                    'buyer_city' => $billingInfo?->city ?? 'N/A',
                    'buyer_postal_code' => $billingInfo?->postal_code ?? 'N/A',
                    'buyer_country' => $billingInfo?->country ?? 'IT',
                    'buyer_sdi_code' => $billingInfo?->sdi_code ?? 'N/A',
                    'buyer_pec' => $billingInfo?->pec_email ?? 'N/A',
                    
                    // Item details
                    'item_code' => $distribution->egi_id ? 'EGI-' . str_pad($distribution->egi_id, 7, '0', STR_PAD_LEFT) : 'N/A',
                    'item_title' => $egi?->title ?? 'N/A',
                    'item_description' => $this->generateItemDescription($distribution),
                    'collection_name' => $egi?->collection?->name ?? 'N/A',
                    
                    // Financial details
                    'quantity' => 1,
                    'unit_price' => $distribution->amount_eur,
                    'subtotal' => $distribution->amount_eur,
                    'vat_rate' => '22', // IVA standard italiana
                    'vat_amount' => round($distribution->amount_eur * 0.22, 2),
                    'total_amount' => round($distribution->amount_eur * 1.22, 2),
                    'currency' => 'EUR',
                    
                    // Payment info
                    'payment_method' => $distribution->payment_method ?? 'stripe',
                    'payment_status' => 'completed',
                    
                    // Blockchain info (if needed for traceability)
                    'blockchain_tx_id' => $egi?->blockchain?->algorand_transaction_id ?? 'N/A',
                    'nft_asset_id' => $egi?->blockchain?->asset_id ?? 'N/A',
                ];
            });

            // Create export file
            $filename = 'aggregation_' . $aggregation->id . '_' . date('YmdHis') . '.' . $format;
            $path = storage_path('app/invoices/exports/' . $filename);

            // Ensure directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            // Log export data count
            $this->logger->info('InvoiceService: Preparing export', [
                'rows_count' => $exportData->count(),
                'format' => $format,
                'path' => $path,
            ]);
            
            // Export based on format
            switch ($format) {
                case 'csv':
                    $this->exportToCsv($exportData, $path);
                    break;
                case 'json':
                    $this->exportToJson($exportData, $path);
                    break;
                default:
                    throw new \Exception("Unsupported export format: {$format}");
            }
            
            // Verify file was created and has content
            if (!file_exists($path)) {
                throw new \Exception("Export file was not created: {$path}");
            }
            
            $fileSize = filesize($path);
            if ($fileSize === 0) {
                throw new \Exception("Export file is empty: {$path}");
            }
            
            $this->logger->info('InvoiceService: Export file created', [
                'file_size' => $fileSize,
                'path' => $path,
            ]);

            // Mark aggregation as exported
            $aggregation->markAsExported($format, $filename);

            $this->logger->info('InvoiceService: Aggregation exported', [
                'aggregation_id' => $aggregation->id,
                'path' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            $this->logger->error('InvoiceService: Failed to export aggregation', [
                'aggregation_id' => $aggregation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get next invoice number for user
     *
     * @param int $userId
     * @return int
     */
    protected function getNextInvoiceNumber(int $userId): int {
        $lastInvoice = Invoice::forSeller($userId)
            ->orderBy('invoice_number', 'desc')
            ->first();

        return $lastInvoice ? $lastInvoice->invoice_number + 1 : 1;
    }

    /**
     * Generate invoice code
     *
     * @param int $userId
     * @param int $invoiceNumber
     * @return string
     */
    protected function generateInvoiceCode(int $userId, int $invoiceNumber): string {
        $year = date('Y');
        $userPrefix = 'U' . str_pad($userId, 4, '0', STR_PAD_LEFT);
        $number = str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT);

        return "INV-{$year}-{$userPrefix}-{$number}";
    }

    /**
     * Generate item description from payment distribution
     *
     * @param PaymentDistribution $distribution
     * @return string
     */
    protected function generateItemDescription(PaymentDistribution $distribution): string {
        if ($distribution->egi) {
            return "EGI #{$distribution->egi->serial_number} - {$distribution->egi->title}";
        }

        if ($distribution->description) {
            return $distribution->description;
        }

        return __('invoices.fields.item_description_default');
    }

    /**
     * Export data to CSV
     *
     * @param Collection $data
     * @param string $path
     * @return void
     */
    protected function exportToCsv(Collection $data, string $path): void {
        $file = fopen($path, 'w');

        // Write header
        if ($data->isNotEmpty()) {
            fputcsv($file, array_keys($data->first()));
        }

        // Write data
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }

    /**
     * Export data to JSON
     *
     * @param Collection $data
     * @param string $path
     * @return void
     */
    protected function exportToJson(Collection $data, string $path): void {
        $json = json_encode(
            $data->values()->toArray(), 
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        
        if ($json === false) {
            throw new \Exception('Failed to encode JSON: ' . json_last_error_msg());
        }
        
        $written = file_put_contents($path, $json);
        
        if ($written === false) {
            throw new \Exception('Failed to write JSON file to: ' . $path);
        }
    }
}
