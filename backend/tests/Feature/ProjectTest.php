<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_own_projects(): void
    {
        Project::factory(3)->create(['owner_id' => $this->user->id]);
        Project::factory(2)->create(); // Other users' projects

        $this->actingAs($this->user)
            ->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_project(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/projects', [
                'name'        => 'My Project',
                'description' => 'A great project',
                'color'       => '#6366f1',
            ])
            ->assertStatus(201)
            ->assertJsonPath('name', 'My Project');

        $this->assertDatabaseHas('projects', [
            'name'     => 'My Project',
            'owner_id' => $this->user->id,
        ]);
    }

    public function test_project_creation_adds_owner_as_member(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/projects', ['name' => 'Test Project'])
            ->assertStatus(201);

        $this->assertDatabaseHas('project_members', [
            'project_id' => $response->json('id'),
            'user_id'    => $this->user->id,
            'role'       => 'owner',
        ]);
    }

    public function test_user_can_view_project_they_are_member_of(): void
    {
        $project = Project::factory()->create();
        $project->members()->attach($this->user->id, ['role' => 'member']);

        $this->actingAs($this->user)
            ->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('id', $project->id);
    }

    public function test_user_cannot_view_project_they_are_not_member_of(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/projects/{$project->id}")
            ->assertForbidden();
    }

    public function test_owner_can_update_project(): void
    {
        $project = Project::factory()->create(['owner_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->putJson("/api/projects/{$project->id}", ['name' => 'Updated Name'])
            ->assertOk()
            ->assertJsonPath('name', 'Updated Name');
    }

    public function test_non_member_cannot_update_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user)
            ->putJson("/api/projects/{$project->id}", ['name' => 'Hacked Name'])
            ->assertForbidden();
    }

    public function test_owner_can_delete_project(): void
    {
        $project = Project::factory()->create(['owner_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/projects/{$project->id}")
            ->assertOk();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_member_cannot_delete_project(): void
    {
        $project = Project::factory()->create();
        $project->members()->attach($this->user->id, ['role' => 'member']);

        $this->actingAs($this->user)
            ->deleteJson("/api/projects/{$project->id}")
            ->assertForbidden();
    }

    public function test_owner_can_archive_project(): void
    {
        $project = Project::factory()->create(['owner_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->patchJson("/api/projects/{$project->id}/archive")
            ->assertOk()
            ->assertJsonPath('is_archived', true);
    }

    public function test_project_requires_name(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/projects', ['description' => 'No name project'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
