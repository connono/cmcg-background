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
        Schema::table('equipment_apply_records', function (Blueprint $table) {
            if(!Schema::hasColumn('equipment_apply_records', 'isAdvance')) {
                $table->enum('isAdvance', ["false", "true"])->nullable()->comment('是否垫付');
            }
            if(!Schema::hasColumn('equipment_apply_records', 'advance_status')) {
                $table->enum('advance_status', ["0","1","2"])->nullable()->comment('垫付状态');
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
        Schema::table('equipment_apply_records', function (Blueprint $table) {
            if(Schema::hasColumn('equipment_apply_records', 'isAdvance')) {
                $table->dropColumn(['isAdvance']);
            }
            if(Schema::hasColumn('equipment_apply_records', 'advance_status')) {
                $table->dropColumn(['advance_status']);
            }
        });
    }
};
