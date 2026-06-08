<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prijzen toevoegen</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            @php
                $pendingImports = auth()->user()->priceImports()->where('status', 'review')->get();
            @endphp

            @if ($pendingImports->isNotEmpty())
                <div class="rounded-md bg-amber-50 border border-amber-200 p-4">
                    <p class="text-sm font-semibold text-amber-900">Openstaande controles</p>
                    <ul class="mt-2 space-y-1">
                        @foreach ($pendingImports as $pending)
                            <li>
                                <a href="{{ route('prices.import.review', $pending) }}" class="text-sm text-amber-800 underline">
                                    {{ ucfirst($pending->source) }} — {{ $pending->created_at->format('d-m-Y H:i') }} controleren
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-900">Foto of PDF</h3>
                    <p class="mt-2 text-sm text-gray-600">Upload een factuur of prijslijst. AI leest de prijzen uit — jij controleert ze daarna.</p>
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
                <h3 class="font-semibold text-lg text-gray-900">Via e-mail</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Stuur je factuur of prijslijst als bijlage naar:
                </p>
                <p class="mt-2 font-mono text-sm bg-gray-100 rounded px-3 py-2 inline-block">{{ $emailUploadAddress }}</p>
                <p class="mt-3 text-sm text-gray-600">
                    <strong>Belangrijk:</strong> stuur vanaf <strong>{{ $userEmail }}</strong> (je geregistreerde adres).
                    Je krijgt daarna een import die je moet controleren voordat prijzen worden opgeslagen.
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Vereist inbound mail-routing (bijv. Mailgun). Webhook: <code class="bg-gray-100 px-1 rounded">/webhooks/inbound-email</code>
                </p>
            </div>

            <p class="text-center">
                <a href="{{ route('wholesalers.index') }}" class="text-sm text-amber-600 hover:underline">Beheer je groothandels →</a>
            </p>
        </div>
    </div>
</x-app-layout>
