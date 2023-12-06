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
        Schema::create('repair_records', function (Blueprint $table) {
            $table->id();

            $table->string('serial_number')->comment('申请编号');
            //申请0-验收1-完成2
            $table->enum('status',["0","1","2"])->comment('状态');

            $table->string('name')->comment('维修项目名称');
            $table->string('equipment')->comment('设备名称');
            $table->string('department')->comment('申请科室');
            $table->bigInteger('budget')->comment('最高报价');
            
            $table->date('apply_date')->nullable()->comment('申请日期');
            $table->date('apply_file')->nullable()->comment('报价单');
            $table->bigInteger('price')->nullable()->comment('发票金额');

            $table->string('install_file')->nullable()->comment('验收入库资料');

            $table->enum('isAdvance', ["false", "true"])->nullable()->comment('是否垫付');

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
        Schema::dropIfExists('repair_records');
    }
};
