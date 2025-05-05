<div class="flex flex-wrap gap-2 mb-6">
    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'rounds_desc'])"
        :variant="request('sort') === 'rounds_desc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-timer-fill class="size-4 mr-1" />
        Most Rounds
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'rounds_asc'])"
        :variant="request('sort') === 'rounds_asc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-timer-fill class="size-4 mr-1" />
        Fewest Rounds
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'date_desc'])"
        :variant="request('sort', 'date_desc') === 'date_desc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-clock-fill class="size-4 mr-1" />
        Latest Matches
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'date_asc'])"
        :variant="request('sort') === 'date_asc' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-clock-fill class="size-4 mr-1" />
        Oldest Matches
    </x-ui.button>
</div>
