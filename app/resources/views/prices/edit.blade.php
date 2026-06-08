<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prijsregel bewerken</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('prices.update', $submission) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="product_name" value="Product" />
                    <x-text-input id="product_name" name="product_name" class="mt-1 block w-full" :value="old('product_name', $submission->product->name)" required />
                </div>

                <div>
                    <x-input-label for="wholesaler_id" value="Groothandel" />
                    <select id="wholesaler_id" name="wholesaler_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($wholesalers as $wholesaler)
                            <option value="{{ $wholesaler->id }}" @selected(old('wholesaler_id', $submission->wholesaler_id) == $wholesaler->id)>{{ $wholesaler->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="price" value="Prijs (€)" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $submission->price)" required />
                    </div>
                    <div>
                        <x-input-label for="unit" value="Eenheid" />
                        <x-text-input id="unit" name="unit" class="mt-1 block w-full" :value="old('unit', $submission->unit)" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="quantity_per_unit" value="Hoeveelheid per eenheid" />
                        <x-text-input id="quantity_per_unit" name="quantity_per_unit" class="mt-1 block w-full" :value="old('quantity_per_unit', $submission->quantity_per_unit)" />
                    </div>
                    <div>
                        <x-input-label for="effective_date" value="Datum" />
                        <x-text-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" :value="old('effective_date', $submission->effective_date->toDateString())" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="specification" value="Specificatie" />
                    <x-text-input id="specification" name="specification" class="mt-1 block w-full" :value="old('specification', $submission->specification)" />
                </div>

                <div>
                    <x-input-label for="notes" value="Opmerking" />
                    <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('notes', $submission->notes) }}</textarea>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('prices.index') }}" class="text-sm text-gray-600 hover:underline">Terug</a>
                    <x-primary-button>Opslaan</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('prices.destroy', $submission) }}" class="mt-4 text-right" onsubmit="return confirm('Weet je zeker dat je deze regel wilt verwijderen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Verwijderen</button>
            </form>
        </div>
    </div>
</x-app-layout>
