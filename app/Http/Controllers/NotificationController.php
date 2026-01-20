<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Mark a specific notification as read and redirect to its action.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect to the target URL if it exists
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        return back();
    }
}
