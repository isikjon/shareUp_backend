<?php

namespace App\Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function getProfile($id)
    {
        return User::withCount(['posts', 'likes', 'comments'])
            ->findOrFail($id);
    }

    public function updateProfile($userId, array $data)
    {
        $user = User::findOrFail($userId);
        $user->update($data);

        return $user;
    }

    public function uploadAvatar($userId, $file)
    {
        $user = User::findOrFail($userId);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $user;
    }
}

