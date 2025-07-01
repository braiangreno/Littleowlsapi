<?php

namespace App\Payments;

use Exception;
use Stripe\Stripe as StripeClient;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Product;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Stripe
{
    private $config = [];
    private $errors;

    public function __construct($config = null)
    {
        $this->config['SUCCESS_URL'] = $config['STRIPE_SUCCESS_URL'] ?? env("STRIPE_SUCCESS_URL");
        $this->config['CANCEL_URL'] = $config['STRIPE_CANCEL_URL'] ?? env("STRIPE_CANCEL_URL");
        StripeClient::setApiKey($config['STRIPE_CANCEL_URL'] ?? env('STRIPE_SECRET'));
    }

    public function login()
    {

    }

    public function order($data)
    {
        try {
            $session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $data['currency'],
                        'product_data' => [
                            'name' => $data['product']['name'],
                        ],
                        'unit_amount' => $data['amount'] * 100,
                    ],
                    'quantity' => $data['quantity'],
                ]],
                'metadata' => [
                    'orderId' => $data['orderId'],
                ],
                'mode'          => 'payment',
                'success_url'   => $this->config['SUCCESS_URL'],
                'cancel_url'    => $this->config['CANCEL_URL'],
            ]);
    
            return $session->url;
        } catch(Exception $e) {
            $this->errors = $e->getMessage();
        }

        return null;
    }

    public function subscription($data)
    {
        $productId = $this->findProduct($data);
        if(is_null($productId)) 
        {
            $productId = $this->createProduct($data);
            $this->createPrice(['productId' => $productId, 'amount' => $data['amount'], 'days' => $data['days']]);
        }

        $priceId = $this->findPrice($productId);
        try {
            $session = CheckoutSession::create([
                'client_reference_id'       => $data['user']['id'],
                'metadata'                  => ['plan_id' => $data['id'], 'user_id' => $data['user']['id']],
                'payment_method_types'      => ['card'],
                'line_items' => [[
                    'price'     => $priceId,
                    'quantity'  => $data['quantity'] ?? 1,
                ]],
                'mode'          => 'subscription',
                'success_url'   => $this->config['SUCCESS_URL'],
                'cancel_url'    => $this->config['CANCEL_URL'],
            ]);
            
            return ['url' => $session->url];
        } catch(Exception $e) {
            $this->errors = $e->getMessage();
        }

        return null;
    }

    public function changeSubscription($data) 
    {
        $subscriptionId = $data['subscription_id'];

        $subscription = Subscription::retrieve($subscriptionId);

        // Cambiar al nuevo plan (ID del precio)
        $subscription->items->create([
            'price' => $data['nuevo_price_id'],
        ]);

        // Eliminar el plan anterior
        $subscriptionItem = $subscription->items->data[0]->id;
        Subscription::update($subscriptionId, [
            'items' => [
                ['id' => $subscriptionItem, 'deleted' => true],
            ],
        ]);

        $subscription->save();
    }

    public function cancelSubscription($data) 
    {

    }

    public function webhook($data) 
    {
        //Log::info('payment webhook', $data->all());
        $payload = $data->getContent();
        $event = null;
        Log::channel('stack')->info('payload', [$data]);
        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            Log::error('Stripe UnexpectedValueException: ' . $e->getMessage());
            return false;
        }

        switch ($event->type) 
        {
            case 'checkout.session.completed':
                $session = $event->data->object;
                
                try {
   
                    DB::beginTransaction();

                    DB::commit();
                    
                } catch(Exception $e) {
                    DB::rollback();
                    Log::error('Stripe checkout.session.completed: ' . $e->getMessage());
                    return false;
                }
            break;
            case 'customer.subscription.created':
                $subscription = $event->data->object;
            break;
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
            break;
            case 'customer.subscription.updated':
                $subscription = $event->data->object;
            break;
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
            break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;

                try {
                    DB::beginTransaction();

                    DB::commit();
                } catch(Exception $e) {
                    DB::rollback();
                    Log::error('Stripe invoice.payment_succeeded: ' . $e->getMessage());
                    return false;
                }
            break;
            case 'subscription_schedule.completed':
                $subscriptionSchedule = $event->data->object;
            break;
            default:
                Log::error('Received unknown event type ' . $event->type);
            break;
          }
        return true;
    }

    public function getError()
    {
        return $this->errors;
    }

    private function createProduct($product) 
    {
        $product = Product::create([
            'name'          => $product['name'],
            'description'   => $product['description'],
            'active'        => true
        ]);

        return $product->id;
    }

    private function findProduct($product) 
    { 
        $productFind = Product::search([
            'query' => 'active:\'true\' AND name:\'' . $product['name'] . '\''
        ]);
        return $productFind->id;
    }

    private function createPrice($product) 
    {
        $price = Price::create([
            'product'       => $product['productId'], // ID del producto que creaste
            'unit_amount'   => $product['amount'] * 100, // Por ejemplo, $20.00 USD
            'currency'      => 'usd',
            'recurring'     => ['interval' => 'day', "interval_count" => $product['days']], 
        ]);
        
        return $price->id;
    }

    private function findPrice($productId)
    {
        $prices = Price::all([
            'product' => $productId,
            'active' => true,
        ]);
    
        foreach ($prices->data as $price) {
            if ($price->product == $productId) {
                return $price->id; // Retorna el primer precio activo encontrado para el producto
            }
        }
    
        return null; // Retorna null si no se encuentra ningún precio para el producto
    }
    
}
