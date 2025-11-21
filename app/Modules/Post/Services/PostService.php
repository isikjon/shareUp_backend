<?php

namespace App\Modules\Post\Services;

use App\Models\Post;
use App\Models\PointTransaction;
use App\Services\ImageService;

class PostService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getFeed($page = 1)
    {
        $userId = auth()->id();
        
        return Post::with(['user'])
            ->withCount('likes', 'comments')
            ->selectRaw('posts.*, EXISTS(SELECT 1 FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?) as isLiked', [$userId])
            ->latest()
            ->paginate(15, ['*'], 'page', $page);
    }

    public function getPost($id)
    {
        $userId = auth()->id();
        
        return Post::with(['user'])
            ->withCount('likes', 'comments')
            ->selectRaw('posts.*, EXISTS(SELECT 1 FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?) as isLiked', [$userId])
            ->findOrFail($id);
    }

    public function createPost($userId, array $data)
    {
        $imagePath = null;
        
        if (isset($data['image'])) {
            $imagePath = $this->imageService->processAndStore($data['image'], 'posts');
        }

        $post = Post::create([
            'user_id' => $userId,
            'content' => $data['content'] ?? '',
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
            $this->imageService->delete($post->image);
        }

        $post->delete();

        return true;
    }

    public function getUserPosts($userId)
    {
        $currentUserId = auth()->id();
        
        return Post::where('user_id', $userId)
            ->with('user')
            ->withCount('likes', 'comments')
            ->selectRaw('posts.*, EXISTS(SELECT 1 FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?) as isLiked', [$currentUserId])
            ->latest()
            ->paginate(15);
    }
}


