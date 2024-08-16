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
        Schema::create('consumable_directory_tables', function (Blueprint $table) {
            $table->id();
            $table->string('consumable_apply_id')->comment('申请编号');
            $table->string('platform_id')->nullable()->comment('平台ID');
            $table->string('department')->comment('申请科室');
            $table->string('consumable')->nullable()->comment('耗材名称 ');
            $table->string('model')->nullable()->comment('型号');
            $table->bigInteger('price')->nullable()->comment('采购单价');
            $table->date('start_date')->nullable()->comment('合同日期');
            $table->date('exp_date')->nullable()->comment('失效日期');
            $table->string('registration_num')->nullable()->comment('注册证号');
            $table->string('company')->nullable()->comment('供应商');
            $table->string('manufacturer')->nullable()->comment('生产厂家');
            $table->string('category_zj')->nullable()->comment('浙江分类');
            $table->string('parent_directory')->nullable()->comment('一级目录');
            $table->string('child_directory')->nullable()->comment('二级目录');
            $table->enum('apply_type',[0,1,2,3])->nullable()->comment('采购类型');//0中标产品，1阳光采购，2自行采购，3线下采购
            $table->enum('in_drugstore',[0,1])->nullable()->comment('是否为便民药房');//0放便民药房，1非便民药房
            $table->enum('status',[0,1,2,3,4])->nullable()->comment('状态');//0启用，1待重新采购，2待审批，3待审核，4终止
            $table->string('stop_reason')->nullable()->comment('不执行采购理由');
            $table->date('stop_date')->nullable()->comment('停用日期');
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
        Schema::dropIfExists('consumable_directory_tables');
    }
};
