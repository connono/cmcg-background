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
        Permission::create(['name' => 'can_see_payment_document']);
        Permission::create(['name' => 'can_create_payment_document']);
        Permission::create(['name' => 'can_finance_audit_payment_document']);
        Permission::create(['name' => 'can_dean_audit_payment_document']);
        Permission::create(['name' => 'can_finance_dean_audit_payment_document']);
        
        $payment_document_creater = Role::where('name','制单员')->first();
        $payment_document_creater->givePermissionTo(['name' => 'can_see_payment_document']);
        $payment_document_creater->givePermissionTo(['name' => 'can_create_payment_document']);
        
        $finance_officer = Role::where('name', '财务科科长')->first();
        $finance_officer->givePermissionTo(['name' => 'can_see_payment_document']);
        $finance_officer->givePermissionTo(['name' => 'can_finance_audit_payment_document']);

        $dean = Role::create(['name' => '院长']);
        $dean->givePermissionTo(['name' => 'can_see_payment_document']);
        $dean->givePermissionTo(['name' => 'can_dean_audit_payment_document']);

        $finance_dean = Role::create(['name' => '财务科院长']);
        $finance_dean->givePermissionTo(['name' => 'can_see_payment_document']);
        $finance_dean->givePermissionTo(['name' => 'can_dean_audit_payment_document']);
        $finance_dean->givePermissionTo(['name' => 'can_finance_dean_audit_payment_document']);

        $functional_officer = Role::where('name','职能科室')->first();
        $functional_officer->givePermissionTo(['name' => 'can_see_payment_process']);
        $functional_officer->givePermissionTo(['name' => 'can_create_payment_process']);
        $functional_officer->givePermissionTo(['name' => 'can_update_payment_process']);
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
