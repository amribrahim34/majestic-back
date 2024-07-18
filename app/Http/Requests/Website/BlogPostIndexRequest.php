<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'sometimes|integer|min:1|max:50',
        ];
    }
}
