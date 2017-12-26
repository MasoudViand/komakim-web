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

Route::middleware('auth:api')->prefix('profile')->group(function (){
    Route::post('/', 'Api\ProfileController@addprofile')->name('api.profile.insert.submit');
});

Route::middleware('auth:api','profile')->post('profile/fcm', 'Api\ProfileController@registerFcmToken')->name('api.fcm.insert.submit');

Route::middleware('auth:api','profile')->prefix('service')->group(function (){
    Route::get('/category', 'Api\ServiceController@listcategory')->name('api.service.category.list');
    Route::post('/', 'Api\ServiceController@listservice')->name('api.service.list');
    Route::post('/order', 'Api\ServiceController@registerOrder')->name('api.order.submit');
    Route::post('/order/accept', 'Api\ServiceController@acceptOrder')->name('api.accept.order');

});



