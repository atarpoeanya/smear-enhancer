<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Image Upload Dashboard')</title>
    @stack('script')
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-2 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-700">Image Dashboard</a>
            <div class="flex space-x-4">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-gray-900">Home</a>
                <a href="{{ route('image.create') }}" class="text-gray-700 hover:text-gray-900">Upload</a>
                <!-- Add more links as needed -->
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        @yield('content')
    </div>
</body>
</html>
