<?php

declare(strict_types=1);

namespace App\Dtos;

/**
 * Plain data carrier for a well-being check-in.
 * Values must arrive already validated and normalized by upstream layers.
 *
 * Contract:
 * - $checkedAt must be a date string in 'Y-m-d' format.
 * - $score must be an integer in the business-accepted range.
 * - $note may be null or a pre-trimmed string.
 */
final readonly class CheckInData
{
    public function __construct(
        public string $checkedAt,
        public int $score,
        public ?string $note = null,
    ) {}

    /**
     * Returns a shallow array representation suitable for persistence layers.
     */
    public function toArray(): array
    {
        return [
            'checked_at' => $this->checkedAt,
            'score'      => $this->score,
            'note'       => $this->note,
        ];
    }
}
