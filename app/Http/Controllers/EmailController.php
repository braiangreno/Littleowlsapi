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