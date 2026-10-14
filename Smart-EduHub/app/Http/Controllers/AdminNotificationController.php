<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function getNewNotifications(Request $request)
    {
        $lastShownId = $request->input('last_shown_id', 0);
        
        $notifications = AdminNotification::whereNull('read_at')
            ->where('id', '>', $lastShownId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
}
