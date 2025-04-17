@props(['model'])

<a href="{{ route('models.show.rps', $model) }}"
   class="block bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 rounded-xl"
   x-data="{ hover: false }"
   @mouseenter="hover = true"
   @mouseleave="hover = false">
    <div class="relative p-6">
        <div class="absolute top-4 right-4">
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $model->rps_rank <= 3 ? 'bg-amber-100 border-2 border-amber-200' : 'bg-gray-100 border border-gray-200' }}">
                <span class="{{ $model->rps_rank <= 3 ? 'text-amber-800' : 'text-gray-600' }} text-sm font-bold">{{ $model->rps_rank }}</span>
            </div>
        </div>

        <div class="flex flex-col items-center">
            <!-- Avatar -->
            <div class="mb-4">
                <div class="w-20 h-20 rounded-xl bg-amber-100 border-4 border-white shadow-md flex items-center justify-center">
                    <x-phosphor-robot-fill class="w-10 h-10 text-amber-500" />
                </div>
            </div>

            <!-- Name and stats -->
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $model->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $model->description ?? 'AI Model' }}</p>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900">{{ $model->total_rps_matches }}</div>
                        <div class="text-xs text-gray-500">Matches</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold {{ $model->win_rate > 0.5 ? 'text-green-600' : ($model->win_rate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ Number::percentage($model->win_rate * 100, 1) }}
                        </div>
                        <div class="text-xs text-gray-500">Win Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-amber-600">{{ $model->rps_matches_won_count }}</div>
                        <div class="text-xs text-gray-500">Wins</div>
                    </div>
                </div>

                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-4">
                    <div class="h-2 rounded-full {{ $model->win_rate > 0.5 ? 'bg-green-500' : ($model->win_rate == 0.5 ? 'bg-amber-500' : 'bg-red-500') }}"
                         x-data="{width: 0}"
                         x-init="setTimeout(() => width = {{ $model->win_rate * 100 }}, 100)"
                         :style="`width: ${width}%`"
                         style="transition: width 1s ease-out;"></div>
                </div>

                <div class="flex items-center justify-center text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors">
                    View detailed performance
                    <x-phosphor-arrow-right class="ml-1 h-4 w-4 transition-transform" x-bind:class="{'transform translate-x-0.5': hover}" />
                </div>
            </div>
        </div>
    </div>
</a>
