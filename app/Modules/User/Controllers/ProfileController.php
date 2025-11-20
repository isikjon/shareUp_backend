<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Modules\User\Requests\UpdateProfileRequest;
use App\Modules\User\Services\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show($id): JsonResponse
    {
        $user = $this->profileService->getProfile($id);

        return response()->json(new UserResource($user));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile(auth()->id(), $request->validated());

        return response()->json(new UserResource($user));
    }

    public function uploadAvatar(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->uploadAvatar(auth()->id(), $request->file('avatar'));

        return response()->json(new UserResource($user));
    }

    public function suggestions(): JsonResponse
    {
        $users = $this->profileService->getSuggestedUsers(auth()->id());

        return response()->json(['data' => UserResource::collection($users)]);
    }
}
