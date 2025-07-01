<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\JsonFormRequest;

class OrderStoreRequest extends JsonFormRequest
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
            'amount'    => 'required|numeric',
            'quantity_all'  => 'required|numeric',
            'product'   => 
                function ($attribute, $value, $fail) {
                    foreach($value as $name) {
                        if(trim($name['name']) == "") {
                            $fail('invalid.name');
                            return;
                        }

                        if(intval($name['quantity']) <= 0) {
                            $fail('invalid.quantity');
                            return;
                        }
                    }
                },
        ];
    }
}
