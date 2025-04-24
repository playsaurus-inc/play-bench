<!-- "How it works" -->
<section class="mt-8">
    <h2 class="text-xl md:text-2xl font-bold mb-4 md:mb-6 text-gray-900 flex items-center">
        <x-phosphor-question-fill class="w-5 h-5 md:w-6 md:h-6 mr-2 text-amber-500" />
        How the Rock Paper Scissors Benchmark Works
    </h2>

    <!-- First row: Basic explanation boxes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6 mb-6 md:mb-8">
        <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-full mb-3 md:mb-4">
                <x-phosphor-strategy-fill class="w-5 h-5 md:w-6 md:h-6 text-red-600" />
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-1 md:mb-2">Strategic AI Competition</h3>
            <p class="text-sm md:text-base text-gray-600">
                AI models compete against each other in a series of Rock Paper Scissors rounds, with full visibility of previous moves to inform their strategy.
            </p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full mb-3 md:mb-4">
                <x-phosphor-brain-fill class="w-5 h-5 md:w-6 md:h-6 text-blue-600" />
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-1 md:mb-2">Pattern Recognition</h3>
            <p class="text-sm md:text-base text-gray-600">
                Models analyze the complete match history to detect patterns and predict their opponent's next choice, showcasing adaptive learning capabilities.
            </p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full mb-3 md:mb-4">
                <x-phosphor-chart-bar-fill class="w-5 h-5 md:w-6 md:h-6 text-green-600" />
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-1 md:mb-2">Insightful Metrics</h3>
            <p class="text-sm md:text-base text-gray-600">
                Each match generates data on win rates, pattern predictability, and strategic adaptation, revealing AI capabilities over time.
            </p>
        </div>
    </div>

    <!-- Game Rules Section -->
    <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-gray-900 flex items-center">
            <x-phosphor-list-checks-fill class="w-4 h-4 md:w-5 md:h-5 mr-2 text-amber-500" />
            Game Rules
        </h3>

        <div class="flex flex-col sm:flex-row justify-start sm:justify-center gap-4 md:gap-6 mb-4 md:mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-rock class="size-5 md:size-6" />
                </div>
                <span class="text-sm md:text-base text-gray-700">Rock crushes Scissors</span>
            </div>

            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-scissors class="size-5 md:size-6" />
                </div>
                <span class="text-sm md:text-base text-gray-700">Scissors cuts Paper</span>
            </div>

            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-paper class="size-5 md:size-6" />
                </div>
                <span class="text-sm md:text-base text-gray-700">Paper covers Rock</span>
            </div>
        </div>

        <p class="text-sm md:text-base text-gray-600">
            Each match typically consists of 50-150 rounds, providing enough data to evaluate the model's strategic capabilities.
            A truly random strategy would result in a win rate close to 33%, but models that can successfully
            detect and exploit patterns in their opponent's moves can achieve significantly higher win rates.
        </p>
    </div>

    <!-- How AI Models Play Section -->
    <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-gray-900 flex items-center">
            <x-phosphor-brain-fill class="w-4 h-4 md:w-5 md:h-5 mr-2 text-amber-500" />
            How AI Models Play
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            <!-- Left column: Description -->
            <div>
                <h4 class="text-base font-medium text-gray-800 mb-2 md:mb-3 flex items-center">
                    <x-phosphor-hand-pointing-fill class="w-4 h-4 mr-2 text-amber-600" />
                    Complete Match Visibility
                </h4>

                <p class="text-sm md:text-base text-gray-600 mb-3 md:mb-4">
                    Models have <strong>full access to all previous rounds</strong> when making each decision. This gives them the opportunity to:
                </p>

                <ul class="list-inside space-y-1 md:space-y-2 text-sm md:text-base text-gray-600 mb-4 md:mb-5">
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <x-phosphor-check class="w-3 h-3 text-amber-800" />
                        </span>
                        <span>
                            <strong>Analyze opponent patterns</strong> from previous moves
                        </span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <x-phosphor-check class="w-3 h-3 text-amber-800" />
                        </span>
                        <span>
                            <strong>Adapt strategies</strong> based on the current score
                        </span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <x-phosphor-check class="w-3 h-3 text-amber-800" />
                        </span>
                        <span>
                            <strong>Employ counter-strategies</strong> when opponents show predictable behavior
                        </span>
                    </li>
                </ul>

                <div class="bg-blue-50 border-l-4 border-blue-300 px-3 md:px-4 py-2 md:py-3 rounded-r mb-4">
                    <div class="flex">
                        <x-phosphor-info-fill class="h-4 w-4 md:h-5 md:w-5 text-blue-500 flex-shrink-0" />
                        <p class="ml-2 md:ml-3 text-xs md:text-sm text-blue-700">
                            <span class="font-semibold">Strategic Depth:</span>
                            Models that effectively learn from game history consistently outperform random strategies.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right column: Example Prompt -->
            <div class="bg-gray-50 p-3 md:p-4 rounded-lg">
                <h4 class="text-base font-medium text-gray-800 mb-2 md:mb-3 flex items-center">
                    <x-phosphor-code-fill class="w-4 h-4 mr-2 text-amber-600" />
                    Real World Example Prompt for an AI Player
                </h4>

                <div class="bg-gray-800 text-gray-200 p-3 md:p-4 rounded-md font-mono text-xs leading-relaxed overflow-auto max-h-40">
                    <span class="text-pink-400">Game:</span> Rock-Paper-Scissors<br>
                    <span class="text-pink-400">You are:</span> player1<br>
                    <span class="text-pink-400">Current Score -</span> Player1: 13, Player2: 7<br>
                    <span class="text-pink-400">Condensed History:</span> 1rs1 2pr2 3sp1 4rs1 5pr2 6sp1 7rs1 8pr2 9sp1 10rs1 11pr2 12sp1 13rs1 14pr2 15sp1 16rs1 17pr2 18sp1 19rs1 20pr2<br>
                    <span class="text-pink-400">Interpretation:</span> Each history token is of the form [round][P1 move][P2 move][result]. 'r' = rock, 'p' = paper, 's' = scissors; result '1' means Player1 wins, '2' means Player2 wins, 'T' means tie.<br>
                    <span class="text-pink-400">Legal moves:</span> rock, paper, scissors<br>
                    <span class="text-pink-400">Please provide your move in JSON format (e.g., {"move":"rock"}).</span>
                </div>

                <p class="text-xs md:text-sm text-gray-600 mt-3 mb-4">
                    Each AI model receives this information before making its next move, allowing for strategic analysis of previous rounds.
                </p>

                <!-- Example JSON response -->
                <div class="flex items-center mb-1 text-sm md:text-base font-medium text-gray-800">
                    <div class="size-8 md:size-10 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                        <x-phosphor-robot-fill class="size-4 md:size-5 text-amber-500" />
                    </div>
                    <span>AI Model Response:</span>
                </div>

                <div class="bg-gray-800 text-gray-200 p-3 rounded-md font-mono text-xs leading-relaxed overflow-auto">
                    <span class="text-pink-400">{"move":"rock"}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Scoring Section -->
    <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-gray-900 flex items-center">
            <x-phosphor-calculator-fill class="w-4 h-4 md:w-5 md:h-5 mr-2 text-amber-500" />
            Match Scoring & Ties
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Left Column -->
            <div class="w-full lg:col-span-3">
                <h4 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                    <x-phosphor-hand-pointing-fill class="w-4 h-4 mr-2 text-amber-600" />
                    How We Pick The Winner
                </h4>

                <p class="text-sm md:text-base text-gray-600 mb-3 md:mb-4">
                    First bot to grab <strong>50 wins</strong> usually wins the match.
                    But if both bots are neck-and-neck we run a quick "is this just luck?" check.
                    If the gap is tiny, we call it a tie so nobody brags without proof.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-300 px-3 md:px-4 py-2 md:py-3 rounded-r mb-4">
                    <div class="flex">
                        <x-phosphor-info-fill class="h-4 w-4 md:h-5 md:w-5 text-blue-500 flex-shrink-0" />
                        <p class="ml-2 md:ml-3 text-xs md:text-sm text-blue-700">
                            <span class="font-semibold">Statistical Tie:</span>
                            Scores different but difference is so small it could be pure coin-flip luck, not real skill.
                        </p>
                    </div>
                </div>

                <!-- 🧑‍🔬 Optional nerd corner -->
                <div x-data="{ open: false }" class="mt-4 md:mt-6">
                    <button
                        @click="open = !open"
                        class="text-sm md:text-base text-gray-600 hover:text-gray-800 flex items-center cursor-pointer"
                    >
                        <x-phosphor-flask-fill class="size-4 md:size-5 mr-2" />
                        Info for Nerds (don't open if scared of math)
                        <x-phosphor-caret-down
                            class="w-3 h-3 md:w-4 md:h-4 ml-1 transform transition-transform"
                            x-bind:class="{ 'rotate-180': open }"
                        />
                    </button>

                    <div x-show="open" x-collapse class="mt-3 md:mt-4 text-xs md:text-sm text-gray-700 leading-relaxed rounded p-3 md:p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <!-- 📝 Short story version -->
                            <div>
                                <p>
                                    We run a <strong>90 % one-sided binomial z-test.</strong>
                                    It answers one question: "Is the score gap big enough that luck
                                    is an unlikely explanation?"
                                </p>

                                <ul class="list-disc list-inside mt-2 md:mt-3 space-y-1">
                                    <li><em>Decisive rounds only.</em> Ties don't help us judge skill.</li>
                                    <li>If the gap is below the cut-off, we say "statistical tie."</li>
                                    <li>If the gap beats the cut-off, we say the leader showed real skill.</li>
                                </ul>

                                <a
                                    href="{{ config('playbench.github_repo_url') }}"
                                    target="_blank"
                                    class="mt-3 md:mt-4 inline-flex items-center text-xs font-medium text-amber-600 hover:text-amber-500 hover:underline"
                                    rel="noopener noreferrer"
                                >
                                    <x-phosphor-github-logo-fill class="w-3 h-3 md:w-4 md:h-4 mr-1" />
                                    Full implementation lives in the GitHub repo.
                                </a>
                            </div>

                            <!-- 📐 Exact math, as compact as possible -->
                            <div class="text-xs md:text-sm">
                                <p><strong>Hypotheses</strong></p>
                                <p>
                                    H₀: winner win rate = 0.5 (no skill)<br>
                                    H₁: winner win rate > 0.5 (skill)
                                </p>

                                <p class="mt-2 md:mt-3"><strong>Statistical Model</strong></p>
                                <p>
                                    n = decisive rounds<br>
                                    X ~ Binomial(n, 0.5) = winner's wins<br>
                                    z = (X / n − 0.5) / √(0.25 / n)
                                </p>

                                <p class="mt-2 md:mt-3"><strong>Decision rule (α = 0.05, one-sided)</strong></p>
                                <p>
                                    z > 1.64 ⇒ we reject H₀ ⇒ declare skill<br>
                                    Otherwise ⇒ call it statistical tie
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-full lg:col-span-2">
                <h4 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                    <x-phosphor-sigma-fill class="w-4 h-4 mr-2 text-amber-600" />
                    How Big Is "Big Enough"?
                </h4>

                <p class="text-sm md:text-base text-gray-600 mb-3">
                    Rough guide for what counts as a decisive win (ties don't count):
                </p>

                <ul class="space-y-2 mb-4">
                    <li class="flex items-center p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full mr-3 flex-shrink-0">
                            <span class="text-amber-800 text-sm font-medium">50</span>
                        </div>
                        <div>
                            <span class="text-sm md:text-base text-gray-800"><strong>50 rounds:</strong></span>
                            <span class="text-sm md:text-base text-gray-600"> need about <strong>14-point</strong> lead</span>
                        </div>
                    </li>

                    <li class="flex items-center p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full mr-3 flex-shrink-0">
                            <span class="text-amber-800 text-sm font-medium">100</span>
                        </div>
                        <div>
                            <span class="text-sm md:text-base text-gray-800"><strong>100 rounds:</strong></span>
                            <span class="text-sm md:text-base text-gray-600"> need about <strong>20-point</strong> lead</span>
                        </div>
                    </li>

                    <li class="flex items-center p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full mr-3 flex-shrink-0">
                            <span class="text-amber-800 text-sm font-medium">150</span>
                        </div>
                        <div>
                            <span class="text-sm md:text-base text-gray-800"><strong>150 rounds:</strong></span>
                            <span class="text-sm md:text-base text-gray-600"> need about <strong>24-point</strong> lead</span>
                        </div>
                    </li>
                </ul>

                <p class="text-xs md:text-sm text-gray-500 italic">
                    Bigger match → we demand a bigger gap before declaring a winner
                </p>
            </div>
        </div>
    </div>

    <!-- ELO Ratings Explanation -->
    <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-gray-900 flex items-center">
            <x-phosphor-chart-line-up-fill class="w-4 h-4 md:w-5 md:h-5 mr-2 text-amber-500" />
            Understanding ELO Ratings
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Left Column: Description -->
            <div>
                <p class="text-sm md:text-base text-gray-600 mb-3">
                    ELO ratings provide a more sophisticated measure of performance than simple win rates:
                </p>

                <ul class="space-y-2 mb-4">
                    <li class="flex items-start p-2 bg-gray-50 rounded-lg">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-bold">1</span>
                        </span>
                        <div>
                            <span class="text-sm md:text-base text-gray-800 font-medium">Opponent strength matters</span>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">Beating strong models earns more points than beating weak ones</p>
                        </div>
                    </li>

                    <li class="flex items-start p-2 bg-gray-50 rounded-lg">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-bold">2</span>
                        </span>
                        <div>
                            <span class="text-sm md:text-base text-gray-800 font-medium">Statistical ties handled well</span>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">Close matches don't add noise to the rankings</p>
                        </div>
                    </li>

                    <li class="flex items-start p-2 bg-gray-50 rounded-lg">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-bold">3</span>
                        </span>
                        <div>
                            <span class="text-sm md:text-base text-gray-800 font-medium">Progressive adjustment</span>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">Rankings evolve as models play more matches</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Right Column: Example -->
            <div class="col-span-1 lg:col-span-2">
                <div class="bg-gray-50 p-3 md:p-4 rounded-lg">
                    <div class="w-full max-w-md mx-auto">
                        <!-- Simple ELO calculation example -->
                        <div class="bg-white rounded-lg p-3 md:p-4 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between mb-3 md:mb-4 pb-2 md:pb-3 border-b border-gray-100">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                                        <x-phosphor-robot-fill class="w-4 h-4 md:w-5 md:h-5 text-amber-500" />
                                    </div>
                                    <div>
                                        <div class="text-xs md:text-sm font-medium">Model A</div>
                                        <div class="text-xs text-gray-500">Higher ranked</div>
                                    </div>
                                </div>

                                <div class="text-lg md:text-xl font-bold text-amber-600">
                                    VS
                                </div>

                                <div class="flex items-center">
                                    <div>
                                        <div class="text-xs md:text-sm font-medium text-right">Model B</div>
                                        <div class="text-xs text-gray-500 text-right">Lower ranked</div>
                                    </div>
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-blue-100 flex items-center justify-center ml-2">
                                        <x-phosphor-robot-fill class="w-4 h-4 md:w-5 md:h-5 text-blue-500" />
                                    </div>
                                </div>
                            </div>

                            <!-- Before match -->
                            <div class="mb-3 md:mb-4">
                                <div class="text-xs md:text-sm font-medium text-gray-700 mb-2">Before Match:</div>
                                <div class="grid grid-cols-2 gap-3 md:gap-4">
                                    <div class="bg-gray-50 p-2 rounded text-center">
                                        <div class="text-base md:text-lg font-bold text-amber-600">1050</div>
                                        <div class="text-xs text-gray-500">Model A's ELO</div>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded text-center">
                                        <div class="text-base md:text-lg font-bold text-blue-600">950</div>
                                        <div class="text-xs text-gray-500">Model B's ELO</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Match result -->
                            <div class="relative mb-4 md:mb-6 mt-4 md:mt-6">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center">
                                    <span class="px-2 md:px-3 bg-white text-xs md:text-sm text-gray-500">Surprise! Model B wins</span>
                                </div>
                            </div>

                            <!-- After match with points change -->
                            <div>
                                <div class="text-xs md:text-sm font-medium text-gray-700 mb-2">After Match:</div>
                                <div class="grid grid-cols-2 gap-3 md:gap-4">
                                    <div class="bg-gray-50 p-2 rounded text-center relative">
                                        <div class="text-base md:text-lg font-bold text-amber-600">1030</div>
                                        <div class="text-xs text-gray-500">Model A's ELO</div>
                                        <div class="absolute -top-2 -right-2 w-6 h-6 md:w-8 md:h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-600 border-2 border-white">
                                            -20
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded text-center relative">
                                        <div class="text-base md:text-lg font-bold text-blue-600">970</div>
                                        <div class="text-xs text-gray-500">Model B's ELO</div>
                                        <div class="absolute -top-2 -right-2 w-6 h-6 md:w-8 md:h-8 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-600 border-2 border-white">
                                            +20
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Explanation -->
                            <div class="mt-3 md:mt-4 text-xs md:text-sm text-gray-600 bg-amber-50 p-2 rounded">
                                <p class="text-center">If a <strong>weaker model</strong> beats a <strong>stronger model</strong>, it gains <strong>more points</strong> than expected!</p>
                            </div>
                        </div>

                        <p class="text-xs text-center text-gray-500 mt-2">
                            ELO ratings increase more when you beat a stronger opponent and decrease more when you lose to a weaker one
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 md:mt-6 flex justify-center">
        <x-ui.button href="{{ route('models.index') }}" variant="outline">
            <x-phosphor-robot-fill class="size-3 md:size-4 mr-2 md:mr-3" />
            Browse AI Models
        </x-ui.button>
    </div>
</section>
