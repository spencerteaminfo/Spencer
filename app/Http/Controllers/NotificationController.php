<?php

namespace App\Http\Controllers;

use App\Events\NotificationMarkedRead;
use App\Events\NotificationsMarkedRead;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View
    {
        return view('notifications');
    }

    /**
     * get user's notifications.
     */
    public function list(Request $request): JsonResponse // TODO maybe this should be standardized?
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications,
            'all'    => $request->user()->notifications()->paginate(10) // TODO forgot what this does, maybe should rework?
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function read(DatabaseNotification $notification): JsonResponse
    {
        $actor = auth()->user();

        if ($notification->notifiable_id !== $actor->id) {
            abort(403, 'Neoprávněná akce.');
        }

        $notification->update(['read_at' => now()]);

        event(new NotificationMarkedRead($notification, $actor));

        return response()->json([
                'message' => 'Notification was marked as read successfully.',
                'data' => $notification
            ]
        );
    }

    /**
     * Mark all user's notifications as read.
     */
    public function readAll(): JsonResponse
    {
        $actor = auth()->user();
        $notifications = $actor->unreadNotifications;
        $notifications->each(function ($n) {
            $n->update(['read_at' => now()]);
        });

        event(new NotificationsMarkedRead($notifications, $actor));

        return response()->json([
            'message' => 'All notifications were marked as read successfully.',
            'data' => $notifications
        ]);
    }
}
