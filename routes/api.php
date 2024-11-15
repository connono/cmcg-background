<?php

use App\Http\Controllers\Api\ConsumableNetController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\LeaderController;
use App\Http\Controllers\Api\PaymentDocumentController;
use App\Http\Controllers\Api\PaymentProcessRecordsController;
use App\Http\Controllers\Api\EngineersController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\EquipmentApplyRecordController;
use App\Http\Controllers\Api\InstrumentApplyRecordController;
use App\Http\Controllers\Api\RepairApplyRecordController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PaymentPlansController;
use App\Http\Controllers\Api\PaymentProcessesController;
use App\Http\Controllers\Api\PaymentRecordsController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\AdvanceRecordController;
use App\Http\Controllers\Api\ComsumableItemApplyRecordController;

use App\Http\Controllers\Api\ConsumableTemporaryApplyController;
use App\Http\Controllers\Api\ConsumableApplyController;
use App\Http\Controllers\Api\ConsumableDirectoryController;
use App\Http\Controllers\Api\ConsumableTrendsController;
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
    Route::post('users/setSignature/{user}',[UsersController::class,'setSignature'])
        ->name('users.setSignature');
    Route::get('department/leader/index',[DepartmentController::class, 'leaderIndex'])
        ->name('department.leader.index');
    Route::get('engineers/index', [EngineersController::class,'index'])
        ->name('engineers.index');
    Route::post('engineers', [EngineersController::class,'store'])
        ->name('engineers.store');
    Route::post('engineers/{engineer}', [EngineersController::class,'update'])
        ->name('engineers.update');
    Route::delete('engineers/{engineer}', [EngineersController::class,'delete'])
        ->name('engineers.delete');
    Route::get('leaders/index', [LeaderController::class,'index'])
        ->name('leaders.index');
    Route::post('leaders', [LeaderController::class,'store'])
        ->name('leaders.store');
    Route::post('leaders/{leader}', [LeaderController::class,'update'])
        ->name('leaders.update');
    Route::delete('leaders/{leader}', [LeaderController::class,'delete'])
        ->name('leaders.delete');
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
    Route::get('instrument/item',[InstrumentApplyRecordController::class, 'getItem'])
        ->name('instrument.item');
    Route::delete('instrument/delete/{record}',[InstrumentApplyRecordController::class, 'delete'])
        ->name('instrument.delete');
    Route::patch('instrument/back/{record}',[InstrumentApplyRecordController::class, 'back'])
        ->name('instrument.back');
    Route::get('instrument/serialNumber',[InstrumentApplyRecordController::class, 'getSerialNumber'])
        ->name('instrument.serialNumber');
    Route::post('instrument/store',[InstrumentApplyRecordController::class, 'store'])
        ->name('instrument.store');
    Route::post('instrument/update/{method}/{record}',[InstrumentApplyRecordController::class, 'update'])
        ->name('instrument.update');
    Route::get('maintain/index',[RepairApplyRecordController::class, 'index'])
        ->name('maintain.index');
    Route::get('maintain/item',[RepairApplyRecordController::class, 'getItem'])
        ->name('maintain.item');
    Route::delete('maintain/delete/{record}',[RepairApplyRecordController::class, 'delete'])
        ->name('maintain.delete');
    Route::patch('maintain/back/{record}',[RepairApplyRecordController::class, 'back'])
        ->name('maintain.back');
    Route::get('maintain/serialNumber',[RepairApplyRecordController::class, 'getSerialNumber'])
        ->name('maintain.serialNumber');
    Route::post('maintain/store',[RepairApplyRecordController::class, 'store'])
        ->name('maintain.store');
    Route::post('maintain/update/{method}/{record}',[RepairApplyRecordController::class, 'update'])
        ->name('maintain.update');
    Route::get('permissions/{user}', [PermissionsController::class, 'index'])
        ->name('permissions.index');
    Route::get('allRoles', [RoleController::class, 'allRoles'])
        ->name('allRoles');
    Route::get('userAllRoles', [RoleController::class, 'userAllRoles'])
        ->name('userAllRoles');   
    Route::get('allPermissions', [PermissionsController::class, 'allPermissions'])
        ->name('allPermissions');
    Route::post('updateRole', [RoleController::class, 'updateRole'])
        ->name('updateRole');      
    Route::post('createRole', [RoleController::class, 'createRole'])
        ->name('createRole');     
    Route::post('permissions/store', [PermissionsController::class, 'store'])
        ->name('permissions.store');
    Route::patch('permissions/update', [PermissionsController::class, 'update'])
        ->name('permissions.update');
    Route::patch('users/{user}', [UsersController::class, 'update'])
        ->name('users.update');
    Route::get('payment/plans/index',[PaymentPlansController::class, 'index'])
        ->name('payment.plans.index');
    Route::get('payment/plans/getItem/{plan}',[PaymentPlansController::class, 'getItem'])
        ->name('payment.plans.getItem');
    Route::post('payment/plans/store',[PaymentPlansController::class, 'store'])
        ->name('payment.plans.store');
    Route::get('payment/plans/stop/{plan}',[PaymentPlansController::class, 'stop'])
        ->name('payment.plans.stop');
    Route::delete('payment/plans/delete/{plan}',[PaymentPlansController::class, 'delete'])
        ->name('payment.plans.delete');
    Route::get('payment/processes/index',[PaymentProcessesController::class, 'index'])
        ->name('payment.processes.index');
    Route::get('payment/processes/getItem/{process}',[PaymentProcessesController::class, 'getItem'])
        ->name('payment.processes.getItem');
    Route::post('payment/processes/store',[PaymentProcessesController::class, 'store'])
        ->name('payment.processes.store');
    Route::get('payment/processes/stop/{plan}',[PaymentProcessesController::class, 'stop'])
        ->name('payment.processes.stop');
    Route::delete('payment/processes/delete/{plan}',[PaymentProcessesController::class, 'delete'])
        ->name('payment.processes.delete');
    Route::get('payment/records/index/{plan}',[PaymentRecordsController::class, 'index'])
        ->name('payment.records.index');
    Route::get('payment/process/records/index/{process}',[PaymentProcessRecordsController::class, 'index'])
        ->name('payment.process.records.index');
    Route::get('payment/records/getItem',[PaymentRecordsController::class, 'getItem'])
        ->name('payment.getItem');
    Route::post('payment/records/store',[PaymentRecordsController::class, 'store'])
        ->name('payment.records.store');
    Route::post('payment/records/update/{record}',[PaymentRecordsController::class, 'update'])
        ->name('payment.records.update');
    Route::delete('payment/records/delete/{record}',[PaymentRecordsController::class, 'delete'])
        ->name('payment.records.delete');
    Route::patch('payment/records/back/{record}',[PaymentRecordsController::class, 'back'])
        ->name('payment.records.back');
    Route::get('payment/process/records/getItem',[PaymentProcessRecordsController::class, 'getItem'])
        ->name('payment.process.getItem');
    Route::post('payment/process/records/store',[PaymentProcessRecordsController::class, 'store'])
        ->name('payment.process.records.store');
    Route::post('payment/process/records/update/{record}',[PaymentProcessRecordsController::class, 'update'])
        ->name('payment.process.records.update');
    Route::delete('payment/process/records/delete/{record}',[PaymentProcessRecordsController::class, 'delete'])
        ->name('payment.process.records.delete');
    Route::patch('payment/process/records/back/{record}',[PaymentProcessRecordsController::class, 'back'])
        ->name('payment.process.records.back');
    Route::get('notifications/index/{user}',[NotificationsController::class, 'index'])
        ->name('notifications.index');
    Route::get('notifications/ignore/{notification}',[NotificationsController::class, 'ignore'])
        ->name('notifications.ignore');    
    Route::delete('notifications/delete',[NotificationsController::class, 'delete'])
        ->name('notifications.delete');
    Route::get('department/index',[DepartmentController::class, 'index'])
        ->name('department.index');
    Route::get('department/engineer/index',[DepartmentController::class, 'engineerIndex'])
        ->name('department.engineer.index');
    Route::get('advance/records/index',[AdvanceRecordController::class, 'index'])
        ->name('advance.records.index');
    Route::get('advance/records/getItem',[AdvanceRecordController::class, 'getItem'])
        ->name('advance.records.getItem');
    Route::get('advance/budget/index',[AdvanceRecordController::class, 'getAdvanceBudget'])
        ->name('advance.budget.index');
    Route::post('advance/budget/store',[AdvanceRecordController::class, 'storeAdvanceBudget'])
        ->name('advance.budget.store');
    Route::post('advance/records/store',[AdvanceRecordController::class, 'store'])
        ->name('advance.records.store');
    Route::post('advance/records/update/{record}',[AdvanceRecordController::class, 'update'])
        ->name('advance.records.update');
    Route::delete('advance/records/delete/{record}',[AdvanceRecordController::class, 'delete'])
        ->name('advance.records.delete');
    Route::get('payment/contracts/index',[ContractController::class, 'index'])
        ->name('payment.contracts.index');
    Route::get('payment/contracts/getItem',[ContractController::class, 'getItem'])
        ->name('payment.contracts.getItem');
    Route::post('payment/contracts/store',[ContractController::class, 'store'])
        ->name('payment.contracts.store');
    Route::post('payment/contracts/storeDocx/{contract}',[ContractController::class, 'storeDocx'])
        ->name('payment.contracts.storeDocx');
    Route::delete('payment/contracts/delete/{contract}',[ContractController::class, 'delete'])
        ->name('payment.contracts.delete');
    Route::get('payment/contracts/plans/{contract}',[ContractController::class, 'plans'])
        ->name('payment.contracts.plans');
    Route::get('payment/contracts/processes/{contract}',[ContractController::class, 'processes'])
        ->name('payment.contracts.processes');
    Route::delete('payment/contracts/plans/delete/{contract}',[ContractController::class, 'deletePlan'])
        ->name('payment.contracts.plans.delete');
    Route::delete('payment/contracts/processes/delete/{contract}',[ContractController::class, 'deleteProcess'])
        ->name('payment.contracts.processes.delete');
    Route::post('payment/contracts/update/{contract}',[ContractController::class, 'update'])
        ->name('payment.contracts.update');
    Route::get('comsumable/apply/index',[ComsumableItemApplyRecordController::class, 'index'])
        ->name('comsumable.apply.index');
    Route::get('comsumable/apply/getItem',[ComsumableItemApplyRecordController::class,'getItem'])
        ->name('comsumable.apply.getItem');
    Route::post('comsumable/apply/store',[ComsumableItemApplyRecordController::class, 'store'])
        ->name('comsumable.apply.store');
    Route::get('payment/document/records/index', [PaymentDocumentController::class, 'index'])
        ->name('payment.document.records.index');
    Route::get('payment/document/records/item/{record}', [PaymentDocumentController::class, 'item'])
        ->name('payment.document.records.item');
    Route::get('payment/document/records/getDocumentRecordList', [PaymentProcessRecordsController::class, 'getDocumentRecordList'])
        ->name('payment.document.records.getDocumentRecordList');
    Route::post('payment/document/records/store', [PaymentDocumentController::class, 'store'])
        ->name('payment.document.records.store');
    Route::post('payment/document/records/storeXlsx/{record}', [PaymentDocumentController::class, 'storeXlsx'])
        ->name('payment.document.records.storeXlsx');
    Route::delete('payment/document/records/delete/{record}',[PaymentDocumentController::class, 'delete'])
        ->name('payment.document.records.delete');
    Route::post('payment/document/records/update/{record}',[PaymentDocumentController::class, 'update'])
        ->name('payment.document.records.update');
    Route::get('payment/document/records/getItem/{record}', [PaymentDocumentController::class, 'getItem'])
        ->name('payment.document.records.getItem');
    Route::get('consumable/tempory/serialNumber',[ConsumableTemporaryApplyController::class, 'getSerialNumber'])
        ->name('consumable.tempory.serialNumber');
    Route::post('consumable/tempory/store',[ConsumableTemporaryApplyController::class, 'store'])
        ->name('consumable.tempory.store');
    Route::get('consumable/tempory/index',[ConsumableTemporaryApplyController::class, 'index'])
        ->name('consumable.tempory.index');
    Route::get('comsumable/tempory/getItem',[ConsumableTemporaryApplyController::class,'getItem'])
        ->name('comsumable.tempory.getItem');
    Route::post('consumable/tempory/update/{record}',[ConsumableTemporaryApplyController::class, 'update'])
        ->name('consumable.tempory.update');
    Route::post('consumable/tempory/back/{record}',[ConsumableTemporaryApplyController::class, 'back'])
        ->name('consumable.tempory.back');
    Route::post('consumable/directory/stop/{record}',[ConsumableDirectoryController::class, 'stop'])
        ->name('consumable.directory.stop');
        //耗材动态目录管理
    Route::get('consumable/apply/serialNumber',[ConsumableApplyController::class, 'getSerialNumber'])
        ->name('consumable.apply.serialNumber');
    Route::post('consumable/apply/store',[ConsumableApplyController::class, 'store'])
        ->name('consumable.apply.store');
    Route::get('consumable/apply/index',[ConsumableApplyController::class, 'index'])
        ->name('consumable.apply.index');   
    Route::post('consumable/apply/update/{record}',[ConsumableApplyController::class, 'update'])
        ->name('consumable.apply.update');
    Route::get('consumable/apply/getItem',[ConsumableApplyController::class,'getItem'])
        ->name('consumable.apply.getItem');
    Route::post('consumable/trends/store',[ConsumableTrendsController::class, 'store'])
        ->name('consumable.trends.store');
    Route::get('consumable/trends/getFirstItem',[ConsumableTrendsController::class,'getFirstItem'])
        ->name('consumable.trends.getFirstItem'); 
    Route::get('consumable/trends/getLastItem',[ConsumableTrendsController::class,'getLastItem'])
        ->name('consumable.trends.getLastItem');   
    Route::get('consumable/trends/index',[ConsumableTrendsController::class,'index'])
        ->name('consumable.trends.index');  
    Route::post('consumable/directory/store',[ConsumableDirectoryController::class, 'store'])
        ->name('consumable.directory.store');
    Route::post('consumable/directory/update/{record}',[ConsumableDirectoryController::class, 'update'])
        ->name('consumable.directory.update');
    Route::get('consumable/directory/index',[ConsumableDirectoryController::class, 'index'])
        ->name('consumable.directory.index');  
    Route::get('consumable/directory/getItem',[ConsumableDirectoryController::class, 'getItem'])
        ->name('consumable.directory.getItem');  
    Route::get('consumable/net/index',[ConsumableNetController::class, 'index'])
        ->name('consumable.net.index'); 

    Route::middleware('auth:api')->group(function() {
        // 当前登录用户信息
        Route::get('user', [UsersController::class, 'me'])
            ->name('user.show');
        Route::get('users', [UsersController::class, 'index'])
            ->name('user.list');
    });
});