<?php

declare(strict_types=1);

namespace App\Http\Requests\CheckIn;

use App\Models\CheckIn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class UpdateCheckInRequest extends FormRequest
{
    /**
     * Authorizes the request using the update policy on the bound model.
     */
    public function authorize(): bool
    {
        /** @var CheckIn|null $checkIn */
        $checkIn = $this->route('checkIn');

        return $checkIn !== null && $this->user()?->can('update', $checkIn) === true;
    }

    /**
     * Validation rules for updating a check-in.
     */
    public function rules(): array
    {
        /** @var CheckIn|null $checkIn */
        $checkIn = $this->route('checkIn');

        return [
            'checked_at' => [
                'required',
                'date',
                'date_format:Y-m-d',
                Rule::unique('check_ins', 'checked_at')
                    ->ignore($checkIn?->getKey(), 'id')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'score' => ['required', 'integer', 'between:1,5'],
            'note'  => ['nullable', 'string'],
        ];
    }
}
