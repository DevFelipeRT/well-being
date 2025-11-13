<?php

declare(strict_types=1);

namespace App\Http\Requests\CheckIn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class StoreCheckInRequest extends FormRequest
{
    /**
     * Authorizes the request for authenticated users.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Validation rules for creating a check-in.
     */
    public function rules(): array
    {
        return [
            'checked_at' => [
                'required',
                'date',
                'date_format:Y-m-d',
                Rule::unique('check_ins', 'checked_at')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'score' => ['required', 'integer', 'between:1,5'],
            'note'  => ['nullable', 'string'],
        ];
    }
}
