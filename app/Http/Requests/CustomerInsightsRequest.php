<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerInsightsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period' => 'required|in:30days,6months,1year',
            'top_customers_limit' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
