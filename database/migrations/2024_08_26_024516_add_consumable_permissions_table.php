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
        Permission::create(['name' => 'can_apply_tempory_consumable_record']);
        Permission::create(['name' => 'can_purchase_tempory_consumable_record']);
        Permission::create(['name' => 'can_approve_tempory_consumable_record']);
        Permission::create(['name' => 'can_stop_tempory_consumable_record']);
        Permission::create(['name' => 'can_apply_consumable_record']);
        Permission::create(['name' => 'can_purchase_consumable_record']);
        Permission::create(['name' => 'can_approve_consumable_record']);
        Permission::create(['name' => 'can_engineer_approve_consumable_record']);
        Permission::create(['name' => 'can_back_consumable_record']);
        Permission::create(['name' => 'can_delete_consumable_record']);
        Permission::create(['name' => 'can_purchase_consumable_list']);
        Permission::create(['name' => 'can_approve_consumable_list']);
        Permission::create(['name' => 'can_engineer_approve_consumable_list']);
        Permission::create(['name' => 'can_delete_consumable_list']);

        $consumable_purchaser = Role::create(['name' => '耗材采购员']);
        $consumable_purchaser->givePermissionTo('can_purchase_tempory_consumable_record');
        $consumable_purchaser->givePermissionTo('can_purchase_consumable_record');
        $consumable_purchaser->givePermissionTo('can_purchase_consumable_list');
        
        $medical_engineering_manager = Role::where('name','医学工程科科长')->first();
        $medical_engineering_manager->givePermissionTo('can_apply_tempory_consumable_record');
        $medical_engineering_manager->givePermissionTo('can_apply_consumable_record');
        $medical_engineering_manager->givePermissionTo('can_approve_tempory_consumable_record');
        $medical_engineering_manager->givePermissionTo('can_engineer_approve_consumable_record');
        $medical_engineering_manager->givePermissionTo('can_engineer_approve_consumable_list');

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
