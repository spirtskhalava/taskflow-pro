<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['owner_id' => $this->user->id]);
        $this->project->members()->attach($this->user->id, ['role' => 'owner']);
    }

    public function test_member_can_list_tasks(): void
    {
        Task::factory(5)->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user)
            ->getJson("/api/projects/{$this->project->id}/tasks")
            ->assertOk()
            ->assertJsonCount(5);
    }

    public function test_tasks_can_be_filtered_by_status(): void
    {
        Task::factory(3)->create(['project_id' => $this->project->id, 'status' => 'todo']);
        Task::factory(2)->create(['project_id' => $this->project->id, 'status' => 'done']);

        $this->actingAs($this->user)
            ->getJson("/api/projects/{$this->project->id}/tasks?status=todo")
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_member_can_create_task(): void
    {
        $this->actingAs($this->user)
            ->postJson("/api/projects/{$this->project->id}/tasks", [
                'title'    => 'New Task',
                'priority' => 'high',
                'status'   => 'todo',
            ])
            ->assertStatus(201)
            ->assertJsonPath('title', 'New Task')
            ->assertJsonPath('priority', 'high');
    }

    public function test_task_status_can_be_updated(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status'     => 'todo',
        ]);

        $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'in_progress'])
            ->assertOk()
            ->assertJsonPath('status', 'in_progress');
    }

    public function test_completing_task_sets_completed_at(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status'     => 'in_review',
        ]);

        $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'done'])
            ->assertOk();

        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_non_member_cannot_create_task(): void
    {
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->postJson("/api/projects/{$this->project->id}/tasks", [
                'title' => 'Unauthorized Task',
            ])
            ->assertForbidden();
    }

    public function test_task_requires_title(): void
    {
        $this->actingAs($this->user)
            ->postJson("/api/projects/{$this->project->id}/tasks", [
                'priority' => 'high',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_task_invalid_status_is_rejected(): void
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'invalid_status'])
            ->assertStatus(422);
    }

    public function test_reporter_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'reporter_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertOk();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
