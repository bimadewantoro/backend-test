<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('add', [WalletController::class, 'store']);
    Route::get('get', [WalletController::class, 'index']);
    Route::get('get/{id}', [WalletController::class, 'show']);
    Route::put('update/{id}', [WalletController::class, 'update']);
    Route::delete('delete/{id}', [WalletController::class, 'destroy']);
});