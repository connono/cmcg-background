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
            if(!Schema::hasColumn('equipment_apply_records', 'is_stop')) {
                $table->enum('is_stop', ['true', 'false'])->nullable()->comment('是否终止');
                $table->string('stop_reason')->nullable()->comment('终止原因');
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
            if(Schema::hasColumn('equipment_apply_records', 'is_stop')) {
                $table->dropColumn(['is_stop', 'stop_reason']);
            }
        });
    }
};
