<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                if ($user->isAdmin()) {
                    return redirect(route('admin.dashboard'));
                } elseif ($user->isBranchManager()) {
                    return redirect(route('branch.dashboard'));
                } elseif ($user->isShopOwner()) {
                    return redirect(route('shop-owner.dashboard'));
                }
                
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
} 