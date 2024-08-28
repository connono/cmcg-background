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
        Permission::create(['name' => 'can_dean_audit_payment_record']);
        Permission::create(['name' => 'can_finance_dean_audit_payment_record']);
        $dean = Role::where('name','院长')->first();
        $dean->givePermissionTo('can_dean_audit_payment_record');
        $dean->givePermissionTo('can_finance_dean_audit_payment_record');
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
