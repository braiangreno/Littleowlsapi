# LittleOwls Email API

API REST desarrollada en Laravel para el envío de emails mediante SMTP.

## Requisitos

- PHP >= 8.1
- Composer
- Extensiones PHP requeridas:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

## Instalación

1. **Clonar el repositorio o copiar los archivos**

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Copiar el archivo de configuración**
   ```bash
   cp .env.example .env
   ```

4. **Generar la clave de aplicación**
   ```bash
   php artisan key:generate
   ```

5. **Configurar las credenciales SMTP**
   
   El archivo `.env` ya contiene las credenciales SMTP proporcionadas:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=info@studiokidsmiami.com
   MAIL_PASSWORD=oiplilqiliwyogzb
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=info@studiokidsmiami.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Iniciar el servidor de desarrollo**
   ```bash
   php artisan serve
   ```
   
   La API estará disponible en `http://localhost:8000`

## Endpoints

### 1. Verificar estado del servicio
```
GET /api/health
```

**Respuesta exitosa:**
```json
{
    "status": "ok",
    "service": "LittleOwls Email API",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### 2. Enviar email de texto plano
```
POST /api/email/send
```

**Headers requeridos:**
```
Content-Type: application/json
Accept: application/json
```

**Body de la petición:**
```json
{
    "to": "destinatario@example.com",
    "subject": "Asunto del correo",
    "body": "Este es el contenido del mensaje",
    "cc": ["copia1@example.com", "copia2@example.com"],
    "bcc": ["copiaoculta@example.com"],
    "reply_to": "responder@example.com",
    "attachments": [
        {
            "data": "base64_encoded_content",
            "name": "documento.pdf",
            "mime": "application/pdf"
        }
    ]
}
```

**Campos obligatorios:**
- `to`: Email del destinatario
- `subject`: Asunto del correo
- `body`: Cuerpo del mensaje

**Campos opcionales:**
- `cc`: Array de emails para copia
- `bcc`: Array de emails para copia oculta
- `reply_to`: Email para responder
- `attachments`: Array de archivos adjuntos

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Email enviado exitosamente",
    "data": {
        "to": "destinatario@example.com",
        "subject": "Asunto del correo",
        "sent_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Respuesta de error de validación (422):**
```json
{
    "success": false,
    "message": "Error de validación",
    "errors": {
        "to": ["El destinatario debe ser una dirección de email válida."]
    }
}
```

### 3. Enviar email HTML
```
POST /api/email/send-html
```

El endpoint acepta los mismos parámetros que `/api/email/send`, pero el campo `body` será interpretado como HTML.

**Ejemplo de body HTML:**
```json
{
    "to": "destinatario@example.com",
    "subject": "Email con formato HTML",
    "body": "<h1>Título del Email</h1><p>Este es un <strong>email HTML</strong></p>"
}
```

## Ejemplos de uso

### cURL - Email simple
```bash
curl -X POST http://localhost:8000/api/email/send \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "to": "test@example.com",
    "subject": "Prueba de API",
    "body": "Este es un mensaje de prueba"
  }'
```

### cURL - Email con CC y BCC
```bash
curl -X POST http://localhost:8000/api/email/send \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "to": "principal@example.com",
    "subject": "Email con copias",
    "body": "Mensaje con copias",
    "cc": ["copia1@example.com", "copia2@example.com"],
    "bcc": ["oculta@example.com"]
  }'
```

### JavaScript (Fetch API)
```javascript
const sendEmail = async () => {
    const response = await fetch('http://localhost:8000/api/email/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            to: 'destinatario@example.com',
            subject: 'Prueba desde JavaScript',
            body: 'Este es el contenido del mensaje'
        })
    });
    
    const data = await response.json();
    console.log(data);
};
```

### PHP
```php
$client = new \GuzzleHttp\Client();

$response = $client->post('http://localhost:8000/api/email/send', [
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ],
    'json' => [
        'to' => 'destinatario@example.com',
        'subject' => 'Prueba desde PHP',
        'body' => 'Contenido del mensaje'
    ]
]);

$result = json_decode($response->getBody(), true);
```

## Archivos adjuntos

Los archivos adjuntos se pueden enviar de dos formas:

### 1. Como datos base64
```json
{
    "attachments": [
        {
            "data": "JVBERi0xLjQKJeLjz9MKNCAwIG9iago8PC...",
            "name": "documento.pdf",
            "mime": "application/pdf"
        }
    ]
}
```

### 2. Como ruta de archivo (solo si el archivo existe en el servidor)
```json
{
    "attachments": [
        {
            "path": "/path/to/file.pdf",
            "name": "documento.pdf",
            "mime": "application/pdf"
        }
    ]
}
```

## Tests

Para ejecutar los tests:

```bash
php artisan test
```

Para ejecutar tests específicos:

```bash
php artisan test --filter EmailControllerTest
```

## Logs

Los logs de la aplicación se guardan en `storage/logs/laravel.log`. Cada envío exitoso o fallido queda registrado con detalles.

## Consideraciones de seguridad

1. **Rate Limiting**: La API incluye limitación de peticiones por defecto (60 peticiones por minuto).

2. **Validación**: Todos los campos son validados antes de procesar el envío.

3. **Credenciales**: Nunca expongas las credenciales SMTP en el código o repositorios públicos.

4. **CORS**: Si necesitas acceso desde un dominio diferente, configura CORS apropiadamente.

## Solución de problemas

### Error "Connection could not be established"
- Verifica las credenciales SMTP en el archivo `.env`
- Asegúrate de que el puerto 587 no esté bloqueado
- Confirma que la cuenta de Gmail permite aplicaciones menos seguras o usa contraseñas de aplicación

### Error 500
- Revisa los logs en `storage/logs/laravel.log`
- Verifica que todas las extensiones PHP requeridas estén instaladas
- Asegúrate de que la carpeta `storage` tenga permisos de escritura

## Licencia

Este proyecto es de código abierto. 