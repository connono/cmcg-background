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
        Permission::create(['name' => 'can_stop_equipment_apply_record']);
        $medical_engineering_manager = Role::where('name','医学工程科科长')->first();
        $medical_engineering_manager->givePermissionTo('can_stop_equipment_apply_record');
        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo('can_stop_equipment_apply_record');
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
