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
        Permission::create(['name' => 'can_create_leader']);
        Permission::create(['name'=> 'can_add_department_to_leader']);
        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo('can_create_leader');
        $founder->givePermissionTo('can_add_department_to_leader');
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
