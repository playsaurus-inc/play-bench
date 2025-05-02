<!-- Navigation -->
<nav
    class="z-50 bg-white border-b border-amber-100 shadow-sm sticky top-0"
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
                                href="{{ route('svg.index') }}"
                                @click="open = false"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors"
                            >
                                <x-phosphor-paint-brush-fill class="w-5 h-5 mr-3 text-gray-400" />
                                <div class="flex flex-col">
                                    <span class="font-medium">SVG Drawing</span>
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
                <a href="{{ route('svg.index') }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('svg.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                    <x-phosphor-paint-brush-fill class="size-5 mr-2 {{ request()->routeIs('svg.*') ? 'text-amber-700' : 'text-gray-400' }}" />
                    <span>SVG Drawing</span>
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
                href="{{ route('svg.index') }}"
                class="flex items-center px-4 py-3 {{ request()->routeIs('svg.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}"
                x-on:click="mobileMenuOpen = false"
            >
                <x-phosphor-paint-brush-fill class="w-5 h-5 mr-3 {{ request()->routeIs('svg.*') ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="font-medium">SVG Drawing</span>
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
