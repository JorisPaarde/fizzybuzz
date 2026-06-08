<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mijn prijzen</h2>
            <a href="{{ route('prices.import.create') }}" class="inline-flex items-center rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
                Prijzen toevoegen
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($submissions->isEmpty())
                        <p class="text-gray-600">Je hebt nog geen prijzen gedeeld.</p>
                        <a href="{{ route('prices.import.create') }}" class="mt-4 inline-block text-amber-600 font-semibold hover:underline">
                            Upload een foto, PDF of voer handmatig in →
                        </a>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left text-gray-500">
                                        <th class="py-2 pe-4">Product</th>
                                        <th class="py-2 pe-4">Groothandel</th>
                                        <th class="py-2 pe-4">Prijs</th>
                                        <th class="py-2 pe-4">Datum</th>
                                        <th class="py-2 pe-4">Status</th>
                                        <th class="py-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submissions as $submission)
                                        <tr class="border-b border-gray-100">
                                            <td class="py-3 pe-4 font-medium text-gray-900">{{ $submission->product->name }}</td>
                                            <td class="py-3 pe-4">{{ $submission->wholesaler->name }}</td>
                                            <td class="py-3 pe-4">€ {{ number_format($submission->price, 2, ',', '.') }} / {{ $submission->unit }}</td>
                                            <td class="py-3 pe-4">{{ $submission->effective_date->format('d-m-Y') }}</td>
                                            <td class="py-3 pe-4">
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $submission->status }}</span>
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('prices.edit', $submission) }}" class="text-amber-600 hover:underline">Bewerken</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $submissions->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
