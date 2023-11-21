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
            $table->integer('payment_plan_id')->nullable()->comment('关联计划外键')->change();
            $table->integer('payment_process_id')->nullable()->comment('关联流程外键');
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
            if(Schema::hasColumn('notifications', 'payment_process_id')) {
                $table->dropColumn(['payment_process_id']);
            }
        });
    }
};
