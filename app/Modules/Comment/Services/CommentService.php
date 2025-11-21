<?php

namespace App\Modules\Comment\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PointTransaction;
use App\Services\NotificationService;

class CommentService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function createComment($userId, $postId, array $data)
    {
        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'user_id' => $userId,
            'post_id' => $postId,
            'content' => $data['content'],
        ]);

        $post->increment('comments_count');

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => 5,
            'type' => 'comment',
            'description' => 'Created a comment',
            'transactionable_type' => Comment::class,
            'transactionable_id' => $comment->id,
        ]);

        $comment->user->increment('points', 5);

        $user = auth()->user();
        $this->notificationService->createNotification(
            $post->user_id,
            $userId,
            'comment',
            "{$user->name} прокомментировал ваш пост: {$data['content']}",
            $post
        );

        return $comment->load('user');
    }

    public function deleteComment($commentId, $userId)
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== $userId && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $comment->post->decrement('comments_count');
        $comment->delete();

        return true;
    }

    public function getComments($postId)
    {
        return Comment::where('post_id', $postId)
            ->with('user')
            ->latest()
            ->paginate(20);
    }
}

