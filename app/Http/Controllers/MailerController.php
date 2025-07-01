<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mailer\SendMailRequest;
use App\Mail\OrderMail;
use App\Mail\FileOrderMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @OA\Tag(name="Legacy Mail")
 */
class MailerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/sendmail",
     *     tags={"Legacy Mail"},
     *     summary="Enviar correo simple (legacy)",
     *     requestBody=@OA\RequestBody(request="EmailPayloadLegacy", ref="#/components/requestBodies/EmailPayload"),
     *     @OA\Response(response=200, description="Correo enviado")
     * )
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
     * @OA\Post(
     *     path="/v1/sendfiles",
     *     tags={"Legacy Mail"},
     *     summary="Enviar correo con PDFs adjuntos (legacy)",
     *     requestBody=@OA\RequestBody(request="EmailPayloadLegacyFile", ref="#/components/requestBodies/EmailPayload"),
     *     @OA\Response(response=200, description="Correo enviado")
     * )
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