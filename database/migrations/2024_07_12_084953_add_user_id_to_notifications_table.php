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
        Schema::table('notifications', function (Blueprint $table) {
            if(!Schema::hasColumn('notifications', 'user_id')) {
                $table->string('user_id')->nullable()->comment('通知用户');
            }
            if(!Schema::hasColumn('notifications', 'payment_document_id')) {
                $table->string('payment_document_id')->nullable()->comment('制单外键');
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
        Schema::table('notifications', function (Blueprint $table) {
            if(Schema::hasColumn('notifications', 'user_id')) {
                $table->dropColumn(['user_id']);
            }
            if(Schema::hasColumn('notifications', 'payment_document_id')) {
                $table->dropColumn(['payment_document_id']);
            }
        });
    }
};
