<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Bedrijfsprofiel
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Deze gegevens worden gebruikt voor anonieme vergelijkingen. Je bedrijfsnaam is nooit zichtbaar voor andere leden.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="business_name" value="Bedrijfsnaam" />
            <x-text-input id="business_name" name="business_name" type="text" class="mt-1 block w-full" :value="old('business_name', $user->business_name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
        </div>

        <div>
            <x-input-label for="business_type" value="Type horeca" />
            <select id="business_type" name="business_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                @foreach (['restaurant' => 'Restaurant', 'cafe' => 'Café', 'hotel' => 'Hotel', 'catering' => 'Catering', 'other' => 'Overig'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('business_type', $user->business_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('business_type')" />
        </div>

        <div>
            <x-input-label for="region" value="Regio / stad" />
            <x-text-input id="region" name="region" type="text" class="mt-1 block w-full" :value="old('region', $user->region)" required />
            <x-input-error class="mt-2" :messages="$errors->get('region')" />
        </div>

        <div>
            <x-input-label for="purchase_size" value="Maandelijkse inkoopomvang" />
            <select id="purchase_size" name="purchase_size" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                @foreach (\App\Enums\PurchaseSize::options() as $value => $label)
                    <option value="{{ $value }}" @selected(old('purchase_size', $user->purchase_size) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Prijzen worden vergeleken met bedrijven in dezelfde inkoopcategorie.</p>
            <x-input-error class="mt-2" :messages="$errors->get('purchase_size')" />
        </div>

        <div>
            <x-input-label for="employees_count" value="Aantal medewerkers (optioneel)" />
            <x-text-input id="employees_count" name="employees_count" type="number" class="mt-1 block w-full" :value="old('employees_count', $user->employees_count)" min="1" />
            <x-input-error class="mt-2" :messages="$errors->get('employees_count')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Opslaan</x-primary-button>

            @if (session('status') === 'business-profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Opgeslagen.</p>
            @endif
        </div>
    </form>
</section>
