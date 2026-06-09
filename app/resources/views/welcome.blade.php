<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PriceSignal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-navy text-white antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-6">
        <div class="max-w-xl text-center">
            <p class="text-brand-signal text-sm font-semibold uppercase tracking-widest">PriceSignal</p>
            <h1 class="mt-4 text-4xl font-bold">Lokale ontwikkelomgeving</h1>
            <p class="mt-4 text-slate-300">
                Dit is de Laravel-app. De publieke landingspagina staat op pricesignal.nl.
                Hier bouwen we registratie, dashboard en prijsvergelijking.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-signal px-6 py-3 font-semibold text-white hover:bg-cyan-700">Naar dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-signal px-6 py-3 font-semibold text-white hover:bg-cyan-700">Word lid</a>
                    <a href="{{ route('login') }}" class="rounded-lg border border-slate-500 px-6 py-3 font-semibold text-slate-100 hover:border-white">Inloggen</a>
                @endauth
            </div>
            <p class="mt-8 text-sm text-slate-400">
                Marketing site:
                <a class="underline text-brand-signal" href="https://pricesignal.nl" target="_blank" rel="noreferrer">
                    pricesignal.nl
                </a>
            </p>
        </div>
    </div>
</body>
</html>
