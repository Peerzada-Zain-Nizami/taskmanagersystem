<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = Auth::user();

        // Get all unread notifications & mark them as read
        $notifications = $user->unreadNotifications()->latest()->get();

        // Mark all retrieved notifications as read
        $notifications->markAsRead();

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
