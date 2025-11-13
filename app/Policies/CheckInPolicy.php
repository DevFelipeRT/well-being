<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CheckIn;
use App\Models\User;

final class CheckInPolicy
{
    /**
     * Determines if the authenticated user can view the given check-in.
     */
    public function view(User $user, CheckIn $checkIn): bool
    {
        return $checkIn->user_id === $user->id;
    }

    /**
     * Determines if the authenticated user can update the given check-in.
     */
    public function update(User $user, CheckIn $checkIn): bool
    {
        return $checkIn->user_id === $user->id;
    }

    /**
     * Determines if the authenticated user can delete the given check-in.
     */
    public function delete(User $user, CheckIn $checkIn): bool
    {
        return $checkIn->user_id === $user->id;
    }
}
