<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionUser;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class DashboardStaticController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager)
    {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    public function index()
    {
        $this->logger->info('DashboardStaticController: index');

        $collectionsCount = Collection::where('creator_id', Auth::id())->count();
        $collectionMembersCount = CollectionUser::whereHas('collection', function ($query) {
            $query->where('creator_id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->count();

        $pendingNotifications = Auth::user()
            ->customNotifications()
            ->where(function ($query) {
                $query->where('outcome', 'LIKE', '%pending%')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                                ->whereNull('read_at');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->with('model')
            ->get();

        $activeNotificationId = $pendingNotifications->isNotEmpty() ? $pendingNotifications->first()->id : null;

        return view('dashboard-static', [
            'collectionsCount' => $collectionsCount,
            'collectionMembersCount' => $collectionMembersCount,
            'pendingNotifications' => $pendingNotifications,
            'activeNotificationId' => $activeNotificationId,
        ]);
    }
}
