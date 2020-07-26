<?php

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



// Without middleware api
Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('forgot_password', 'API\RegisterController@forgotPassword');
Route::put('reset_password', 'API\RegisterController@resetPassword');

// With middleware api
Route::middleware('auth:api')->group(function(){
    Route::group(['prefix'=>'user'], function() {
        Route::get('', 'API\UserController@index');
        Route::put('/', 'API\UserController@update');
        Route::get('/logout', 'API\UserController@logout');
        Route::delete('/', 'API\UserController@destroy');
    });
});
