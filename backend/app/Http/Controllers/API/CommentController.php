<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Task $task): AnonymousResourceCollection
    {
        $this->authorize('view', $task->project);

        $comments = $task->comments()
            ->with('user')
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task->project);

        $request->validate(['body' => 'required|string|max:5000']);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->body,
        ]);

        return (new CommentResource($comment->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);

        $request->validate(['body' => 'required|string|max:5000']);

        $comment->update(['body' => $request->body]);

        return new CommentResource($comment->fresh('user'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted.']);
    }
}
