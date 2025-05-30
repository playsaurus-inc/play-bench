@props(['model'])

<div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex flex-col md:flex-row md:items-center">
        <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-amber-100 border-4 border-white shadow-lg flex items-center justify-center">
                <x-phosphor-robot-fill class="w-8 h-8 text-amber-500" />
            </div>
        </div>

        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2">
                {{ $model->name }}
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                Showing all matches involving this AI model
            </p>

            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <x-phosphor-trophy-fill class="w-3.5 h-3.5 mr-1" />
                    {{ $model->rpsMatchesWon()->count() }} wins
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <x-phosphor-x-circle-fill class="w-3.5 h-3.5 mr-1" />
                    {{ $model->rpsMatchesLost()->count() }} losses
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <x-phosphor-equals-fill class="w-3.5 h-3.5 mr-1" />
                    {{ $model->rpsMatches()->whereNull('winner_id')->count() }} ties
                </span>
            </div>
        </div>

        <div class="mt-4 md:mt-0 md:ml-auto">
            <x-ui.button :href="route('models.show.rps', $model)" variant="secondary" size="sm">
                <x-phosphor-chart-bar-fill class="size-4 mr-1" />
                View Detailed Analysis
            </x-ui.button>
        </div>
    </div>
</div>
