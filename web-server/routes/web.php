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


Route::get('2fa', 'Auth\TwoFactorController@showTwoFactorForm');
Route::post('2fa', 'Auth\TwoFactorController@verifyTwoFactor')->name('2fa');



//Auth::routes();

Route::post('/callback', 'HomeController@callback')->name('home');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('testhome');
Route::get('/workwithus', 'HomeController@getworkwithusForm')->name('register.worker');
Route::post('/workwithus', 'HomeController@registerWorker')->name('register.worker.submit');

Route::get('/faq', 'HomeController@showRepeadQuestions')->name('list.repeat.questions');
Route::get('/rules', 'HomeController@showrules')->name('list.rules');
Route::get('/conditions', 'HomeController@showWorkWithUsCondition')->name('list.work.with.us.condition');


Route::prefix('admin')->group(function (){
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout')->middleware();
});
Route::middleware(['auth:admin','two_factor'])->prefix('admin')->group(function (){
    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');



    Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');
    Route::get('/change/pass', 'Admin\AdminController@showChangePassForm')->name('admin.change.pass');
    Route::post('/change/pass', 'Admin\AdminController@changePass')->name('admin.change.pass.submit');

    Route::prefix('service')->group(function (){
        Route::get('/', 'Admin\ServiceController@index')->name('admin.service');
        Route::get('/insert/', 'Admin\ServiceController@addServiceForm')->name('admin.service.insert');
        Route::post('/insert/', 'Admin\ServiceController@addService')->name('admin.service.insert.submit');
        Route::get('subcategory/{category_id}', 'Admin\ServiceController@getSubCategory')->name('admin.service.subcategory');
        Route::get('/update/{service_id}', 'Admin\ServiceController@showEditServiceForm')->name('admin.service.update');
        Route::post('/update/', 'Admin\ServiceController@editService')->name('admin.service.update.submit');
        Route::get('/delete/{service_id}', 'Admin\ServiceController@deleteService')->name('admin.service.delete');
        Route::post('/question/insert', 'Admin\ServiceController@addQuestionService')->name('admin.service.question.insert.submit');
        Route::get('/question/delete/{question_id}', 'Admin\ServiceController@deleteQuestionService')->name('admin.service.question.delete');

    });
    Route::prefix('map')->group(function (){
        Route::get('/', 'Admin\MapController@index')->name('admin.map');

    });


    Route::prefix('financial')->group(function (){
        Route::get('/', 'Admin\FinancialController@index')->name('admin.financial');
        Route::get('/remain/wallet/', 'Admin\FinancialController@showRemainWallet')->name('admin.financial.remain_wallet');
        Route::post('/filter', 'Admin\FinancialController@filter')->name('admin.financial.daily');

    });
    Route::prefix('transactions')->group(function (){
        Route::get('/', 'Admin\TransactionsController@index')->name('admin.transactions.list');
        Route::get('/export', 'Admin\TransactionsController@export')->name('admin.transaction.list.export');

    });

    Route::prefix('discountcode')->group(function (){
        Route::get('/', 'Admin\DiscountCodeController@index')->name('admin.discount_code.list');
        Route::get('/insert', 'Admin\DiscountCodeController@insertForm')->name('admin.discount_code.insert');
        Route::post('/insert', 'Admin\DiscountCodeController@insert')->name('admin.discount_code.insert.submit');
        Route::get('/inactive/{discount_code_id}', 'Admin\DiscountCodeController@inactive')->name('admin.discount_code.inactive');

    });
    Route::prefix('notification')->group(function (){
        Route::get('/', 'Admin\NotificationController@index')->name('admin.notification');
        Route::get('/sms', 'Admin\NotificationController@showsmsform')->name('admin.sms');
        Route::post('/send', 'Admin\NotificationController@sendNotification')->name('admin.notification.send.submit');
        Route::post('/sms', 'Admin\NotificationController@sendSms')->name('admin.sms.send.submit');


    });

    Route::prefix('user')->group(function (){
        Route::get('/', 'Admin\UserController@index')->name('admin.user.list');
        Route::post('/filter/', 'Admin\UserController@filterUser')->name('admin.user.filter.submit');
        Route::get('/update/{user_id}', 'Admin\UserController@showEditUserForm')->name('admin.user.update');
        Route::post('/update/', 'Admin\UserController@editUser')->name('admin.user.update.submit');
        Route::post('/update/workerprofile', 'Admin\UserController@editWorkerProfile')->name('admin.worker.profile.update.submit');
        Route::get('/review/{user_id}', 'Admin\UserController@listReviewUser')->name('admin.user.review');

    });
    Route::prefix('order')->group(function (){
        Route::get('/', 'Admin\OrderController@index')->name('admin.order.list');
        Route::post('/filter/', 'Admin\OrderController@filterOrder')->name('admin.order.filter.submit');
        Route::get('/detail/{order_id}', 'Admin\OrderController@showDetailOrder')->name('admin.order.detail');
        Route::get('/revisions/{order_id}', 'Admin\OrderController@showRevisionsOfOrder')->name('admin.order.revisions');
        Route::post('/cancel/', 'Admin\OrderController@CancelOrderByAdmin')->name('admin.order.cancel');

    });
    Route::prefix('setting')->group(function (){
        Route::get('/', 'Admin\SettingController@index')->name('admin.setting');
        Route::post('/radius/edit', 'Admin\SettingController@editRadiusSearch')->name('admin.setting.radius_search');
        Route::post('/commission/edit', 'Admin\SettingController@editCommission')->name('admin.setting.commission');
        Route::get('/', 'Admin\SettingController@index')->name('admin.setting');
        Route::get('/edit/version/{id}', 'Admin\SettingController@showEditVersionForm')->name('admin.edit.version');
        Route::post('/edit/version', 'Admin\SettingController@editVersion')->name('admin.edit.version.submit');
        Route::get('/work_with_us_condition', 'Admin\SettingController@showWorkWithUsConditionForm')->name('admin.work.with.us.condition.insert');
        Route::post('/work_with_us_condition/edit', 'Admin\SettingController@EditWorkWithUsConditionForm')->name('admin.work.with.us.condition.insert.submit');
        Route::get('/rules', 'Admin\SettingController@showRolesForm')->name('admin.rules.insert');
        Route::post('/rules/edit', 'Admin\SettingController@editRoles')->name('admin.rules.insert.submit');
        Route::get('/repeat_questions', 'Admin\SettingController@ListRepeatQuestions')->name('admin.repeat.question');
        Route::get('/repeat_questions/insert', 'Admin\SettingController@ShowRepeatQuestionsForm')->name('admin.repeat.question.insert');
        Route::post('/repeat_questions/insert', 'Admin\SettingController@CreateRepeatQuestions')->name('admin.repeat.question.insert.submit');
        Route::get('/repeat_questions/update/{repeat_question_id}', 'Admin\SettingController@ShowEditRepeatQuestionsForm')->name('admin.repeat.question.update');
        Route::get('/repeat_questions/delete/{repeat_question_id}', 'Admin\SettingController@delete')->name('admin.repeat.question.delete');
        Route::post('/repeat_questions/update/', 'Admin\SettingController@edit')->name('admin.repeat.question.update.submit');

//        Route::post('/cancel/', 'Admin\OrderController@CancelOrderByAdmin')->name('admin.order.cancel');

    });
    Route::prefix('settle')->group(function (){
        Route::get('/', 'Admin\SettleDeptController@index')->name('admin.settle.dept.list');
        Route::post('/done/', 'Admin\SettleDeptController@settleWorker')->name('admin.settle.worker');
        Route::get('/export/scv/', 'Admin\SettleDeptController@export')->name('admin.settle.export');

    });
    Route::prefix('category')->group(function (){
        Route::get('/', 'Admin\CategoryController@index')->name('admin.category');
        Route::get('/insert/', 'Admin\CategoryController@addCategoryForm')->name('admin.category.insert');
        Route::post('/insert/', 'Admin\CategoryController@addCategory')->name('admin.category.insert.submit');
        Route::get('/update/{category_id}', 'Admin\CategoryController@showEditCategoryForm')->name('admin.category.update');
        Route::post('/update/', 'Admin\CategoryController@editCategory')->name('admin.category.update.submit');
        Route::get('/delete/{category_id_id}', 'Admin\CategoryController@deleteCategory')->name('admin.category.delete');

    });
    Route::prefix('user_admin')->group(function (){
        Route::get('/', 'Admin\UserAdminController@index')->name('admin.user_admin');
        Route::get('/insert/', 'Admin\UserAdminController@addForm')->name('admin.user_admin.insert');
        Route::post('/insert/', 'Admin\UserAdminController@add')->name('admin.user_admin.insert.submit');
        Route::get('/update/{admin_user_id}', 'Admin\UserAdminController@showEditForm')->name('admin.user_admin.update');
        Route::post('/update/', 'Admin\UserAdminController@edit')->name('admin.user_admin.update.submit');
        Route::get('/delete/{admin_user_id}', 'Admin\UserAdminController@delete')->name('admin.user_admin.delete');

    });
    Route::prefix('dissatisfied/reason')->group(function (){
        Route::get('/', 'Admin\DissatisfiedReasonController@index')->name('admin.dissatisfied.reason.list');
        Route::get('/insert/', 'Admin\DissatisfiedReasonController@addDissatisfiedReasonForm')->name('admin.dissatisfied.reason.insert');
        Route::post('/insert/', 'Admin\DissatisfiedReasonController@addDissatisfiedReason')->name('admin.dissatisfied.reason.insert.submit');
        Route::get('/update/{category_id}', 'Admin\DissatisfiedReasonController@showEditDissatisfiedReasonForm')->name('admin.dissatisfied.reason.update');
        Route::post('/update/', 'Admin\DissatisfiedReasonController@editDissatisfiedReason')->name('admin.dissatisfied.reason.update.submit');
        Route::get('/delete/{category_id_id}', 'Admin\DissatisfiedReasonController@deleteDissatisfiedReason')->name('admin.dissatisfied.reason.delete');

    });
    Route::prefix('cancel/reason')->group(function (){
        Route::get('/', 'Admin\CancelReasonController@index')->name('admin.cancel.reason.list');
        Route::get('/insert/', 'Admin\CancelReasonController@addCancelReasonForm')->name('admin.cancel.reason.insert');
        Route::post('/insert/', 'Admin\CancelReasonController@addCancelReason')->name('admin.cancel.reason.insert.submit');
        Route::get('/update/{category_id}', 'Admin\CancelReasonController@showEditCancelReasonForm')->name('admin.cancel.reason.update');
        Route::post('/update/', 'Admin\CancelReasonController@editCancelReason')->name('admin.cancel.reason.update.submit');
        Route::get('/delete/{category_id_id}', 'Admin\CancelReasonController@deleteCancelReason')->name('admin.cancel.reason.delete');

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
    Route::get('/listsurvey/', 'Admin\SurveyController@index')->name('admin.list.survey');
















});

Route::prefix('pay')->group(function (){
    Route::get('/{phone_number}/{amount}', 'PayController@index')->name('pay');
    Route::post('/callback', 'PayController@callback')->name('pay.redirect.bank.callback');

});

