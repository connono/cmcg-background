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
        Schema::table('notifications', function (Blueprint $table) {
            if(!Schema::hasColumn('notifications', 'repair_apply_record_id')) {
                $table->integer('repair_apply_record_id')->comment('维修关联外键');
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
        Schema::table('notifications', function (Blueprint $table) {
            if(Schema::hasColumn('notifications', 'repair_apply_record_id')) {
                $table->dropColumn(['repair_apply_record_id']);
            }
        });
    }
};
