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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Task $task)
    {
        return response()->json($task->comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
}
