<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;

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