<?php

namespace App\Modules\Admin\Services;

use App\Models\Comment;
use App\Models\Like;
use App\Models\PointTransaction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminService
{
    public function getDashboardStats()
    {
        return [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_likes' => Like::count(),
            'total_comments' => Comment::count(),
            'total_points' => User::sum('points'),
            'banned_users' => User::where('is_banned', true)->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_posts' => Post::with('user')->latest()->take(5)->get(),
        ];
    }

    public function getUsers($page = 1)
    {
        return User::withCount(['posts', 'likes', 'comments'])
            ->latest()
            ->paginate(20, ['*'], 'page', $page);
    }

    public function banUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
        ]);

        return $user;
    }

    public function unbanUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
        ]);

        return $user;
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->posts()->each(function ($post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
        });

        $user->delete();

        return true;
    }

    public function getPosts($page = 1)
    {
        return Post::with('user')
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(20, ['*'], 'page', $page);
    }

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return true;
    }

    public function addPoints($userId, $amount, $description = null)
    {
        $user = User::findOrFail($userId);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'admin_add',
            'description' => $description ?? 'Manual points addition by admin',
            'transactionable_type' => null,
            'transactionable_id' => null,
        ]);

        $user->increment('points', $amount);

        return $user;
    }

    public function deductPoints($userId, $amount, $description = null)
    {
        $user = User::findOrFail($userId);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => -$amount,
            'type' => 'admin_deduct',
            'description' => $description ?? 'Manual points deduction by admin',
            'transactionable_type' => null,
            'transactionable_id' => null,
        ]);

        $user->decrement('points', $amount);

        return $user;
    }

    public function getLogs($page = 1)
    {
        return PointTransaction::with('user')
            ->latest()
            ->paginate(50, ['*'], 'page', $page);
    }
}

