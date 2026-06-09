<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-navy leading-tight">
            Prijzen vergelijken
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <p class="text-gray-600">
                    Zoek een product om te zien hoe jouw prijs zich verhoudt tot de markt.
                    Marktdata wordt pas getoond bij minimaal {{ $minDatapoints }} anonieme bijdragen.
                </p>

                <div class="mt-4">
                    @include('compare.partials.filters', [
                        'action' => route('compare.index'),
                        'filters' => $filters,
                        'wholesalers' => $wholesalers,
                        'query' => $query,
                        'showSearch' => true,
                    ])
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 text-center">
                    <p class="text-gray-600">
                        @if (filled($query))
                            Geen producten gevonden voor &ldquo;{{ $query }}&rdquo;.
                        @else
                            Nog geen producten om te vergelijken. Upload eerst prijzen via
                            <a href="{{ route('prices.import.create') }}" class="text-brand-blue font-semibold hover:underline">Prijzen toevoegen</a>.
                        @endif
                    </p>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <ul class="divide-y divide-gray-100">
                        @foreach ($products as $product)
                            <li>
                                <a
                                    href="{{ route('compare.show', array_merge(['product' => $product], $filters->toQueryArray())) }}"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition"
                                >
                                    <span class="font-medium text-brand-navy">{{ $product->name }}</span>
                                    <span class="text-sm text-brand-blue font-semibold">Vergelijk →</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
