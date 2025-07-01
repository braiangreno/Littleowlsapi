<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class FileOrderMail extends Mailable
{

    protected $data;
    protected $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->files = [
            storage_path('app/files/Al0rCa-childcareap.pdf'),
            storage_path('app/files/policies and procedures LO.pdf'),
            storage_path('app/files/rsZrzm-emergency.pdf'),
            storage_path('app/files/t5sei2-photoper.pdf'),
			storage_path('app/files/ProCare App permission.pdf')
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = 'mails.order';
        $email = $this->view($view)
            ->from(env('MAIL_USERNAME'))
            ->subject($this->data['subject'])
            ->with(['html' => $this->data['body']]);

        foreach ($this->files as $file) {
            $email->attach($file, [
                'as' => basename($file),
                'mime' => mime_content_type($file),
            ]);
        }

        return $email;
    }
}
