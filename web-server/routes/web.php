<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->group(function (){
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');

    Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');
    Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');


    Route::get('/service', 'Admin\ServiceController@index')->name('admin.service');
    Route::get('/addservice/', 'Admin\ServiceController@addServiceForm')->name('admin.addservice');
    Route::post('/addservice/', 'Admin\ServiceController@addService')->name('admin.addservice.submit');
    Route::post('/editservice/', 'Admin\ServiceController@editService')->name('admin.editservice.submit');
    Route::get('/getsubcategory/{category_id}', 'Admin\ServiceController@getSubCategory')->name('admin.getsubcategory');
    Route::get('/editservice/{service_id}', 'Admin\ServiceController@showEditServiceForm')->name('admin.service.edit');
    Route::post('/addquestionservice', 'Admin\ServiceController@addQuestionService')->name('admin.add.question.service.submit');
    Route::get('/deletequestionservice/{question_id}', 'Admin\ServiceController@deleteQuestionService')->name('admin.service.question.delete');

    Route::get('/listemailtemplate/', 'Admin\EmailTemplateController@index')->name('admin.list.email.template');
    Route::get('/editemailtemplate/{mail_template_id}', 'Admin\EmailTemplateController@showMialEditForm')->name('admin.mail.edit');
    Route::post('/editemailtemplate/', 'Admin\EmailTemplateController@eEditEmailTemplateForm')->name('admin.edit.email.template.submit');
    Route::get('/addemailtemplate/', 'Admin\EmailTemplateController@addEmailTemplateForm')->name('admin.create.email.template');
    Route::post('/addemailtemplate/', 'Admin\EmailTemplateController@addEmailTemplate')->name('admin.create.email.template.submit');


    Route::get('/listsurvey/{type?}', 'Admin\SurveyController@index')->name('admin.list.survey');










});
