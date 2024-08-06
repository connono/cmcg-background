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
        Schema::create('consumable_temporary_applies', function (Blueprint $table) {
            $table->id()->index();
            $table->string('serial_number')->comment('申请单号');
            //申请0-待采购1-待审核2-完成3-终止4
            $table->enum('status',[0,1,2,3,4])->comment('状态');
            $table->string('department')->comment('申请科室');
            $table->string('consumable')->nullable()->comment('申耗材名称 ');
            $table->integer('count')->comment('数量');
            $table->bigInteger('budget')->comment('预算');
            $table->string('model')->nullable()->comment('型号');
            $table->string('manufacturer')->nullable()->comment('生产厂家');
            $table->bigInteger('telephone')->nullable()->comment('联系方式');
            $table->string('registration_num')->nullable()->comment('注册证号');
            $table->longText('reason')->nullable()->comment('申请理由');
            $table->date('apply_date')->nullable()->comment('申请日期');
            $table->enum('apply_type',[0,1,2,3])->nullable()->comment('采购类型');
            $table->string('apply_file')->nullable()->comment('申请单附件');
            //采购后录入
            $table->string('product_id')->comment('平台产品ID');
            $table->date('arrive_date')->nullable()->comment('采购日期');
            $table->bigInteger('arrive_price')->nullable()->comment('采购单价');
            $table->string('company')->nullable()->comment('生产厂家');
            $table->bigInteger('telephone2')->nullable()->comment('供应商电话');
            $table->string('accept_file')->nullable()->comment('验收单附件');
            $table->string('stop_reason')->nullable()->comment('终止原因');
            $table->date('updated_at')->nullable()->comment('');
            $table->date('created_at')->nullable()->comment('');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumable_temporary_applies');
    }
};
