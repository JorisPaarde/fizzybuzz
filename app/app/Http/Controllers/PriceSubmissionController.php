<?php

namespace App\Http\Controllers;

use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\Wholesaler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $submissions = PriceSubmission::query()
            ->with(['product', 'wholesaler'])
            ->where('user_id', $request->user()->id)
            ->latest('effective_date')
            ->latest('id')
            ->paginate(20);

        return view('prices.index', compact('submissions'));
    }

    public function edit(Request $request, PriceSubmission $submission): View
    {
        $this->authorizeSubmission($request, $submission);

        return view('prices.edit', [
            'submission' => $submission,
            'wholesalers' => Wholesaler::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PriceSubmission $submission): RedirectResponse
    {
        $this->authorizeSubmission($request, $submission);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'wholesaler_id' => ['required', 'exists:wholesalers,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'specification' => ['nullable', 'string', 'max:255'],
            'quantity_per_unit' => ['nullable', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrCreateFromName($validated['product_name']);

        $submission->update([
            'product_id' => $product->id,
            'wholesaler_id' => $validated['wholesaler_id'],
            'price' => $validated['price'],
            'unit' => $validated['unit'],
            'specification' => $validated['specification'] ?? null,
            'quantity_per_unit' => $validated['quantity_per_unit'] ?? null,
            'effective_date' => $validated['effective_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => PriceSubmission::STATUS_PENDING,
        ]);

        return redirect()
            ->route('prices.index')
            ->with('status', 'Prijsregel bijgewerkt.');
    }

    public function destroy(Request $request, PriceSubmission $submission): RedirectResponse
    {
        $this->authorizeSubmission($request, $submission);
        $submission->delete();

        return redirect()
            ->route('prices.index')
            ->with('status', 'Prijsregel verwijderd.');
    }

    private function authorizeSubmission(Request $request, PriceSubmission $submission): void
    {
        if ($submission->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
