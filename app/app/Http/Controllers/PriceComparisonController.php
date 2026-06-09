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
        $isGuest = ! $request->user();

        if ($isGuest) {
            $products = $comparisonService->searchPublicProducts(
                filled($query) ? $query : null,
                $filters,
            );
            $wholesalers = $comparisonService->getPublicWholesalersForFilter($filters);
        } else {
            $products = $comparisonService->searchProducts(
                $request->user(),
                filled($query) ? $query : null,
                $filters,
            );
            $wholesalers = $comparisonService->getWholesalersForFilter($request->user());
        }

        return view('compare.index', [
            'query' => $query,
            'products' => $products,
            'filters' => $filters,
            'wholesalers' => $wholesalers,
            'isGuest' => $isGuest,
            'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
        ]);
    }

    public function show(Request $request, Product $product, PriceComparisonService $comparisonService): View
    {
        $filters = ComparisonFilters::fromRequest($request);
        $isGuest = ! $request->user();

        if ($isGuest) {
            $comparison = $comparisonService->compareProductForGuest($product, $filters);

            return view('compare.show', [
                'product' => $comparison['product'],
                'rows' => $comparison['rows'],
                'purchaseSize' => PriceComparisonService::GUEST_PREVIEW_PURCHASE_SIZE,
                'purchaseSizeLabel' => $comparison['purchase_size_label'],
                'filters' => $filters,
                'wholesalers' => $comparisonService->getPublicWholesalersForFilter($filters),
                'isGuest' => true,
                'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
            ]);
        }

        $comparison = $comparisonService->compareProductForUser($request->user(), $product, $filters);

        return view('compare.show', [
            'product' => $comparison['product'],
            'rows' => $comparison['rows'],
            'purchaseSize' => $comparison['purchase_size'],
            'purchaseSizeLabel' => $comparison['purchase_size_label'],
            'filters' => $filters,
            'wholesalers' => $comparisonService->getWholesalersForFilter($request->user()),
            'isGuest' => false,
            'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
        ]);
    }
}
