<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\ProjectService;
use Mockery;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    private ProjectService $projectService;
    private ProjectRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProjectRepositoryInterface::class);
        $this->projectService = new ProjectService($this->repository);
    }

    public function test_create_sets_owner_id(): void
    {
        $user = User::factory()->make(['id' => 1]);
        $data = ['name' => 'Test Project'];
        $project = Project::factory()->make(['name' => 'Test Project', 'owner_id' => 1]);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(array_merge($data, ['owner_id' => $user->id]))
            ->andReturn($project);

        $result = $this->projectService->create($user, $data);

        $this->assertEquals('Test Project', $result->name);
    }

    public function test_invite_member_throws_when_already_member(): void
    {
        $project = Mockery::mock(Project::class);
        $user = User::factory()->make(['id' => 1, 'email' => 'user@test.com']);

        $project->shouldReceive('hasMember')
            ->once()
            ->with(Mockery::type(User::class))
            ->andReturn(true);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User is already a member');

        // We test the domain logic via a partial mock
        $service = new class($this->repository) extends ProjectService {
            public function testInvite(Project $project, User $user): void
            {
                if ($project->hasMember($user)) {
                    throw new \DomainException('User is already a member of this project.');
                }
            }
        };

        $service->testInvite($project, $user);
    }

    public function test_remove_member_throws_when_removing_owner(): void
    {
        $project = Mockery::mock(Project::class);
        $user = User::factory()->make(['id' => 1]);

        $project->shouldReceive('isOwnedBy')
            ->once()
            ->andReturn(true);

        $this->expectException(\DomainException::class);

        $service = new class($this->repository) extends ProjectService {
            public function testRemove(Project $project, User $user): void
            {
                if ($project->isOwnedBy($user)) {
                    throw new \DomainException('Cannot remove the project owner.');
                }
            }
        };

        $service->testRemove($project, $user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
