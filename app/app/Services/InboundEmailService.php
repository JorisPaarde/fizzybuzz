<?php

namespace App\Services;

use App\Models\PriceImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InboundEmailService
{
    public function __construct(
        private readonly PriceExtractionService $extractionService,
        private readonly PriceImportService $importService,
    ) {}

    /**
     * @param  array<int, UploadedFile>  $attachments
     */
    public function process(User $user, array $attachments, ?string $subject = null): PriceImport
    {
        $file = $this->firstSupportedAttachment($attachments);

        if ($file === null) {
            throw new \InvalidArgumentException('Geen ondersteunde bijlage gevonden. Stuur een PDF of foto (JPG/PNG).');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $source = $extension === 'pdf' ? PriceImport::SOURCE_EMAIL : PriceImport::SOURCE_EMAIL;

        $storedPath = $file->store('price-imports/'.$user->id.'/email', 'local');

        $import = PriceImport::query()->create([
            'user_id' => $user->id,
            'source' => $source,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'status' => PriceImport::STATUS_EXTRACTING,
        ]);

        try {
            $extracted = $this->extractionService->extractFromUpload($file);

            $import->update([
                'extracted_items' => $extracted['items'],
                'wholesaler_id' => $this->importService->guessWholesalerId($extracted['wholesaler_guess']),
                'effective_date' => $this->importService->parseEffectiveDate($extracted['document_date']),
                'status' => PriceImport::STATUS_REVIEW,
            ]);

            if ($import->wholesaler_id) {
                $user->wholesalers()->syncWithoutDetaching([$import->wholesaler_id]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Inbound email extraction failed', [
                'user_id' => $user->id,
                'import_id' => $import->id,
                'error' => $exception->getMessage(),
            ]);

            $import->update([
                'status' => PriceImport::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $import->fresh();
    }

    public function resolveUserFromSender(string $sender): ?User
    {
        $email = $this->extractEmailAddress($sender);

        if ($email === null) {
            return null;
        }

        return User::query()->where('email', $email)->first();
    }

    public function extractEmailAddress(string $sender): ?string
    {
        if (preg_match('/<([^>]+)>/', $sender, $matches)) {
            return strtolower(trim($matches[1]));
        }

        $sender = strtolower(trim($sender));

        return filter_var($sender, FILTER_VALIDATE_EMAIL) ? $sender : null;
    }

    /**
     * @param  array<int, UploadedFile>  $attachments
     */
    private function firstSupportedAttachment(array $attachments): ?UploadedFile
    {
        foreach ($attachments as $attachment) {
            $extension = strtolower($attachment->getClientOriginalExtension());
            $mime = $attachment->getMimeType() ?? '';

            if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true) || str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                return $attachment;
            }
        }

        return null;
    }

    /**
     * @return array<int, UploadedFile>
     */
    public function attachmentsFromMailgunRequest(array $input, array $files): array
    {
        $attachments = [];
        $count = (int) ($input['attachment-count'] ?? 0);

        for ($i = 1; $i <= $count; $i++) {
            $key = 'attachment-'.$i;

            if (isset($files[$key]) && $files[$key]->isValid()) {
                $attachments[] = $files[$key];
            }
        }

        return $attachments;
    }
}
