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
Route::get('/workwithus', 'HomeController@getworkwithusForm')->name('register.worker');
Route::post('/workwithus', 'HomeController@registerWorker')->name('register.worker.submit');

Route::prefix('admin')->group(function (){
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');

    Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');

    Route::prefix('service')->group(function (){
        Route::get('/', 'Admin\ServiceController@index')->name('admin.service');
        Route::get('/insert/', 'Admin\ServiceController@addServiceForm')->name('admin.service.insert');
        Route::post('/insert/', 'Admin\ServiceController@addService')->name('admin.service.insert.submit');
        Route::get('/subcategory/{category_id}', 'Admin\ServiceController@getSubCategory')->name('admin.service.subcategory');
        Route::get('/update/{service_id}', 'Admin\ServiceController@showEditServiceForm')->name('admin.service.update');
        Route::post('/update/', 'Admin\ServiceController@editService')->name('admin.service.update.submit');
        Route::get('/delete/{service_id}', 'Admin\ServiceController@deleteService')->name('admin.service.delete');
        Route::post('/question/insert', 'Admin\ServiceController@addQuestionService')->name('admin.service.question.insert.submit');
        Route::get('/question/delete/{question_id}', 'Admin\ServiceController@deleteQuestionService')->name('admin.service.question.delete');

    });

    Route::prefix('user')->group(function (){
        Route::get('/', 'Admin\UserController@index')->name('admin.user.list');
        Route::get('/update/{service_id}', 'Admin\UserController@showEditUserForm')->name('admin.user.update');
        Route::post('/update/', 'Admin\UserController@editUser')->name('admin.user.update.submit');
        Route::post('/update/workerprofile', 'Admin\UserController@editWorkerProfile')->name('admin.worker.profile.update.submit');

    });

    Route::prefix('category')->group(function (){
        Route::get('/', 'Admin\CategoryController@index')->name('admin.category');
        Route::get('/insert/', 'Admin\CategoryController@addCategoryForm')->name('admin.category.insert');
        Route::post('/insert/', 'Admin\CategoryController@addCategory')->name('admin.category.insert.submit');
        Route::get('/update/{category_id}', 'Admin\CategoryController@showEditCategoryForm')->name('admin.category.update');
        Route::post('/update/', 'Admin\CategoryController@editCategory')->name('admin.category.update.submit');
        Route::get('/delete/{category_id_id}', 'Admin\CategoryController@deleteCategory')->name('admin.category.delete');

    });
    Route::prefix('dissatisfied/reason')->group(function (){
        Route::get('/', 'Admin\DissatisfiedReasonController@index')->name('admin.dissatisfied.reason.list');
        Route::get('/insert/', 'Admin\DissatisfiedReasonController@addDissatisfiedReasonForm')->name('admin.dissatisfied.reason.insert');
        Route::post('/insert/', 'Admin\DissatisfiedReasonController@addDissatisfiedReason')->name('admin.dissatisfied.reason.insert.submit');
        Route::get('/update/{category_id}', 'Admin\DissatisfiedReasonController@showEditDissatisfiedReasonForm')->name('admin.dissatisfied.reason.update');
        Route::post('/update/', 'Admin\DissatisfiedReasonController@editDissatisfiedReason')->name('admin.dissatisfied.reason.update.submit');
        Route::get('/delete/{category_id_id}', 'Admin\DissatisfiedReasonController@deleteDissatisfiedReason')->name('admin.dissatisfied.reason.delete');

    });

    Route::prefix('subcategory')->group(function (){
        Route::get('/', 'Admin\SubCategoryController@index')->name('admin.subcategory');
        Route::get('/insert/', 'Admin\SubCategoryController@addSubCategoryForm')->name('admin.subcategory.insert');
        Route::post('/insert/', 'Admin\SubCategoryController@addSubCategory')->name('admin.subcategory.insert.submit');
        Route::get('/update/{subcategory_id}', 'Admin\SubCategoryController@showEditSubCategoryForm')->name('admin.subcategory.update');
        Route::post('/update/', 'Admin\SubCategoryController@editSubCategory')->name('admin.subcategory.update.submit');
        Route::get('/delete/{subcategory_id}', 'Admin\SubCategoryController@deleteSubCategory')->name('admin.subcategory.delete');

    });

    Route::prefix('emailtemplate')->group(function (){
        Route::get('/', 'Admin\EmailTemplateController@index')->name('admin.emailtemplate');
        Route::get('/insert/', 'Admin\EmailTemplateController@addEmailTemplateForm')->name('admin.emailtemplate.insert');
        Route::post('/insert/', 'Admin\EmailTemplateController@addEmailTemplate')->name('admin.emailtemplate.insert.submit');
        Route::get('/update/{emailtemplate_id}', 'Admin\EmailTemplateController@showMialEditForm')->name('admin.emailtemplate.update');
        Route::post('/update/', 'Admin\EmailTemplateController@EditEmailTemplateForm')->name('admin.emailtemplate.update.submit');
        Route::get('/delete/{emailtemplate_id}', 'Admin\SubCategoryController@deleteSubCategory')->name('admin.emailtemplate.delete');

    });



//    Route::get('/listemailtemplate/', 'Admin\EmailTemplateController@index')->name('admin.list.email.template');
//    Route::get('/editemailtemplate/{mail_template_id}', 'Admin\EmailTemplateController@showMialEditForm')->name('admin.mail.edit');
//    Route::post('/editemailtemplate/', 'Admin\EmailTemplateController@eEditEmailTemplateForm')->name('admin.edit.email.template.submit');
//    Route::get('/addemailtemplate/', 'Admin\EmailTemplateController@addEmailTemplateForm')->name('admin.create.email.template');
//    Route::post('/addemailtemplate/', 'Admin\EmailTemplateController@addEmailTemplate')->name('admin.create.email.template.submit');


    Route::get('/listsurvey/{type?}', 'Admin\SurveyController@index')->name('admin.list.survey');
















});
