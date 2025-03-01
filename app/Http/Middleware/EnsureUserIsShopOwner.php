<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsShopOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'shop_owner') {
            return redirect()->route('home');
        }

        if (!$request->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. Please wait for admin activation.');
        }

        return $next($request);
    }
} 