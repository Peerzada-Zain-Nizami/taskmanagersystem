<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        $query = User::query();

        // Search by keyword in title
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        // Filter by status
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $users = $query->paginate(10);

        return response()->json($users, 200);
    }

    // ✅ Approve user
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return response()->json(['message' => 'User role updated successfully']);
    }
}
