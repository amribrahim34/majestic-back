<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
            'first_name' => 'required|array',
            'first_name.*' => 'string|max:255',
            'last_name' => 'required|array',
            'last_name.*' => 'string|max:255',
            'middle_name' => 'nullable|array',
            'middle_name.*' => 'string|max:255',
            'biography' => 'nullable|array',
            'biography.*' => 'string|max:255',
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date|after_or_equal:birth_date',
            'country' => 'nullable|string|max:255',
        ];
    }



    public function messages()
    {
        // Optionally, you can customize the error messages for specific rules
        return [
            'first_name.required' => __('validation.required', ['attribute' => 'first name']),
            'last_name.required' => __('validation.required', ['attribute' => 'last name']),
            // Add other messages as needed
        ];
    }
}
