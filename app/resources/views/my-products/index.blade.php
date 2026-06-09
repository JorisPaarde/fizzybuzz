<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-navy leading-tight">Mijn producten</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 border border-green-100 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <p class="text-gray-600">
                    Dit zijn de producten die PriceSignal voor jou monitort. Ze komen automatisch op je lijst
                    na een upload, of voeg ze handmatig toe.
                </p>

                <form method="GET" action="{{ route('my-products.index') }}" class="mt-4 flex gap-2">
                    <label for="product-search" class="sr-only">Zoek product om toe te voegen</label>
                    <input
                        type="search"
                        id="product-search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Zoek product om toe te voegen…"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-brand-blue focus:ring-brand-blue"
                    >
                    <button type="submit" class="inline-flex items-center rounded-md bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:bg-brand-navy">
                        Zoeken
                    </button>
                </form>

                @if (filled($searchQuery))
                    <div class="mt-4 rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-brand-navy mb-3">Resultaten voor &ldquo;{{ $searchQuery }}&rdquo;</p>
                        @if ($searchResults->isEmpty())
                            <p class="text-sm text-gray-600">Geen producten gevonden of ze staan al op je lijst.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach ($searchResults as $product)
                                    <li class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                        <form method="POST" action="{{ route('my-products.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="text-sm font-semibold text-brand-blue hover:underline">
                                                Toevoegen
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>

            @if ($rows === [])
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8 text-center">
                    <p class="text-gray-600">Je lijst is nog leeg.</p>
                    <p class="mt-2 text-sm text-gray-500">
                        Upload een factuur via
                        <a href="{{ route('prices.import.create') }}" class="font-semibold text-brand-blue hover:underline">Prijzen toevoegen</a>
                        of zoek hierboven een product om toe te voegen.
                    </p>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50 text-left text-gray-500">
                                    <th class="px-6 py-3 font-medium">Product</th>
                                    <th class="px-6 py-3 font-medium">Jouw prijs</th>
                                    <th class="px-6 py-3 font-medium">Markt vanaf</th>
                                    <th class="px-6 py-3 font-medium">Positie</th>
                                    <th class="px-6 py-3 font-medium text-right">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    @php
                                        $submission = $row['latest_submission'];
                                        $positionClasses = match ($row['position']) {
                                            'above' => 'bg-red-50 text-red-800',
                                            'below' => 'bg-emerald-50 text-emerald-800',
                                            'within' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-50 text-gray-600',
                                        };
                                    @endphp
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td class="px-6 py-4">
                                            <a href="{{ route('compare.show', $row['product']) }}" class="font-medium text-brand-navy hover:underline">
                                                {{ $row['product']->name }}
                                            </a>
                                            @if ($row['product']->category)
                                                <span class="block text-xs text-gray-500">{{ $row['product']->category }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($submission)
                                                <div class="font-medium text-gray-900">
                                                    € {{ number_format($submission->price, 2, ',', '.') }}
                                                    <span class="text-gray-500 font-normal">/ {{ $submission->unit }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $submission->wholesaler?->name }} · {{ $submission->effective_date->format('d-m-Y') }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">Nog geen prijs</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($row['market_min'] !== null)
                                                <span class="font-medium">€ {{ number_format($row['market_min'], 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $positionClasses }}">
                                                {{ $row['position_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form method="POST" action="{{ route('my-products.destroy', $row['product']) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                                    title="Verwijderen van lijst"
                                                    aria-label="Verwijder {{ $row['product']->name }} van lijst"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
