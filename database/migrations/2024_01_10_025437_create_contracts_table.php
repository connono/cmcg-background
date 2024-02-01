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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name')->comment('合同名称');
            $table->string('series_number')->comment('合同编号');
            $table->string('contract_file')->comment('合同文件');
            // JJ-基建项目  YP-药品采购  XX-信息采购  XS-医疗协商
            // HZ-医疗合作  ZW-物资采购  FW-服务项目  QX-器械采购  
            $table->enum('category', ['JJ', 'YP', 'XX', 'XS', 'HZ', 'ZW', 'FW', 'QX'])->comment('类型');
            $table->string('contractor')->comment('签订对象');
            $table->string('source')->comment('资金来源');
            $table->integer('price')->comment('金额');
            $table->enum('isImportant',['true', 'false'])->comment('是否为重大项目');
            $table->text('comment')->nullable()->comment('备注');
            $table->enum('isComplement', ['true', 'false'])->nullable()->comment('是否补充协议');
            $table->integer('manager_id')->comment('归口管理科室负责人id');
            $table->integer('manage_dean_id')->comment('分管院长id');
            $table->integer('dean_id')->comment('院长id');
            $table->string('contract_docx')->comment('生成的备案表');
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
        Schema::dropIfExists('contracts');
    }
};
