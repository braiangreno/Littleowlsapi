<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailController extends Controller
{
    /**
     * Enviar un email
     *
     * @OA\Post(
     *     path="/email/send",
     *     tags={"Email"},
     *     summary="Enviar email de texto plano",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"to","subject","body"},
     *             @OA\Property(property="to", type="string", format="email"),
     *             @OA\Property(property="subject", type="string"),
     *             @OA\Property(property="body", type="string"),
     *             @OA\Property(property="cc", type="array", @OA\Items(type="string", format="email")),
     *             @OA\Property(property="bcc", type="array", @OA\Items(type="string", format="email")),
     *             @OA\Property(property="reply_to", type="string", format="email"),
     *             @OA\Property(property="attachments", type="array", @OA\Items(
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="data", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="mime", type="string")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Email enviado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     *
     * @param SendEmailRequest $request
     * @return JsonResponse
     */
    public function send(SendEmailRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Enviar el email
            Mail::raw($data['body'], function ($message) use ($data) {
                $message->to($data['to'])
                    ->subject($data['subject']);

                // CC opcional
                if (!empty($data['cc'])) {
                    $message->cc($data['cc']);
                }

                // BCC opcional
                if (!empty($data['bcc'])) {
                    $message->bcc($data['bcc']);
                }

                // Reply-to opcional
                if (!empty($data['reply_to'])) {
                    $message->replyTo($data['reply_to']);
                }

                // Adjuntos opcionales
                if (!empty($data['attachments'])) {
                    foreach ($data['attachments'] as $attachment) {
                        if (isset($attachment['path'])) {
                            $message->attach($attachment['path'], [
                                'as' => $attachment['name'] ?? null,
                                'mime' => $attachment['mime'] ?? null,
                            ]);
                        } elseif (isset($attachment['data'])) {
                            $message->attachData(
                                base64_decode($attachment['data']),
                                $attachment['name'],
                                ['mime' => $attachment['mime'] ?? 'application/octet-stream']
                            );
                        }
                    }
                }
            });

            // Log de éxito
            Log::info('Email enviado exitosamente', [
                'to' => $data['to'],
                'subject' => $data['subject']
            ]);

            Log::channel('activity')->info('email_send', [
                'endpoint' => 'email/send',
                'payload' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email enviado exitosamente',
                'data' => [
                    'to' => $data['to'],
                    'subject' => $data['subject'],
                    'sent_at' => now()->toIso8601String()
                ]
            ], 200);

        } catch (Exception $e) {
            // Log del error
            Log::error('Error al enviar email', [
                'error' => $e->getMessage(),
                'to' => $data['to'] ?? null,
                'subject' => $data['subject'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => config('app.debug') ? $e->getMessage() : 'Ha ocurrido un error al procesar su solicitud'
            ], 500);
        }
    }

    /**
     * Enviar email con plantilla HTML
     *
     * @OA\Post(
     *     path="/email/send-html",
     *     tags={"Email"},
     *     summary="Enviar email HTML",
     *     @OA\RequestBody(request="EmailPayload", ref="#/components/requestBodies/EmailPayload"),
     *     @OA\Response(response=200, description="Email enviado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     *
     * @param SendEmailRequest $request
     * @return JsonResponse
     */
    public function sendHtml(SendEmailRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Enviar el email con HTML
            Mail::html($data['body'], function ($message) use ($data) {
                $message->to($data['to'])
                    ->subject($data['subject']);

                // CC opcional
                if (!empty($data['cc'])) {
                    $message->cc($data['cc']);
                }

                // BCC opcional
                if (!empty($data['bcc'])) {
                    $message->bcc($data['bcc']);
                }

                // Reply-to opcional
                if (!empty($data['reply_to'])) {
                    $message->replyTo($data['reply_to']);
                }

                // Adjuntos opcionales
                if (!empty($data['attachments'])) {
                    foreach ($data['attachments'] as $attachment) {
                        if (isset($attachment['path'])) {
                            $message->attach($attachment['path'], [
                                'as' => $attachment['name'] ?? null,
                                'mime' => $attachment['mime'] ?? null,
                            ]);
                        } elseif (isset($attachment['data'])) {
                            $message->attachData(
                                base64_decode($attachment['data']),
                                $attachment['name'],
                                ['mime' => $attachment['mime'] ?? 'application/octet-stream']
                            );
                        }
                    }
                }
            });

            // Log de éxito
            Log::info('Email HTML enviado exitosamente', [
                'to' => $data['to'],
                'subject' => $data['subject']
            ]);

            Log::channel('activity')->info('email_send_html', [
                'endpoint' => 'email/send-html',
                'payload' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email enviado exitosamente',
                'data' => [
                    'to' => $data['to'],
                    'subject' => $data['subject'],
                    'sent_at' => now()->toIso8601String()
                ]
            ], 200);

        } catch (Exception $e) {
            // Log del error
            Log::error('Error al enviar email HTML', [
                'error' => $e->getMessage(),
                'to' => $data['to'] ?? null,
                'subject' => $data['subject'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => config('app.debug') ? $e->getMessage() : 'Ha ocurrido un error al procesar su solicitud'
            ], 500);
        }
    }
} 