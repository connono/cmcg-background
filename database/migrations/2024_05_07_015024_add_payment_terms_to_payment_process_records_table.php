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
        Schema::table('payment_process_records', function (Blueprint $table) {
            if(!Schema::hasColumn('payment_process_records', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->comment('本期合同支付条件');
            }
            if(!Schema::hasColumn('payment_process_records', 'payment_date')) {
                $table->date('payment_date')->nullable()->comment('付款日期');
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
        Schema::table('payment_process_records', function (Blueprint $table) {
            if(Schema::hasColumn('payment_process_records', 'payment_terms')) {
                $table->dropColumn(['payment_terms']);
            }
            if(Schema::hasColumn('payment_process_records', 'payment_date')) {
                $table->dropColumn(['payment_date']);
            }
        });
    }
};
