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
        $functional_officer = Role::where('name','财务科科长')->first();
        $functional_officer->givePermissionTo(['name' => 'can_see_payment_monitor']);

        $cashier = Role::where('name','出纳')->first();
        $cashier->givePermissionTo(['name' => 'can_see_payment_monitor']);
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
