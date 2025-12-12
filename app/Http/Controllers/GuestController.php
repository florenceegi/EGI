<?php

namespace App\Http\Controllers;

use App\Services\CollectorCarouselService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Guest Homepage with Top Collectors Carousel
 * 🎯 Purpose: Display homepage for non-authenticated users featuring top collectors
 * 🛡️ Strategy: Marketing-driven homepage to encourage user registration and engagement
 * 🧱 Core Logic: Show top spending collectors to create social proof and FOMO
 *
 * Business Logic:
 * - Showcase top 10 collectors by spending to create incentive
 * - Display platform statistics and social proof
 * - Encourage registration through aspirational marketing
 * - Create competition among collectors for visibility
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (Guest Homepage with Collector Carousel)
 * @date 2025-08-10
 */
class GuestController extends Controller {
    protected CollectorCarouselService $collectorCarouselService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        CollectorCarouselService $collectorCarouselService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->collectorCarouselService = $collectorCarouselService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Display the guest homepage
     */
    public function index(): View {
        // Get top collectors for carousel
        $topCollectors = $this->collectorCarouselService->getTopCollectors(10);

        // Get platform statistics for social proof
        $platformStats = $this->getPlatformStats();

        return view('guest.homepage', [
            'topCollectors' => $topCollectors,
            'platformStats' => $platformStats,
        ]);
    }

    /**
     * Get platform statistics for social proof
     */
    private function getPlatformStats(): array {
        // You can implement these queries based on your needs
        return [
            'total_creators' => \App\Models\User::whereHas('createdCollections')->count(),
            'total_collectors' => \App\Models\User::whereHas('reservations', function ($query) {
                $query->where('is_current', true)->where('status', 'active');
            })->count(),
            'total_egis' => \App\Models\Egi::count(),
            'total_collections' => \App\Models\Collection::count(),
        ];
    }
}
