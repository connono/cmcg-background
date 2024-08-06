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
        Schema::create('consumable_apply_tables', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->comment('申请编号');
            $table->string('platform_id')->nullable()->comment('平台ID');
            $table->string('department')->comment('申请科室');
            $table->string('consumable')->nullable()->comment('申耗材名称 ');
            $table->string('model')->nullable()->comment('型号');
            $table->bigInteger('price')->nullable()->comment('采购单价');
            $table->date('apply_date')->nullable()->comment('申请日期');
            $table->integer('count_year')->nullable()->comment('年用量');
            $table->string('registration_num')->nullable()->comment('注册证号');
            $table->string('company')->nullable()->comment('供应商');
            $table->string('manufacturer')->nullable()->comment('生产厂家');
            $table->string('category_zj')->nullable()->comment('浙江分类');
            $table->string('parent_directory')->nullable()->comment('一级目录');
            $table->string('child_directory')->nullable()->comment('二级目录');
            $table->enum('apply_type',[0,1,2,3])->nullable()->comment('采购类型');//0中标产品，1阳光采购，2自行采购，3线下采购
            $table->longText('pre_assessment')->nullable()->comment('初评意见');
            $table->enum('final',[0,1])->nullable()->comment('终评结论');//0同意引进，1不同意引进
            $table->string('apply_file')->nullable()->comment('申请表附件');
            $table->enum('in_drugstore',[0,1])->nullable()->comment('是否为便民药房');//0放便民药房，1非便民药房
            $table->enum('status',[0,1,2,3])->nullable()->comment('状态');//0待询价，1待分管院长审批，2待审批，3完成
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
        Schema::dropIfExists('consumable_apply_tables');
    }
};
