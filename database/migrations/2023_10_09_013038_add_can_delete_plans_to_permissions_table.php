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

        Permission::create(['name' => 'can_delete_payment_plan']);

        $founder = Role::where('name','网站开发维护者')->first();
        $founder->givePermissionTo(['name' => 'can_delete_payment_plan']);

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

