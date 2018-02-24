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
Route::middleware('auth:api','profile')->get('profile/info' ,'Api\ProfileController@getprofileInfo');
Route::middleware('auth:api','profile')->post('profile/account_number' ,'Api\ProfileController@addAcounNumberToWorkerProfile');

Route::middleware('auth:api','profile')->post('profile/fcm', 'Api\ProfileController@registerFcmToken')->name('api.fcm.insert.submit');
Route::middleware('auth:api','profile')->post('profile/location', 'Api\ProfileController@registerLocation')->name('api.location.insert.submit');



Route::middleware('auth:api','profile')->prefix('service')->group(function (){
    Route::get('/category', 'Api\ServiceController@listcategory')->name('api.service.category.list');
    Route::post('/', 'Api\ServiceController@listservice')->name('api.service.list');
    Route::post('/order', 'Api\ServiceController@registerOrder')->name('api.order.submit');
    Route::post('/order/accept', 'Api\ServiceController@acceptOrder')->name('api.accept.order');
    Route::post('/order/start', 'Api\ServiceController@startOrder')->name('api.start.order');
    Route::post('/order/finish', 'Api\ServiceController@claimFinishOrderByWorker')->name('api.finish.order');
    Route::post('/order/pay', 'Api\ServiceController@payByClient')->name('api.pay.order');

});
Route::middleware('auth:api','profile')->prefix('order')->group(function (){
    Route::post('/archive', 'Api\OrderController@listArchiveOrder')->name('api.order.list');
    Route::post('/detail', 'Api\OrderController@detailOrder')->name('api.order.active.list');
    Route::get('/active', 'Api\OrderController@listActiveOrder')->name('api.order.active.list');
    Route::post('/edit', 'Api\OrderController@editOrder')->name('api.order.active.list');
    Route::post('/edit/approve', 'Api\OrderController@approveEditOrder')->name('api.order.active.list');
    Route::post('/cancel', 'Api\OrderController@cancelOrder')->name('api.order.cancel');
    Route::get('/cancel/reason', 'Api\OrderController@receiveCancelReason')->name('api.order.cancel');

    Route::post('/archive/worker', 'Api\OrderController@listArchiveOrderWorker')->name('api.order.list');
    Route::post('/detail/worker', 'Api\OrderController@detailOrderWorker')->name('api.order.active.list');
    Route::get('/active/worker', 'Api\OrderController@listActiveOrderWorker')->name('api.order.cancel.reason.list');

});

Route::middleware('auth:api','profile')->prefix('wallet')->group(function (){
    Route::post('/charge', 'Api\WalletController@charge')->name('api.wallet.charge');
    Route::post('/pay', 'Api\WalletController@payOrder')->name('api.wallet.pay');
    Route::post('/discount_code', 'Api\WalletController@validateDiscountCode')->name('api.wallet.discount_code');

});

Route::middleware('auth:api','profile')->prefix('review')->group(function (){
    Route::post('/', 'Api\ReviewController@review')->name('api.review.submit');
    Route::get('/reason', 'Api\ReviewController@listReason')->name('api.reasons');

});



