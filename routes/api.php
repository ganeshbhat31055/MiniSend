<?php

use App\Http\Controllers\EmailController;
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

Route::group(['prefix'=>'email'],function (){
    Route::post('/send',[EmailController::class,'send']);
    Route::get('/search',[EmailController::class,'search']);
    Route::get('/dashboard',[EmailController::class,'dashboard']);
    Route::get('/show/{email}',[EmailController::class,'show']);
});

