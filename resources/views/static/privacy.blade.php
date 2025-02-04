<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Privacy Policy</h1>
                    <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Home</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <div class="prose max-w-none">
                    <p class="mb-4">Last updated: {{ now()->format('F d, Y') }}</p>

                    <h2 class="text-2xl font-bold mb-4">1. Information We Collect</h2>
                    <p class="mb-4">We collect information that you provide directly to us, including:</p>
                    <ul class="list-disc pl-5 mb-4">
                        <li>Name and contact information</li>
                        <li>Delivery address</li>
                        <li>Payment information</li>
                        <li>Order history</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4">2. How We Use Your Information</h2>
                    <p class="mb-4">We use the information we collect to:</p>
                    <ul class="list-disc pl-5 mb-4">
                        <li>Process your orders</li>
                        <li>Send order updates</li>
                        <li>Improve our services</li>
                        <li>Communicate with you</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4">3. Information Sharing</h2>
                    <p class="mb-4">We do not sell or share your personal information with third parties except as necessary to provide our services.</p>

                    <h2 class="text-2xl font-bold mb-4">4. Data Security</h2>
                    <p class="mb-4">We implement appropriate security measures to protect your personal information.</p>

                    <h2 class="text-2xl font-bold mb-4">5. Your Rights</h2>
                    <p class="mb-4">You have the right to access, correct, or delete your personal information.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')
</body>
</html> 