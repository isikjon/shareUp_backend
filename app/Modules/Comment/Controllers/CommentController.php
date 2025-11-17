<?php

namespace App\Modules\Comment\Controllers;

use App\Http\Controllers\Controller;
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

        return response()->json($comment, 201);
    }

    public function destroy($id): JsonResponse
    {
        $this->commentService->deleteComment($id, auth()->id());

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function index($postId): JsonResponse
    {
        $comments = $this->commentService->getComments($postId);

        return response()->json($comments);
    }
}

