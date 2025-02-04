@props(['filters'])

<div class="bg-white p-4 mb-4 rounded-lg shadow">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($filters as $name => $filter)
        <div>
            @if($filter['type'] === 'select')
            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $filter['label'] }}</label>
            <select name="{{ $name }}" id="{{ $name }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All {{ $filter['label'] }}s</option>
                @foreach($filter['options'] as $value => $label)
                <option value="{{ $value }}" {{ (string)request($name) === (string)$value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
            @endif

            @if($filter['type'] === 'text')
            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $filter['label'] }}</label>
            <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ request($name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Search {{ strtolower($filter['label']) }}...">
            @endif

            @if($filter['type'] === 'date')
            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $filter['label'] }}</label>
            <input type="date" name="{{ $name }}" id="{{ $name }}" value="{{ request($name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @endif
        </div>
        @endforeach

        <div class="flex items-end space-x-2">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Filter
            </button>
            <a href="{{ request()->url() }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset
            </a>
        </div>
    </form>
</div>