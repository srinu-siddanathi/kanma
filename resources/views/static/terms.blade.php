<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Terms & Conditions</h1>
                    <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Home</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <div class="prose max-w-none">
                    <p class="mb-4">Last updated: {{ now()->format('F d, Y') }}</p>

                    <h2 class="text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
                    <p class="mb-4">By accessing and using {{ config('app.name') }}, you accept and agree to be bound by the terms and provision of this agreement.</p>

                    <h2 class="text-2xl font-bold mb-4">2. Use License</h2>
                    <p class="mb-4">Permission is granted to temporarily download one copy of the materials (information or software) on {{ config('app.name') }}'s website for personal, non-commercial transitory viewing only.</p>

                    <h2 class="text-2xl font-bold mb-4">3. User Accounts</h2>
                    <p class="mb-4">Users are responsible for maintaining the confidentiality of their account and password information.</p>

                    <h2 class="text-2xl font-bold mb-4">4. Ordering & Payment</h2>
                    <p class="mb-4">All orders are subject to product availability. We reserve the right to discontinue any products at any time.</p>

                    <h2 class="text-2xl font-bold mb-4">5. Delivery</h2>
                    <p class="mb-4">Delivery times shown on the platform are estimates and not guaranteed times.</p>

                    <h2 class="text-2xl font-bold mb-4">6. Modifications</h2>
                    <p class="mb-4">{{ config('app.name') }} may revise these terms of service at any time without notice.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')
</body>
</html> 