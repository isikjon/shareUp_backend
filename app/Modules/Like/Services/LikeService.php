<?php

namespace App\Modules\Like\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\PointTransaction;
use App\Services\NotificationService;

class LikeService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function toggleLike($userId, $postId)
    {
        $post = Post::findOrFail($postId);
        $like = Like::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $post->user->decrement('points', 2);

            PointTransaction::where('transactionable_type', Like::class)
                ->where('transactionable_id', $like->id)
                ->delete();

            return [
                'liked' => false,
                'likes_count' => $post->likes_count,
            ];
        }

        $like = Like::create([
            'user_id' => $userId,
            'post_id' => $postId,
        ]);

        $post->increment('likes_count');
        $post->user->increment('points', 2);

        PointTransaction::create([
            'user_id' => $post->user_id,
            'amount' => 2,
            'type' => 'like',
            'description' => 'Received a like',
            'transactionable_type' => Like::class,
            'transactionable_id' => $like->id,
        ]);

        $user = auth()->user();
        $this->notificationService->createNotification(
            $post->user_id,
            $userId,
            'like',
            "{$user->name} понравился ваш пост",
            $post
        );

        return [
            'liked' => true,
            'likes_count' => $post->likes_count,
        ];
    }
}

