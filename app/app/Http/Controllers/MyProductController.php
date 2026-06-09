<?php

namespace App\Http\Controllers;

use App\Enums\AddedVia;
use App\Models\Product;
use App\Models\UserProduct;
use App\Services\UserProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyProductController extends Controller
{
    public function index(Request $request, UserProductService $userProductService): View
    {
        $query = $request->string('q')->trim()->toString();
        $user = $request->user();

        return view('my-products.index', [
            'rows' => $userProductService->listRows($user),
            'searchQuery' => $query,
            'searchResults' => filled($query)
                ? $userProductService->searchProductsToAdd($user, $query)
                : collect(),
        ]);
    }

    public function store(Request $request, UserProductService $userProductService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ], [
            'product_id.required' => 'Kies een product om toe te voegen.',
            'product_id.exists' => 'Dit product bestaat niet.',
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $user = $request->user();

        $exists = UserProduct::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return back()->with('status', "{$product->name} staat al op je lijst.");
        }

        $userProductService->add($user, $product, AddedVia::Manual);

        return back()->with('status', "{$product->name} toegevoegd aan Mijn producten.");
    }

    public function destroy(Request $request, Product $product, UserProductService $userProductService): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        $userProductService->remove($request->user(), $product);

        return back()->with('status', "{$product->name} verwijderd van je lijst. Je prijsdata blijft bewaard.");
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        $owns = UserProduct::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->exists();

        if (! $owns) {
            abort(404);
        }
    }
}
