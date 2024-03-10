<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Assuming the same rules as StoreAuthorRequest, but adjust as necessary
            'first_name' => 'sometimes|required|array',
            'first_name.*' => 'string|max:255',
            'last_name' => 'sometimes|required|array',
            'last_name.*' => 'string|max:255',
            'middle_name' => 'sometimes|array',
            'middle_name.*' => 'string|max:255',
            'biography' => 'sometimes|array',
            'biography.*' => 'string|max:255',
            'birth_date' => 'sometimes|date',
            'death_date' => 'sometimes|date|after_or_equal:birth_date',
            'country' => 'sometimes|string|max:255',
        ];
    }
}
