<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mailer\SendMailRequest;
use App\Mail\OrderMail;
use App\Mail\FileOrderMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class MailerController extends Controller
{
    /**
     * Enviar correo sin adjuntos
     */
    public function store(SendMailRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            Mail::to($data['email'])->send(new OrderMail($data));

            Log::channel('activity')->info('sendmail', [
                'endpoint' => 'v1/sendmail',
                'payload' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado correctamente',
            ]);
        } catch (Exception $e) {
            Log::error('MailerController@store', [$e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error al enviar el correo',
            ], 500);
        }
    }

    /**
     * Enviar correo con adjuntos predefinidos
     */
    public function sendFile(SendMailRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            Mail::to($data['email'])->send(new FileOrderMail($data));

            Log::channel('activity')->info('sendfiles', [
                'endpoint' => 'v1/sendfiles',
                'payload' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo con archivos adjuntos enviado correctamente',
            ]);
        } catch (Exception $e) {
            Log::error('MailerController@sendFile', [$e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error al enviar el correo con adjuntos',
            ], 500);
        }
    }
} 