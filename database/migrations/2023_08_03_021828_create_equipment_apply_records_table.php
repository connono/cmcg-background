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
        Schema::create('equipment_apply_records', function (Blueprint $table) {
            $table->id()->index();
            $table->string('serial_number')->comment('申请编号');
            //申请0-调研1-政府审批2-招标3-合同4-安装验收5-完成6
            $table->enum('status',[0,1,2,3,4,5,6])->comment('状态');

            $table->string('equipment')->comment('设备名称');
            $table->enum('department',['bf013','bf012','bf010','bf006'])->comment('申请科室');
            $table->integer('count')->comment('数量');
            $table->bigInteger('budget')->comment('预算');
            //年度采购0-经费采购1-临时采购2
            $table->enum('apply_type',[0,1,2])->nullable()->comment('申请方式');
            $table->string('apply_picture')->nullable()->comment('申请图片');

            $table->date('survey_date')->nullable()->comment('调研日期');
            //展会采购0-招标1-自行采购2
            $table->enum('purchase_type',[0,1,2])->nullable()->comment('采购方式');
            $table->longText('survey_record')->nullable()->comment('调研记录');
            $table->longText('meeting_record')->nullable()->comment('会议记录');
            $table->string('survey_picture')->nullable()->comment('调研图片');

            $table->date('approve_date')->nullable()->comment('审批日期');
            $table->date('execute_date')->nullable()->comment('预算执行单日期');
            $table->string('approve_picture')->nullable()->comment('审批图片');

            $table->date('tender_date')->nullable()->comment('招标书日期');
            $table->string('tender_file')->nullable()->comment('招标书附件');
            $table->string('tender_boardcast_file')->nullable()->comment('招标公告附件');
            $table->date('tender_out_date')->nullable()->comment('招标日期');
            $table->string('bid_winning_file')->nullable()->comment('中标通知书');
            $table->string('send_tender_file')->nullable()->comment('投标文件');

            $table->date('purchase_date')->nullable()->comment('合同日期');
            $table->date('arrive_date')->nullable()->comment('到货日期');
            $table->bigInteger('price')->nullable()->comment('合同价格');
            $table->string('purchase_picture')->nullable()->comment('合同图片');

            $table->date('install_date')->nullable()->comment('安装日期');
            $table->string('install_picture')->nullable()->comment('安装图片');

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
        Schema::dropIfExists('equipment_apply_records');
    }
};
