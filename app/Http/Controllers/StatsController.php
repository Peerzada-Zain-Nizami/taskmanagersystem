<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;

class StatsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_tasks' => Task::count(),
            'total_categories' => Category::count(),
        ];

        return response()->json($stats, 200);
    }
}
