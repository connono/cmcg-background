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
        Schema::table('payment_processes', function (Blueprint $table) {
            if(!Schema::hasColumn('payment_processes', 'install_picture')) {
                $table->string('install_picture')->nullable()->comment('验收资料');
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
        Schema::table('payment_processes', function (Blueprint $table) {
            if(Schema::hasColumn('payment_processes', 'install_picture')) {
                $table->dropColumn(['install_picture']);
            }
        });
    }
};
