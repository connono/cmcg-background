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
            if(!Schema::hasColumn('payment_documents', 'user_id')) {
                $table->string('user_id')->comment('用户外键');
            }
            if(!Schema::hasColumn('payment_documents', 'department')) {
                $table->string('department')->comment('科室');
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
            if(Schema::hasColumn('payment_documents', 'user_id')) {
                $table->dropColumn(['user_id']);
            }
            if(Schema::hasColumn('payment_documents', 'department')) {
                $table->dropColumn(['department']);
            }
        });
    }
};
