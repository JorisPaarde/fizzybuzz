<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-navy leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <p class="text-sm uppercase tracking-wide text-brand-signal font-semibold">Welkom bij PriceSignal</p>
                    <h3 class="mt-2 text-2xl font-bold text-brand-navy">
                        {{ auth()->user()->business_name ?? auth()->user()->name }}
                    </h3>
                    <p class="mt-2 text-gray-600">
                        {{ auth()->user()->region }} ·
                        {{ ucfirst(auth()->user()->business_type ?? 'horeca') }}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h4 class="font-semibold text-lg text-brand-navy">Marktinzicht</h4>
                        <p class="mt-1 text-sm text-gray-600">
                            Producten waar jij boven de marktrange betaalt (laatste {{ $filters->periodDays }} dagen).
                        </p>
                    </div>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label for="wholesaler_id" class="sr-only">Groothandel</label>
                            <select
                                id="wholesaler_id"
                                name="wholesaler_id"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-blue focus:ring-brand-blue"
                            >
                                <option value="">Alle groothandels</option>
                                @foreach ($wholesalers as $wholesaler)
                                    <option value="{{ $wholesaler->id }}" @selected($filters->wholesalerId === $wholesaler->id)>
                                        {{ $wholesaler->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="period" class="sr-only">Periode</label>
                            <select
                                id="period"
                                name="period"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-blue focus:ring-brand-blue"
                            >
                                @foreach (\App\Data\ComparisonFilters::PERIOD_OPTIONS as $days)
                                    <option value="{{ $days }}" @selected($filters->periodDays === $days)>
                                        {{ $days }} dagen
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button type="submit" class="text-sm">Toepassen</x-primary-button>
                    </form>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-red-50 border border-red-100 p-4">
                        <p class="text-sm text-red-700">Boven marktrange</p>
                        <p class="mt-1 text-3xl font-bold text-red-900">{{ $insights['total_above'] }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                        <p class="text-sm text-gray-600">Binnen marktrange</p>
                        <p class="mt-1 text-3xl font-bold text-brand-navy">{{ $insights['total_within'] }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-4">
                        <p class="text-sm text-emerald-700">Onder marktrange</p>
                        <p class="mt-1 text-3xl font-bold text-emerald-900">{{ $insights['total_below'] }}</p>
                    </div>
                </div>

                @if ($insights['total_above'] > 0)
                    <div class="mt-6">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3">Hoogste kansen om te besparen</h5>
                        <ul class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                            @foreach ($insights['above_market'] as $item)
                                <li>
                                    <a
                                        href="{{ route('compare.show', array_merge(['product' => $item['product']], $filters->toQueryArray())) }}"
                                        class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-gray-50 transition"
                                    >
                                        <div>
                                            <span class="font-medium text-brand-navy">{{ $item['product']->name }}</span>
                                            <span class="text-gray-500 text-sm"> · {{ $item['wholesaler_name'] }}</span>
                                        </div>
                                        <span class="shrink-0 inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-800">
                                            +{{ $item['difference_from_max_percent'] }}% boven markt
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @if ($insights['total_above'] > count($insights['above_market']))
                            <p class="mt-2 text-xs text-gray-500 text-center">
                                Toont top {{ count($insights['above_market']) }} van {{ $insights['total_above'] }} producten.
                                <a href="{{ route('compare.index', $filters->toQueryArray()) }}" class="text-brand-blue font-semibold hover:underline">Alle vergelijken →</a>
                            </p>
                        @endif
                    </div>
                @elseif (! auth()->user()->purchase_size)
                    <p class="mt-6 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        Stel je <a href="{{ route('profile.edit') }}" class="font-semibold underline">inkoopomvang</a> in om marktinzicht te zien.
                    </p>
                @else
                    <p class="mt-6 text-sm text-gray-600">
                        Geen producten boven de marktrange in deze periode
                        @if ($insights['total_no_data'] > 0)
                            ({{ $insights['total_no_data'] }} producten hebben nog onvoldoende marktdata).
                        @endif
                    </p>
                @endif
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <h4 class="font-semibold text-lg text-brand-navy">Prijzen vergelijken</h4>
                        <p class="mt-2 text-gray-600">
                            Zoek een product en bekijk je positie t.o.v. de marktrange.
                        </p>
                        <a href="{{ route('compare.index') }}" class="mt-4 inline-flex items-center rounded-md border border-brand-blue px-4 py-2 text-sm font-semibold text-brand-blue hover:bg-sky-50">
                            Naar vergelijking
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <h4 class="font-semibold text-lg text-brand-navy">Prijzen delen</h4>
                        <p class="mt-2 text-gray-600">
                            Upload een foto of PDF van je factuur of prijslijst — of voer handmatig in.
                        </p>
                        <a href="{{ route('prices.import.create') }}" class="mt-4 inline-flex items-center rounded-md bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:bg-brand-navy">
                            Prijzen toevoegen
                        </a>
                    </div>
                </div>
            </div>

            @php $pending = auth()->user()->priceImports()->where('status', 'review')->count(); @endphp
            @if ($pending > 0)
                <div class="bg-sky-50 border border-sky-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="font-semibold text-brand-navy">{{ $pending }} import(s) wachten op jouw controle</p>
                    <a href="{{ route('prices.import.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-blue underline">Nu controleren →</a>
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">Gedeelde prijsregels</p>
                    <p class="mt-2 text-3xl font-bold text-brand-navy">{{ auth()->user()->priceSubmissions()->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">Anonimiteit</p>
                    <p class="mt-2 text-lg font-semibold text-brand-navy">Altijd actief</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">Lid sinds</p>
                    <p class="mt-2 text-lg font-semibold text-brand-navy">{{ auth()->user()->created_at->format('d-m-Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
