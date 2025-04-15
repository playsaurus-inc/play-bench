<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'GameArena') : config('app.name', 'GameArena') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-amber-50/20 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white border-b border-amber-100 shadow-sm sticky top-0 z-10">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center text-xl font-bold text-amber-600 hover:text-amber-700 transition-colors">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-sm mr-3">
                                    <x-phosphor-game-controller-fill class="w-6 h-6 text-white" />
                                </div>
                                {{ config('app.name', 'GameArena') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:ml-10 sm:flex">
                            <a href="{{ route('rps.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('rps.*') ? 'border-amber-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-amber-300' }} transition-colors duration-200">
                                <x-phosphor-hand-fill class="w-5 h-5 mr-1 {{ request()->routeIs('rps.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                                Rock Paper Scissors
                            </a>
                            <a href="{{ route('rps.models.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('models.*') ? 'border-amber-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-amber-300' }} transition-colors duration-200">
                                <x-phosphor-robot-fill class="w-5 h-5 mr-1 {{ request()->routeIs('models.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                                AI Models
                            </a>
                        </div>
                    </div>

                    <!-- Right side menu -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        <a href="#" class="text-gray-500 hover:text-amber-600 transition-colors">
                            <x-phosphor-question-fill class="w-5 h-5" />
                        </a>
                        <a href="#" class="text-gray-500 hover:text-amber-600 transition-colors">
                            <x-phosphor-gear-six-fill class="w-5 h-5" />
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                x-data="{open: false}"
                                @click="open = !open"
                                :aria-expanded="open">
                            <span class="sr-only">Open main menu</span>
                            <x-phosphor-list x-show="!open" class="h-6 w-6" />
                            <x-phosphor-x x-show="open" class="h-6 w-6" x-cloak />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="sm:hidden" x-data="{open: false}" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" x-cloak>
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('rps.index') }}" class="flex items-center px-3 py-2 {{ request()->routeIs('rps.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}">
                        <x-phosphor-hand-fill class="w-5 h-5 mr-3 {{ request()->routeIs('rps.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        Rock Paper Scissors
                    </a>
                    <a href="{{ route('rps.models.index') }}" class="flex items-center px-3 py-2 {{ request()->routeIs('models.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}">
                        <x-phosphor-robot-fill class="w-5 h-5 mr-3 {{ request()->routeIs('models.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                        AI Models
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
            <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-amber-500 to-amber-600 rounded-md shadow-sm mr-2">
                            <x-phosphor-game-controller-fill class="w-4 h-4 text-white" />
                        </div>
                        <span class="font-semibold text-amber-600">{{ config('app.name', 'GameArena') }}</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <div class="flex space-x-4 mb-2 justify-center md:justify-end">
                            <a href="#" class="text-gray-400 hover:text-amber-600 transition-colors">About</a>
                            <a href="#" class="text-gray-400 hover:text-amber-600 transition-colors">Documentation</a>
                            <a href="#" class="text-gray-400 hover:text-amber-600 transition-colors">GitHub</a>
                        </div>
                        <div class="text-center md:text-right">&copy; {{ date('Y') }} {{ config('app.name', 'GameArena') }} - AI Benchmark Platform</div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine JS initialization -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobileMenu', () => ({
                open: false,
                toggle() {
                    this.open = !this.open
                }
            }))
        })
    </script>
</body>
</html>
