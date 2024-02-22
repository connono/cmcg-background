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
        Schema::table('notifications', function($table){
            $table->integer('equipment_apply_record_id')->nullable()->comment('设备申请外键');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            if(Schema::hasColumn('notifications', 'equipment_apply_record_id')) {
                $table->dropColumn(['equipment_apply_record_id']);
            }
        });
    }
};
