@props(['headers' => [], 'striped' => true, 'hover' => true])

<div class="overflow-x-auto rounded-lg">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 table-auto']) }}>
        <thead class="bg-gray-50 border-y border-gray-200">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" x-data="{ activeRow: null }">
            {{ $slot }}
        </tbody>
    </table>
</div>
