<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EppProject;
use App\Models\User;

class FixEppProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Trova l'utente EPP con ID 27
        $user = User::find(27);

        if (!$user) {
            $this->command->error("User with ID 27 not found!");
            return;
        }

        // Verifica che sia un utente EPP (o platform_role EPP)
        if ($user->usertype !== 'epp' && $user->platform_role !== 'EPP') {
            $this->command->warn("User 27 is not marked as EPP (usertype: {$user->usertype}, role: {$user->platform_role}). Creating project anyway as requested.");
        }

        // Crea un progetto EPP di esempio
        EppProject::create([
            'epp_user_id' => $user->id,
            'name' => 'Riforestazione Appennino',
            'description' => 'Un progetto per ripiantare 10.000 alberi nell\'Appennino Tosco-Emiliano, supportando la biodiversità locale e combattendo l\'erosione del suolo.',
            'project_type' => 'ARF', // Reforestation
            'status' => 'in_progress',
            'target_value' => 10000,
            'current_value' => 150,
            'target_funds' => 50000.00,
            'current_funds' => 750.00,
            'target_date' => now()->addYear(),
        ]);

        $this->command->info("Created sample EPP Project 'Riforestazione Appennino' for User 27.");
    }
}
