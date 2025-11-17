<?php

namespace App\Modules\Point\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Point\Services\PointService;
use Illuminate\Http\JsonResponse;

class PointController extends Controller
{
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function balance(): JsonResponse
    {
        $balance = $this->pointService->getBalance(auth()->id());

        return response()->json($balance);
    }

    public function transactions(): JsonResponse
    {
        $transactions = $this->pointService->getTransactions(auth()->id());

        return response()->json($transactions);
    }
}

