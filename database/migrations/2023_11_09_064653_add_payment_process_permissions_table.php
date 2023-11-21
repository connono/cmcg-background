<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
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
        Permission::create(['name' => 'can_see_payment_process']);
        Permission::create(['name' => 'can_create_payment_process']);
        Permission::create(['name' => 'can_update_payment_process']);
        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo(['name' => 'can_see_payment_process']);
        $founder->givePermissionTo(['name' => 'can_create_payment_process']);
        $founder->givePermissionTo(['name' => 'can_update_payment_process']);

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
