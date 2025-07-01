<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'       => 'required|numeric|min:0.5',
            'currency'     => 'required|string|size:3',
            'description'  => 'required|string|max:255',
            'metadata'     => 'sometimes|array',
            'success_url'  => 'required|url',
            'cancel_url'   => 'required|url',
            'customer_email' => 'sometimes|email'
        ];
    }
} 