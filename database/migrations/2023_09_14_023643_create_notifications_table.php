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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('permission')->comment('有权利看到这一通知的角色');
            $table->string('title')->comment('通知标题');
            $table->longText('body')->comment('通知内容');
            $table->string('link')->comment('跳转链接');
            $table->integer('payment_plan_id')->comment('关联计划外键');
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
        Schema::dropIfExists('notifications');
    }
};
