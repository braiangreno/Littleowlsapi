<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class FileOrderMail extends Mailable
{
    protected array $data;
    protected array $files;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->files = [
            storage_path('app/files/Al0rCa-childcareap.pdf'),
            storage_path('app/files/policies and procedures LO.pdf'),
            storage_path('app/files/rsZrzm-emergency.pdf'),
            storage_path('app/files/t5sei2-photoper.pdf'),
            storage_path('app/files/ProCare App permission.pdf'),
        ];
    }

    public function build(): static
    {
        $email = $this->view('mails.order')
            ->from(env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')))
            ->subject($this->data['subject'] ?? '')
            ->with([
                'html' => $this->data['body'] ?? ''
            ]);

        foreach ($this->files as $file) {
            if (is_readable($file)) {
                $email->attach($file, [
                    'as'   => basename($file),
                    'mime' => mime_content_type($file),
                ]);
            }
        }

        return $email;
    }
} 