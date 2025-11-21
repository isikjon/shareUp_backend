<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->id());

        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
        ]);
    }

    public function unread(): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->id(), true);

        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(auth()->id());

        return response()->json(['count' => $count]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $this->notificationService->markAsRead($id, auth()->id());

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());

        return response()->json([
            'message' => 'All notifications marked as read',
            'count' => $count,
        ]);
    }
}

