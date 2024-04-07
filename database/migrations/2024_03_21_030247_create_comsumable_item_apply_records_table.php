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
        Schema::create('comsumable_item_apply_records', function (Blueprint $table) {
            $table->id();
            $table->string('department')->comment('申请科室');
            $table->string('name')->comment('耗材名称');
            $table->string('specification')->comment('规格型号');
            $table->string('production_id')->comment('产品id');
            $table->integer('price')->comment('平台价格');
            $table->string('registration_number')->comment('注册证号');
            $table->string('category_ZJ')->comment('浙江分类');
            $table->string('parent_directory')->comment('一级目录');
            $table->string('child_directory')->comment('二级目录');
            $table->string('type')->comment('类型');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comsumable_item_apply_records');
    }
};
