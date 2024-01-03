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
            if(!Schema::hasColumn('notifications', 'category')) {
                $table->string('category')->comment('一级来源');
            }
            if(!Schema::hasColumn('notifications', 'n_category')) {
                $table->string('n_category')->comment('二级来源');
            }
            if(!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->comment('状态');
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
            if(Schema::hasColumn('notifications', 'category')) {
                $table->dropColumn(['category']);
            }
            if(Schema::hasColumn('notifications', 'n_category')) {
                $table->dropColumn(['n_category']);
            }
            if(Schema::hasColumn('notifications', 'type')) {
                $table->dropColumn(['type']);
            }
        });
    }
};
