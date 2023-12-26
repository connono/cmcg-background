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
        Schema::create('advance_records', function (Blueprint $table) {
            $table->id();
            $table->date('create_date')->nullable()->comment('制单日期');
            $table->integer('all_price')->nullable()->comment('总金额');
            // 0-待制单 1-待回款 2-待审核 3-已回款
            $table->enum('status',["0", "1", "2", "3"])->nullable()->comment('回款状态');
            $table->date('payback_date')->nullable()->comment('回款日期');
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
        Schema::dropIfExists('advance_records');
    }
};
