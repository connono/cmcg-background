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
        Schema::table('departments', function (Blueprint $table) {
            if(!Schema::hasColumn('departments', 'chief_leader_id')) {
                $table->string('chief_leader_id')->nullable()->comment('科长id外链');
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
        Schema::table('departments', function (Blueprint $table) {
            if(Schema::hasColumn('departments', 'chief_leader_id')) {
                $table->dropColumn(['chief_leader_id']);
            }
        });
    }
};
