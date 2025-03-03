<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userTasks(Request $request)
    {
        $userId = Auth::id();
        $query = Task::where('user_id', $userId);

        // Search by keyword in title
        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $tasks = $query->paginate(10);

        return response()->json($tasks);
    }

    public function adminIndex(Request $request)
    {
        $query = Task::query();

        // Search by keyword in title
        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $tasks = $query->paginate(10);

        return response()->json($tasks);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'status' => 'in:pending,in_progress,completed',
            'category_id' => 'required|exists:categories,id',
        ], [], [
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'category_id' => 'Category',
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

            $task = Auth::user()->tasks()->create($request->all());

            return response()->json(['message' => 'Task created', 'task' => $task], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['task' => $task], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($request->only('title', 'category_id', 'status'));
        return response()->json(['message' => 'Task updated', 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function recentActivities()
    {
        $activities = Task::with('user:id,name') // Get user name
            ->select('id', 'user_id', 'title', 'status', 'updated_at')
            ->orderBy('updated_at', 'desc') // Order by latest update
            ->limit(10) // Limit to 10 recent activities
            ->get()
            ->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'updated_at' => Carbon::parse($task->updated_at)->diffForHumans(),
                'user' => ['name' => $task->user->name]
            ]);

        return response()->json(['activities' => $activities], 200);
    }

    /**
     * Get task counts based on status.
     */
    public function getTaskCounts()
    {
        $counts = [
            'completed' => Task::where('status', 'completed')->count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
        ];

        return response()->json(['task_counts' => $counts], 200);
    }
}
