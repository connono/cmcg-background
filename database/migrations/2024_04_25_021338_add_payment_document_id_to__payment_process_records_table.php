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
            if(!Schema::hasColumn('payment_process_records', 'payment_document_id')) {
                $table->integer('payment_document_id')->nullable()->comment('关联流程外键');
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
            if(Schema::hasColumn('payment_process_records', 'payment_document_id')) {
                $table->dropColumn(['payment_document_id']);
            }
        });
    }
};
