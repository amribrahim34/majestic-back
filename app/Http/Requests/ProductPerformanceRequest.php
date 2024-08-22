<?php
// app/Http/Requests/ProductPerformanceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPerformanceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'top_selling_limit' => 'sometimes|integer|min:1|max:100',
            'low_stock_threshold' => 'sometimes|integer|min:1',
        ];
    }
}
