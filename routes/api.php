<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MailerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas API para tu aplicación.
| Estas rutas son cargadas por el RouteServiceProvider y todas ellas
| serán asignadas al grupo de middleware "api".
|
*/

// Rutas para envío de emails
Route::prefix('email')->group(function () {
    Route::post('/send', [EmailController::class, 'send'])->name('email.send');
    Route::post('/send-html', [EmailController::class, 'sendHtml'])->name('email.sendHtml');
});

// Ruta de prueba
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'LittleOwls Email API',
        'timestamp' => now()->toIso8601String()
    ]);
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('sendmail', [MailerController::class, 'store'])->name('v1.sendmail');
    Route::post('sendfiles', [MailerController::class, 'sendFile'])->name('v1.sendfiles');
}); 