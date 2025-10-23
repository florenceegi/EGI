<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PadminAiPreviewTest extends TestCase
{
    use WithFaker;

    public function test_preview_ai_fix_returns_structure()
    {
        // Ensure an application encryption key is present for the test runtime
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);
        }

        // Arrange: prepare a fake violation in session
        $violation = [
            'id' => 'test-1',
            'file' => 'app/Example.php',
            'line' => 10,
            'rule' => 'REGOLA_ZERO',
        ];

    $this->withSession(['padmin_violations' => [$violation]]);
    // Disable middleware to avoid DB calls (roles, auth) in CI/test environment
    $this->withoutMiddleware();

    // Authenticate a user (bypass superadmin middleware by using an existing or freshly created user)
    // Create a lightweight User instance without touching DB
    $userClass = collect(app()->make('config')->get('auth.providers.users.model'))->first() ?? \App\Models\User::class;
    $user = new $userClass();
    $user->id = 1;
    $this->actingAs($user);

    // Mock AiFixService to avoid calling Anthropic
        $mock = Mockery::mock('App\Services\Padmin\AiFixService');
        $mock->shouldReceive('generateFix')->once()->with($violation)->andReturn([
            'success' => true,
            'original_code' => '<?php // old ?>',
            'fixed_code' => '<?php // fixed ?>',
            'explanation' => 'Fixed by AI'
        ]);

        $this->app->instance('App\Services\Padmin\AiFixService', $mock);

        // Act
    $response = $this->postJson(route('superadmin.padmin.violations.ai-preview', ['id' => 'test-1']));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'original_code', 'fixed_code', 'explanation', 'file', 'line', 'rule']);
    }
}
