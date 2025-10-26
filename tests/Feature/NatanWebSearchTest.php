<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NatanChatMessage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * N.A.T.A.N. Web Search Feature Tests
 *
 * Tests complete workflow: UI → Controller → Service → Provider → Response
 *
 * @package Tests\Feature
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Tests)
 * @date 2025-10-26
 */
class NatanWebSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_accepts_web_search_parameter_in_api()
    {
        $user = User::factory()->create();
        $user->assignRole('pa_entity');

        $response = $this->actingAs($user)->postJson(route('pa.natan.chat.message'), [
            'message' => 'Test query',
            'use_web_search' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'response',
            'sources',
            'web_sources',
            'web_search_metadata',
        ]);
    }

    /** @test */
    public function it_saves_web_search_metadata_to_database()
    {
        $user = User::factory()->create();
        $user->assignRole('pa_entity');

        $this->actingAs($user)->postJson(route('pa.natan.chat.message'), [
            'message' => 'Best practices gestione rifiuti',
            'use_web_search' => true,
        ]);

        $this->assertDatabaseHas('natan_chat_messages', [
            'user_id' => $user->id,
            'web_search_enabled' => true,
        ]);

        $message = NatanChatMessage::where('user_id', $user->id)
            ->where('web_search_enabled', true)
            ->first();

        $this->assertNotNull($message);
        $this->assertNotNull($message->web_search_provider);
    }

    /** @test */
    public function it_does_not_enable_web_search_by_default()
    {
        $user = User::factory()->create();
        $user->assignRole('pa_entity');

        $this->actingAs($user)->postJson(route('pa.natan.chat.message'), [
            'message' => 'Riassumi ultimo atto',
            // NO use_web_search parameter
        ]);

        $message = NatanChatMessage::where('user_id', $user->id)
            ->where('role', 'assistant')
            ->first();

        // Should be false by default
        $this->assertFalse($message->web_search_enabled);
    }
}

