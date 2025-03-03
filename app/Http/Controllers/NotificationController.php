<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = Auth::user();

        // Get only the latest unique unread notifications
        $notifications = $user->unreadNotifications()
            ->latest() // Latest notifications first
            ->get()
            ->unique('type'); // Remove duplicates based on notification type

        // Mark all retrieved notifications as read
        $notifications->markAsRead();

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
