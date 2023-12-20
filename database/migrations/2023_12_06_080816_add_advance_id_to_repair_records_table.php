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
        Schema::table('repair_records', function (Blueprint $table) {
            if(!Schema::hasColumn('repair_records', 'advance_record_id')) {
                $table->integer('advance_record_id')->nullable()->comment('关联流程外键');
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
        Schema::table('repair_records', function (Blueprint $table) {
            if(Schema::hasColumn('repair_records', 'advance_record_id')) {
                $table->dropColumn(['advance_record_id']);
            }
        });
    }
};
