<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateOrderRequest extends FormRequest
{
    // доступные часы для аренды
    private const HOURS = [4, 8, 12, 24];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * @throws ValidationException
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('type')) {
            $this->merge(['type' => 'buy']);
        }
        if ($this->get('type') === 'rent' && $this->has('hours')) {
            if (!in_array($this->get('hours'), self::HOURS, true)) {
                throw ValidationException::withMessages(['hours' => 'Hours must be in '.implode(',', self::HOURS).' values']);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|int',
            'type' => 'nullable|string|in:buy,rent',
            'hours' => 'required_if:type,rent|int',
        ];
    }
}
