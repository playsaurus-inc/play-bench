@props(['model', 'index' => 0 ])

<a href="{{ route('models.show.svg', $model) }}" class="block bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 rounded-xl hover:-translate-y-1">
    <div class="p-6">
        <!-- Header with model name and stats -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="size-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <x-phosphor-robot-fill class="size-6 text-amber-600" />
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $model->name }}</h3>
                    <p class="text-xs text-gray-500">AI Artist</p>
                </div>
            </div>
            @if(isset($model->svg_matches_won_count) && $model->svg_matches_won_count > 0)
                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <x-phosphor-trophy-fill class="size-3.5 mr-1" />
                    {{ $model->svg_matches_won_count }} wins
                </div>
            @endif
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gray-50 rounded-lg p-3 flex flex-col items-center justify-center">
                <span class="text-xs text-gray-500">Matches</span>
                <span class="text-lg font-bold text-amber-700">{{ $model->total_svg_matches ?? 0 }}</span>
            </div>

            <div class="bg-gray-50 rounded-lg p-3 flex flex-col items-center justify-center">
                <span class="text-xs text-gray-500">Win Rate</span>
                <span class="text-lg font-bold {{ isset($model->win_rate) && $model->win_rate > 0.5 ? 'text-green-600' : 'text-gray-700' }}">
                    {{ isset($model->win_rate) ? Number::percentage($model->win_rate * 100, 1) : '0.0%' }}
                </span>
            </div>
        </div>

        <!-- Sample style (visual preview) -->

        @empty($model->svg_samples)
            <div class="h-32 bg-amber-50 rounded-lg overflow-hidden flex gap-3 items-center justify-center p-3 mb-4">
                <div class="flex space-x-2">
                    <!-- Simple geometric shapes to represent the model's style -->
                    <div class="size-12 rounded-full bg-amber-200 border-2 border-amber-300"></div>
                    <div class="size-12 bg-amber-300 transform rotate-45"></div>
                    <div class="size-12 bg-amber-400 transform rotate-12 rounded-sm"></div>
                    <div class="size-12 bg-amber-200 transform -rotate-12 rounded-lg"></div>
                </div>
            </div>
        @else
            <div
                x-data="{
                    urls: {{ json_encode($model->svg_samples) }},
                    samples: [
                        {urlIndex: 0, url: '{{ $model->svg_samples[0] ?? '' }}'},
                        {urlIndex: 1, url: '{{ $model->svg_samples[1] ?? '' }}'},
                        {urlIndex: 2, url: '{{ $model->svg_samples[2] ?? '' }}'},
                    ],
                    sampleIndex: 0,
                    changeInterval: 3000,
                    initialDelay: {{ $index * 1000 }},
                    isPlaying: true,
                    init() {
                        setTimeout(() => this.startAnimation(), this.initialDelay);
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => this.isPlaying = entry.isIntersecting);
                        });
                        observer.observe(this.$el);
                    },
                    startAnimation() {
                        setInterval(() => {
                            if (!this.isPlaying) return;
                            this.sampleIndex = (this.sampleIndex + 1) % this.samples.length;
                            const sample = this.samples[this.sampleIndex];
                            sample.urlIndex = (sample.urlIndex + this.samples.length) % this.urls.length;
                            sample.url = this.urls[sample.urlIndex];
                        }, this.changeInterval);
                    }
                }"
                class="h-32 bg-gray-50 rounded-lg flex gap-3 items-center justify-center p-3 mb-4"
            >
                <template x-for="(sample, index) in samples" :key="index">
                    <div class="flex-shrink-0">
                        <img
                            :src="sample.url"
                            alt="Sample SVG"
                            class="size-30 object-cover rounded-lg shadow-md transition-transform bg-white"
                            x-bind:class="{ 'scale-105': index === sampleIndex }"
                        />
                    </div>
                </template>
            </div>

        @endempty

        <!-- View button -->
        <div class="flex justify-end">
            <div class="text-sm font-medium text-amber-600 hover:text-amber-800 inline-flex items-center">
                View artist profile
                <x-phosphor-arrow-right class="size-4 ml-1" />
            </div>
        </div>
    </div>
</a>
