<footer class="bg-white border-t">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('static.about') }}" class="text-gray-500 hover:text-gray-700">About Us</a>
                <a href="{{ route('static.terms') }}" class="text-gray-500 hover:text-gray-700">Terms & Conditions</a>
                <a href="{{ route('static.privacy') }}" class="text-gray-500 hover:text-gray-700">Privacy Policy</a>
            </div>
            <div class="mt-4 md:mt-0 text-gray-400">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</footer> 