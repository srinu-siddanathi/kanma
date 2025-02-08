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
            class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <!-- Logo and Branch Name -->
            <div class="px-4">
                <div class="flex items-center space-x-2 mb-3">
                    <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-2xl font-extrabold">Kanma.in</span>
                </div>
                <div class="text-sm text-gray-400">{{ auth()->user()->branch->name }}</div>
            </div>

            <nav class="mt-10">
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

            <!-- User Info and Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="border-t border-gray-700 pt-4 mb-4">
                    <div class="flex items-center px-4 mb-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 rounded-full text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <!-- <div class="ml-3">
                            <div class="text-sm font-medium text-gray-300">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">
                                <a href="{{ route('branch.profile.edit') }}" class="hover:text-gray-300">Edit
                                    Profile</a>
                            </div>
                        </div> -->
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
</body>

</html>