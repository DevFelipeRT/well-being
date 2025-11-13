<?php

declare(strict_types=1);

namespace App\Services\CheckIn;

use App\Dtos\CheckInData;
use App\Mappers\CheckInMapper;
use App\Models\CheckIn;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Application service for managing well-being check-ins.
 * Enforces one check-in per user per calendar day and ownership constraints.
 */
final class CheckInService
{
    /**
     * Creates a new check-in for the given user.
     * Throws \DomainException when a check-in already exists for the same day.
     */
    public function createForUser(int $userId, CheckInData $data): CheckIn
    {
        $day = $data->checkedAt; // 'Y-m-d'

        $exists = CheckIn::query()
            ->where('user_id', $userId)
            ->whereDate('checked_at', $day)
            ->exists();

        if ($exists) {
            throw new \DomainException('Check-in for this day already exists.');
        }

        $attrs = CheckInMapper::toAttributes($data);
        $attrs['user_id']   = $userId;
        $attrs['checked_at'] = $day;

        /** @var CheckIn $checkIn */
        $checkIn = CheckIn::query()->create($attrs);

        return $checkIn;
    }

    /**
     * Updates an existing check-in owned by the given user.
     * Throws ModelNotFoundException if not owned. Throws \DomainException on day collision.
     */
    public function updateForUser(int $userId, int $checkInId, CheckInData $data): CheckIn
    {
        /** @var CheckIn $model */
        $model = CheckIn::query()
            ->whereKey($checkInId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $day = $data->checkedAt; // 'Y-m-d'

        $collision = CheckIn::query()
            ->where('user_id', $userId)
            ->whereDate('checked_at', $day)
            ->whereKeyNot($model->getKey())
            ->exists();

        if ($collision) {
            throw new \DomainException('Another check-in already exists for this day.');
        }

        $attrs = CheckInMapper::toAttributes($data);
        $attrs['checked_at'] = $day;

        $model->fill($attrs);
        $model->save();

        return $model;
    }

    /**
     * Deletes an existing check-in owned by the given user.
     * Throws ModelNotFoundException if not owned.
     */
    public function deleteForUser(int $userId, int $checkInId): void
    {
        $deleted = CheckIn::query()
            ->whereKey($checkInId)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted === 0) {
            throw (new ModelNotFoundException())->setModel(CheckIn::class, [$checkInId]);
        }
    }

    /**
     * Finds today's check-in for the given user or null when not found.
     */
    public function findToday(int $userId, ?CarbonInterface $today = null): ?CheckIn
    {
        $date = ($today ?? CarbonImmutable::today())->toDateString();

        /** @var CheckIn|null $checkIn */
        $checkIn = CheckIn::query()
            ->where('user_id', $userId)
            ->whereDate('checked_at', $date)
            ->first();

        return $checkIn;
    }
}
