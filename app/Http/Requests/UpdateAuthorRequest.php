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
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'middle_name' => 'sometimes|string',
            'biography' => 'sometimes|string',
            'birth_date' => 'sometimes|date',
            'death_date' => 'sometimes|date|after_or_equal:birth_date',
            'country' => 'sometimes|string|max:255',
        ];
    }
}
