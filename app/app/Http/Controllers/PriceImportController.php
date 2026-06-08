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
            'emailUploadAddress' => config('mail.from.address', 'upload@prijsplein.nl'),
        ]);
    }

    public function store(Request $request, PriceExtractionService $extractionService, PriceImportService $importService): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
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
            'wholesalers' => Wholesaler::query()->orderBy('name')->get(),
            'items' => $import->extracted_items ?? [],
        ]);
    }

    public function confirm(Request $request, PriceImport $import, PriceImportService $importService): RedirectResponse
    {
        $this->authorizeImport($request, $import);

        $validated = $request->validate([
            'wholesaler_id' => ['required', 'exists:wholesalers,id'],
            'effective_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.specification' => ['nullable', 'string', 'max:255'],
            'items.*.quantity_per_unit' => ['nullable', 'string', 'max:255'],
        ]);

        $saved = $importService->confirm(
            $import,
            $request->user(),
            (int) $validated['wholesaler_id'],
            $validated['effective_date'],
            $validated['items'],
        );

        return redirect()
            ->route('prices.index')
            ->with('status', "{$saved} prijsregel(s) opgeslagen. Ze worden na controle gedeeld met andere leden.");
    }

    public function manualCreate(): View
    {
        return view('prices.manual', [
            'wholesalers' => Wholesaler::query()->orderBy('name')->get(),
        ]);
    }

    public function manualStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wholesaler_id' => ['required', 'exists:wholesalers,id'],
            'effective_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.specification' => ['nullable', 'string', 'max:255'],
            'items.*.quantity_per_unit' => ['nullable', 'string', 'max:255'],
        ]);

        $import = PriceImport::query()->create([
            'user_id' => $request->user()->id,
            'source' => PriceImport::SOURCE_MANUAL,
            'wholesaler_id' => $validated['wholesaler_id'],
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
