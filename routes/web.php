<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkashPaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [BkashPaymentController::class, 'index']);

Route::group(['prefix' => 'v1', 'middleware' => []], function () {
    Route::get('/bkash-create-payment', [BkashPaymentController::class, 'createPayment'])->name('bkash-create-payment');
    Route::get('/bkash-callback/{payment_id}', [BkashPaymentController::class, 'callBack'])->name('bkash-callBack');
    Route::get('/bkash-search/{trxID}', [BkashPaymentController::class, 'searchTnx'])->name('bkash-serach');
    Route::get('/bkash-success', [BkashPaymentController::class, 'successPayment'])->name('success');
    Route::get('/bkash-cancel', [BkashPaymentController::class, 'cancelPayment'])->name('cancel');
    Route::get('/bkash-failed', [BkashPaymentController::class, 'failedPayment'])->name('failed');
});
