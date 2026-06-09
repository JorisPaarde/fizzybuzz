@props([
    'action',
    'filters',
    'wholesalers',
    'query' => null,
    'showSearch' => false,
])

<form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
    @if ($showSearch)
        <div class="flex-1 min-w-[200px]">
            <label for="q" class="block text-xs font-medium text-gray-500 mb-1">Product</label>
            <input
                type="search"
                id="q"
                name="q"
                value="{{ $query }}"
                placeholder="Bijv. tomaten, cola, melk…"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-blue focus:ring-brand-blue text-sm"
            >
        </div>
    @endif

    <div class="min-w-[160px]">
        <label for="wholesaler_id" class="block text-xs font-medium text-gray-500 mb-1">Groothandel</label>
        <select
            id="wholesaler_id"
            name="wholesaler_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-blue focus:ring-brand-blue text-sm"
        >
            <option value="">Alle groothandels</option>
            @foreach ($wholesalers as $wholesaler)
                <option value="{{ $wholesaler->id }}" @selected($filters->wholesalerId === $wholesaler->id)>
                    {{ $wholesaler->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="min-w-[140px]">
        <label for="period" class="block text-xs font-medium text-gray-500 mb-1">Periode</label>
        <select
            id="period"
            name="period"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-blue focus:ring-brand-blue text-sm"
        >
            @foreach (\App\Data\ComparisonFilters::PERIOD_OPTIONS as $days)
                <option value="{{ $days }}" @selected($filters->periodDays === $days)>
                    Laatste {{ $days }} dagen
                </option>
            @endforeach
        </select>
    </div>

    <x-primary-button type="submit" class="shrink-0">
        {{ $showSearch ? 'Zoeken' : 'Toepassen' }}
    </x-primary-button>
</form>
