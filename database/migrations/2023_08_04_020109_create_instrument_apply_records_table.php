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
        Schema::create('instrument_apply_records', function (Blueprint $table) {
            $table->id();

            $table->string('serial_number')->comment('申请编号');
            //申请0-调研1-合同2-安装验收3
            $table->enum('status',[0,1,2,3])->comment('状态');

            $table->string('instrument')->comment('设备名称');
            $table->enum('department',['外一科','外二科','ICU病区'])->comment('申请科室');
            $table->integer('count')->comment('数量');
            $table->bigInteger('budget')->comment('预算');
            
            $table->date('survey_date')->nullable()->comment('调研日期');

            $table->bigInteger('price')->nullable()->comment('合同价格');

            $table->date('install_date')->nullable()->comment('安装日期');

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
        Schema::dropIfExists('instrument_apply_records');
    }
};
