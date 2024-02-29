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
        $medical_engineering_manager = Role::create(['name' => '医学工程科科长']);
        Permission::create(['name' => 'can_engineer_approve_equipment']);
        Permission::create(['name' => 'can_engineer_approve_instrument']);
        Permission::create(['name' => 'can_engineer_approve_repair']);
        Permission::create(['name'=> 'can_create_contract_process']);

        $medical_engineering_manager->givePermissionTo('can_engineer_approve_equipment');
        $medical_engineering_manager->givePermissionTo('can_engineer_approve_instrument');
        $medical_engineering_manager->givePermissionTo('can_engineer_approve_repair');
        $medical_engineering_manager->givePermissionTo('can_create_contract_process');
    
        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo('can_engineer_approve_equipment');
        $founder->givePermissionTo('can_engineer_approve_instrument');
        $founder->givePermissionTo('can_engineer_approve_repair');
        $founder->givePermissionTo('can_create_contract_process');
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
