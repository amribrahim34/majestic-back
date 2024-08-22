<?php
// app/Http/Requests/SalesOverviewRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesOverviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period' => 'required|in:30days,6months,1year',
        ];
    }
}
