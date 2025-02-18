<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" 
         style="background: linear-gradient(135deg, #FDB813 0%, #FF4E50 100%);">
        <div class="max-w-md w-full bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-white">
                    {{ config('app.name', 'Laravel') }}
                </h2>
                <p class="mt-2 text-white/80">Sign in to your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required 
                               class="appearance-none block w-full px-3 py-2 border border-white/20 rounded-md 
                                      shadow-sm bg-white/10 text-white placeholder-white/50
                                      focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               value="{{ old('email') }}"
                               placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="appearance-none block w-full px-3 py-2 border border-white/20 rounded-md 
                                      shadow-sm bg-white/10 text-white placeholder-white/50
                                      focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="Enter your password">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                               class="h-4 w-4 rounded border-white/20 bg-white/10 text-yellow-500 
                                      focus:ring-yellow-500 focus:ring-offset-0">
                        <label for="remember_me" class="ml-2 block text-sm text-white">
                            Remember me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-sm text-white/80 hover:text-white">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md 
                                   shadow-sm text-sm font-medium text-white bg-gradient-to-r from-yellow-500 
                                   to-red-500 hover:from-yellow-600 hover:to-red-600 focus:outline-none 
                                   focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout> 