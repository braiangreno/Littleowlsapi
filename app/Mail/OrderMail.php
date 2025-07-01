<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OrderMail extends Mailable
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Construir el mensaje.
     */
    public function build(): static
    {
        return $this->view('mails.order')
            ->from(env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')))
            ->subject($this->data['subject'] ?? '')
            ->with([
                'html' => $this->data['body'] ?? ''
            ]);
    }
} 