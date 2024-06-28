<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'publication_date' => 'nullable|date',
            'language_id' => 'required|exists:languages,id',
            'isbn10' => 'nullable|string|size:10',
            'isbn13' => 'nullable|string|size:13',
            'num_pages' => 'nullable|integer|min:1',
            'dimensions' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'format' => 'required|in:PDF,Hard Copy,Audiobook',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|array',
            'description.*' => 'string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
