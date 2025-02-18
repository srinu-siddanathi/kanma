<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin Login</title>

    <!-- Use CDN for Tailwind instead of Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
        style="background: linear-gradient(135deg, #FDB813 0%, #FF4E50 100%);">
        <div class="max-w-md w-full bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-white">
                    {{ config('app.name', 'Laravel') }} Admin
                </h2>
                <p class="mt-2 text-white/80">Admin Control Panel</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
            <div class="mb-4 bg-white/10 text-white p-4 rounded-md">
                {{ session('status') }}
            </div>
            @endif

            <form class="space-y-6" method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required class="appearance-none block w-full px-3 py-2 border border-white/20 rounded-md 
                                      shadow-sm bg-white/10 text-white placeholder-white/50
                                      focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            value="{{ old('email') }}" placeholder="Enter admin email">
                    </div>
                    @error('email')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-white/20 rounded-md 
                                      shadow-sm bg-white/10 text-white placeholder-white/50
                                      focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Enter admin password">
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-white/10 text-yellow-500 
                                      focus:ring-yellow-500 focus:ring-offset-0">
                        <label for="remember_me" class="ml-2 block text-sm text-white">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md 
                                   shadow-sm text-sm font-medium text-white bg-gradient-to-r from-yellow-500 
                                   to-red-500 hover:from-yellow-600 hover:to-red-600 focus:outline-none 
                                   focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                        Sign in to Admin Panel
                    </button>
                </div>
            </form>

            <!-- Back to Main Site -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-white/80 hover:text-white">
                    ← Back to Main Site
                </a>
            </div>
        </div>
    </div>
</body>

</html>