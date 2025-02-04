<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kanma - Your Trusted Partner for Home Delivery</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-br from-yellow-400 via-orange-500 to-red-500 min-h-screen">
        <!-- Header -->
        <header class="bg-white/10 backdrop-blur-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-white">KANMA</span>
                    </div>
                    <nav class="flex space-x-4">
                        <a href="{{ route('static.about') }}" class="text-white/90 hover:text-white">About</a>
                        <a href="#" class="text-white/90 hover:text-white">Contact</a>
                        <a href="{{ route('admin.login') }}" class="text-white/90 hover:text-white">Login</a>
                    </nav>
                </div>
            </div>
        </header>

        <div class="container mx-auto px-4 min-h-[calc(100vh-4rem)] flex items-center justify-center">
            <div class="text-center">
                <!-- Main Content -->
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 md:p-12 shadow-2xl">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                        Welcome to Kanma
                    </h1>
                    <h2 class="text-xl md:text-2xl text-white/90 mb-8">
                        Your Trusted Partner for Home Delivery
                    </h2>
                    <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                        At Kanma, we understand the importance of convenience in today's fast-paced world. We're committed to making your life easier by delivering essential items right to your doorstep.
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <div class="text-3xl font-bold text-white mb-1">2+</div>
                            <div class="text-white/70">Glorious years</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <div class="text-3xl font-bold text-white mb-1">1800+</div>
                            <div class="text-white/70">Kanma members</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <div class="text-3xl font-bold text-white mb-1">23+</div>
                            <div class="text-white/70">Vendors</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <div class="text-3xl font-bold text-white mb-1">1200+</div>
                            <div class="text-white/70">Products</div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <h3 class="text-white font-semibold mb-2">Best Prices & Offers</h3>
                            <p class="text-white/70 text-sm">Discover a world of savings with our exclusive offers and promotions</p>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <h3 class="text-white font-semibold mb-2">Free Delivery</h3>
                            <p class="text-white/70 text-sm">24/7 amazing services with free delivery on all orders</p>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 backdrop-blur-sm">
                            <h3 class="text-white font-semibold mb-2">100% Satisfaction</h3>
                            <p class="text-white/70 text-sm">We measure our success by the smiles we bring to your face</p>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="text-white/80 text-sm mb-8">
                        <p>Call Us: (+91) 89852 36524</p>
                        <p>Working Hours: 6:00 AM to 9:00 PM</p>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="max-w-md mx-auto">
                        <div class="flex gap-2">
                            <input type="email" placeholder="Enter your email for updates" 
                                class="flex-1 px-4 py-2 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50">
                            <button class="px-6 py-2 bg-white text-orange-600 rounded-lg font-semibold hover:bg-white/90 transition-colors">
                                Stay Updated
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white/10 backdrop-blur-lg border-t border-white/20">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('static.about') }}" class="text-white/80 hover:text-white">About Us</a>
                        <a href="{{ route('static.terms') }}" class="text-white/80 hover:text-white">Terms & Conditions</a>
                        <a href="{{ route('static.privacy') }}" class="text-white/80 hover:text-white">Privacy Policy</a>
                    </div>
                    <div class="mt-4 md:mt-0 text-white/60">
                        © {{ date('Y') }} Kanma. All rights reserved. A product of Kanmark India.
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
