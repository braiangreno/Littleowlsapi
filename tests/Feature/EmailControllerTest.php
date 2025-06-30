<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Setup antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mockear el envío de emails
        Mail::fake();
    }

    /**
     * Test envío de email básico exitoso
     */
    public function test_puede_enviar_email_basico(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test Subject',
            'body' => 'Este es el cuerpo del mensaje de prueba'
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email enviado exitosamente'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'to',
                    'subject',
                    'sent_at'
                ]
            ]);

        // Verificar que el email fue enviado
        Mail::assertSent(function ($mail) use ($data) {
            return $mail->hasTo($data['to']) &&
                   $mail->subject === $data['subject'];
        });
    }

    /**
     * Test envío de email con CC y BCC
     */
    public function test_puede_enviar_email_con_cc_y_bcc(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test con CC y BCC',
            'body' => 'Email con copias',
            'cc' => ['cc1@example.com', 'cc2@example.com'],
            'bcc' => ['bcc1@example.com']
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(200);

        Mail::assertSent(function ($mail) use ($data) {
            return $mail->hasTo($data['to']) &&
                   $mail->hasCc('cc1@example.com') &&
                   $mail->hasCc('cc2@example.com') &&
                   $mail->hasBcc('bcc1@example.com');
        });
    }

    /**
     * Test envío de email con reply-to
     */
    public function test_puede_enviar_email_con_reply_to(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test con Reply-To',
            'body' => 'Email con dirección de respuesta',
            'reply_to' => 'reply@example.com'
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(200);

        Mail::assertSent(function ($mail) use ($data) {
            return $mail->hasReplyTo($data['reply_to']);
        });
    }

    /**
     * Test envío de email HTML
     */
    public function test_puede_enviar_email_html(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test HTML',
            'body' => '<h1>Test</h1><p>Este es un email HTML</p>'
        ];

        $response = $this->postJson('/api/email/send-html', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email enviado exitosamente'
            ]);
    }

    /**
     * Test validación - email inválido
     */
    public function test_validacion_email_invalido(): void
    {
        $data = [
            'to' => 'email-invalido',
            'subject' => 'Test',
            'body' => 'Test body'
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ])
            ->assertJsonValidationErrors(['to']);
    }

    /**
     * Test validación - campos requeridos
     */
    public function test_validacion_campos_requeridos(): void
    {
        $response = $this->postJson('/api/email/send', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to', 'subject', 'body']);
    }

    /**
     * Test validación - CC inválido
     */
    public function test_validacion_cc_invalido(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test',
            'body' => 'Test body',
            'cc' => ['email-invalido', 'otro-invalido']
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cc.0', 'cc.1']);
    }

    /**
     * Test envío con adjunto desde base64
     */
    public function test_puede_enviar_email_con_adjunto_base64(): void
    {
        $data = [
            'to' => 'test@example.com',
            'subject' => 'Test con adjunto',
            'body' => 'Email con archivo adjunto',
            'attachments' => [
                [
                    'data' => base64_encode('Contenido del archivo'),
                    'name' => 'documento.txt',
                    'mime' => 'text/plain'
                ]
            ]
        ];

        $response = $this->postJson('/api/email/send', $data);

        $response->assertStatus(200);

        Mail::assertSent(function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    /**
     * Test ruta de salud del API
     */
    public function test_ruta_health_check(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'service' => 'LittleOwls Email API'
            ])
            ->assertJsonStructure([
                'status',
                'service',
                'timestamp'
            ]);
    }
} 