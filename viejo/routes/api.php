<?php

use App\Http\Controllers\MailerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group(['prefix' => 'v1', 'as' => 'v1.api.', 'middleware' => []], function ()
{
    Route::post('sendmail',             [MailerController::class, 'store'])->name('store');
    Route::post('sendfiles',            [MailerController::class, 'sendFile'])->name('sendFile');
    Route::post('order',                [PaymentController::class, 'createOrder'])->name('createOrder');
    Route::post('payments/webhook',     [PaymentController::class, 'webhook'])->name('webhook');
});    