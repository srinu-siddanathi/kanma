<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Branch Manager') - {{ auth()->user()->branch->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div
            class="bg-gray-800 text-white w-64 flex flex-col absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <!-- Logo and Branch Name -->
            <div class="px-4 py-7">
                <div class="flex items-center space-x-2 mb-3">
                    <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-2xl font-extrabold">Kanma.in</span>
                </div>
                <div class="text-sm text-gray-400">{{ auth()->user()->branch->name }}</div>
            </div>

            <nav class="flex-1 px-2 mt-10 overflow-y-auto">
                <a href="{{ route('branch.dashboard') }}"
                    class="{{ request()->routeIs('branch.dashboard') ? 'bg-gray-700' : '' }} flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('branch.products.index') }}"
                    class="{{ request()->routeIs('branch.products.*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Products
                </a>

                <a href="{{ route('branch.products.available') }}"
                    class="{{ request()->routeIs('branch.products.available') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>
                    Add Products
                </a>

                <a href="{{ route('branch.chat-orders.index') }}"
                    class="{{ request()->routeIs('branch.chat-orders.*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Chat Orders
                </a>

                <a href="{{ route('branch.delivery-boys.index') }}"
                    class="{{ request()->routeIs('branch.delivery-boys.*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>
                    Delivery Boys
                </a>

                <a href="{{ route('branch.order-assignments.index') }}"
                    class="{{ request()->routeIs('branch.order-assignments.*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>
                    Order Assignments
                </a>

                <a href="{{ route('branch.orders.history') }}"
                    class="{{ request()->routeIs('branch.orders.history') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Order History
                </a>

                <!-- <a href="{{ route('branch.categories.index') }}"
                    class="{{ request()->routeIs('branch.categories*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Categories
                </a>

                <a href="{{ route('branch.subcategories.index') }}"
                    class="{{ request()->routeIs('branch.subcategories*') ? 'bg-gray-700' : '' }} flex items-center mt-5 py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Subcategories
                </a> -->
            </nav>

            <!-- Spacer to ensure navigation doesn't overlap with user section -->
            <div class="h-4"></div>

            <!-- User Info and Logout -->
            <div class="p-4 border-t border-gray-700">
                <div class="pt-4 mb-4">
                    <div class="flex items-center px-4 mb-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 rounded-full text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-300">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">
                                <a href="{{ route('branch.profile.edit') }}" class="hover:text-gray-300">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="py-6 px-8">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Global password toggle function -->
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
</body>

</html>