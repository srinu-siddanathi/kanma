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
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="mx-auto mb-4">

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
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 pr-10 border border-white/20 rounded-md 
                                      shadow-sm bg-white/10 text-white placeholder-white/50
                                      focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Enter admin password">
                        <button class="absolute inset-y-0 right-0 pr-3 flex items-center" type="button" onclick="togglePassword('password')">
                            <svg width="16" height="16" class="text-white/60 hover:text-white">
                                <use xlink:href="#eye"></use>
                            </svg>
                        </button>
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

    <!-- SVG Icons for Password Toggle -->
    <svg style="display: none;">
        <defs>
            <symbol id="eye" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </symbol>
            <symbol id="eye-slash" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
            </symbol>
        </defs>
    </svg>

    <!-- Password Toggle JavaScript -->
    <script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleBtn = passwordInput.nextElementSibling;
        const icon = toggleBtn.querySelector('use');
        
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        
        // Update icon
        icon.setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
    }
    </script>
</body>

</html>