<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    /**
     * Get unread notifications count for the authenticated user.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0]);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Get the 8 latest notifications formatted.
     */
    public function latest()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $notifications = $user->notifications->take(8);

        return response()->json($notifications->map(function ($n) {
            $data = $n->data;
            return [
                'id' => $n->id,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? ($data['body'] ?? ''),
                'icon' => $data['icon'] ?? '🔔',
                'url' => $data['url'] ?? '#',
                'is_unread' => is_null($n->read_at),
                'time' => $n->created_at->diffForHumans(),
            ];
        }));
    }
}
