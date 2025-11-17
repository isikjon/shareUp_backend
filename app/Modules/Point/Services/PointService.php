<?php

namespace App\Modules\Point\Services;

use App\Models\PointTransaction;
use App\Models\User;

class PointService
{
    public function getBalance($userId)
    {
        $user = User::findOrFail($userId);

        return [
            'points' => $user->points,
            'user' => $user,
        ];
    }

    public function getTransactions($userId)
    {
        return PointTransaction::where('user_id', $userId)
            ->with('transactionable')
            ->latest()
            ->paginate(20);
    }

    public function addPoints($userId, $amount, $type, $description = null)
    {
        $user = User::findOrFail($userId);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'transactionable_type' => null,
            'transactionable_id' => null,
        ]);

        $user->increment('points', $amount);

        return $user;
    }

    public function deductPoints($userId, $amount, $type, $description = null)
    {
        $user = User::findOrFail($userId);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => -$amount,
            'type' => $type,
            'description' => $description,
            'transactionable_type' => null,
            'transactionable_id' => null,
        ]);

        $user->decrement('points', $amount);

        return $user;
    }
}

