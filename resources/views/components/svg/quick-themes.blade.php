@php

$themes = collect([
   'floating' => 516,
   'wearing' => 331,
   'under' => 309,
   'jellyfish' => 253,
   'sky' => 251,
   'clock' => 251,
   'juggling' => 234,
   'giraffe' => 205,
   'cityscape' => 204,
   'octopus' => 202,
   'starry' => 197,
   'melting' => 168,
   'city' => 167,
   'cat' => 164,
   'giant' => 161,
   'rainbow' => 157,
   'planets' => 155,
   'style' => 148,
   'hat' => 148,
   'surreal' => 144,
   'playing' => 137,
   'neon' => 131,
   'glowing' => 120,
   'moon' => 119,
   'snail' => 116,
   'desert' => 113,
   'space' => 111,
   'stars' => 102,
   'through' => 99,
   'geometric' => 91,
   'night' => 90,
   'cactus' => 87,
   'moonlit' => 85,
   'teacup' => 81,
   'cosmic' => 79,
   'shell' => 78,
   'flamingo' => 78,
   'tiny' => 76,
   'dancing' => 75,
   'tea' => 73,
   'fish' => 73,
   'flying' => 72,
   'sea' => 71,
   'skyline' => 63,
   'gears' => 62,
   'underwater' => 61,
   'galaxy' => 61,
   'crescent' => 60,
   'wings' => 59,
   'disco' => 58,
   'clockwork' => 58,
   'tree' => 57,
   'clouds' => 57,
   'balancing' => 57,
   'unicycle' => 56,
   'spacesuit' => 55,
   'sipping' => 55,
   'island' => 55,
   'umbrella' => 53,
   'sunset' => 53,
   'astronaut' => 53,
   'futuristic' => 51,
   'steampunk' => 50,
   'ocean' => 49,
   'penguin' => 48,
   'ball' => 47,
   'turtle' => 46,
   'whale' => 44,
   'tightrope' => 44,
   'helmet' => 44,
   'colorful' => 44,
   'suit' => 41,
   'chess' => 38,
   'swirling' => 37,
   'riding' => 37,
   'ice' => 37,
   'monocle' => 36,
   'face' => 36,
   'tuxedo' => 35,
   'tower' => 35,
   'surfing' => 35,
   'lily' => 35,
   'numbers' => 34,
   'violin' => 33,
   'butterfly' => 33,
   'balloon' => 33,
   'skyscraper' => 32,
   'landscape' => 32,
   'colored' => 32,
   'wave' => 31,
   'roller' => 31,
   'forest' => 31,
   'surrealist' => 30,
   'pouring' => 30,
   'bioluminescent' => 30,
   'swimming' => 29,
   'sunglasses' => 29,
   'owl' => 29,
   'coral' => 29,
   'art' => 29,
   'teacups' => 28,
   'musical' => 28,
   'inside' => 28,
   'dragon' => 28,
   'cloud' => 28,
])->map(fn ($count, $theme) => [
    'theme' => $theme,
    'count' => $count,
])->values();

@endphp

<div
    {{ $attributes->class('flex flex-wrap gap-2 items-center') }}
    x-data="{
        showMore: false,
        themes: @js($themes),
        currentPrompt: '{{ request('prompt') }}',
        get filteredThemes() {
            return this.showMore ? this.themes : this.themes.slice(0, 10);
        },
    }"
>
    <label class="block text-xs font-medium text-gray-700 mr-2">Common Themes</label>
    <template x-for="({ theme, count }, index) in filteredThemes" :key="index">
        <a
            :href="`{{ route('svg.matches.index', ['prompt' => '']) }}` + theme"
            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-colors duration-150"
            x-bind:class="{
                'bg-amber-100 text-amber-800': currentPrompt === theme,
                'bg-slate-100 text-slate-800 hover:bg-slate-50': currentPrompt !== theme,
            }"
        >
            <x-phosphor-tag-fill class="size-3 mr-1" />
            <span x-text="`${theme.charAt(0).toUpperCase()}${theme.slice(1)}`" class="mr-1"></span> (<span x-text="count"></span>)
        </a>
    </template>
    <button
        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-slate-800 hover:bg-slate-50 border border-slate-200 cursor-pointer"
        x-show="!showMore"
        x-on:click.prevent="showMore = true"
    >
        <x-phosphor-plus-bold class="size-3 mr-1" />
        Show More
    </button>
    <button
        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-slate-800 hover:bg-slate-50 border border-slate-200 cursor-pointer"
        x-show="showMore"
        x-on:click.prevent="showMore = false"
    >
        <x-phosphor-minus-bold class="size-3 mr-1" />
        Show Less
    </button>
</div>
