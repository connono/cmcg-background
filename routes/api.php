<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\ImagesController;
use App\Http\Controllers\Api\EquipmentApplyRecordController;
use App\Http\Controllers\Api\InstrumentApplyRecordController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PaymentPlansController;
use App\Http\Controllers\Api\PaymentProcessesController;
use App\Http\Controllers\Api\PaymentRecordsController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\DepartmentController;


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

Route::prefix('v1')->name('api.v1.')->group(function() {
    Route::get('version', function() {
        return 'this is version v1';
    })->name('version');

    Route::post('users', [UsersController::class, 'store'])
        ->name('users.store');
    Route::post('authorizations', [AuthorizationController::class, 'store'])
        ->name('authorizations.store');
    Route::get('users/index', [UsersController::class, 'index'])
        ->name('users.index');
    Route::get('user',[UsersController::class, 'me'])
        ->name('users.me');
    Route::get('users/{user}',[UsersController::class, 'show'])
        ->name('users.show');
    Route::patch('users/reset/{user}',[UsersController::class, 'resetPassword'])
        ->name('users.reset');
    Route::delete('users/{user}',[UsersController::class, 'delete'])
        ->name('users.delete');
    Route::post('images', [ImagesController::class, 'store'])
        ->name('images.store');
    Route::get('equipment/index',[EquipmentApplyRecordController::class, 'index'])
        ->name('equipment.index');
    Route::get('equipment/item',[EquipmentApplyRecordController::class, 'getItem'])
        ->name('equipment.item');
    Route::delete('equipment/delete/{record}',[EquipmentApplyRecordController::class, 'delete'])
        ->name('equipment.delete');
    Route::patch('equipment/back/{record}',[EquipmentApplyRecordController::class, 'back'])
        ->name('equipment.back');
    Route::get('equipment/serialNumber',[EquipmentApplyRecordController::class, 'getSerialNumber'])
        ->name('equipment.serialNumber');
    Route::post('equipment/store',[EquipmentApplyRecordController::class, 'store'])
        ->name('equipment.store');
    Route::post('equipment/update/{method}/{record}',[EquipmentApplyRecordController::class, 'update'])
        ->name('equipment.update');
    Route::get('instrument/index',[InstrumentApplyRecordController::class, 'index'])
        ->name('instrument.index');
    Route::post('instrument/store',[InstrumentApplyRecordController::class, 'store'])
        ->name('instrument.store');
    Route::post('instrument/update/{method}/{record}',[InstrumentApplyRecordController::class, 'update'])
        ->name('instrument.update');
    // Route::patch('user/read/notifications',[NotificationsController::class, 'read'])
    //     ->name('user.notifications.read');
    Route::get('permissions/{user}', [PermissionsController::class, 'index'])
        ->name('permissions.index');
    Route::get('allRoles', [PermissionsController::class, 'allRoles'])
        ->name('allRoles');
    Route::post('permissions/store', [PermissionsController::class, 'store'])
        ->name('permissions.store');
    Route::patch('permissions/update', [PermissionsController::class, 'update'])
        ->name('permissions.update');
    Route::patch('users/{user}', [UsersController::class, 'update'])
        ->name('users.update');
    Route::get('payment/plans/index',[PaymentPlansController::class, 'index'])
        ->name('payment.plans.index');
    Route::get('payment/plans/getItem',[PaymentPlansController::class, 'getItem'])
        ->name('payment.plans.getItem');
    Route::post('payment/plans/store',[PaymentPlansController::class, 'store'])
        ->name('payment.plans.store');
    Route::get('payment/plans/stop/{plan}',[PaymentPlansController::class, 'stop'])
        ->name('payment.plans.stop');
    Route::delete('payment/plans/delete/{plan}',[PaymentPlansController::class, 'delete'])
        ->name('payment.plans.delete');
    Route::get('payment/processes/index',[PaymentProcessesController::class, 'index'])
        ->name('payment.processes.index');
    Route::get('payment/processes/getItem',[PaymentProcessesController::class, 'getItem'])
        ->name('payment.processes.getItem');
    Route::post('payment/processes/store',[PaymentProcessesController::class, 'store'])
        ->name('payment.processes.store');
    Route::get('payment/processes/stop/{plan}',[PaymentProcessesController::class, 'stop'])
        ->name('payment.processes.stop');
    Route::delete('payment/processes/delete/{plan}',[PaymentProcessesController::class, 'delete'])
        ->name('payment.processes.delete');
    Route::get('payment/records/planIndex/{plan}',[PaymentRecordsController::class, 'planIndex'])
        ->name('payment.records.planIndex');
    Route::get('payment/records/processIndex/{process}',[PaymentRecordsController::class, 'processIndex'])
        ->name('payment.records.processIndex');
    Route::get('payment/records/getItem',[PaymentRecordsController::class, 'getItem'])
        ->name('equipment.item');
    Route::post('payment/records/store',[PaymentRecordsController::class, 'store'])
        ->name('payment.records.store');
    Route::post('payment/records/update/{record}',[PaymentRecordsController::class, 'update'])
        ->name('payment.records.update');
    Route::delete('payment/records/delete/{record}',[PaymentRecordsController::class, 'delete'])
        ->name('payment.records.delete');
    Route::patch('payment/records/back/{record}',[PaymentRecordsController::class, 'back'])
        ->name('payment.records.back');
    Route::get('notifications/index/{user}',[NotificationsController::class, 'index'])
        ->name('notifications.index');
    Route::get('department/index',[DepartmentController::class, 'index'])
        ->name('department.index');
    Route::middleware('auth:api')->group(function() {
        // 当前登录用户信息
        Route::get('user', [UsersController::class, 'me'])
            ->name('user.show');
        Route::get('users', [UsersController::class, 'index'])
            ->name('user.list');
    });
});