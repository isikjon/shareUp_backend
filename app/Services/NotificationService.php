<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function createNotification(
        int $userId,
        int $fromUserId,
        string $type,
        string $message,
        $notifiable
    ): Notification {
        if ($userId === $fromUserId) {
            return new Notification();
        }

        return Notification::create([
            'user_id' => $userId,
            'from_user_id' => $fromUserId,
            'type' => $type,
            'message' => $message,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
        ]);
    }

    public function getUserNotifications(int $userId, bool $unreadOnly = false)
    {
        $query = Notification::where('user_id', $userId)
            ->with('fromUser')
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->paginate(20);
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return true;
        }

        return false;
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}

