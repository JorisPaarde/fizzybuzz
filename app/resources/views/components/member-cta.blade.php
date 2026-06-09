@props([
    'title' => 'Word lid om prijzen te zien',
    'description' => 'Meld je gratis aan om marktprijzen en jouw positie te bekijken. Jouw bedrijf blijft altijd anoniem.',
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-brand-blue/20 bg-gradient-to-br from-sky-50 to-white p-'.($compact ? '4' : '6')]) }}>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="font-semibold text-brand-navy">{{ $title }}</p>
            @unless ($compact)
                <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
            @endunless
        </div>
        <div class="flex shrink-0 flex-col sm:flex-row gap-2">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:bg-brand-navy">
                Gratis aanmelden
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Inloggen
            </a>
        </div>
    </div>
</div>
