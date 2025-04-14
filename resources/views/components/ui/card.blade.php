@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden']) }}>
    @if($title)
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex flex-col">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $title }}
                </h3>
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>
    @endif
    <div class="px-6 py-5">
        {{ $slot }}
    </div>
</div>
