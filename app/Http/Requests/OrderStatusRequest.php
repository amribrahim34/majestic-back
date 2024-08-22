<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'recent_orders_limit' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
