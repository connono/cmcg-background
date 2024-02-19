<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('equipment_apply_records', function (Blueprint $table) {
            //申请0-调研1-政府审批2-招标3-合同4-安装验收5-入库6-完成7
            $table->string('status')->comment('状态')->change();
            $table->date('warehousing_date')->comment('入库日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_apply_records', function (Blueprint $table) {
            if(Schema::hasColumn('equipment_apply_records', 'warehousing_date')) {
                $table->dropColumn(['warehousing_date']);
            }
        });
    }
};
