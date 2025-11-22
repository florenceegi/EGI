<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAggregation;
use App\Models\UserInvoicePreference;
use App\Services\Invoicing\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Invoicing System)
 * @date 2025-11-21
 * @purpose Handles invoice management operations
 */
class InvoiceController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected InvoiceService $invoiceService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        InvoiceService $invoiceService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->invoiceService = $invoiceService;
        $this->middleware('auth');
    }

    /**
     * Display invoices dashboard with tabs
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request) {
        try {
            $user = Auth::user();
            $activeTab = $request->get('tab', 'sales');
            $isPartial = $request->get('partial') || $request->ajax();

            $this->logger->info('InvoiceController: Displaying invoices dashboard', [
                'user_id' => $user->id,
                'active_tab' => $activeTab,
                'is_partial' => $isPartial,
            ]);

            // Get filters from request
            $filters = [
                'status' => $request->get('status'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'invoice_type' => $request->get('invoice_type'),
            ];

            // Get data based on active tab
            $salesInvoices = $activeTab === 'sales'
                ? $this->invoiceService->getUserInvoices($user, 'sales', $filters)
                : collect();

            $purchaseInvoices = $activeTab === 'purchases'
                ? $this->invoiceService->getUserInvoices($user, 'purchases', $filters)
                : collect();

            $aggregations = $activeTab === 'aggregations'
                ? $this->invoiceService->getUserAggregations($user, $filters)
                : collect();
            
            // If partial request for aggregations, return only that tab
            if ($isPartial && $activeTab === 'aggregations') {
                return view('account.invoices.partials.aggregations-tab', [
                    'aggregations' => $aggregations,
                    'filters' => $filters,
                ]);
            }

            // Get user invoice preferences
            $preferences = $user->invoicePreferences ?? new UserInvoicePreference();

            return view('account.invoices.index', compact(
                'salesInvoices',
                'purchaseInvoices',
                'aggregations',
                'preferences',
                'activeTab',
                'filters'
            ));
        } catch (\Exception $e) {
            return $this->errorManager->handle('INVOICE_INDEX_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Display single invoice
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View {
        try {
            $user = Auth::user();

            $invoice = Invoice::with(['seller', 'buyer', 'items.egi', 'items.paymentDistribution'])
                ->findOrFail($id);

            // Check authorization
            if ($invoice->seller_user_id !== $user->id && $invoice->buyer_user_id !== $user->id) {
                abort(403, __('invoices.errors.unauthorized'));
            }

            $this->logger->info('InvoiceController: Displaying invoice', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
            ]);

            return view('account.invoices.show', compact('invoice'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('INVOICE_SHOW_FAILED', [
                'user_id' => Auth::id(),
                'invoice_id' => $id,
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Generate invoice from aggregation
     *
     * @param int $aggregationId
     * @return RedirectResponse
     */
    public function generateFromAggregation(int $aggregationId): RedirectResponse {
        try {
            $user = Auth::user();

            $this->logger->info('InvoiceController: Generating invoice from aggregation', [
                'user_id' => $user->id,
                'aggregation_id' => $aggregationId,
            ]);

            $aggregation = InvoiceAggregation::forUser($user->id)->findOrFail($aggregationId);

            // Check if already invoiced
            if ($aggregation->isInvoiced()) {
                return redirect()->route('account.invoices', ['tab' => 'aggregations'])
                    ->withErrors(['error' => __('invoices.errors.already_invoiced')]);
            }

            $invoice = $this->invoiceService->generateInvoiceFromAggregation($aggregation);

            $this->logger->info('InvoiceController: Invoice generated successfully', [
                'invoice_id' => $invoice->id,
                'aggregation_id' => $aggregationId,
            ]);

            return redirect()->route('account.invoices.show', $invoice->id)
                ->with('success', __('invoices.messages.aggregation_generated'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('INVOICE_GENERATION_FAILED', [
                'user_id' => Auth::id(),
                'aggregation_id' => $aggregationId,
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Export aggregation data
     *
     * @param int $aggregationId
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportAggregation(int $aggregationId, Request $request) {
        try {
            $user = Auth::user();
            $format = $request->get('format', 'csv');

            $this->logger->info('InvoiceController: Exporting aggregation', [
                'user_id' => $user->id,
                'aggregation_id' => $aggregationId,
                'format' => $format,
            ]);

            $aggregation = InvoiceAggregation::forUser($user->id)->findOrFail($aggregationId);

            $filename = $this->invoiceService->exportAggregation($aggregation, $format);
            $path = storage_path('app/invoices/exports/' . $filename);

            $this->logger->info('InvoiceController: Aggregation exported successfully', [
                'filename' => $filename,
            ]);

            return response()->download($path)->deleteFileAfterSend(false);
        } catch (\Exception $e) {
            return $this->errorManager->handle('AGGREGATION_EXPORT_FAILED', [
                'user_id' => Auth::id(),
                'aggregation_id' => $aggregationId,
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Update invoice settings
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateSettings(Request $request): RedirectResponse {
        try {
            $user = Auth::user();

            $this->logger->info('InvoiceController: Updating invoice settings', [
                'user_id' => $user->id,
            ]);

            $validated = $request->validate([
                'invoicing_mode' => 'required|in:platform_managed,user_managed',
                'external_system_name' => 'nullable|string|max:255',
                'external_system_notes' => 'nullable|string',
                'auto_generate_monthly' => 'boolean',
                'invoice_frequency' => 'required|in:instant,monthly,manual',
                'notify_on_invoice_generated' => 'boolean',
                'notify_buyer_on_invoice' => 'boolean',
            ]);

            // Get or create preferences
            $preferences = $user->invoicePreferences ?? new UserInvoicePreference(['user_id' => $user->id]);
            $preferences->fill($validated);
            $preferences->save();

            $this->logger->info('InvoiceController: Settings updated successfully', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('account.invoices', ['tab' => 'settings'])
                ->with('success', __('invoices.messages.settings_saved'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('INVOICE_SETTINGS_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Download invoice PDF
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
     */
    public function downloadPdf(int $id) {
        try {
            $user = Auth::user();

            $invoice = Invoice::findOrFail($id);

            // Check authorization
            if ($invoice->seller_user_id !== $user->id && $invoice->buyer_user_id !== $user->id) {
                abort(403, __('invoices.errors.unauthorized'));
            }

            if (!$invoice->pdf_path || !file_exists(storage_path('app/' . $invoice->pdf_path))) {
                return redirect()->back()
                    ->withErrors(['error' => __('invoices.errors.pdf_not_found')]);
            }

            $this->logger->info('InvoiceController: Downloading PDF', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
            ]);

            return response()->download(
                storage_path('app/' . $invoice->pdf_path),
                $invoice->invoice_code . '.pdf'
            );
        } catch (\Exception $e) {
            return $this->errorManager->handle('INVOICE_PDF_DOWNLOAD_FAILED', [
                'user_id' => Auth::id(),
                'invoice_id' => $id,
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Get aggregation details (items or buyers) via AJAX
     *
     * @param int $aggregationId
     * @param string $type 'items' or 'buyers'
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAggregationDetails(int $aggregationId, string $type)
    {
        try {
            $user = Auth::user();
            
            $aggregation = \App\Models\InvoiceAggregation::findOrFail($aggregationId);
            
            // Check authorization
            if ($aggregation->user_id !== $user->id) {
                abort(403, __('invoices.errors.unauthorized'));
            }

            $distributionIds = $aggregation->metadata['distribution_ids'] ?? [];
            
            if ($type === 'items') {
                // Load items
                $distributions = \App\Models\PaymentDistribution::whereIn('id', $distributionIds)
                    ->with('egi')
                    ->get();
                
                $items = $distributions->filter(function($dist) {
                    return $dist->egi !== null;
                })->map(function($dist) {
                    return [
                        'egi_id' => $dist->egi_id,
                        'egi_id_padded' => str_pad($dist->egi_id, 7, '0', STR_PAD_LEFT),
                        'title' => $dist->egi->title,
                        'thumbnail_url' => $dist->egi->thumbnail_image_url,
                        'amount' => $dist->amount_eur,
                        'amount_formatted' => number_format($dist->amount_eur, 2, ',', '.'),
                    ];
                })->values();
                
                return response()->json(['items' => $items]);
                
            } elseif ($type === 'buyers') {
                // Load buyers
                $distributions = \App\Models\PaymentDistribution::whereIn('id', $distributionIds)
                    ->with('egi.blockchain.buyer')
                    ->get();
                
                // Group by buyer
                $buyerData = [];
                foreach($distributions as $dist) {
                    if($dist->egi && $dist->egi->blockchain && $dist->egi->blockchain->buyer) {
                        $buyer = $dist->egi->blockchain->buyer;
                        $buyerId = $buyer->id;
                        
                        if(!isset($buyerData[$buyerId])) {
                            $buyerData[$buyerId] = [
                                'user' => $buyer,
                                'count' => 0,
                                'total' => 0
                            ];
                        }
                        
                        $buyerData[$buyerId]['count']++;
                        $buyerData[$buyerId]['total'] += $dist->amount_eur;
                    }
                }
                
                $buyers = collect($buyerData)->map(function($data) {
                    $buyerRoute = match ($data['user']->usertype ?? 'creator') {
                        'creator' => route('creator.home', $data['user']->id),
                        'collector' => route('collector.home', $data['user']->id),
                        'commissioner' => route('profile.show'),
                        default => route('creator.home', $data['user']->id),
                    };
                    
                    return [
                        'name' => $data['user']->name,
                        'avatar_url' => $data['user']->profile_photo_url,
                        'profile_url' => $buyerRoute,
                        'count' => $data['count'],
                        'total' => $data['total'],
                        'total_formatted' => number_format($data['total'], 2, ',', '.'),
                    ];
                })->values();
                
                return response()->json(['buyers' => $buyers]);
            }
            
            return response()->json(['error' => 'Invalid type'], 400);
            
        } catch (\Exception $e) {
            $this->logger->error('InvoiceController: Failed to load aggregation details', [
                'user_id' => Auth::id(),
                'aggregation_id' => $aggregationId,
                'type' => $type,
                'error_message' => $e->getMessage(),
            ]);
            
            return response()->json([
                'error' => __('invoices.errors.export_error')
            ], 500);
        }
    }
}
