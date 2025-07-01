<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as FormRequest;

abstract class JsonFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    protected function failedValidation(Validator $validator)
    {
        $errors = collect((new ValidationException($validator))->errors());

        throw new HttpResponseException(
            response()->json(['state' => 'fail', 'error' => $errors->first()[0]], 401)
        );
    }
}
