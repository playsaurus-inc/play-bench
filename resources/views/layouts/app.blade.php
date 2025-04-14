<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'PlayBench') : config('app.name', 'PlayBench') }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100 shadow">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                                PlayBench
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:ml-10 sm:flex">
                            <a href="{{ route('rps.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('rps.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Rock Paper Scissors
                            </a>
                            <a href="{{ route('models.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('models.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                AI Models
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="container mx-auto px-4 py-8 sm:px-6 lg:px-8 flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 shadow mt-auto">
            <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} PlayBench - AI Benchmark Platform
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
