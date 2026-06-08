<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prijzen toevoegen</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-900">Foto of PDF</h3>
                    <p class="mt-2 text-sm text-gray-600">Upload een factuur of prijslijst. We lezen de prijzen automatisch uit — jij controleert ze daarna.</p>
                    <form class="mt-6 space-y-4" method="POST" action="{{ route('prices.import.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <x-input-label for="file" value="Bestand" />
                            <input id="file" name="file" type="file" accept="image/*,.pdf" capture="environment" class="mt-1 block w-full text-sm" required>
                            <p class="mt-2 text-xs text-gray-500">JPG, PNG of PDF · max 10 MB</p>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>
                        <x-primary-button>Uitlezen met AI</x-primary-button>
                    </form>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-900">Handmatig</h3>
                    <p class="mt-2 text-sm text-gray-600">Zelf producten en prijzen invoeren — handig voor losse regels.</p>
                    <a href="{{ route('prices.manual.create') }}" class="mt-6 inline-flex rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                        Handmatig invoeren
                    </a>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-amber-900">Via e-mail (binnenkort)</h3>
                <p class="mt-2 text-sm text-amber-900/80">
                    Stuur straks je factuur of prijslijst naar
                    <strong>{{ $emailUploadAddress }}</strong>
                    vanaf het e-mailadres van je account. Dezelfde review-stap geldt ook hier.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
