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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                                    <th class="px-6 py-3 font-medium">Marktgemiddelde</th>
                                    <th class="px-6 py-3 font-medium">Verschil</th>
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
                                                € {{ number_format($row['market']->avg_price, 2, ',', '.') }}
                                                <span class="text-gray-500">/ gemiddelde</span>
                                            @else
                                                <span class="text-gray-500">Nog onvoldoende data</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($row['position'] === 'below')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                                    {{ $row['difference_percent'] }}% onder gemiddelde
                                                </span>
                                            @elseif ($row['position'] === 'above')
                                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-800">
                                                    +{{ $row['difference_percent'] }}% boven gemiddelde
                                                </span>
                                            @elseif ($row['position'] === 'at')
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                                                    Rond gemiddelde
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
                    Marktprijzen zijn anoniem geaggregeerd. Individuele bedrijven zijn nooit zichtbaar.
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
