<?php

declare(strict_types=1);

use App\Http\Controllers\V1\IflowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/iflow/token', [IflowController::class, 'getToken'])->name('v1.iflow.getToken');
    Route::get('/iflow/get-status/{trackId}', [IflowController::class, 'getStatusOrder'])->name('v1.iflow.getStatusOrder');
    Route::get('/iflow/get-seller-orders', [IflowController::class, 'getSellerOrders'])->name('v1.iflow.getSellerOrders');
});
