<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()
        ->getDatabasePlatform()
        ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('consumable_directory_tables', function (Blueprint $table) {
            $table->string('apply_type')->nullable()->comment('采购类型')->change();
        });

        Schema::table('consumable_apply_tables', function (Blueprint $table) {
            $table->string('apply_type')->nullable()->comment('采购类型')->change();
        });

        Schema::table('consumable_temporary_applies', function (Blueprint $table) {
            $table->string('apply_type')->nullable()->comment('采购类型')->change();
        });

        Schema::table('consumable_trends_tables', function (Blueprint $table) {
            $table->string('apply_type')->nullable()->comment('采购类型')->change();
        });
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
