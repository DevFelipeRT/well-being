<?php

declare(strict_types=1);

namespace App\Http\Requests\CheckIn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class IndexCheckInRequest extends FormRequest
{
    /**
     * Authorizes the request for authenticated users.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Normalizes optional inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'from'     => $this->filled('from') ? trim((string) $this->input('from')) : null,
            'to'       => $this->filled('to') ? trim((string) $this->input('to')) : null,
            'per_page' => $this->filled('per_page') ? (int) $this->input('per_page') : null,
        ]);
    }

    /**
     * Validation rules for listing check-ins with optional filters.
     */
    public function rules(): array
    {
        return [
            'from' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:to',
            ],
            'to' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
