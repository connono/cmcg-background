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
        Schema::table('payment_documents', function (Blueprint $table) {
            if(!Schema::hasColumn('payment_documents', 'user_id_1')) {
                $table->integer('user_id_1')->nullable()->comment('申请用户');
            }
            if(!Schema::hasColumn('payment_documents', 'user_id_2')) {
                $table->integer('user_id_2')->nullable()->comment('制单用户');
            }
            if(!Schema::hasColumn('payment_documents', 'user_id_3')) {
                $table->integer('user_id_3')->nullable()->comment('财务科长');
            }
            if(!Schema::hasColumn('payment_documents', 'user_id_4')) {
                $table->integer('user_id_4')->nullable()->comment('分管院长');
            }
            if(!Schema::hasColumn('payment_documents', 'user_id_5')) {
                $table->integer('user_id_5')->nullable()->comment('财务院长');
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
        Schema::table('payment_documents', function (Blueprint $table) {
            if(Schema::hasColumn('payment_documents', 'user_id_1')) {
                $table->dropColumn(['user_id_1']);
            }
            if(Schema::hasColumn('payment_documents', 'user_id_2')) {
                $table->dropColumn(['user_id_2']);
            }
            if(Schema::hasColumn('payment_documents', 'user_id_3')) {
                $table->dropColumn(['user_id_3']);
            }
            if(Schema::hasColumn('payment_documents', 'user_id_4')) {
                $table->dropColumn(['user_id_4']);
            }
            if(Schema::hasColumn('payment_documents', 'user_id_5')) {
                $table->dropColumn(['user_id_5']);
            }
        });
    }
};
