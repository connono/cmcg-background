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
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create(['name' => 'can_see_home']);
        Permission::create(['name' => 'can_see_equipment']);
        Permission::create(['name' => 'can_see_instrument']);
        Permission::create(['name' => 'can_see_userlist']);
        Permission::create(['name' => 'can_create_user']);
        Permission::create(['name' => 'can_update_user']);
        Permission::create(['name' => 'can_see_payment_monitor']);
        Permission::create(['name' => 'can_create_payment_plan']);
        Permission::create(['name' => 'can_update_payment_plan']);
        Permission::create(['name' => 'can_apply_payment_record']);
        Permission::create(['name' => 'can_audit_payment_record']);
        Permission::create(['name' => 'can_process_payment_record']);
        Permission::create(['name' => 'can_stop_payment_record']);

        $founder = Role::create(['name' => '网站开发维护者']);
        $founder->givePermissionTo(['name' => 'can_see_home']);
        $founder->givePermissionTo(['name' => 'can_see_equipment']);
        $founder->givePermissionTo(['name' => 'can_see_instrument']);
        $founder->givePermissionTo(['name' => 'can_see_userlist']);
        $founder->givePermissionTo(['name' => 'can_create_user']);
        $founder->givePermissionTo(['name' => 'can_update_user']);
        $founder->givePermissionTo(['name' => 'can_see_payment_monitor']);
        $founder->givePermissionTo(['name' => 'can_create_payment_plan']);
        $founder->givePermissionTo(['name' => 'can_update_payment_plan']);
        $founder->givePermissionTo(['name' => 'can_apply_payment_record']);
        $founder->givePermissionTo(['name' => 'can_audit_payment_record']);
        $founder->givePermissionTo(['name' => 'can_process_payment_record']);
        $founder->givePermissionTo(['name' => 'can_stop_payment_record']);

        $visitor = Role::create(['name' => '用户']);
        $visitor->givePermissionTo('can_see_home');

        $medical_engineering_officer = Role::create(['name' => '医学工程科']);
        $medical_engineering_officer->givePermissionTo('can_see_equipment');
        $medical_engineering_officer->givePermissionTo('can_see_instrument');

        $functional_officer = Role::create(['name' => '职能科室']);
        $functional_officer->givePermissionTo(['name' => 'can_see_payment_monitor']);
        $functional_officer->givePermissionTo(['name' => 'can_create_payment_plan']);
        $functional_officer->givePermissionTo(['name' => 'can_update_payment_plan']);
        $functional_officer->givePermissionTo(['name' => 'can_apply_payment_record']);
        $functional_officer->givePermissionTo(['name' => 'can_stop_payment_record']);

        $finance_section_manager = Role::create(['name' => '财务科科长']);
        $finance_section_manager->givePermissionTo(['name' => 'can_audit_payment_record']);

        $cashier = Role::create(['name' => '出纳']);
        $cashier->givePermissionTo(['name' => 'can_process_payment_record']);

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $tableNames = config('permission.table_names');
        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
};
