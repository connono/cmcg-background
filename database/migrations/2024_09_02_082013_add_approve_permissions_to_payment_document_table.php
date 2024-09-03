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
        Permission::create(['name' => 'can_upload_payment_document']);
        Permission::create(['name' => 'can_delete_payment_document']);
        $document_manager = Role::where('name', '制单员')->first();
        $document_manager->givePermissionTo('can_create_payment_document');
        $document_manager->givePermissionTo('can_upload_payment_document');
        $document_manager->givePermissionTo('can_delete_payment_document');
        $functional_officer = Role::where('name', '财务科科长')->first();
        $functional_officer->givePermissionTo('can_finance_audit_payment_document');
        $dean = Role::where('name', '院长')->first();
        $dean->givePermissionTo('can_dean_audit_payment_document');
        $dean->givePermissionTo('can_finance_dean_audit_payment_document');
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
