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
        Schema::table('instrument_apply_records', function (Blueprint $table) {
            if(!Schema::hasColumn('instrument_apply_records', 'type')) {
                $table->string('type')->nullable()->comment('器械类型');
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
        Schema::table('instrument_apply_records', function (Blueprint $table) {
            if(Schema::hasColumn('instrument_apply_records', 'type')) {
                $table->dropColumn(['type']);
            }
        });
    }
};
