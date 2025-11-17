<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function dashboard(): JsonResponse
    {
        $stats = $this->adminService->getDashboardStats();

        return response()->json($stats);
    }

    public function users(Request $request): JsonResponse
    {
        $users = $this->adminService->getUsers($request->get('page', 1));

        return response()->json($users);
    }

    public function banUser($userId): JsonResponse
    {
        $user = $this->adminService->banUser($userId);

        return response()->json($user);
    }

    public function unbanUser($userId): JsonResponse
    {
        $user = $this->adminService->unbanUser($userId);

        return response()->json($user);
    }

    public function deleteUser($userId): JsonResponse
    {
        $this->adminService->deleteUser($userId);

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function posts(Request $request): JsonResponse
    {
        $posts = $this->adminService->getPosts($request->get('page', 1));

        return response()->json($posts);
    }

    public function deletePost($postId): JsonResponse
    {
        $this->adminService->deletePost($postId);

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function addPoints(Request $request, $userId): JsonResponse
    {
        $user = $this->adminService->addPoints(
            $userId,
            $request->amount,
            $request->description
        );

        return response()->json($user);
    }

    public function deductPoints(Request $request, $userId): JsonResponse
    {
        $user = $this->adminService->deductPoints(
            $userId,
            $request->amount,
            $request->description
        );

        return response()->json($user);
    }

    public function logs(Request $request): JsonResponse
    {
        $logs = $this->adminService->getLogs($request->get('page', 1));

        return response()->json($logs);
    }
}

