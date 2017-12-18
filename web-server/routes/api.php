<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::get('/user', function () {

})->middleware('auth:api');

Route::middleware('api')->prefix('number')->group(function (){
    Route::post('/send', 'Api\PhoneNumberController@receiveCode')->name('api.number.receive.code');
    Route::post('/verify', 'Api\PhoneNumberController@verifyCode')->name('api.number.verify.code');
    Route::post('/worker/verify', 'Api\PhoneNumberController@verifyWorkerCode')->name('api.number.worker/verify.code');

});

Route::middleware('api')->prefix('profile')->group(function (){
    Route::post('/', 'Api\ProfileController@addprofile')->name('api.profile.insert.submit');

});



