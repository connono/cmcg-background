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
        Schema::create('payment_processes', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name')->comment('合同名称');
            $table->string('department')->comment('职能科室');
            $table->string('company')->comment('目标商户');
            $table->string('payment_file')->comment('付款/收款合同附件');
            $table->date('next_date')->nullable()->comment('下次付款/收款日期');
            $table->date('contract_date')->comment('合同签订日期');
            $table->bigInteger('assessment')->nullable()->comment('应缴费用');
            $table->bigInteger('target_amount')->nullable()->comment('目标费用');
            // true是付款 false是收款
            $table->enum('is_pay',["true", "false"])->comment('付款或者收款');
            $table->string('category')->comment('项目类别');
            $table->integer('records_count')->comment('累计收款/付款次数');
            $table->integer('assessments_count')->comment('累加收/付款');
            // wait是等待设置下次付款日期
            // apply是准备申请付款
            // audit是待审核
            // process是待收款
            // stop是项目已经中止
            $table->enum('status',['wait','apply','audit','process','stop'])->comment('计划状态');
            $table->integer('current_payment_record_id')->nullable()->comment('当前处理记录的id');
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
        Schema::dropIfExists('payment_processes');
    }
};
