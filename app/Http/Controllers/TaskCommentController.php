<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskCommentController extends Controller
{
    /**
     * Get all comments for a task.
     */
    public function index(Task $task)
    {
        return response()->json([
            'message' => 'Task comments retrieved successfully',
            'comments' => $task->comments()->with('user:id,name')->latest()->get()
        ]);
    }

    /**
     * Store a new comment for a task.
     */
    public function store(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ], [], [
            'comment' => 'Comment',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json([
                'errors' => $errors,
            ], 400);
        } else {
            $request->validate(['comment' => 'required|string']);

            $comment = $task->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $request->comment,
            ]);

            return response()->json(['message' => 'Comment added', 'comment' => $comment], 201);
        }
    }

    /**
     * Admin deletes any comment.
     */
    public function adminCommentDestroy($id)
    {
        $comment = TaskComment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted by admin'], 200);
    }
}
