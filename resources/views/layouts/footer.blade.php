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
