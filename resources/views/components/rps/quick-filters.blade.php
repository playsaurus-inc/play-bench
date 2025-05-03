<div class="flex flex-wrap gap-2 mb-6">
    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'rounds'])"
        :variant="request('sort') === 'rounds' ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-timer-fill class="size-4 mr-1" />
        Most Rounds
    </x-ui.button>

    <x-ui.button
        :href="route('rps.matches.index', ['sort' => 'date_desc'])"
        :variant="request('sort', 'date_desc') === 'date_desc' && !request()->hasAny(['winner', 'model', 'contender']) ? 'primary' : 'outline'"
        size="sm"
        class="!px-3 !py-1.5"
    >
        <x-phosphor-clock-fill class="size-4 mr-1" />
        Latest Matches
    </x-ui.button>
</div>
