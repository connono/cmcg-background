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
        Schema::table('leaders', function (Blueprint $table) {
            if(!Schema::hasColumn('leaders', 'type')) {
                $table->string('type')->nullable()->comment('科长或院长');
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
        Schema::table('leaders', function (Blueprint $table) {
            if(Schema::hasColumn('users', 'type')) {
                $table->dropColumn(['type']);
            }
        });
    }
};
