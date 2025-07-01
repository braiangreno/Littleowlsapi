<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Payment\OrderStoreRequest;
use App\Payments\Stripe;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $payment;

    public function __construct(Request $request)
    {
        $this->payment = new Stripe();
    }

    public function createOrder(OrderStoreRequest $request)
    {
        $data = $request->all();
        
        $result = $this->payment->order(
            [
                'currency'  => 'usd',
                'amount'    => $data['amount'], 
                'quantity'  => $data['quantity_all'],
                'orderId'   => $data['scheduleId'],
                'product'   => 
                    [
                        'name' => $data['product'][0]['name']
                    ]
            ]);

        if(!is_null($result))
        {
            return $this->success(['payment' => $result]);
        }

        return $this->error($this->payment->getError());
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::channel('stack')->info('PaymentController@webhook', [$data]);
        $this->payment->webhook($data);
        return $this->success();
    }
}
