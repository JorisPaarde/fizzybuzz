<?php

namespace App\Http\Controllers;

use App\Data\ComparisonFilters;
use App\Models\Product;
use App\Services\AnonymizationService;
use App\Services\PriceComparisonService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceComparisonController extends Controller
{
    public function index(Request $request, PriceComparisonService $comparisonService): View
    {
        $filters = ComparisonFilters::fromRequest($request);
        $query = $request->string('q')->trim()->toString();

        $products = $comparisonService->searchProducts(
            $request->user(),
            filled($query) ? $query : null,
            $filters,
        );

        return view('compare.index', [
            'query' => $query,
            'products' => $products,
            'filters' => $filters,
            'wholesalers' => $comparisonService->getWholesalersForFilter($request->user()),
            'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
        ]);
    }

    public function show(Request $request, Product $product, PriceComparisonService $comparisonService): View
    {
        $filters = ComparisonFilters::fromRequest($request);
        $comparison = $comparisonService->compareProductForUser($request->user(), $product, $filters);

        return view('compare.show', [
            'product' => $comparison['product'],
            'rows' => $comparison['rows'],
            'purchaseSize' => $comparison['purchase_size'],
            'purchaseSizeLabel' => $comparison['purchase_size_label'],
            'filters' => $filters,
            'wholesalers' => $comparisonService->getWholesalersForFilter($request->user()),
            'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
        ]);
    }
}
