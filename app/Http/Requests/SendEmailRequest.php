<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendEmailRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta petición.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtener las reglas de validación que aplican a la petición.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'cc' => 'sometimes|array',
            'cc.*' => 'email',
            'bcc' => 'sometimes|array', 
            'bcc.*' => 'email',
            'reply_to' => 'sometimes|email',
            'attachments' => 'sometimes|array',
            'attachments.*.path' => 'required_without:attachments.*.data|string',
            'attachments.*.data' => 'required_without:attachments.*.path|string',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.mime' => 'sometimes|string|max:100',
        ];
    }

    /**
     * Obtener mensajes de error personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to.required' => 'El campo destinatario es obligatorio.',
            'to.email' => 'El destinatario debe ser una dirección de email válida.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.max' => 'El asunto no puede exceder los 255 caracteres.',
            'body.required' => 'El cuerpo del mensaje es obligatorio.',
            'cc.*.email' => 'Todas las direcciones CC deben ser emails válidos.',
            'bcc.*.email' => 'Todas las direcciones BCC deben ser emails válidos.',
            'reply_to.email' => 'La dirección de respuesta debe ser un email válido.',
            'attachments.*.path.required_without' => 'Cada adjunto debe tener una ruta o datos.',
            'attachments.*.data.required_without' => 'Cada adjunto debe tener datos o una ruta.',
            'attachments.*.name.required' => 'Cada adjunto debe tener un nombre.',
            'attachments.*.name.max' => 'El nombre del adjunto no puede exceder los 255 caracteres.',
        ];
    }

    /**
     * Manejar una validación fallida.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
} 