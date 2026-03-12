<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService)
    {
        $this->authorizeResource(Project::class, 'project');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projectService->listForUser(
            $request->user(),
            $request->only(['search', 'archived', 'per_page'])
        );

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create($request->user(), $request->validated());

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource(
            $project->load(['owner', 'members', 'tasks.assignee'])
        );
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project = $this->projectService->update($project, $request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->delete($project);

        return response()->json(['message' => 'Project deleted successfully.']);
    }

    public function archive(Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        return new ProjectResource($this->projectService->archive($project));
    }

    public function restore(Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        return new ProjectResource($this->projectService->restore($project));
    }
}
