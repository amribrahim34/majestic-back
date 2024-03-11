<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublisherRequest extends FormRequest
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
            'publisher_name' => 'sometimes|array',
            'publisher_name.*' => 'string|max:255',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'location' => 'sometimes|string|max:255',
            'website' => 'sometimes|url|max:255',
        ];
    }
}
