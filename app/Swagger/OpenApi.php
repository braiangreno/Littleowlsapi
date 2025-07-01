<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="LittleOwls Email & Payment API",
 *     description="API para envío de correos y generación de órdenes de pago con Stripe."
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Servidor API"
 * )
 *
 * @OA\RequestBody(
 *     request="EmailPayload",
 *     required=true,
 *     @OA\JsonContent(
 *         required={"to","subject","body"},
 *         @OA\Property(property="to", type="string", format="email"),
 *         @OA\Property(property="subject", type="string"),
 *         @OA\Property(property="body", type="string"),
 *         @OA\Property(property="cc", type="array", @OA\Items(type="string", format="email")),
 *         @OA\Property(property="bcc", type="array", @OA\Items(type="string", format="email")),
 *         @OA\Property(property="reply_to", type="string", format="email"),
 *         @OA\Property(property="attachments", type="array", @OA\Items(
 *             @OA\Property(property="path", type="string"),
 *             @OA\Property(property="data", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="mime", type="string")
 *         ))
 *     )
 * )
 */
class OpenApi {} 