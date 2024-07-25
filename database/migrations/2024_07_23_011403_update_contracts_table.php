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

        Schema::table('contracts', function (Blueprint $table) {
            $table->string('type')->nullable()->comment('新签or变更');
            $table->string('complement_code')->nullable()->comment('原协议编号');
            $table->string('department_source')->nullable()->comment('归口');
            $table->string('dean_type')->nullable()->comment('分管院长or院长');
            $table->string('law_advice')->nullable()->comment('法律意见');
            $table->string('purchase_type')->nullable()->comment('采购方式');
            $table->string('category')->nullable()->comment('类型')->change();
        });
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
