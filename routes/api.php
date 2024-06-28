<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//abc
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('forget-password', [\App\Http\Controllers\ForgetPasswordController::class, 'ForgetPasswordStore'])->name('ForgetPasswordPost');
Route::post('reset-password/{token}', [\App\Http\Controllers\ForgetPasswordController::class, 'ResetPasswordStore'])->name('ResetPasswordPost');
//Admin
Route::post('admin/login',[\App\Http\Controllers\Admin\AuthenticationController::class,'login']);
//Admin End


//Client
Route::post('client/login',[\App\Http\Controllers\Client\AuthenticationController::class,'login']);
Route::post('client/verification', [\App\Http\Controllers\Client\RegistrationController::class, 'verify']);
Route::post('client/registration',[\App\Http\Controllers\Client\RegistrationController::class,'register']);

//Client End


Route::middleware(['auth:api'])->group( function () {


//    Route::post('admin/update/client/subscription/status',[\App\Http\Controllers\Admin\NewSubscriptionManageController::class,'update_status']);
    Route::get('admin/get/subscriptions/with/clients',[\App\Http\Controllers\Admin\NewSubscriptionManageController::class,'index']);
//    Route::get('admin/get/single/subscription/with/client/{id}',[\App\Http\Controllers\Admin\NewSubscriptionManageController::class,'index']);

    Route::post('admin/logout',[\App\Http\Controllers\Admin\AuthenticationController::class,'logout']);
    Route::post('admin/change/client/status',[\App\Http\Controllers\Admin\ClientController::class,'change_client_status']);
    Route::get('admin/get/all/clients',[\App\Http\Controllers\Admin\ClientController::class,'index']);
    Route::get('admin/get/client/{id}',[\App\Http\Controllers\Admin\ClientController::class,'single_client']);
//    Route::post('admin/update/client',[\App\Http\Controllers\Admin\ClientController::class,'update']);
    Route::delete('admin/del/client/{id}',[\App\Http\Controllers\Admin\ClientController::class,'del_client']);

    Route::get('admin/get/form/users',[\App\Http\Controllers\Admin\InitiativeTwoController::class,'index']);
    Route::get('admin/get/form/with/user/{id}',[\App\Http\Controllers\Admin\InitiativeTwoController::class,'get_form_with_user']);
    Route::post('admin/update/form',[\App\Http\Controllers\Admin\InitiativeTwoController::class,'update_form']);
    Route::delete('admin/del/form/{id}',[\App\Http\Controllers\Admin\InitiativeTwoController::class,'destroy']);




});
Route::get('client/get/all/plans',[\App\Http\Controllers\Client\NewSubscriptionController::class,'all_plans']);
//Route::middleware(['auth:api'])->group( function () {


    Route::post('client/subscribe/to/plan/stripe',[\App\Http\Controllers\Client\NewSubscriptionController::class,'subscribe_package']);
    Route::post('client/subscribe/to/plan/paypal',[\App\Http\Controllers\Client\NewSubscriptionController::class,'handlePayment']);
    Route::post('/payment/success/{planID}/{transactionID}/{emailID}/{name}/{city}/{state}/{address}', [\App\Http\Controllers\Client\NewSubscriptionController::class,'paymentSuccess']);
    Route::post('/payment/cancel/{planID}/{transactionID}/{emailID}/{name}/{city}/{state}/{address}', [\App\Http\Controllers\Client\NewSubscriptionController::class,'paymentCancel']);


    Route::post('client/logout',[\App\Http\Controllers\Client\AuthenticationController::class,'logout']);

//});
Route::post('client/store/form',[\App\Http\Controllers\Client\InitiativeTwoController::class,'store']);
Route::post('client/store/participant/organization/form',[\App\Http\Controllers\Client\ParticipantFormController::class,'store_organization_form']);
Route::post('client/store/participant/female/form',[\App\Http\Controllers\Client\ParticipantFormController::class,'store_female_form']);
Route::post('client/store/participant/male/form',[\App\Http\Controllers\Client\ParticipantFormController::class,'store_male_form']);
Route::post('client/store/participant/business/form',[\App\Http\Controllers\Client\ParticipantFormController::class,'store_business_form']);



Route::middleware(['auth:api'])->group( function () {
Route::middleware(['Subscription-package'])->group( function () {
    Route::post('client/update/profile',[\App\Http\Controllers\Client\ProfileController::class,'update']);
    Route::get('client/view/profile',[\App\Http\Controllers\Client\ProfileController::class,'profile']);
    Route::post('client/update/password',[\App\Http\Controllers\Client\ProfileController::class,'updatePassword']);
    Route::get('client/get/subscription/details',[\App\Http\Controllers\Client\ProfileController::class,'subscription_details']);

    Route::post('client/update/form',[\App\Http\Controllers\Client\InitiativeTwoController::class,'update']);
    Route::delete('client/del/form/{id}',[\App\Http\Controllers\Client\InitiativeTwoController::class,'destroy']);
    Route::get('client/get/form',[\App\Http\Controllers\Client\InitiativeTwoController::class,'index']);

//    Route::get('client/check/subscription/status',[\App\Http\Controllers\Client\SubscriptionController::class,'checkSubscription']);
});
//
});
