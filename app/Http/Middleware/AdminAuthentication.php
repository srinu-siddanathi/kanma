<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !in_array($request->user()->role, ['admin', 'dataentry'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin or Data Entry access required.'
            ], 403);
        }

        return $next($request);
    }
} 