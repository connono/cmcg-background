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
        Schema::table('departments', function (Blueprint $table) {
            if(!Schema::hasColumn('departments', 'engineer_id')) {
                $table->string('engineer_id')->nullable()->comment('工程师外键');
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
        Schema::table('departments', function (Blueprint $table) {
            if(Schema::hasColumn('departments', 'engineer_id')) {
                $table->dropColumn(['engineer_id']);
            }
        });
    }
};
