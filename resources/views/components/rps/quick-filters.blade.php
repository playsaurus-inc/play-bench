<div class="flex flex-wrap gap-3 mb-6">
    <x-ui.button
        :href="route('rps.matches.index', ['match_type' => 'close'])"
        :variant="request('match_type') === 'close' ? 'primary' : 'outline'"
        size="sm"
    >
        <x-phosphor-arrows-in-fill class="size-4 mr-1" />
        Close Matches
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'rounds'])"
        :variant="request('sort') === 'rounds' ? 'primary' : 'outline'"
        size="sm"
    >
        <x-phosphor-timer-fill class="size-4 mr-1" />
        Most Rounds
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['winner' => 'tie'])"
        :variant="request('winner') === 'tie' ? 'primary' : 'outline'"
        size="sm"
    >
        <x-phosphor-equals-fill class="size-4 mr-1" />
        Tied Matches
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'date_desc'])"
        :variant="request('sort', 'date_desc') === 'date_desc' && !request()->hasAny(['winner', 'model', 'match_type']) ? 'primary' : 'outline'"
        size="sm"
    >
        <x-phosphor-clock-fill class="size-4 mr-1" />
        Latest Matches
    </x-ui.button>
</div>
