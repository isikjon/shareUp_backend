<?php

namespace App\Modules\Like\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Like\Services\LikeService;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    protected LikeService $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    public function toggle($postId): JsonResponse
    {
        $result = $this->likeService->toggleLike(auth()->id(), $postId);

        return response()->json($result);
    }
}

