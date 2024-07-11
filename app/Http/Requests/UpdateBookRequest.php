<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'title' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'publisher_id' => 'sometimes|exists:publishers,id',
            'publication_date' => 'nullable|date',
            'language_id' => 'sometimes|exists:languages,id',
            'isbn10' => 'nullable|string|size:10',
            'isbn13' => 'nullable|string|size:13',
            'num_pages' => 'nullable|integer|min:1',
            'dimensions' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'format' => 'sometimes|in:PDF,Hard Copy,Audiobook',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'description' => 'sometimes|string',
        ];
    }
}
