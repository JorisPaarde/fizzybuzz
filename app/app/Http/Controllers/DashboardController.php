<?php

namespace App\Http\Controllers;

use App\Data\ComparisonFilters;
use App\Services\AnonymizationService;
use App\Services\PriceComparisonService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, PriceComparisonService $comparisonService): View
    {
        $filters = ComparisonFilters::fromRequest($request);
        $insights = $comparisonService->getMarketInsights($request->user(), $filters);

        return view('dashboard', [
            'filters' => $filters,
            'insights' => $insights,
            'wholesalers' => $comparisonService->getWholesalersForFilter($request->user()),
            'minDatapoints' => AnonymizationService::MIN_DATAPOINTS,
        ]);
    }
}
