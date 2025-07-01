<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use App\Mail\FileOrderMail;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'littleowls:send-test-emails {--to=braiangreno@gmail.com : Dirección de destino de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos de prueba (simple y con adjuntos) a la dirección indicada';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $to = $this->option('to');

        $this->info("Enviando correo simple a {$to} ...");
        Mail::to($to)->send(new OrderMail([
            'subject' => 'Correo de prueba – Simple',
            'body'    => '<p>Este es un <strong>correo de prueba</strong> en formato HTML.</p>'
        ]));
        $this->info('Correo simple enviado.');

        $this->info("Enviando correo con adjuntos a {$to} ...");
        Mail::to($to)->send(new FileOrderMail([
            'subject' => 'Correo de prueba – Con adjuntos',
            'body'    => '<p>Este correo incluye los archivos PDF predeterminados.</p>'
        ]));
        $this->info('Correo con adjuntos enviado.');

        $this->info('¡Proceso completado!');
        return Command::SUCCESS;
    }
} 