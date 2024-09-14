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
        Schema::create('consumable_trends_tables', function (Blueprint $table) {
            $table->id();
            $table->string('consumable_apply_id')->comment('申请编号');
            $table->string('platform_id')->nullable()->comment('平台ID');
            $table->string('department')->comment('申请科室');
            $table->string('consumable')->comment('耗材名称');
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
            $table->string('contract_file')->nullable()->comment('合同附件');
            $table->enum('is_need',[0,1])->comment('是否执行采购'); //0不执行采购，1执行采购
            $table->string('reason')->nullable()->comment('不执行采购理由');
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
        Schema::dropIfExists('consumable_trends_tables');
    }
};
