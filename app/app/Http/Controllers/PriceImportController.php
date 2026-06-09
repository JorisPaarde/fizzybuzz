<?php

namespace App\Http\Controllers;

use App\Models\PriceImport;
use App\Models\Wholesaler;
use App\Services\PriceExtractionService;
use App\Services\PriceImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceImportController extends Controller
{
    public function create(): View
    {
        return view('prices.import', [
            'emailUploadAddress' => config('pricesignal.inbound_email'),
            'userEmail' => auth()->user()->email,
        ]);
    }

    public function store(Request $request, PriceExtractionService $extractionService, PriceImportService $importService): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ], [
            'file.required' => 'Kies een foto of PDF om te uploaden.',
            'file.mimes' => 'Alleen JPG, PNG, WEBP of PDF zijn toegestaan.',
            'file.max' => 'Het bestand mag maximaal 10 MB zijn.',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $source = $extension === 'pdf' ? PriceImport::SOURCE_PDF : PriceImport::SOURCE_PHOTO;

        $import = PriceImport::query()->create([
            'user_id' => $request->user()->id,
            'source' => $source,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $file->store('price-imports/'.$request->user()->id, 'local'),
            'status' => PriceImport::STATUS_EXTRACTING,
        ]);

        try {
            $extracted = $extractionService->extractFromUpload($file);

            $import->update([
                'extracted_items' => $extracted['items'],
                'wholesaler_id' => $importService->guessWholesalerId($extracted['wholesaler_guess']),
                'effective_date' => $importService->parseEffectiveDate($extracted['document_date']),
                'status' => PriceImport::STATUS_REVIEW,
            ]);
        } catch (\Throwable $exception) {
            $import->update([
                'status' => PriceImport::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('prices.import.create')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('prices.import.review', $import);
    }

    public function review(Request $request, PriceImport $import): View
    {
        $this->authorizeImport($request, $import);

        return view('prices.review', [
            'import' => $import,
            'wholesalers' => $request->user()->wholesalersForSelect(),
            'items' => $import->extracted_items ?? [],
        ]);
    }

    public function confirm(Request $request, PriceImport $import, PriceImportService $importService): RedirectResponse
    {
        $this->authorizeImport($request, $import);

        $validated = $request->validate([
            'wholesaler_id' => ['nullable', 'exists:wholesalers,id'],
            'new_wholesaler_name' => ['nullable', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.specification' => ['nullable', 'string', 'max:255'],
            'items.*.quantity_per_unit' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Voeg minimaal één prijsregel toe.',
            'items.*.product_name.required' => 'Elke regel heeft een productnaam nodig.',
            'items.*.price.required' => 'Elke regel heeft een prijs nodig.',
        ]);

        $wholesalerId = $importService->resolveWholesalerId(
            $validated['wholesaler_id'] ?? null,
            $validated['new_wholesaler_name'] ?? null,
        );

        $saved = $importService->confirm(
            $import,
            $request->user(),
            $wholesalerId,
            $validated['effective_date'],
            $validated['items'],
        );

        return redirect()
            ->route('prices.index')
            ->with('status', "{$saved} prijsregel(s) opgeslagen en gevalideerd. Ze tellen mee zodra voldoende leden data delen.");
    }

    public function manualCreate(): View
    {
        return view('prices.manual', [
            'wholesalers' => auth()->user()->wholesalersForSelect(),
        ]);
    }

    public function manualStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wholesaler_id' => ['nullable', 'exists:wholesalers,id'],
            'new_wholesaler_name' => ['nullable', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.specification' => ['nullable', 'string', 'max:255'],
            'items.*.quantity_per_unit' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Voeg minimaal één prijsregel toe.',
        ]);

        $wholesalerId = app(PriceImportService::class)->resolveWholesalerId(
            $validated['wholesaler_id'] ?? null,
            $validated['new_wholesaler_name'] ?? null,
        );

        $import = PriceImport::query()->create([
            'user_id' => $request->user()->id,
            'source' => PriceImport::SOURCE_MANUAL,
            'wholesaler_id' => $wholesalerId,
            'effective_date' => $validated['effective_date'],
            'extracted_items' => $validated['items'],
            'status' => PriceImport::STATUS_REVIEW,
        ]);

        return redirect()->route('prices.import.review', $import);
    }

    private function authorizeImport(Request $request, PriceImport $import): void
    {
        if ($import->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
