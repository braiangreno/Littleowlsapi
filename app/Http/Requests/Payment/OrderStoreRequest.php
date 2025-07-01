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
            'currency'     => 'sometimes|string|size:3',
            'description'  => 'sometimes|string|max:255',
            'metadata'     => 'sometimes|array',
            'quantity_all' => 'sometimes|integer|min:1',
            'product'      => 'sometimes|array',
            'scheduleId'   => 'sometimes',
            'success_url'  => 'required|url',
            'cancel_url'   => 'required|url',
            'customer_email' => 'sometimes|email'
        ];
    }
} 