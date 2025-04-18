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
        <!-- Navigation -->
        <nav
            class="bg-white border-b border-amber-100 shadow-sm sticky top-0 z-10"
            x-data="{mobileMenuOpen: false, gamesMenuOpen: false}"
            @keydown.escape="mobileMenuOpen = false; gamesMenuOpen = false"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Brand (always visible) -->
                    <div class="flex items-center flex-shrink-0">
                        <a href="{{ url('/') }}" class="flex items-center text-xl font-bold text-amber-600 hover:text-amber-700 transition-colors">
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-sm mr-3">
                                <x-phosphor-game-controller-fill class="w-6 h-6 text-white" />
                            </div>
                            <span>{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <!-- Medium Screen Navigation (md screens only) -->
                    <div class="hidden md:flex xl:hidden items-center space-x-4">
                        <a href="{{ route('models.index') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('models.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-robot-fill class="size-5 mr-2 {{ request()->routeIs('models.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                            <span>AI Models</span>
                        </a>

                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button
                                @click="open = !open"
                                class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                                :class="{ 'bg-gray-50': open }"
                            >
                                <x-phosphor-game-controller-fill class="size-5 mr-2 text-gray-400" />
                                <span>Games</span>
                                <x-phosphor-caret-down class="ml-1 w-4 h-4 transition-transform" x-bind:class="{'rotate-180': open}" />
                            </button>

                            <!-- Games Dropdown Menu -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-2"
                                class="absolute right-0 z-10 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-100"
                                x-cloak
                            >
                                <div class="py-2">
                                    <a
                                        href="{{ route('rps.index') }}"
                                        @click="open = false"
                                        class="flex items-center px-4 py-3 {{ request()->routeIs('rps.*') ? 'bg-amber-50 text-amber-700' : 'hover:bg-gray-50' }} transition-colors"
                                    >
                                        <x-phosphor-hand-fill class="w-5 h-5 mr-3 {{ request()->routeIs('rps.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                                        <div class="flex flex-col">
                                            <span class="font-medium">Rock Paper Scissors</span>
                                            <span class="text-xs text-gray-500">Strategic game benchmark</span>
                                        </div>
                                    </a>

                                    <a
                                        href="javascript:void"
                                        @click="open = false"
                                        class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors"
                                    >
                                        <x-phosphor-paint-brush-fill class="w-5 h-5 mr-3 text-gray-400" />
                                        <div class="flex flex-col">
                                            <div class="flex items-center">
                                                <span class="font-medium">SVG Drawing</span>
                                                <span class="ml-2 text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded">Coming soon</span>
                                            </div>
                                            <span class="text-xs text-gray-500">Visual creativity benchmark</span>
                                        </div>
                                    </a>

                                    <a
                                        href="javascript:void"
                                        @click="open = false"
                                        class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors"
                                    >
                                        <x-phosphor-crown-cross-fill class="w-5 h-5 mr-3 text-gray-400" />
                                        <div class="flex flex-col">
                                            <div class="flex items-center">
                                                <span class="font-medium">Chess</span>
                                                <span class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded">Coming soon</span>
                                            </div>
                                            <span class="text-xs text-gray-500">Strategic thinking benchmark</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('about') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('about') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-question-fill class="size-5 mr-2 {{ request()->routeIs('about') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>About</span>
                        </a>
                    </div>

                    <!-- Large Screen Navigation (xl screens and up) -->
                    <div class="hidden xl:flex items-center space-x-1 xl:space-x-2">
                        <a href="{{ route('models.index') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('models.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-robot-fill class="size-5 mr-2 {{ request()->routeIs('models.*') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>AI Models</span>
                        </a>
                        <a href="{{ route('rps.index') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('rps.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-hand-fill class="size-5 mr-2 {{ request()->routeIs('rps.*') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>Rock Paper Scissors</span>
                        </a>
                        <a href="javascript:void" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('svg.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-paint-brush-fill class="size-5 mr-2 {{ request()->routeIs('svg.*') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>SVG Drawing</span>
                            <span class="ml-2 text-xs px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded">Coming soon</span>
                        </a>
                        <a href="javascript:void" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('chess.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-crown-cross-fill class="size-5 mr-2 {{ request()->routeIs('chess.*') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>Chess</span>
                            <span class="ml-2 text-xs px-1.5 py-0.5 bg-green-100 text-green-800 rounded">Coming soon</span>
                        </a>
                        <a href="{{ route('about') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('about') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                            <x-phosphor-question-fill class="size-5 mr-2 {{ request()->routeIs('about') ? 'text-amber-700' : 'text-gray-400' }}" />
                            <span>About</span>
                        </a>
                    </div>

                    <!-- Mobile menu button (visible on sm and smaller screens) -->
                    <div class="flex items-center md:hidden">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-amber-500"
                            x-on:click="mobileMenuOpen = !mobileMenuOpen"
                            :aria-expanded="mobileMenuOpen"
                            aria-label="Toggle menu"
                        >
                            <span class="sr-only">Open main menu</span>
                            <x-phosphor-list x-show="!mobileMenuOpen" class="h-6 w-6" />
                            <x-phosphor-x x-show="mobileMenuOpen" class="h-6 w-6" x-cloak />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu (sm and smaller screens) -->
            <div
                class="md:hidden"
                x-show="mobileMenuOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
            >
                <div class="pt-2 pb-3 space-y-1 border-t border-gray-100 bg-white shadow-lg">
                    <a
                        href="{{ route('models.index') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('models.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                        x-on:click="mobileMenuOpen = false"
                    >
                        <x-phosphor-robot-fill class="w-5 h-5 mr-3 {{ request()->routeIs('models.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        <span class="font-medium">AI Models</span>
                    </a>

                    <a
                        href="{{ route('rps.index') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('rps.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                        x-on:click="mobileMenuOpen = false"
                    >
                        <x-phosphor-hand-fill class="w-5 h-5 mr-3 {{ request()->routeIs('rps.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        <span class="font-medium">Rock Paper Scissors</span>
                    </a>

                    <a
                        href="javascript:void"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('svg.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                        x-on:click="mobileMenuOpen = false"
                    >
                        <x-phosphor-paint-brush-fill class="w-5 h-5 mr-3 {{ request()->routeIs('svg.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        <span class="font-medium">SVG Drawing</span>
                        <span class="ml-2 text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded">Coming soon</span>
                    </a>

                    <a
                        href="javascript:void"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('chess.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                        x-on:click="mobileMenuOpen = false"
                    >
                        <x-phosphor-crown-cross-fill class="w-5 h-5 mr-3 {{ request()->routeIs('chess.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        <span class="font-medium">Chess</span>
                        <span class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded">Coming soon</span>
                    </a>

                    <a
                        href="{{ route('about') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('about') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                        x-on:click="mobileMenuOpen = false"
                    >
                        <x-phosphor-question-fill class="w-5 h-5 mr-3 {{ request()->routeIs('about') ? 'text-amber-500' : 'text-gray-400' }}" />
                        <span class="font-medium">About {{ config('app.name') }}</span>
                    </a>
                </div>
            </div>
        </nav>

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
