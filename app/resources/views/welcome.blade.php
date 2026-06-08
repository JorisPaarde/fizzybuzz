<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prijsplein App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-6">
        <div class="max-w-xl text-center">
            <p class="text-amber-400 text-sm font-semibold uppercase tracking-widest">Prijsplein App</p>
            <h1 class="mt-4 text-4xl font-bold">Lokale ontwikkelomgeving</h1>
            <p class="mt-4 text-slate-300">
                Dit is de Laravel-app. De publieke landingspagina staat op GitHub Pages.
                Hier bouwen we registratie, dashboard en prijsvergelijking.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-amber-400 px-6 py-3 font-semibold text-slate-950">Naar dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="rounded-full bg-amber-400 px-6 py-3 font-semibold text-slate-950">Word lid</a>
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-600 px-6 py-3 font-semibold text-slate-100">Inloggen</a>
                @endauth
            </div>
            <p class="mt-8 text-sm text-slate-500">
                Marketing site:
                <a class="underline text-amber-300" href="https://jorispaarde.github.io/fizzybuzz/" target="_blank" rel="noreferrer">
                    jorispaarde.github.io/fizzybuzz
                </a>
            </p>
        </div>
    </div>
</body>
</html>
