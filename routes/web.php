<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {return redirect()->route('payment.index');});
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.index');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');
