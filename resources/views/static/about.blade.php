<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">About Us</h1>
                    <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Home</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <div class="prose max-w-none">
                    <h2 class="text-2xl font-bold mb-4">Welcome to {{ config('app.name') }}</h2>
                    
                    <p class="mb-4">At {{ config('app.name') }}, we're revolutionizing the way people shop locally. Our platform connects local businesses with customers in their community, making it easier than ever to shop from nearby stores.</p>

                    <h3 class="text-xl font-semibold mb-3">Our Mission</h3>
                    <p class="mb-4">Our mission is to empower local businesses and provide convenience to customers through technology. We believe in supporting local economies while providing modern shopping experiences.</p>

                    <h3 class="text-xl font-semibold mb-3">What We Offer</h3>
                    <ul class="list-disc pl-5 mb-4">
                        <li>Easy access to local stores</li>
                        <li>Quick delivery options</li>
                        <li>Wide range of products</li>
                        <li>Secure payment methods</li>
                        <li>Customer support</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-3">Our Values</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="p-4 border rounded-lg">
                            <h4 class="font-semibold mb-2">Community First</h4>
                            <p>Supporting local businesses and strengthening community bonds</p>
                        </div>
                        <div class="p-4 border rounded-lg">
                            <h4 class="font-semibold mb-2">Innovation</h4>
                            <p>Leveraging technology to improve shopping experiences</p>
                        </div>
                        <div class="p-4 border rounded-lg">
                            <h4 class="font-semibold mb-2">Reliability</h4>
                            <p>Ensuring quality service and customer satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')
</body>
</html> 