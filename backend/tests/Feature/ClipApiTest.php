<?php

namespace Tests\Feature;

use App\Jobs\ProcessClipJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ClipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_clips_endpoints(): void
    {
        $this->getJson('/api/clips')->assertUnauthorized();
        $this->postJson('/api/clips', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_clips_with_status_filter(): void
    {
        DB::table('clips')->insert([
            [
                'title' => 'Active clip',
                'description' => 'Active description',
                'url' => 'https://example.com/active',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Inactive clip',
                'description' => 'Inactive description',
                'url' => 'https://example.com/inactive',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->withHeaders($this->authHeaders())->getJson('/api/clips?status=active');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_authenticated_user_can_create_clip(): void
    {
        $payload = [
            'title' => 'My first clip',
            'description' => 'Short description',
            'url' => 'https://example.com/video/123',
            'status' => 'active',
        ];

        $response = $this->withHeaders($this->authHeaders())->postJson('/api/clips', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'My first clip')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('clips', [
            'title' => 'My first clip',
            'status' => 'active',
        ]);
    }

    public function test_clip_processing_job_is_dispatched_on_creation(): void
    {
        Queue::fake();

        $payload = [
            'title' => 'Queue test clip',
            'description' => 'Dispatch processing',
            'url' => 'https://example.com/video/queue',
            'status' => 'active',
        ];

        $response = $this->withHeaders($this->authHeaders())->postJson('/api/clips', $payload);

        $response->assertCreated();

        Queue::assertPushed(ProcessClipJob::class);
    }

    public function test_authenticated_user_can_update_clip(): void
    {
        $clipId = DB::table('clips')->insertGetId([
            'title' => 'Original title',
            'description' => 'Original description',
            'url' => 'https://example.com/original',
            'status' => 'inactive',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())->putJson("/api/clips/{$clipId}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'url' => 'https://example.com/updated',
            'status' => 'active',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated title')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('clips', [
            'id' => $clipId,
            'title' => 'Updated title',
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_delete_clip(): void
    {
        $clipId = DB::table('clips')->insertGetId([
            'title' => 'To delete',
            'description' => 'Delete me',
            'url' => 'https://example.com/delete',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())->deleteJson("/api/clips/{$clipId}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('clips', [
            'id' => $clipId,
        ]);
    }

    private function authHeaders(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        return [
            'Authorization' => "Bearer {$token}",
        ];
    }
}
