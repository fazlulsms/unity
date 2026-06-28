<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * A selected reporting period. Flows (collection, deposits, withdrawals, …) are
 * summed within [from, to]; position figures (cash in hand, available balance,
 * active FDR) are taken as of the period end (asOf()).
 *
 * null from/to means "open ended" — All Time when both are null.
 */
class DateRange
{
    public const PRESETS = [
        'this_month' => 'Current Month',
        'this_year'  => 'Current Year',
        'last_month' => 'Last Month',
        'last_year'  => 'Last Year',
        'all'        => 'All Time',
    ];

    public function __construct(
        public ?Carbon $from,
        public ?Carbon $to,
        public string $preset,
        public string $label,
    ) {}

    public static function fromRequest(Request $request, string $default = 'all'): self
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        // Explicit custom range takes priority.
        if ($from || $to) {
            $f = $from ? Carbon::parse($from)->startOfDay() : null;
            $t = $to ? Carbon::parse($to)->endOfDay() : null;
            return new self($f, $t, 'custom', self::buildLabel($f, $t, 'custom'));
        }

        $preset = $request->input('preset') ?: $default;
        return self::preset($preset);
    }

    public static function preset(string $preset): self
    {
        $now = Carbon::now();

        [$from, $to] = match ($preset) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'last_year'  => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default      => [null, null],
        };

        if (!array_key_exists($preset, self::PRESETS)) {
            $preset = 'all';
        }

        return new self($from, $to, $preset, self::buildLabel($from, $to, $preset));
    }

    public function isAll(): bool
    {
        return $this->from === null && $this->to === null;
    }

    /** Reference date for point-in-time (position) figures. */
    public function asOf(): Carbon
    {
        return $this->to ? $this->to->copy() : Carbon::now();
    }

    /** Number of whole calendar months the range spans (min 1). */
    public function monthCount(): int
    {
        if (!$this->from || !$this->to) {
            return 1;
        }
        return (int) ($this->from->copy()->startOfMonth()->diffInMonths($this->to->copy()->startOfMonth()) + 1);
    }

    /** Query params to preserve the period across links (shortcuts, PDF export). */
    public function queryParams(): array
    {
        if ($this->preset === 'custom') {
            return array_filter([
                'from' => $this->from?->toDateString(),
                'to'   => $this->to?->toDateString(),
            ]);
        }
        return ['preset' => $this->preset];
    }

    public function queryString(): string
    {
        return http_build_query($this->queryParams());
    }

    private static function buildLabel(?Carbon $from, ?Carbon $to, string $preset): string
    {
        if ($preset === 'all' || (!$from && !$to)) {
            return 'All Time';
        }
        $fl = $from ? $from->format('d M Y') : '…';
        $tl = $to ? $to->format('d M Y') : '…';
        return "{$fl} – {$tl}";
    }
}
