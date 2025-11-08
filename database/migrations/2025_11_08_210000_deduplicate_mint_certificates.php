<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Deduplicate mint certificates generated twice for the same blockchain record.
     */
    public function up(): void {
        $duplicates = DB::table('egi_reservation_certificates')
            ->select('egi_blockchain_id', DB::raw('COUNT(*) as total'))
            ->where('certificate_type', 'mint')
            ->whereNotNull('egi_blockchain_id')
            ->groupBy('egi_blockchain_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $duplicates->each(function ($duplicate): void {
            $certificateIds = DB::table('egi_reservation_certificates')
                ->where('certificate_type', 'mint')
                ->where('egi_blockchain_id', $duplicate->egi_blockchain_id)
                ->orderByDesc('created_at') // keep the most recent certificate
                ->pluck('id');

            /** @var Collection<int> $certificateIds */
            $idsToDelete = $certificateIds->slice(1); // skip the first (latest) certificate

            if ($idsToDelete->isEmpty()) {
                return;
            }

            Log::warning('Removing duplicate mint certificates', [
                'egi_blockchain_id' => $duplicate->egi_blockchain_id,
                'removed_certificate_ids' => $idsToDelete->values()->all(),
            ]);

            DB::table('egi_reservation_certificates')
                ->whereIn('id', $idsToDelete->all())
                ->delete();
        });
    }

    /**
     * No rollback: duplicates cannot be safely restored once removed.
     */
    public function down(): void {
        // Intentionally left blank.
    }
};

