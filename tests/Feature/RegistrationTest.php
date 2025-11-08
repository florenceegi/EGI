<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\ConsentVersion;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CollectionService;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\LegalContentService;
use App\Services\Notifications\WalletService;
use App\Services\Wallet\WalletProvisioningService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Ultra\ErrorManager\Exceptions\UltraErrorException;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        // Prepare Florence EGI tenant required by registration workflow
        if (! Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        DB::table('tenants')->insert([
            'name' => 'Florence EGI',
            'slug' => 'florence-egi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure collector role exists (no create_collection permission by default)
        Role::firstOrCreate([
            'name' => 'collector',
            'guard_name' => 'web',
        ]);

        Config::set('app.role_mapping', [
            'collector' => 'collector',
        ]);

        // Mock wallet provisioning to avoid external integrations
        $walletMock = Mockery::mock(WalletProvisioningService::class);
        $mockWallet = Wallet::make();
        $mockWallet->id = 9999;
        $mockWallet->setAttribute('wallet', str_repeat('A', 58));
        $walletMock->shouldReceive('provisionUserWallet')
            ->once()
            ->andReturn($mockWallet);
        $this->app->instance(WalletProvisioningService::class, $walletMock);

        $collectionServiceMock = Mockery::mock(CollectionService::class);
        $walletServiceMock = Mockery::mock(WalletService::class);

        // Mock GDPR consent services
        $consentMock = Mockery::mock(ConsentService::class);
        $consentMock->shouldReceive('getConsentTypes')->andReturn(collect([
            (object) ['key' => 'privacy_policy', 'required' => true],
            (object) ['key' => 'terms-of-service', 'required' => true],
            (object) ['key' => 'age_confirmation', 'required' => true],
            (object) ['key' => 'analytics', 'required' => false],
        ]));
        $consentMock->shouldReceive('createDefaultConsents')->andReturn([
            'privacy_policy' => true,
            'age_confirmation' => true,
        ]);
        $consentMock->shouldReceive('recordTermsConsent')->andReturnTrue();
        $this->app->instance(ConsentService::class, $consentMock);

        $legalMock = Mockery::mock(LegalContentService::class);
        $legalMock->shouldReceive('getCurrentVersionString')->andReturn('1.0.0');
        $this->app->instance(LegalContentService::class, $legalMock);

        $collection = Collection::factory()->create([
            'creator_id' => null,
        ]);

        $collectionServiceMock
            ->shouldReceive('findOrCreateUserCollection')
            ->andReturn($collection);
        $this->app->instance(CollectionService::class, $collectionServiceMock);

        $walletServiceMock
            ->shouldReceive('attachDefaultWalletsToCollection')
            ->andReturnTrue();
        $this->app->instance(WalletService::class, $walletServiceMock);

        $payload = [
            'name' => 'Test User',
            'nick_name' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'collector',
            'terms_accepted' => 'on',
            'privacy_policy_accepted' => 'on',
            'age_confirmation' => 'on',
        ];

        ConsentVersion::create([
            'version' => '1.0.0-test',
            'consent_types' => ['privacy_policy', 'terms_of_service'],
            'changes' => [],
            'effective_date' => now(),
            'is_active' => true,
            'created_by' => null,
            'notes' => 'Test consent version for registration feature test',
        ]);

        $response = $this->post('/register', $payload);

        $response->assertStatus(200);
        $response->assertViewIs('auth.register-wallet-setup');
        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('collector', $user->usertype);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'usertype' => 'collector',
        ]);
    }
}
