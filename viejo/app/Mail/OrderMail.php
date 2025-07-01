<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OrderMail extends Mailable
{

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = 'mails.order';

        return $this->view($view)
            ->from(env('MAIL_USERNAME'))
            ->subject($this->data['subject'])
            ->with(['html' => $this->data['body']]);
    }
}
