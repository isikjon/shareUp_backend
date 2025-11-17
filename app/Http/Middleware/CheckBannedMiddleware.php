<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_banned) {
            auth()->user()->currentAccessToken()->delete();
            
            return response()->json([
                'message' => 'Your account has been banned.',
            ], 403);
        }

        return $next($request);
    }
}
