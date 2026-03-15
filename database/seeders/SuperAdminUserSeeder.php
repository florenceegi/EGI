<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'fabio@florenceegi.com';

        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            $this->command->error("Utente con email {$adminEmail} non trovato!");
            return;
        }

        $adminRole = Role::where('name', 'superadmin')->first();

        if (!$adminRole) {
            $this->command->error('Ruolo admin non trovato! Esegui prima il seeder dei ruoli.');
            return;
        }

        $user->assignRole($adminRole);
        $this->command->info("Ruolo admin assegnato con successo a {$adminEmail}!");
    }
}
