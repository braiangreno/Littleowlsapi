<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    | Esta archivo almacena las credenciales de servicios de terceros como
    | Stripe, Mailgun, AWS SES, etc. Esto permite mantener esos datos en un
    | solo lugar y utilizar la directiva "env" para que las credenciales no
    | queden en el código fuente.
    */

    'stripe' => [
        'secret'          => env('STRIPE_SECRET'),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET'),
        'key'             => env('STRIPE_KEY'),
        'success_url'     => env('STRIPE_SUCCESS_URL'),
        'cancel_url'      => env('STRIPE_CANCEL_URL'),
    ],

]; 