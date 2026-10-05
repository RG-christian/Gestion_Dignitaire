<?php

namespace App\Support\Reports;

use Carbon\Carbon;
use InvalidArgumentException;

final class ReportPeriodResolver
{
    /**
     * Retourne la dernière période entièrement terminée.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function previous(string $type, ?Carbon $reference = null): array
    {
        $reference = ($reference ?? now())->copy();

        return match ($type) {
            'mensuel' => [
                $reference->copy()->subMonthNoOverflow()->startOfMonth(),
                $reference->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            'trimestriel' => [
                $reference->copy()->subQuarter()->firstOfQuarter(),
                $reference->copy()->subQuarter()->lastOfQuarter(),
            ],
            'semestriel' => $reference->month <= 6
                ? [
                    $reference->copy()->subYear()->month(7)->startOfMonth(),
                    $reference->copy()->subYear()->month(12)->endOfMonth(),
                ]
                : [
                    $reference->copy()->month(1)->startOfMonth(),
                    $reference->copy()->month(6)->endOfMonth(),
                ],
            'annuel' => [
                $reference->copy()->subYear()->startOfYear(),
                $reference->copy()->subYear()->endOfYear(),
            ],
            default => throw new InvalidArgumentException("Période invalide : {$type}."),
        };
    }
}
