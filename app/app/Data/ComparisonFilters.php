<?php

namespace App\Data;

use Carbon\Carbon;
use Illuminate\Http\Request;

readonly class ComparisonFilters
{
    public const DEFAULT_PERIOD_DAYS = 90;

    /** @var list<int> */
    public const PERIOD_OPTIONS = [30, 90, 365];

    public function __construct(
        public ?int $wholesalerId = null,
        public int $periodDays = self::DEFAULT_PERIOD_DAYS,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $periodDays = (int) $request->input('period', self::DEFAULT_PERIOD_DAYS);

        if (! in_array($periodDays, self::PERIOD_OPTIONS, true)) {
            $periodDays = self::DEFAULT_PERIOD_DAYS;
        }

        $wholesalerId = $request->filled('wholesaler_id')
            ? (int) $request->input('wholesaler_id')
            : null;

        return new self($wholesalerId, $periodDays);
    }

    public function periodStart(): Carbon
    {
        return now()->subDays($this->periodDays)->startOfDay();
    }

    public function periodEnd(): Carbon
    {
        return now()->endOfDay();
    }

    /**
     * @return array<string, int>
     */
    public function toQueryArray(): array
    {
        $query = ['period' => $this->periodDays];

        if ($this->wholesalerId !== null) {
            $query['wholesaler_id'] = $this->wholesalerId;
        }

        return $query;
    }
}
