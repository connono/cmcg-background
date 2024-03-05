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
        Schema::table('advance_records', function (Blueprint $table) {
            if(!Schema::hasColumn('advance_records', 'document_file')) {
                $table->integer('document_file')->nullable()->comment('关联设备申请记录外键');
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
        Schema::table('advance_records', function (Blueprint $table) {
            if(Schema::hasColumn('contracts', 'document_file')) {
                $table->dropColumn(['document_file']);
            }
        });
    }
};
