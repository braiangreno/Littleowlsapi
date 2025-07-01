<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\OrderStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Exception;

/**
 * @OA\Post(
 *     path="/v1/order",
 *     tags={"Pagos"},
 *     summary="Crear sesión de pago en Stripe",
 *     @OA\RequestBody(request="StripeOrder", required=true,
 *         @OA\JsonContent(
 *             required={"amount","currency","description","success_url","cancel_url"},
 *             @OA\Property(property="amount", type="number", format="float"),
 *             @OA\Property(property="currency", type="string", example="usd"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="metadata", type="object"),
 *             @OA\Property(property="success_url", type="string", format="url"),
 *             @OA\Property(property="cancel_url", type="string", format="url"),
 *             @OA\Property(property="customer_email", type="string", format="email")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Checkout URL generado")
 * )
 */
class PaymentController extends Controller
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Crear una sesión de pago en Stripe
     */
    public function createOrder(OrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $session = $this->stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency'     => $data['currency'],
                        'product_data' => [
                            'name' => $data['description'],
                        ],
                        'unit_amount'  => (int) round($data['amount'] * 100), // convertir a centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => $data['metadata'] ?? [],
                'success_url' => $data['success_url'],
                'cancel_url'  => $data['cancel_url'],
                'customer_email' => $data['customer_email'] ?? null,
            ]);

            Log::channel('activity')->info('order_created', [
                'endpoint' => 'v1/order',
                'request' => $data,
                'stripe_session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);
        } catch (Exception $e) {
            Log::error('PaymentController@createOrder', [$e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden',
                'error'   => config('app.debug') ? $e->getMessage() : 'Error interno',
            ], 500);
        }
    }

    /**
     * Webhook para eventos de Stripe
     */
    /**
     * @OA\Post(
     *     path="/v1/payments/webhook",
     *     tags={"Pagos"},
     *     summary="Webhook de Stripe",
     *     @OA\Response(response=200, description="Evento procesado")
     * )
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Manejar evento concretos (ejemplo)
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Log::info('Pago completado', ['session' => $session->id]);
                Log::channel('activity')->info('stripe_webhook', [
                    'endpoint' => 'v1/payments/webhook',
                    'type' => $event->type,
                    'session_id' => $session->id,
                ]);
                // Aquí podrías actualizar bases de datos, enviar emails, etc.
                break;
            default:
                Log::info('Evento Stripe recibido', ['type' => $event->type]);
                Log::channel('activity')->info('stripe_webhook', [
                    'endpoint' => 'v1/payments/webhook',
                    'type' => $event->type,
                ]);
        }

        return response()->json(['status' => 'success']);
    }
} 