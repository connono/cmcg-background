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
            Schema::table('notifications', function($table){
                $table->integer('department_id')->nullable()->comment('科室外键');
            });
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
            Schema::table('notifications', function (Blueprint $table) {
                if(Schema::hasColumn('notifications', 'department_id')) {
                    $table->dropColumn(['department_id']);
                }
            });
        });
    }
};
