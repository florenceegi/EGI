<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE coa_events DROP CONSTRAINT IF EXISTS coa_events_type_check");
        
        DB::statement("ALTER TABLE coa_events ADD CONSTRAINT coa_events_type_check CHECK (type IN (
            'ISSUED', 
            'REVOKED', 
            'ANNEX_ADDED', 
            'ADDENDUM_ISSUED', 
            'AUTHOR_SIGNED', 
            'INSPECTOR_SIGNED', 
            'PDF_REGENERATED', 
            'PDF_DOWNLOADED', 
            'SIGNATURE_VALIDATED', 
            'SIGNATURE_REMOVED'
        ))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original (excluding PDF types) - Best effort restoration
        DB::statement("ALTER TABLE coa_events DROP CONSTRAINT IF EXISTS coa_events_type_check");
        
        DB::statement("ALTER TABLE coa_events ADD CONSTRAINT coa_events_type_check CHECK (type IN (
            'ISSUED', 
            'REVOKED', 
            'ANNEX_ADDED', 
            'ADDENDUM_ISSUED', 
            'AUTHOR_SIGNED', 
            'INSPECTOR_SIGNED', 
            'SIGNATURE_VALIDATED', 
            'SIGNATURE_REMOVED'
        ))");
    }
};
