<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_name' => 'required|max:128',
            'customer_lastname' => 'required|max:128',
            'customer_address' => 'required|max:128',
            'customer_phone' => 'required|max:20',
            'customer_email' => 'required|email',
            'customer_total_price' => 'required|numeric|min:0'
        ];
    }
}
