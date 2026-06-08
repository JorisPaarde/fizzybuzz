<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Word lid van Prijsplein</h1>
        <p class="mt-2 text-sm text-gray-600">
            Jouw prijsinzichten worden anoniem gedeeld. Andere leden zien nooit welk bedrijf welke prijs betaalt.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="business_name" value="Bedrijfsnaam" />
            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autofocus />
            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="business_type" value="Type horeca" />
            <select id="business_type" name="business_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Kies een type</option>
                @foreach (['restaurant' => 'Restaurant', 'cafe' => 'Café', 'hotel' => 'Hotel', 'catering' => 'Catering', 'other' => 'Overig'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="region" value="Regio / stad" />
            <x-text-input id="region" class="block mt-1 w-full" type="text" name="region" :value="old('region')" required />
            <x-input-error :messages="$errors->get('region')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="name" value="Contactpersoon" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="E-mailadres" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="employees_count" value="Aantal medewerkers (optioneel)" />
            <x-text-input id="employees_count" class="block mt-1 w-full" type="number" name="employees_count" :value="old('employees_count')" min="1" />
            <x-input-error :messages="$errors->get('employees_count')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Wachtwoord" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Bevestig wachtwoord" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="privacy_acknowledged" value="1" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('privacy_acknowledged')) required>
                <span class="text-sm text-gray-600">
                    Ik begrijp dat mijn ingevoerde prijzen anoniem worden gedeeld met andere leden en nooit herleidbaar zijn tot mijn bedrijf in publieke vergelijkingen.
                </span>
            </label>
            <x-input-error :messages="$errors->get('privacy_acknowledged')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Al lid? Log in
            </a>

            <x-primary-button class="ms-4">
                Account aanmaken
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
