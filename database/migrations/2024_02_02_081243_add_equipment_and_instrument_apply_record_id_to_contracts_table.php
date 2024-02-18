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
        Schema::table('contracts', function (Blueprint $table) {
            if(!Schema::hasColumn('contracts', 'equipment_apply_record_id')) {
                $table->integer('equipment_apply_record_id')->nullable()->comment('关联设备申请记录外键');
            }
            if(!Schema::hasColumn('contracts', 'instrument_apply_record_id')) {
                $table->integer('instrument_apply_record_id')->nullable()->comment('关联设备申请记录外键');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if(Schema::hasColumn('contracts', 'equipment_apply_record_id')) {
                $table->dropColumn(['equipment_apply_record_id']);
            }
            if(Schema::hasColumn('contracts', 'instrument_apply_record_id')) {
                $table->dropColumn(['instrument_apply_record_id']);
            }
        });
    }
};
