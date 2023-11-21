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
        Schema::table('payment_records', function (Blueprint $table) {
            if(!Schema::hasColumn('payment_records', 'type')) {
                $table->enum('type',['plan', 'process'])->comment('类型');
                $table->integer('payment_plan_id')->nullable()->comment('关联计划外键')->change();
                $table->integer('payment_process_id')->nullable()->comment('关联流程外键');
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
        Schema::table('payment_records', function (Blueprint $table) {
            if(Schema::hasColumn('payment_records', 'type')) {
                $table->dropColumn(['type']);
            }
            if(Schema::hasColumn('payment_records', 'payment_process_id')) {
                $table->dropColumn(['payment_process_id']);
            }
        });
    }
};
