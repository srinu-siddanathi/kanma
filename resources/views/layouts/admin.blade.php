<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
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
</head>

<body class="bg-gradient-to-br from-yellow-50 to-red-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div
            class="bg-gradient-to-b from-brand-yellow to-brand-red text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <!-- Logo and Title -->
            <div class="flex items-center space-x-2 px-4">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-2xl font-extrabold">{{ config('app.name') }}</span>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
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

                <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>

                <a href="{{ route('admin.subscription-plans.index') }}"
                    class="{{ request()->routeIs('admin.subscription-plans.*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Subscription Plans
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="{{ request()->routeIs('admin.products.*') ? 'bg-white/20' : '' }} flex items-center px-4 py-2.5 rounded transition duration-200 hover:bg-white/10">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Products
                </a>

                <!-- Shop Management -->
                <a href="{{ route('admin.shops.index') }}" 
                   class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.shops.*') ? 'bg-gray-700' : '' }}">
                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Shops
                </a>
            </nav>

            <!-- User Info and Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="border-t border-white/20 pt-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
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
                            <div class="ml-3 relative">
                                <div>
                                    <button type="button" 
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
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none" 
                                     role="menu" 
                                     id="notifications-menu"
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
                            <div class="ml-3 relative">
                                <div>
                                    <button type="button" 
                                            class="bg-white flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                                            id="user-menu-button" 
                                            aria-expanded="false" 
                                            aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" 
                                             src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" 
                                             alt="{{ auth()->user()->name }}">
                                    </button>
                                </div>

                                <!-- Profile Dropdown Menu -->
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                     role="menu" 
                                     id="user-menu"
                                     aria-orientation="vertical" 
                                     aria-labelledby="user-menu-button" 
                                     tabindex="-1">
                                    <a href="{{ route('admin.profile.edit') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                       role="menuitem">Your Profile</a>
                                    <a href="{{ route('admin.settings.edit') }}" 
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

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notifications dropdown
        const notificationsButton = document.getElementById('notifications-menu-button');
        const notificationsMenu = document.getElementById('notifications-menu');
        
        notificationsButton.addEventListener('click', function() {
            notificationsMenu.classList.toggle('hidden');
        });

        // User profile dropdown
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!notificationsButton.contains(event.target) && !notificationsMenu.contains(event.target)) {
                notificationsMenu.classList.add('hidden');
            }
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    });
    </script>
</body>

</html>