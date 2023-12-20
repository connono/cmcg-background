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
            if(!Schema::hasColumn('repair_records', 'advance_status')) {
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
        Schema::table('repair_records', function (Blueprint $table) {
            if(Schema::hasColumn('repair_records', 'advance_status')) {
                $table->dropColumn(['advance_status']);
            }
        });
    }
};
