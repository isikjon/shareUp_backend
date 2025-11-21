<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Modules\Post\Requests\CreatePostRequest;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request): JsonResponse
    {
        $posts = $this->postService->getFeed($request->get('page', 1));

        return response()->json([
            'data' => PostResource::collection($posts),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ]);
    }

    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost(auth()->id(), $request->validated());

        return response()->json(new PostResource($post), 201);
    }

    public function show($id): JsonResponse
    {
        $post = $this->postService->getPost($id);

        return response()->json(new PostResource($post));
    }

    public function destroy($id): JsonResponse
    {
        $this->postService->deletePost($id, auth()->id());

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function userPosts($userId): JsonResponse
    {
        $posts = $this->postService->getUserPosts($userId);

        return response()->json([
            'data' => PostResource::collection($posts),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ]);
    }
}

