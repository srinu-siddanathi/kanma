<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'brand-yellow': '#FDB813',
                    'brand-red': '#FF4E50',
                }
            }
        }
    }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gradient-to-br from-yellow-50 to-red-50">
    @auth
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gradient-to-b from-brand-yellow to-brand-red text-white w-64 flex flex-col h-screen space-y-6 py-7 px-2">
            <!-- Logo and Title -->
            <div class="flex items-center space-x-2 px-4">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
            </div>
            <div class="flex-1 overflow-y-auto">
                <nav class="space-y-2">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="{{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>

                        <a href="{{ route('admin.orders') }}"
                            class="{{ request()->routeIs('admin.orders*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Orders
                        </a>

                        <a href="{{ route('admin.branches.index') }}"
                            class="{{ request()->routeIs('admin.branches.*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Branches
                        </a>

                        <a href="{{ route('admin.shops.index') }}" 
                           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.shops.*') ? 'bg-white/10' : '' }}">
                            <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Shops
                        </a>

                        <a href="{{ route('admin.delivery-boys.index') }}"
                            class="{{ request()->routeIs('admin.delivery-boys*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Delivery Boys
                        </a>

                        <a href="{{ route('admin.subscription-plans.index') }}"
                            class="{{ request()->routeIs('admin.subscription-plans*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Subscription Plans
                        </a>

                        <a href="{{ route('admin.subscriptions.index') }}"
                            class="{{ request()->routeIs('admin.subscriptions*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Active Subscriptions
                        </a>

                        <a href="{{ route('admin.newsletters.index') }}"
                            class="{{ request()->routeIs('admin.newsletters*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Newsletter Subscribers
                        </a>

                        <a href="{{ route('admin.settings.index') }}"
                            class="{{ request()->routeIs('admin.settings*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    @endif

                    <a href="{{ route('admin.categories.index') }}"
                        class="{{ request()->routeIs('admin.categories*') ? 'bg-white/20' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-white/10">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Categories
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="{{ request()->routeIs('admin.products.*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products
                    </a>

                    <a href="{{ route('admin.banners.index') }}"
                        class="{{ request()->routeIs('admin.banners.*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Banners
                    </a>
                </nav>
            </div>
            <!-- User Info and Logout -->
            <div class="p-4">
                <div class="border-t border-white/20 pt-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Guest' }}</p>
                            <div class="flex items-center space-x-3 text-xs">
                                <a href="{{ route('admin.profile.edit') }}"
                                    class="text-white/80 hover:text-white transition duration-200">
                                    Edit Profile
                                </a>
                                <span class="text-white/50">•</span>
                                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-white/80 hover:text-white transition duration-200">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-lg shadow-lg">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                        <!-- Add any header content here -->
                        <div class="flex items-center ml-6">
                            <!-- Notifications Dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button type="button" 
                                            @click="open = !open"
                                            class="relative bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            id="notifications-menu-button"
                                            aria-expanded="false"
                                            aria-haspopup="true">
                                        <span class="sr-only">View notifications</span>
                                        <!-- Notification Badge -->
                                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                            3
                                        </span>
                                        <!-- Bell Icon -->
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Notifications Dropdown Menu -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none" 
                                     role="menu" 
                                     aria-orientation="vertical" 
                                     aria-labelledby="notifications-menu-button" 
                                     tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="#" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            <span class="flex-shrink-0 w-2 h-2 mt-2 mr-3 bg-blue-500 rounded-full"></span>
                                            <div>
                                                <p class="font-medium">New shop registration</p>
                                                <p class="text-xs text-gray-500">2 minutes ago</p>
                                            </div>
                                        </a>
                                        <a href="#" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            <span class="flex-shrink-0 w-2 h-2 mt-2 mr-3 bg-green-500 rounded-full"></span>
                                            <div>
                                                <p class="font-medium">New order received</p>
                                                <p class="text-xs text-gray-500">1 hour ago</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button type="button" 
                                            @click="open = !open"
                                            class="bg-white flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                                            id="user-menu-button" 
                                            aria-expanded="false" 
                                            aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" 
                                             src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Guest' }}" 
                                             alt="{{ auth()->user()->name ?? 'Guest' }}">
                                    </button>
                                </div>

                                <!-- Profile Dropdown Menu -->
                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                     role="menu" 
                                     aria-orientation="vertical" 
                                     aria-labelledby="user-menu-button" 
                                     tabindex="-1">
                                    <a href="{{ route('admin.profile.edit') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                       role="menuitem">Your Profile</a>
                                    <a href="{{ route('admin.settings.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                       role="menuitem">Settings</a>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                role="menuitem">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('layouts.partials.admin-footer')
        </div>
    </div>
    @else
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Access Denied</h1>
            <p class="text-gray-600 mb-4">You must be logged in to access this page.</p>
            <a href="{{ route('admin.login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-yellow hover:bg-brand-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-yellow">
                Login
            </a>
        </div>
    </div>
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any global JavaScript functionality here
        window.Swal = Swal;
    });
    </script>

    @stack('scripts')
</body>

</html>