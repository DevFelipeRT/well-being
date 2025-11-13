<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Dtos\CheckInData;
use Carbon\CarbonImmutable;

final class CheckInMapper
{
    /**
     * Builds a DTO from already validated input.
     * Expects keys: checked_at (date string), score (int), note (?string).
     */
    public static function toDto(array $payload): CheckInData
    {
        $checkedAt = CarbonImmutable::parse((string) $payload['checked_at'])->toDateString();
        $score = (int) $payload['score'];

        $note = array_key_exists('note', $payload) ? $payload['note'] : null;
        $note = is_null($note) ? null : trim((string) $note);
        $note = $note === '' ? null : $note;

        return new CheckInData(
            checkedAt: $checkedAt,
            score: $score,
            note: $note,
        );
        }

    /**
     * Converts a DTO into attributes suitable for mass assignment.
     */
    public static function toAttributes(CheckInData $dto): array
    {
        return [
            'checked_at' => $dto->checkedAt,
            'score'      => $dto->score,
            'note'       => $dto->note,
        ];
    }
}
