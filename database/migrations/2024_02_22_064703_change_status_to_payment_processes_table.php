<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('payment_processes', function (Blueprint $table) {
            // wait是等待设置下次付款日期
            // apply是准备申请付款
            // document是待制单
            // finance_audit是待财务科审核
            // dean_audit是待副院长审核
            // process是待收款
            // stop是项目已经中止
            $table->string('status')->comment('状态')->change();
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
            //
        });
    }
};
