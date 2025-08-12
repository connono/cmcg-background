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
            if(!Schema::hasColumn('payment_documents', 'reject_reason')) {
                $table->string('reject_reason')->nullable()->comment('拒绝原因');
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
            if(Schema::hasColumn('payment_documents', 'reject_reason')) {
                $table->dropColumn(['reject_reason']);
            }
        });
    }
};
