<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $cacheKey = "dashboard:{$user->id}";

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            $projectIds = Project::query()
                ->forUser($user)
                ->active()
                ->pluck('id');

            $totalTasks   = Task::whereIn('project_id', $projectIds)->count();
            $myTasks      = Task::whereIn('project_id', $projectIds)->assignedTo($user->id)->count();
            $overdueTasks = Task::whereIn('project_id', $projectIds)->assignedTo($user->id)->overdue()->count();
            $doneTasks    = Task::whereIn('project_id', $projectIds)->assignedTo($user->id)->byStatus('done')->count();

            $recentProjects = Project::query()
                ->with(['owner', 'members'])
                ->withCount('tasks')
                ->forUser($user)
                ->active()
                ->latest()
                ->limit(5)
                ->get();

            $myRecentTasks = Task::query()
                ->with(['project', 'assignee'])
                ->assignedTo($user->id)
                ->whereIn('project_id', $projectIds)
                ->where('status', '!=', 'done')
                ->latest()
                ->limit(8)
                ->get();

            return compact(
                'totalTasks',
                'myTasks',
                'overdueTasks',
                'doneTasks',
                'recentProjects',
                'myRecentTasks'
            );
        });

        return response()->json([
            'stats' => [
                'total_tasks'  => $data['totalTasks'],
                'my_tasks'     => $data['myTasks'],
                'overdue'      => $data['overdueTasks'],
                'completed'    => $data['doneTasks'],
            ],
            'recent_projects' => ProjectResource::collection($data['recentProjects']),
            'my_tasks'        => TaskResource::collection($data['myRecentTasks']),
        ]);
    }
}
