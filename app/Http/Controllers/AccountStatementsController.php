<?php

namespace App\Http\Controllers;

use App\Services\EgiliService;
use App\Services\EurTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Account Statements System)
 * @date 2025-11-21
 * @purpose Manages account statements generation and export (EGILI, invoices, etc.)
 */
class AccountStatementsController extends Controller
{
    protected EgiliService $egiliService;
    protected EurTransactionService $eurService;

    public function __construct(
        EgiliService $egiliService,
        EurTransactionService $eurService
    ) {
        $this->middleware('auth');
        $this->egiliService = $egiliService;
        $this->eurService = $eurService;
    }

    /**
     * Display account statements page with tabs
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Parse date filters
        $filter = $request->get('filter', 'month'); // today|week|month|year|custom
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Calculate date range based on filter
        [$startDate, $endDate] = $this->calculateDateRange($filter, $dateFrom, $dateTo);
        
        // Get EGILI transactions
        $egiliTransactions = $this->egiliService->getTransactionHistory($user)
            ->filter(function ($transaction) use ($startDate, $endDate) {
                return $transaction->created_at->between($startDate, $endDate);
            })
            ->values();
        
        // Calculate EGILI summary
        $egiliSummary = $this->calculateEgiliSummary($egiliTransactions, $startDate, $endDate);
        
        // Get EUR transactions
        $eurTransactions = $this->eurService->getTransactionHistory($user, $startDate, $endDate);
        
        // Calculate EUR summary
        $eurSummary = $this->eurService->calculateSummary($eurTransactions);
        
        return view('account.statements.index', [
            'user' => $user,
            'filter' => $filter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'egiliTransactions' => $egiliTransactions,
            'egiliSummary' => $egiliSummary,
            'eurTransactions' => $eurTransactions,
            'eurSummary' => $eurSummary,
        ]);
    }

    /**
     * Download EGILI statement as PDF
     * 
     * @param Request $request
     * @return Response
     */
    public function downloadEgiliPdf(Request $request): Response
    {
        $user = Auth::user();
        
        // Parse date filters
        $filter = $request->get('filter', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Calculate date range
        [$startDate, $endDate] = $this->calculateDateRange($filter, $dateFrom, $dateTo);
        
        // Get EGILI transactions
        $egiliTransactions = $this->egiliService->getTransactionHistory($user)
            ->filter(function ($transaction) use ($startDate, $endDate) {
                return $transaction->created_at->between($startDate, $endDate);
            })
            ->values();
        
        // Calculate summary
        $egiliSummary = $this->calculateEgiliSummary($egiliTransactions, $startDate, $endDate);
        
        // Generate PDF
        $pdf = Pdf::loadView('account.statements.pdf.egili', [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'egiliTransactions' => $egiliTransactions,
            'egiliSummary' => $egiliSummary,
            'generatedAt' => now(),
        ]);
        
        // Filename format: estratto-conto-egili-2025-11.pdf
        $filename = sprintf(
            'estratto-conto-egili-%s.pdf',
            $startDate->format('Y-m')
        );
        
        return $pdf->download($filename);
    }

    /**
     * Download EUR statement as PDF
     * 
     * @param Request $request
     * @return Response
     */
    public function downloadEurPdf(Request $request): Response
    {
        $user = Auth::user();
        
        // Parse date filters
        $filter = $request->get('filter', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Calculate date range
        [$startDate, $endDate] = $this->calculateDateRange($filter, $dateFrom, $dateTo);
        
        // Get EUR transactions
        $eurTransactions = $this->eurService->getTransactionHistory($user, $startDate, $endDate);
        
        // Calculate summary
        $eurSummary = $this->eurService->calculateSummary($eurTransactions);
        
        // Generate PDF
        $pdf = Pdf::loadView('account.statements.pdf.eur', [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'eurTransactions' => $eurTransactions,
            'eurSummary' => $eurSummary,
            'generatedAt' => now(),
        ]);
        
        // Filename format: estratto-conto-eur-2025-11.pdf
        $filename = sprintf(
            'estratto-conto-eur-%s.pdf',
            $startDate->format('Y-m')
        );
        
        return $pdf->download($filename);
    }

    /**
     * Calculate date range based on filter
     * 
     * @param string $filter
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array [Carbon $startDate, Carbon $endDate]
     */
    private function calculateDateRange(string $filter, ?string $dateFrom, ?string $dateTo): array
    {
        switch ($filter) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            
            case 'custom':
                if ($dateFrom && $dateTo) {
                    $startDate = Carbon::parse($dateFrom)->startOfDay();
                    $endDate = Carbon::parse($dateTo)->endOfDay();
                } else {
                    // Fallback to current month if custom dates invalid
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                }
                break;
            
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }
        
        return [$startDate, $endDate];
    }

    /**
     * Calculate EGILI summary statistics
     * 
     * @param \Illuminate\Support\Collection $transactions
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateEgiliSummary($transactions, Carbon $startDate, Carbon $endDate): array
    {
        $user = Auth::user();
        
        // Calculate starting balance (balance before period start)
        $allTransactions = $this->egiliService->getTransactionHistory($user);
        $transactionsBeforePeriod = $allTransactions->filter(function ($t) use ($startDate) {
            return $t->created_at->lt($startDate);
        });
        
        $startingBalance = $transactionsBeforePeriod->last()?->balance_after ?? 0;
        
        // Calculate period totals
        $totalIncome = $transactions->where('operation', 'add')->sum('amount');
        $totalExpenses = $transactions->where('operation', 'subtract')->sum('amount');
        
        // Ending balance
        $endingBalance = $this->egiliService->getBalance($user);
        
        return [
            'starting_balance' => $startingBalance,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'ending_balance' => $endingBalance,
            'transaction_count' => $transactions->count(),
        ];
    }
}

