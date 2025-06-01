<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('auth.login', [
            'redirect' => $request->get('redirect')
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($request->get('redirect', RouteServiceProvider::HOME));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Handle an AJAX login request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxLogin(LoginRequest $request)
    {
        try {
            \Log::info('Login attempt', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            $request->authenticate();
            $request->session()->regenerate();

            \Log::info('Login successful', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => $request->get('redirect', RouteServiceProvider::HOME)
            ]);
        } catch (ValidationException $e) {
            \Log::warning('Login validation failed', [
                'email' => $request->email,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Login error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all()
                ]
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during login. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 