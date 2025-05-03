<div class="flex flex-wrap gap-2 mb-6">
    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'date_desc'])"
        :variant="request('sort', 'date_desc') === 'date_desc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-clock-fill class="size-5 mr-2" />
        Latest
    </x-ui.button>

    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'date_asc'])"
        :variant="request('sort') === 'date_asc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-clock-counter-clockwise-fill class="size-5 mr-2" />
        Oldest
    </x-ui.button>

    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'complexity'])"
        :variant="request('sort') === 'complexity' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-graph-fill class="size-5 mr-2" />
        Most Complex
    </x-ui.button>

    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'animations'])"
        :variant="request('sort') === 'animations' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-pulse-fill class="size-5 mr-2" />
        Most Animated
    </x-ui.button>

    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'text'])"
        :variant="request('sort') === 'text' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-text-t-fill class="size-5 mr-2" />
        Most Textual
    </x-ui.button>

    <x-ui.button
        :href="route('svg.matches.index', ['sort' => 'gradients'])"
        :variant="request('sort') === 'gradients' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-paint-bucket-fill class="size-5 mr-2" />
        Most Gradients
    </x-ui.button>
</div>
