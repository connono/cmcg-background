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
        Schema::create('consumable_nets', function (Blueprint $table) {
            $table->id();
            $table->string('consumable_net_id')->comment('挂网结果id');
            $table->string('category')->comment('分类');
            $table->string('parent_directory')->nullable()->comment('一级目录');
            $table->string('child_directory')->nullable()->comment('二级目录');
            $table->string('product_id')->comment('产品id');
            $table->string('consumable')->nullable()->comment('产品名称 ');
            $table->string('registration_num')->nullable()->comment('注册证号'); 
            $table->string('registration_name')->nullable()->comment('注册证名称');
            $table->date('registration_date')->nullable()->comment('注册证有效期');
            $table->string('consumable_encoding')->nullable()->comment('国家27位编码');
            $table->string('specification')->nullable()->comment('规格');
            $table->string('model')->nullable()->comment('型号');
            $table->string('units')->nullable()->comment('单位');
            $table->string('manufacturer')->nullable()->comment('生产企业');
            $table->string('company')->nullable()->comment('投标企业');
            $table->string(column: 'company_encoding')->nullable()->comment('投标企业社会信用编码');
            $table->bigInteger('price')->nullable()->comment('中选价');
            $table->bigInteger('tempory_price')->nullable()->comment('限价');
            $table->string('source_name')->nullable()->comment('来源名称');
            $table->string('product_remark')->nullable()->comment('产品备注');
            $table->date('net_date')->nullable()->comment( '挂网时间');
            $table->string(column: 'purchase_category')->nullable()->comment('采购类别');
            $table->string(column: 'net_status')->nullable()->comment('挂网状态');
            $table->date(column: 'withdrawal_time')->nullable()->comment('撤废时间');
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
        Schema::dropIfExists('consumable_nets');
    }
};
