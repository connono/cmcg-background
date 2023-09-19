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
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name')->comment('合同名称');
            $table->string('department')->comment('职能科室'); 
            $table->string('company')->comment('目标商户');
            $table->bigInteger('assessment')->comment('应收金额');
            $table->string('payment_voucher_file')->comment('收款凭证');
            $table->date('assessment_date')->nullable()->comment('缴费时间');
            $table->string('payment_file')->nullable()->comment('收款记录');
            $table->integer('payment_plan_id')->comment('关联计划外键');

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
        Schema::dropIfExists('payment_records');
    }
};
