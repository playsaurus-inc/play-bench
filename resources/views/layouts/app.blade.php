<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="PlayBench" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-amber-50/20 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <x-layouts::navigation />

        <!-- Page Content -->
        <main class="container mx-auto px-4 py-8 sm:px-6 lg:px-8 flex-grow relative">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 shadow-inner mt-auto">
            <div class="container mx-auto px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="text-gray-500 mb-4 md:mb-0">
                        <div class="flex space-x-4 mb-2 justify-center md:justify-start">
                            <a
                                href="{{ route('about') }}"
                                class="text-amber-600 hover:underline hover:text-amber-700 transition-colors"
                            >
                                About
                            </a>
                            <a
                                href="{{ config('playbench.github_repo_url') }}"
                                class="text-amber-600 hover:underline hover:text-amber-700 transition-colors"
                            >
                                GitHub
                            </a>
                        </div>
                        <div class="text-center md:text-left text-sm">&copy; {{ date('Y') }} Playsaurus - {{ config('app.name') }} AI Benchmark Platform</div>
                    </div>
                    <div class="flex flex-row items-center justify-center md:justify-end gap-4">
                        <span class="text-xs sm:text-sm text-gray-500">
                            A project made with
                            <x-phosphor-heart-fill class="inline-block w-4 h-4 text-red-500" />
                            by
                        </span>
                        <a href="https://playsaurus.com" target="_blank" class="flex items-center">
                            <img
                                src="{{ asset('images/playsaurus-logo.svg') }}"
                                alt="Playsaurus"
                                class="w-auto h-10 sm:h-14 hover:opacity-80 transition-all hover:drop-shadow-xl"
                            >
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
