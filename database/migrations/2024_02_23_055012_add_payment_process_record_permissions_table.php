<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'can_apply_payment_process_record']);
        Permission::create(['name' => 'can_document_payment_process_record']);
        Permission::create(['name' => 'can_finance_audit_payment_process_record']);
        Permission::create(['name' => 'can_dean_audit_payment_process_record']);
        Permission::create(['name' => 'can_process_payment_process_record']);
        Permission::create(['name' => 'can_stop_payment_process_record']);
    
        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo('can_apply_payment_process_record');
        $founder->givePermissionTo('can_document_payment_process_record');
        $founder->givePermissionTo('can_finance_audit_payment_process_record');
        $founder->givePermissionTo('can_dean_audit_payment_process_record');
        $founder->givePermissionTo('can_process_payment_process_record');
        $founder->givePermissionTo('can_stop_payment_process_record');

        $medical_engineering_officer = Role::where('name','医学工程科')->first();
        $medical_engineering_officer->givePermissionTo('can_apply_payment_process_record');
        $medical_engineering_officer->givePermissionTo('can_stop_payment_process_record');

        $document_manager = Role::create(['name'=> '仓管员']);
        $document_manager->givePermissionTo('can_document_payment_process_record');

        $vice_president = Role::create(['name'=> '副院长']);
        $vice_president->givePermissionTo('can_dean_audit_payment_process_record');

        $functional_officer = Role::where('name','财务科科长')->first();
        $functional_officer->givePermissionTo('can_finance_audit_payment_process_record');

        $cashier = Role::where('name','出纳')->first();
        $cashier->givePermissionTo('can_process_payment_process_record');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
