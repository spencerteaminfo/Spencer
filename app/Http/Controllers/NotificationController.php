<?php

namespace App\Http\Controllers;

use App\Events\Notifications\NotificationMarkedRead;
use App\Events\Notifications\NotificationsMarkedRead;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
        return view('notifications.notifications');
    }

    /**
     * get user's notifications.
     */
    public function list(Request $request): JsonResponse // TODO maybe this should be standardized?
    {
        $formatNotification = static function (DatabaseNotification $notification): array {
            $data = $notification->data;

            if (isset($data['translation']['key']) && is_string($data['translation']['key'])) {
                $params = is_array($data['translation']['params'] ?? null) ? $data['translation']['params'] : [];
                $data['message'] = __($data['translation']['key'], $params);
            }

            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $data,
                'created_at' => $notification->created_at?->toISOString(),
                'read_at' => $notification->read_at?->toISOString(),
            ];
        };

        $allNotifications = $request->user()->notifications()->paginate(10);
        $allNotifications->setCollection(
            $allNotifications->getCollection()->map(fn (DatabaseNotification $notification) => $formatNotification($notification))
        );

        return response()->json([
            'unread' => $request->user()->unreadNotifications->map(fn (DatabaseNotification $notification) => $formatNotification($notification))->values(),
            'all'    => $allNotifications
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
