<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Branch;

class ShareSelectedAddress
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('ShareSelectedAddress middleware called', [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'cookies' => $request->cookies->all()
        ]);

        // Initialize variables with null values
        $selectedAddress = null;
        $selectedBranch = null;

        // Get selected address from session
        if (session()->has('selected_address_id')) {
            $selectedAddress = Address::find(session('selected_address_id'));
            \Log::info('Found selected address', ['address' => $selectedAddress]);
        }

        // Get selected branch from session
        if (session()->has('selected_branch_id')) {
            $selectedBranch = Branch::find(session('selected_branch_id'));
            \Log::info('Found selected branch', ['branch' => $selectedBranch]);
        }

        // Share with all views
        view()->share([
            'selectedAddress' => $selectedAddress,
            'selectedBranch' => $selectedBranch
        ]);

        return $next($request);
    }
} 