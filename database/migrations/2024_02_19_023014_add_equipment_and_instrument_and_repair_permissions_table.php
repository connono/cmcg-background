<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'can_apply_equipment']);
        Permission::create(['name'=> 'can_survey_equipment']);
        Permission::create(['name'=> 'can_approve_equipment']);
        Permission::create(['name'=> 'can_tender_equipment']);
        Permission::create(['name'=> 'can_contract_equipment']);
        Permission::create(['name'=> 'can_install_equipment']);
        Permission::create(['name'=> 'can_warehouse_equipment']);
        Permission::create(['name'=> 'can_back_equipment']);
        Permission::create(['name'=> 'can_delete_equipment']);
        Permission::create(['name'=> 'can_apply_instrument']);
        Permission::create(['name'=> 'can_survey_instrument']);
        Permission::create(['name'=> 'can_contract_instrument']);
        Permission::create(['name'=> 'can_install_instrument']);
        Permission::create(['name' => 'can_back_instrument']);
        Permission::create(['name'=> 'can_delete_instrument']);
        Permission::create(['name'=> 'can_apply_repair']);
        Permission::create(['name'=> 'can_install_repair']);
        Permission::create(['name' => 'can_back_repair']);
        Permission::create(['name'=> 'can_delete_repair']);

        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo('can_apply_equipment');
        $founder->givePermissionTo('can_survey_equipment');
        $founder->givePermissionTo('can_install_equipment');
        $founder->givePermissionTo('can_approve_equipment');
        $founder->givePermissionTo('can_tender_equipment');
        $founder->givePermissionTo('can_contract_equipment');
        $founder->givePermissionTo('can_warehouse_equipment');
        $founder->givePermissionTo('can_back_equipment');
        $founder->givePermissionTo('can_delete_equipment');
        $founder->givePermissionTo('can_apply_instrument');
        $founder->givePermissionTo('can_survey_instrument');
        $founder->givePermissionTo('can_contract_instrument');
        $founder->givePermissionTo('can_install_instrument');
        $founder->givePermissionTo('can_back_instrument');
        $founder->givePermissionTo('can_delete_instrument');
        $founder->givePermissionTo('can_apply_repair');
        $founder->givePermissionTo('can_install_repair');
        $founder->givePermissionTo('can_back_repair');
        $founder->givePermissionTo('can_delete_repair');


        $medical_engineering_officer = Role::where('name','医学工程科')->first();
        $medical_engineering_officer->givePermissionTo('can_apply_equipment');
        $medical_engineering_officer->givePermissionTo('can_survey_equipment');
        $medical_engineering_officer->givePermissionTo('can_install_equipment');
        $medical_engineering_officer->givePermissionTo('can_back_equipment');
        $medical_engineering_officer->givePermissionTo('can_apply_instrument');
        $medical_engineering_officer->givePermissionTo('can_survey_instrument');
        $medical_engineering_officer->givePermissionTo('can_contract_instrument');
        $medical_engineering_officer->givePermissionTo('can_install_instrument');
        $medical_engineering_officer->givePermissionTo('can_back_instrument');
        $medical_engineering_officer->givePermissionTo('can_apply_repair');
        $medical_engineering_officer->givePermissionTo('can_install_repair');
        $medical_engineering_officer->givePermissionTo('can_back_repair');

        $purchaser = Role::create(['name'=> '采购中心管理员']);
        $purchaser->givePermissionTo('can_approve_equipment');
        $purchaser->givePermissionTo('can_tender_equipment');
        $purchaser->givePermissionTo('can_contract_equipment');
        $purchaser->givePermissionTo('can_warehouse_equipment');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
