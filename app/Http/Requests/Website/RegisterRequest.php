<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'user_name' => 'required|string|max:255|min:4',
            'email' => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|max:255|min:8|confirmed',
            'mobile' => 'numeric|min:8|unique:users,mobile',
            'address' => 'string|max:255|min:10',
            'city' => 'string|max:255|min:4',
            'state_province' => 'string|max:255|min:4',
            'gender' => 'string|max:255|min:4',
            'avatar' => 'string|max:255|min:4',
            'birthday' => 'date|max:255|min:4',
        ];
    }
}
