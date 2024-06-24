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
        Schema::create('payment_documents', function (Blueprint $table) {
            $table->id();
            $table->date('create_date')->nullable()->comment('制单日期');
            $table->integer('all_price')->nullable()->comment('总金额');
            // 0-待制单 1-待财务科审核 2-待分管院长审核 3-待财务院长审核 4-完成
            $table->string('status')->comment('状态');
            $table->string('excel_url')->comment('制单文档地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_document');
    }
};
