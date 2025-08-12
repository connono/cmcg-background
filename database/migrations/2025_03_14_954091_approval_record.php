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
        Schema::create('approval_records', function (Blueprint $table) {
            $table->id();

            $table->string('user_id')->comment('用户ID');
            $table->string('approve_date')->comment('审批日期');
            $table->string('approve_model')->comment('审批Model');
            $table->string('approve_model_id')->comment('审批ModelId');
            $table->string('approve_status')->comment('审批status');

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
        Schema::dropIfExists('approval_records');
    }
};    
