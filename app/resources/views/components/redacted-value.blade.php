@props(['hint' => 'Prijs verborgen'])

<div class="relative inline-flex min-w-[5rem] items-center justify-center">
    <span class="blur-[6px] select-none text-transparent" aria-hidden="true">€ 00,00</span>
    <span class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold uppercase tracking-wide text-gray-400">
        {{ $hint }}
    </span>
</div>
