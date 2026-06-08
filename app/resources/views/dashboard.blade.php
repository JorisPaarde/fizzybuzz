<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm uppercase tracking-wide text-amber-600 font-semibold">Welkom bij Prijsplein</p>
                    <h3 class="mt-2 text-2xl font-bold">
                        {{ auth()->user()->business_name ?? auth()->user()->name }}
                    </h3>
                    <p class="mt-2 text-gray-600">
                        {{ auth()->user()->region }} ·
                        {{ ucfirst(auth()->user()->business_type ?? 'horeca') }}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold text-lg text-gray-900">Jouw volgende stap</h4>
                    <p class="mt-2 text-gray-600">
                        Je hebt nog geen prijzen gedeeld. Upload je eerste inkoopprijzen om toegang te krijgen tot anonieme marktvergelijkingen.
                    </p>
                    <div class="mt-4 inline-flex items-center rounded-full bg-amber-100 px-4 py-2 text-sm font-medium text-amber-800">
                        Fase 2 — prijsupload komt hier
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Gedeelde prijsregels</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ auth()->user()->priceSubmissions()->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Anonimiteit</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">Altijd actief</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Lid sinds</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ auth()->user()->created_at->format('d-m-Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
