<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Check-in entry for a specific user and day.
 *
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon $checked_at
 * @property int $score
 * @property string|null $note
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 *
 * @method static Builder|self forUser(int $userId)
 * @method static Builder|self betweenDates(CarbonInterface $from, CarbonInterface $to)
 * @method static Builder|self ordered()
 */
class CheckIn extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'check_ins';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'checked_at',
        'score',
        'note',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'checked_at' => 'date',
        'score'      => 'integer',
    ];

    /**
     * Owner of the check-in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope by owner.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by inclusive date range on checked_at.
     */
    public function scopeBetweenDates(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->whereDate('checked_at', '>=', $from->toDateString())
                     ->whereDate('checked_at', '<=', $to->toDateString());
    }

    /**
     * Scope default ordering (newest first).
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('checked_at')->orderByDesc('id');
    }

    /**
     * Indicates whether the check-in belongs to the given user.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Indicates whether the check-in is for the provided calendar day.
     */
    public function isForDay(CarbonInterface $day): bool
    {
        return $this->checked_at->isSameDay($day);
    }
}
