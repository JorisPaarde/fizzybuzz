<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Handmatig invoeren</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('prices.manual.store') }}" x-data="priceRows()">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="wholesaler_id" value="Groothandel" />
                            <select id="wholesaler_id" name="wholesaler_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Kies groothandel</option>
                                @foreach ($wholesalers as $wholesaler)
                                    <option value="{{ $wholesaler->id }}" @selected(old('wholesaler_id') == $wholesaler->id)>{{ $wholesaler->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('wholesaler_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="effective_date" value="Datum" />
                            <x-text-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" :value="old('effective_date', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('effective_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Prijsregels</h3>
                            <button type="button" @click="addRow()" class="text-sm font-semibold text-amber-600 hover:underline">+ Regel toevoegen</button>
                        </div>

                        <template x-for="(row, index) in rows" :key="row.id">
                            <div class="grid gap-3 md:grid-cols-6 border border-gray-100 rounded-lg p-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-500">Product</label>
                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300" :name="`items[${index}][product_name]`" x-model="row.product_name" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Prijs (€)</label>
                                    <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border-gray-300" :name="`items[${index}][price]`" x-model="row.price" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Eenheid</label>
                                    <select class="mt-1 w-full rounded-md border-gray-300" :name="`items[${index}][unit]`" x-model="row.unit" required>
                                        <option value="kg">kg</option>
                                        <option value="liter">liter</option>
                                        <option value="doos">doos</option>
                                        <option value="stuk">stuk</option>
                                        <option value="zak">zak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Hoeveelheid</label>
                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300" :name="`items[${index}][quantity_per_unit]`" x-model="row.quantity_per_unit" placeholder="5 kg">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeRow(index)" class="text-sm text-red-600 hover:underline" x-show="rows.length > 1">Verwijder</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('prices.import.create') }}" class="text-sm text-gray-600 hover:underline">Terug</a>
                        <x-primary-button>Naar controle</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function priceRows() {
            return {
                rows: [{ id: 1, product_name: '', price: '', unit: 'kg', quantity_per_unit: '' }],
                addRow() {
                    this.rows.push({ id: Date.now(), product_name: '', price: '', unit: 'kg', quantity_per_unit: '' });
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
