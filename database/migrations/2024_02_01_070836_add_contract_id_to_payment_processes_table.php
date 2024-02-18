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
        Schema::table('payment_processes', function (Blueprint $table) {
            if(!Schema::hasColumn('payment_processes', 'contract_id')) {
                $table->integer('contract_id')->nullable()->comment('关联合同外键');
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
        Schema::table('payment_processes', function (Blueprint $table) {
            if(Schema::hasColumn('payment_processes', 'contract_id')) {
                $table->dropColumn(['contract_id']);
            }
        });
    }
};
