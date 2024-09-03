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
            Schema::table('payment_documents', function($table){
                $table->integer('notification_id')->nullable()->comment('通知外键');
            });
            Schema::table('payment_documents', function($table){
                $table->string('payment_document_file')->nullable()->comment('制单文件');
            });
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
            if(Schema::hasColumn('payment_documents', 'notification_id')) {
                $table->dropColumn(['notification_id']);
            }
            if(Schema::hasColumn('payment_documents', 'payment_document_file')) {
                $table->dropColumn(['payment_document_file']);
            }
        });
    }
};
