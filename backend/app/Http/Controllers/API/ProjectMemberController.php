<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectMemberController extends Controller
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }

    public function index(Project $project): AnonymousResourceCollection
    {
        $this->authorize('view', $project);

        return UserResource::collection($project->members);
    }

    public function invite(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role'  => 'required|in:admin,member',
        ]);

        $user = $this->projectService->inviteMember(
            $project,
            $request->email,
            $request->role
        );

        return response()->json([
            'message' => "Invitation sent to {$user->name}.",
            'user'    => new UserResource($user),
        ], 201);
    }

    public function updateRole(Request $request, Project $project, User $member): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate(['role' => 'required|in:admin,member']);

        $this->projectService->updateMemberRole($project, $member, $request->role);

        return response()->json(['message' => 'Member role updated.']);
    }

    public function remove(Project $project, User $member): JsonResponse
    {
        $this->authorize('update', $project);

        $this->projectService->removeMember($project, $member);

        return response()->json(['message' => 'Member removed from project.']);
    }
}
