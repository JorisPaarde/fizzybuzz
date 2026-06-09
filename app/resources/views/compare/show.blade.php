<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-brand-navy leading-tight">
                {{ $product->name }}
            </h2>
            <a href="{{ route('compare.index') }}" class="text-sm text-brand-blue font-semibold hover:underline">
                ← Terug naar zoeken
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($purchaseSizeLabel)
                <p class="text-sm text-gray-600 text-center">
                    Marktdata voor vergelijkbare bedrijven: <span class="font-medium text-brand-navy">{{ $purchaseSizeLabel }}</span>
                </p>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900 text-center">
                    Stel je <a href="{{ route('profile.edit') }}" class="font-semibold underline">inkoopomvang</a> in voor een betere vergelijking met vergelijkbare bedrijven.
                </div>
            @endif

            @if (empty($rows))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <p class="text-gray-600">
                        Nog geen data voor dit product. Voeg je eigen prijs toe of wacht tot meer leden delen.
                    </p>
                    <a href="{{ route('prices.import.create') }}" class="mt-4 inline-block text-brand-blue font-semibold hover:underline">
                        Prijzen toevoegen →
                    </a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50 text-left text-gray-500">
                                    <th class="px-6 py-3 font-medium">Groothandel</th>
                                    <th class="px-6 py-3 font-medium">Jouw prijs</th>
                                    <th class="px-6 py-3 font-medium">Marktrange</th>
                                    <th class="px-6 py-3 font-medium">Jouw positie</th>
                                    <th class="px-6 py-3 font-medium">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td class="px-6 py-4 font-medium text-brand-navy">
                                            {{ $row['wholesaler_name'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($row['user_price'] !== null)
                                                € {{ number_format($row['user_price'], 2, ',', '.') }}
                                                <span class="text-gray-500">/ {{ $row['user_unit'] }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($row['market'])
                                                @php $market = $row['market']; @endphp
                                                <div class="space-y-2">
                                                    <div class="font-medium text-brand-navy">
                                                        € {{ number_format($market->min_price, 2, ',', '.') }}
                                                        –
                                                        € {{ number_format($market->max_price, 2, ',', '.') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Gem. € {{ number_format($market->avg_price, 2, ',', '.') }}
                                                    </div>
                                                    @if ($row['range_percent'] !== null)
                                                        <div class="relative h-2 w-full max-w-[180px] rounded-full bg-gray-200">
                                                            <div class="absolute inset-y-0 left-0 rounded-full bg-brand-blue/30" style="width: 100%"></div>
                                                            <div
                                                                class="absolute top-1/2 h-3 w-3 -translate-y-1/2 rounded-full border-2 border-white shadow {{ $row['range_position'] === 'below' ? 'bg-emerald-500' : ($row['range_position'] === 'above' ? 'bg-red-500' : 'bg-brand-blue') }}"
                                                                style="left: calc({{ min(100, max(0, $row['range_percent'])) }}% - 6px)"
                                                                title="Jouw prijs in de marktrange"
                                                            ></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-500">Nog onvoldoende data</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($row['range_position'] === 'below')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                                    {{ abs($row['difference_from_min_percent']) }}% onder laagste
                                                </span>
                                            @elseif ($row['range_position'] === 'above')
                                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-800">
                                                    +{{ $row['difference_from_max_percent'] }}% boven hoogste
                                                </span>
                                            @elseif ($row['range_position'] === 'within')
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                                                    Binnen marktrange
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">
                                            @if ($row['market'])
                                                {{ $row['market']->datapoint_count }} leden
                                            @else
                                                &lt; {{ $minDatapoints }} leden
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <p class="text-sm text-gray-500 text-center">
                    Marktprijzen zijn anoniem geaggregeerd per inkoopomvang. Individuele bedrijven zijn nooit zichtbaar.
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
