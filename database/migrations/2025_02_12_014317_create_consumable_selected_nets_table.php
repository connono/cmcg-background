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
        Schema::create('consumable_selected_nets', function (Blueprint $table) {
            $table->id();
            $table->string('consumable_apply_id')->comment('申请编号');
            $table->string('model')->comment('规格型号');
            $table->string('manufacturer')->nullable()->comment('生产厂家');
            $table->string('registration_num')->nullable()->comment('注册证号');
            $table->string('company')->comment('供应商');
            $table->string('price')->nullable()->comment('单价');
            $table->string('product_id')->nullable()->comment('产品id'); 
            $table->string('consumable_net_id')->nullable()->comment('挂网id');
            $table->string('category')->nullable()->comment('浙江分类');
            $table->string('parent_directory')->nullable()->comment('一级目录');
            $table->string('child_directory')->nullable()->comment('二级目录');
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
        Schema::dropIfExists('consumable_selected_nets');
    }
};
