<x-layouts::app :title="$model->name . ' - Chess'">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

    <!-- Coming Soon -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                <x-phosphor-crown-cross-fill class="w-12 h-12 text-gray-400" />
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-3">Chess Benchmark Coming Soon</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            The Chess benchmark is currently in development and will be available soon. This benchmark will test
            AI models on their ability to play chess, evaluating their strategic thinking, planning skills, and
            positional understanding.
        </p>

        <x-ui.button :href="route('models.show', $model)" variant="secondary">
            <x-phosphor-arrow-left class="w-4 h-4 mr-2" />
            Return to Overview
        </x-ui.button>
    </div>
</x-layouts::app>
