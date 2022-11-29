<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailVerificationController;

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

Route::group(['middleware' => 'auth:api', 'verify'=> true], function () {
    Route::get('/send/{token}', 'App\Http\Controllers\MailVerificationController@MailVerification')->name('verify.mail');
    Route::get('/resend', 'App\Http\Controllers\MailVerificationController@resend')->name('resend.mail');
});
