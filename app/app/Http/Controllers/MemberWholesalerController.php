<?php

namespace App\Http\Controllers;

use App\Models\Wholesaler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberWholesalerController extends Controller
{
    public function index(Request $request): View
    {
        return view('wholesalers.index', [
            'linkedWholesalers' => $request->user()->wholesalers()->orderBy('name')->get(),
            'availableWholesalers' => Wholesaler::query()
                ->whereNotIn('id', $request->user()->wholesalers()->pluck('wholesalers.id'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wholesaler_id' => ['nullable', 'exists:wholesalers,id'],
            'wholesaler_name' => ['nullable', 'string', 'max:255'],
        ], [
            'wholesaler_id.exists' => 'Kies een geldige groothandel uit de lijst.',
        ]);

        if (filled($validated['wholesaler_name'] ?? null)) {
            $wholesaler = Wholesaler::findOrCreateFromName($validated['wholesaler_name']);
        } elseif (filled($validated['wholesaler_id'] ?? null)) {
            $wholesaler = Wholesaler::query()->findOrFail($validated['wholesaler_id']);
        } else {
            return back()->withErrors([
                'wholesaler_id' => 'Kies een groothandel of voer een nieuwe naam in.',
            ]);
        }

        $request->user()->wholesalers()->syncWithoutDetaching([$wholesaler->id]);

        return back()->with('status', "{$wholesaler->name} is gekoppeld aan je account.");
    }

    public function destroy(Request $request, Wholesaler $wholesaler): RedirectResponse
    {
        $request->user()->wholesalers()->detach($wholesaler->id);

        return back()->with('status', 'Groothandel ontkoppeld.');
    }
}
