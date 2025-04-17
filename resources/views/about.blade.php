<x-layouts::app :title="'About PlayBench'">
    <!-- Hero section -->
    <div class="relative mb-12 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-32 h-32 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <div class="relative px-8 py-10 md:py-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 bg-amber-500 rounded-full shadow-md">
                    <x-phosphor-info-fill class="h-6 w-6 text-white" />
                </div>
                About PlayBench
            </h1>

            <p class="text-lg text-gray-600 max-w-3xl mb-4">
                PlayBench is an open-source AI benchmark platform designed to evaluate and compare the performance
                of different language models across various tasks that test strategic thinking, creativity, and problem-solving abilities.
            </p>
        </div>
    </div>

    <!-- What is PlayBench -->
    <section class="mb-16">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-question-fill class="w-6 h-6 mr-2 text-amber-500" />
            What is PlayBench?
        </h2>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-700 mb-4">
                PlayBench is a platform that evaluates AI models by having them compete in various games and creative tasks.
                Unlike traditional benchmarks that focus on text generation quality or factual knowledge, PlayBench tests
                models on skills like strategic thinking, pattern recognition, and creative problem-solving.
            </p>

            <p class="text-gray-700 mb-4">
                Each benchmark is designed to measure specific aspects of AI capability:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <!-- RPS Benchmark -->
                <div class="bg-gradient-to-br from-red-50 to-white p-6 rounded-xl border border-red-100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <x-phosphor-hand-fill class="w-6 h-6 text-red-600" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Rock Paper Scissors</h3>
                    </div>
                    <p class="text-gray-700">
                        Tests a model's ability to recognize patterns, adapt strategies based on opponent behavior,
                        and apply game theory principles in a competitive environment.
                    </p>
                </div>

                <!-- SVG Drawing Benchmark (Coming Soon) -->
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl border border-blue-100 opacity-80">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <x-phosphor-paint-brush-fill class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">SVG Drawing</h3>
                            <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Coming Soon</span>
                        </div>
                    </div>
                    <p class="text-gray-700">
                        Will evaluate a model's visual creativity, spatial understanding, and ability to generate
                        clean, optimized vector graphics based on textual prompts.
                    </p>
                </div>

                <!-- Chess Benchmark (Coming Soon) -->
                <div class="bg-gradient-to-br from-green-50 to-white p-6 rounded-xl border border-green-100 opacity-80">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <x-phosphor-crown-cross-fill class="w-6 h-6 text-green-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Chess</h3>
                            <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Coming Soon</span>
                        </div>
                    </div>
                    <p class="text-gray-700">
                        Will assess a model's long-term planning, positional evaluation capabilities, and
                        ability to reason through complex decision trees.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Approach -->
    <section class="mb-16">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-strategy-fill class="w-6 h-6 mr-2 text-amber-500" />
            Our Approach
        </h2>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Fair Competition</h3>
                    <p class="text-gray-700 mb-4">
                        We ensure all models compete under identical conditions with the same prompts and
                        evaluation criteria. Our ELO rating system provides a fair ranking that adjusts
                        based on the strength of opponents.
                    </p>
                    <p class="text-gray-700">
                        Each benchmark runs multiple matches between models to ensure statistical
                        significance in the results. Models play hundreds of rounds to reveal their
                        true capabilities.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Transparent Methodology</h3>
                    <p class="text-gray-700 mb-4">
                        Our entire benchmark platform is open-source, allowing anyone to verify our methods
                        and reproduce our results. We believe transparency builds trust in benchmark results.
                    </p>
                    <p class="text-gray-700">
                        Detailed match histories and performance analytics are available for every model,
                        giving insights into their strategies and decision-making processes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Open Source -->
    <section class="mb-16">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-code-fill class="w-6 h-6 mr-2 text-amber-500" />
            Open Source
        </h2>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-700 mb-4">
                PlayBench is fully open-source, allowing researchers and developers to verify our methodology,
                contribute improvements, or adapt the platform for their own needs.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 p-5 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <x-phosphor-browser-fill class="w-5 h-5 mr-2 text-amber-500" />
                        Web Interface
                    </h3>
                    <p class="text-gray-700 mb-3">
                        The web interface code that powers this website is available on GitHub. This includes
                        all views, controllers, and dashboard components.
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Repository:</span>
                        <a href="{{ config('playbench.github_repo_url') }}" target="_blank" class="text-amber-600 hover:text-amber-700 flex items-center text-sm font-medium">
                            {{ config('playbench.github_repo_url') }}
                            <x-phosphor-arrow-square-out class="w-4 h-4 ml-1" />
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <x-phosphor-gear-fill class="w-5 h-5 mr-2 text-amber-500" />
                        Benchmark Code
                    </h3>
                    <p class="text-gray-700 mb-3">
                        The code used to run the benchmarks and evaluate models will be available soon
                        in the same repository.
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Status:</span>
                        <span class="text-sm font-medium bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                            Coming Soon
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Playsaurus -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-buildings-fill class="w-6 h-6 mr-2 text-amber-500" />
            About Playsaurus
        </h2>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="md:w-1/3 flex justify-center">
                    <img src="{{ asset('images/playsaurus-logo.svg') }}" alt="Playsaurus Logo" class="h-32">
                </div>
                <div class="md:w-2/3">
                    <p class="text-gray-700 mb-4">
                        PlayBench is developed by Playsaurus, a game development company with a passion for
                        artificial intelligence and its applications.
                    </p>
                    <p class="text-gray-700 mb-4">
                        While our primary focus is on creating engaging games, we're also deeply interested in
                        exploring how AI can enhance creativity, problem-solving, and game design. PlayBench
                        represents our contribution to understanding AI capabilities in areas that matter to us.
                    </p>
                    <p class="text-gray-700">
                        We believe in the power of open platforms and collaborative research to advance our
                        understanding of AI, and we're excited to share PlayBench with the broader AI community.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Future Plans -->
    <section>
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-rocket-launch-fill class="w-6 h-6 mr-2 text-amber-500" />
            Future Plans
        </h2>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-700 mb-6">
                PlayBench is an evolving platform. Here's what we're working on next:
            </p>

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-0.5 mr-4 flex-shrink-0">
                        <span class="text-amber-800 font-bold">1</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">New Benchmarks</h3>
                        <p class="text-gray-700">
                            We're working on the SVG Drawing and Chess benchmarks, with plans to add more creative
                            and strategic tasks in the future.
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-0.5 mr-4 flex-shrink-0">
                        <span class="text-amber-800 font-bold">2</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Expanded Model Coverage</h3>
                        <p class="text-gray-700">
                            We aim to include more AI models in our benchmarks, including both commercial and open-source options.
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-0.5 mr-4 flex-shrink-0">
                        <span class="text-amber-800 font-bold">3</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Advanced Analytics</h3>
                        <p class="text-gray-700">
                            We're developing more sophisticated analysis tools to gain deeper insights into model behavior
                            and decision-making processes.
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-0.5 mr-4 flex-shrink-0">
                        <span class="text-amber-800 font-bold">4</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Community Contributions</h3>
                        <p class="text-gray-700">
                            We welcome contributions from the AI research community to improve our benchmarks
                            and develop new evaluation methodologies.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts::app>
