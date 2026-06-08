<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use OpenAI;
use Smalot\PdfParser\Parser as PdfParser;

class PriceExtractionService
{
    private const MODEL = 'gpt-4o-mini';

    public function __construct(
        private readonly PdfParser $pdfParser = new PdfParser,
    ) {}

    /**
     * @return array{
     *   items: array<int, array<string, mixed>>,
     *   wholesaler_guess: ?string,
     *   document_date: ?string
     * }
     */
    public function extractFromUpload(UploadedFile $file): array
    {
        $mime = $file->getMimeType() ?? '';
        $extension = strtolower($file->getClientOriginalExtension());

        if (str_starts_with($mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'heic'], true)) {
            return $this->extractFromImage($file);
        }

        if ($mime === 'application/pdf' || $extension === 'pdf') {
            return $this->extractFromPdf($file);
        }

        throw new \InvalidArgumentException('Alleen foto\'s (JPG, PNG) of PDF-bestanden worden ondersteund.');
    }

    /**
     * @return array{
     *   items: array<int, array<string, mixed>>,
     *   wholesaler_guess: ?string,
     *   document_date: ?string
     * }
     */
    public function extractFromImage(UploadedFile $file): array
    {
        $base64 = base64_encode(file_get_contents($file->getRealPath()));
        $mime = $file->getMimeType() ?: 'image/jpeg';

        $response = $this->client()->chat()->create([
            'model' => self::MODEL,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Haal alle inkoopprijsregels uit deze factuur of prijslijst voor de horeca.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mime};base64,{$base64}",
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $this->parseResponse($response->choices[0]->message->content ?? '{}');
    }

    /**
     * @return array{
     *   items: array<int, array<string, mixed>>,
     *   wholesaler_guess: ?string,
     *   document_date: ?string
     * }
     */
    public function extractFromPdf(UploadedFile $file): array
    {
        $pdf = $this->pdfParser->parseFile($file->getRealPath());
        $text = trim(preg_replace('/\s+/', ' ', $pdf->getText()) ?? '');

        if (mb_strlen($text) < 40) {
            throw new \RuntimeException(
                'Deze PDF bevat weinig leesbare tekst (waarschijnlijk een scan). Maak een foto van het document en upload die.'
            );
        }

        $response = $this->client()->chat()->create([
            'model' => self::MODEL,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => "Haal alle inkoopprijsregels uit onderstaande factuur- of prijslijsttekst:\n\n{$text}",
                ],
            ],
        ]);

        return $this->parseResponse($response->choices[0]->message->content ?? '{}');
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Je bent een assistent die inkoopprijzen uit horeca-facturen en groothandelprijslijsten haalt.
Geef ALLEEN geldige JSON terug met dit schema:
{
  "items": [
    {
      "product_name": "string",
      "specification": "string|null",
      "price": 12.50,
      "unit": "kg|liter|doos|stuk|zak|krat|overig",
      "quantity_per_unit": "string|null"
    }
  ],
  "wholesaler_guess": "string|null",
  "document_date": "YYYY-MM-DD|null"
}
Regels:
- Prijzen als getal in euro, punt als decimaalteken.
- Sla regels zonder duidelijke prijs over.
- Probeer de groothandel en documentdatum te herkennen.
- Nederlandse productnamen behouden.
PROMPT;
    }

    /**
     * @return array{
     *   items: array<int, array<string, mixed>>,
     *   wholesaler_guess: ?string,
     *   document_date: ?string
     * }
     */
    private function parseResponse(string $json): array
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            Log::warning('OpenAI JSON parse failed', ['json' => $json, 'error' => $exception->getMessage()]);
            throw new \RuntimeException('Kon de prijzen niet automatisch uitlezen. Probeer opnieuw of voer handmatig in.');
        }

        $items = collect($data['items'] ?? [])
            ->filter(fn (array $item) => filled($item['product_name'] ?? null) && is_numeric($item['price'] ?? null))
            ->map(fn (array $item) => [
                'product_name' => trim((string) $item['product_name']),
                'specification' => filled($item['specification'] ?? null) ? trim((string) $item['specification']) : null,
                'price' => round((float) $item['price'], 2),
                'unit' => $this->normalizeUnit((string) ($item['unit'] ?? 'stuk')),
                'quantity_per_unit' => filled($item['quantity_per_unit'] ?? null) ? trim((string) $item['quantity_per_unit']) : null,
            ])
            ->values()
            ->all();

        if ($items === []) {
            throw new \RuntimeException('Geen prijsregels gevonden in het document. Controleer het bestand of voer handmatig in.');
        }

        return [
            'items' => $items,
            'wholesaler_guess' => filled($data['wholesaler_guess'] ?? null) ? trim((string) $data['wholesaler_guess']) : null,
            'document_date' => filled($data['document_date'] ?? null) ? (string) $data['document_date'] : null,
        ];
    }

    private function normalizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));

        return match (true) {
            str_contains($unit, 'kg') => 'kg',
            str_contains($unit, 'liter'), str_contains($unit, 'l') => 'liter',
            str_contains($unit, 'doos') => 'doos',
            str_contains($unit, 'zak') => 'zak',
            str_contains($unit, 'krat') => 'krat',
            default => 'stuk',
        };
    }

    private function client(): \OpenAI\Client
    {
        $apiKey = config('services.openai.api_key');

        if (! filled($apiKey)) {
            throw new \RuntimeException('OpenAI API-key ontbreekt. Zet OPENAI_API_KEY in je .env bestand.');
        }

        return OpenAI::client($apiKey);
    }
}
