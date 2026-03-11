<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return ApiResponse::success($notifications, 'Notifications fetched');
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->paginate(10);

        return ApiResponse::success($notifications, 'Unread notifications fetched');
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return ApiResponse::success(null, 'Notification marked as read');
    }
}