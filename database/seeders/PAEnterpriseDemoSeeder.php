<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\{User, Collection, Egi, Coa};
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

/**
 * PA/Enterprise Demo Data Seeder
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA/Enterprise System MVP)
 * @date 2025-10-02
 * @purpose Seed demo data for PA Entity (Comune di Firenze) with heritage items
 *
 * Data Created:
 * - 1 PA Entity User (Comune di Firenze)
 * - 1 Collection (Patrimonio Monumentale Comunale)
 * - 8 EGI (Heritage items: statue, monumenti, affreschi)
 * - 6 CoA (Certificates issued for some EGI)
 * - 1 Inspector user (assigned via collection_user pivot)
 *
 * Notes:
 * - Uses existing 'artwork' type + metadata JSON for MVP
 * - No factory dependencies (direct model creation)
 * - Safe for production (uses find()->delete before recreate)
 */
class PAEnterpriseDemoSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $this->command->info('🏛️ Seeding PA/Enterprise demo data...');

        // 1. Create PA Entity User (Comune di Firenze)
        $this->command->info('Creating PA Entity user...');
        $paUser = $this->createPAUser();

        // 2. Create Inspector User
        $this->command->info('Creating Inspector user...');
        $inspector = $this->createInspectorUser();

        // 3. Create Collection
        $this->command->info('Creating heritage collection...');
        $collection = $this->createCollection($paUser);

        // 4. Assign Inspector to Collection
        $this->command->info('Assigning inspector to collection...');
        $this->assignInspector($collection, $inspector, $paUser);

        // 5. Create Heritage EGI
        $this->command->info('Creating heritage EGI items...');
        $egis = $this->createHeritageEGIs($collection, $paUser);

        // 6. Create CoA for some EGI
        $this->command->info('Creating CoA certificates...');
        $this->createCoA($egis, $paUser, $inspector);

        $this->command->info('✅ PA/Enterprise demo data seeded successfully!');
        $this->command->info("   PA User: {$paUser->email} / password");
        $this->command->info("   Inspector: {$inspector->email} / password");
    }

    /**
     * Create PA Entity user (Comune di Firenze)
     */
    protected function createPAUser(): User {
        // Find or create
        $user = User::where('email', 'pa.firenze@comune.fi.it')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Comune di Firenze',
                'email' => 'pa.firenze@comune.fi.it',
                'password' => Hash::make('password'),
                'usertype' => 'pa_entity',
                'email_verified_at' => now(),
            ]);

            // Assign pa_entity role
            $paRole = Role::findByName('pa_entity');
            $user->assignRole($paRole);
        }

        return $user;
    }

    /**
     * Create Inspector user
     */
    protected function createInspectorUser(): User {
        // Find or create
        $user = User::where('email', 'inspector.demo@florenceegi.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Dr. Marco Bianchi',
                'email' => 'inspector.demo@florenceegi.com',
                'password' => Hash::make('password'),
                'usertype' => 'inspector',
                'email_verified_at' => now(),
            ]);

            // Assign inspector role
            $inspectorRole = Role::findByName('inspector');
            $user->assignRole($inspectorRole);
        }

        return $user;
    }

    /**
     * Create heritage collection
     */
    protected function createCollection(User $paUser): Collection {
        // Find or create
        $collection = Collection::where('collection_name', 'Patrimonio Monumentale Comunale')->first();

        if ($collection) {
            return $collection;
        }

        return Collection::create([
            'creator_id' => $paUser->id,
            'owner_id' => $paUser->id,
            'collection_name' => 'Patrimonio Monumentale Comunale',
            'description' => 'Collezione del patrimonio culturale monumentale del Comune di Firenze. Include statue, monumenti, affreschi e opere d\'arte di proprietà comunale gestite dall\'Ufficio Cultura e Patrimonio.',
            'type' => 'artwork', // MVP: usa tipo esistente
            'is_default' => false,
            'status' => 'published',
            'is_published' => true,
            'featured_in_guest' => false,
            'metadata' => [
                'pa_entity_code' => 'C_H501',
                'institution_name' => 'Comune di Firenze',
                'department' => 'Ufficio Cultura e Patrimonio',
                'contact_email' => 'cultura@comune.fi.it',
                'contact_phone' => '+39 055 2768224',
                'heritage_type' => 'monumentale',
                'classification' => 'bene_culturale_mobile_immobile',
                'unesco_status' => false,
                'public_access' => true,
                'notes' => 'Patrimonio comunale certificato con CoA blockchain per tracciabilità e autenticità',
            ],
        ]);
    }

    /**
     * Assign inspector to collection via pivot
     */
    protected function assignInspector(Collection $collection, User $inspector, User $assignedBy): void {
        // Clean existing assignment
        DB::table('collection_user')
            ->where('collection_id', $collection->id)
            ->where('user_id', $inspector->id)
            ->delete();

        // Attach inspector
        $collection->users()->attach($inspector->id, [
            'role' => 'inspector',
            'is_owner' => false,
            'status' => 'active',
            'metadata' => json_encode([
                'assigned_by' => $assignedBy->id,
                'assignment_date' => now()->toDateTimeString(),
                'specialization' => 'scultura_monumentale_rinascimentale',
                'compensation' => 500.00,
                'notes' => 'Specializzato in opere rinascimentali fiorentine',
            ]),
            'joined_at' => now(),
        ]);
    }

    /**
     * Create heritage EGI items
     */
    protected function createHeritageEGIs(Collection $collection, User $owner): array {
        $heritageItems = [
            [
                'title' => 'Statua David - Replica Piazza Signoria',
                'description' => 'Replica in marmo del celebre David di Michelangelo (Michelangelo Buonarroti), collocata in Piazza della Signoria dal 1910. Opera simbolo di Firenze e del Rinascimento. Tecnica: Scultura in marmo di Carrara. Ubicazione: Piazza della Signoria, Firenze.',
                'creation_date' => '1910-01-01',
            ],
            [
                'title' => 'Affresco Sala dei Gigli - Palazzo Vecchio',
                'description' => 'Ciclo di affreschi nella Sala dei Gigli di Palazzo Vecchio raffigurante eroi romani (Domenico Ghirlandaio, 1482). Capolavoro del Rinascimento fiorentino. Tecnica: Affresco su muro. Ubicazione: Palazzo Vecchio, Sala dei Gigli, Firenze.',
                'creation_date' => '1482-01-01',
            ],
            [
                'title' => 'Fontana del Nettuno',
                'description' => 'Monumentale fontana in marmo e bronzo situata in Piazza della Signoria (Bartolomeo Ammannati, 1575). Conosciuta dai fiorentini come "Il Biancone". Tecnica: Scultura marmo e bronzo. Ubicazione: Piazza della Signoria, Firenze.',
                'creation_date' => '1575-01-01',
            ],
            [
                'title' => 'Loggia dei Lanzi - Perseo',
                'description' => 'Scultura bronzea raffigurante Perseo con la testa di Medusa (Benvenuto Cellini, 1554), capolavoro del Manierismo fiorentino. Tecnica: Fusione in bronzo. Ubicazione: Loggia dei Lanzi, Firenze.',
                'creation_date' => '1554-01-01',
            ],
            [
                'title' => 'Palazzo Vecchio - Torre di Arnolfo',
                'description' => 'Torre campanaria del Palazzo Vecchio (Arnolfo di Cambio, 1310), simbolo architettonico di Firenze. Altezza 94 metri. Tecnica: Architettura pietra forte. Ubicazione: Piazza della Signoria, Firenze.',
                'creation_date' => '1310-01-01',
            ],
            [
                'title' => 'Cappella Brancacci - Ciclo Affreschi',
                'description' => 'Ciclo di affreschi nella Cappella Brancacci, Chiesa del Carmine (Masaccio e Masolino, 1427). Capolavoro del primo Rinascimento. Tecnica: Affresco. Ubicazione: Chiesa del Carmine, Cappella Brancacci, Firenze.',
                'creation_date' => '1427-01-01',
            ],
            [
                'title' => 'Monumento a Dante - Santa Croce',
                'description' => 'Statua in marmo di Dante Alighieri nel sagrato di Santa Croce (Enrico Pazzi, 1865). Inaugurata nel 1865 per il VI centenario della nascita del poeta. Tecnica: Scultura marmo bianco. Ubicazione: Sagrato Santa Croce, Firenze.',
                'creation_date' => '1865-01-01',
            ],
            [
                'title' => 'Porta del Paradiso - Battistero',
                'description' => 'Porta bronzea orientale del Battistero di San Giovanni (Lorenzo Ghiberti, originale 1452, copia 1990). Definita da Michelangelo "Porta del Paradiso". Questa è una copia, originale al Museo dell\'Opera. Tecnica: Bassorilievo bronzo dorato. Ubicazione: Battistero di San Giovanni, Firenze.',
                'creation_date' => '1990-01-01', // Copia moderna
            ],
        ];

        $egis = [];
        foreach ($heritageItems as $index => $item) {
            // Find or create
            $egi = Egi::where('title', $item['title'])
                ->where('collection_id', $collection->id)
                ->first();

            if ($egi) {
                $egis[] = $egi;
                continue;
            }

            $egi = Egi::create([
                'collection_id' => $collection->id,
                'title' => $item['title'],
                'description' => $item['description'],
                'creation_date' => $item['creation_date'],
                'status' => 'published',
                'is_published' => true,
                'user_id' => $owner->id, // Creator
                'owner_id' => $owner->id, // Owner
                'position' => $index + 1, // Position in collection
            ]);

            $egis[] = $egi;
        }

        return $egis;
    }

    /**
     * Create CoA for some EGI
     */
    protected function createCoA(array $egis, User $issuer, User $inspector): void {
        // Issue CoA for first 6 EGI
        $egiWithCoA = array_slice($egis, 0, 6);

        foreach ($egiWithCoA as $index => $egi) {
            // Find or create
            $serial = 'COA-FI-' . now()->format('Y') . '-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT);

            $existingCoa = Coa::where('egi_id', $egi->id)->orWhere('serial', $serial)->first();

            if ($existingCoa) {
                continue;
            }

            Coa::create([
                'egi_id' => $egi->id,
                'serial' => $serial,
                'status' => $index < 4 ? 'valid' : 'valid', // All valid for demo
                'issuer_type' => 'platform', // author|archive|platform
                'issuer_name' => 'Comune di Firenze',
                'issuer_location' => 'Firenze, Italia',
                'issued_at' => now()->subDays(10 + $index * 5),
                'verification_hash' => hash('sha256', $serial . $egi->id . now()->timestamp),
                'metadata' => [
                    'issuer_user_id' => $issuer->id,
                    'inspector_id' => $inspector->id,
                    'inspector_name' => 'Dr. Marco Bianchi',
                    'heritage_classification' => 'bene_culturale',
                    'unesco_protected' => false,
                    'public_domain' => true,
                    'blockchain_hash' => hash('sha256', $serial . microtime()),
                ],
            ]);
        }
    }
}