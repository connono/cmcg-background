<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('platform_id')->comment('平台ID');
            $table->string('department')->comment('部门');
            $table->string('consumable')->comment('耗材名称');
            $table->string('model')->comment('型号');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->date('start_date')->nullable()->comment('开始日期');
            $table->date('exp_date')->nullable()->comment('结束日期');
            $table->string('registration_num')->comment('注册证号');
            $table->string('company')->comment('公司');
            $table->string('manufacturer')->comment('生产厂家');
            $table->string('category_zj')->comment('耗材类别');
            $table->string('parent_directory')->comment('父目录');
            $table->string('child_directory')->comment('子目录');
            $table->string('apply_type')->comment('申请类型');
            $table->date('apply_date')->nullable()->comment('申请日期');
            $table->integer('count_year')->nullable()->comment('年用量');
            $table->boolean('in_drugstore')->default(false)->comment('是否在药房');
            $table->boolean('need_selection')->default(false)->comment('是否需要遴选');
            $table->string('sunshine_purchase_file')->nullable()->comment('阳光采购文件');
            $table->string('bid_purchase_file')->nullable()->comment('中标采购文件');
            $table->string('medical_approval_file')->comment('医疗申请审批单路径');
            $table->timestamps();
            $table->softDeletes();

            // 添加索引
            $table->index('platform_id');
            $table->index('department');
            $table->index('consumable');
            $table->index('model');
            $table->index('price');
            $table->index('registration_num');
            $table->index('company');
            $table->index('manufacturer');
            $table->index('parent_directory');
            $table->index('child_directory');
            $table->index('apply_type');
            $table->index('in_drugstore');
            $table->index('need_selection');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};