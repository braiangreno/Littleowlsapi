<?php

namespace App\Http\Requests\Mailer;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'   => 'required|email',
            'subject' => 'required|string',
            'body'    => 'required|string',
        ];
    }
} 