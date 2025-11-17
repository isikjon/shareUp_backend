<?php

namespace App\Modules\Post\Services;

use App\Models\Post;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function getFeed($page = 1)
    {
        return Post::with(['user', 'comments.user'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(15, ['*'], 'page', $page);
    }

    public function getPost($id)
    {
        return Post::with(['user', 'comments.user'])
            ->withCount('likes', 'comments')
            ->findOrFail($id);
    }

    public function createPost($userId, array $data)
    {
        $imagePath = null;
        
        if (isset($data['image'])) {
            $imagePath = $data['image']->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => $userId,
            'content' => $data['content'],
            'image' => $imagePath,
        ]);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => 10,
            'type' => 'post',
            'description' => 'Created a post',
            'transactionable_type' => Post::class,
            'transactionable_id' => $post->id,
        ]);

        $post->user->increment('points', 10);

        return $post->load('user');
    }

    public function deletePost($postId, $userId)
    {
        $post = Post::findOrFail($postId);

        if ($post->user_id !== $userId && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return true;
    }

    public function getUserPosts($userId)
    {
        return Post::where('user_id', $userId)
            ->with('user')
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(15);
    }
}

