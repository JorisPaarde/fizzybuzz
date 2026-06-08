<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mijn groothandels</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">
                    Koppel de groothandels waar je inkopen doet. Ze verschijnen bovenaan bij het invoeren van prijzen.
                </p>

                <form method="POST" action="{{ route('wholesalers.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="wholesaler_id" value="Bestaande groothandel" />
                        <select id="wholesaler_id" name="wholesaler_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Kies uit de lijst</option>
                            @foreach ($availableWholesalers as $wholesaler)
                                <option value="{{ $wholesaler->id }}">{{ $wholesaler->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="wholesaler_name" value="Of nieuwe groothandel" />
                        <x-text-input id="wholesaler_name" name="wholesaler_name" class="mt-1 block w-full" placeholder="Naam groothandel" />
                        <x-input-error :messages="$errors->get('wholesaler_id')" class="mt-2" />
                    </div>
                    <x-primary-button>Koppelen</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900">Gekoppelde groothandels</h3>
                @if ($linkedWholesalers->isEmpty())
                    <p class="mt-2 text-sm text-gray-600">Nog geen groothandels gekoppeld.</p>
                @else
                    <ul class="mt-4 divide-y divide-gray-100">
                        @foreach ($linkedWholesalers as $wholesaler)
                            <li class="flex items-center justify-between py-3">
                                <span class="font-medium text-gray-900">{{ $wholesaler->name }}</span>
                                <form method="POST" action="{{ route('wholesalers.destroy', $wholesaler) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:underline">Ontkoppelen</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
