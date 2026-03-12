<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $this->authorize('view', $project);

        $tasks = $this->taskService->listForProject(
            $project,
            $request->only(['status', 'priority', 'assignee_id', 'search'])
        );

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $task = $this->taskService->create($project, $request->user(), $request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task->project);

        return new TaskResource($task->load(['assignee', 'reporter', 'comments.user', 'attachments']));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task->project);

        $task = $this->taskService->update($task, $request->validated());

        return new TaskResource($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('update', $task->project);

        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function updateStatus(Request $request, Task $task): TaskResource
    {
        $this->authorize('update', $task->project);

        $request->validate([
            'status' => 'required|in:todo,in_progress,in_review,done',
        ]);

        $task = $this->taskService->updateStatus($task, $request->status);

        return new TaskResource($task);
    }

    public function updateAssignee(Request $request, Task $task): TaskResource
    {
        $this->authorize('update', $task->project);

        $request->validate([
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $task = $this->taskService->update($task, ['assignee_id' => $request->assignee_id]);

        return new TaskResource($task);
    }

    public function uploadAttachment(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task->project);

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $url = $this->taskService->uploadAttachment($task, $request->file('file'));

        return response()->json(['url' => $url], 201);
    }
}
