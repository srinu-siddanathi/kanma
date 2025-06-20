<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsBranchManager
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user || !$user->isBranchManager() || !$user->branch || !$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized - Branch manager access required'], 403);
            }
            
            return redirect()->route('admin.login')->with('error', 'You do not have access to this area.');
        }

        return $next($request);
    }
} 