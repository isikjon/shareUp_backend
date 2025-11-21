<?php

namespace App\Modules\Comment\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Modules\Comment\Requests\CreateCommentRequest;
use App\Modules\Comment\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(CreateCommentRequest $request, $postId): JsonResponse
    {
        $comment = $this->commentService->createComment(
            auth()->id(),
            $postId,
            $request->validated()
        );

        return response()->json(new CommentResource($comment), 201);
    }

    public function destroy($id): JsonResponse
    {
        $this->commentService->deleteComment($id, auth()->id());

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function index($postId): JsonResponse
    {
        $comments = $this->commentService->getComments($postId);

        return response()->json([
            'data' => CommentResource::collection($comments),
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'per_page' => $comments->perPage(),
            'total' => $comments->total(),
        ]);
    }
}

