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
            $table->integer('consumable_temporary_apply_id')->nullable()->comment('通知外键');
            $table->integer('consumable_apply_table_id')->nullable()->comment('通知外键');
            $table->integer('consumable_directory_table_id')->nullable()->comment('通知外键');
            $table->integer('consumable_trends_table_id')->nullable()->comment('通知外键');
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
            if(Schema::hasColumn('notifications', 'consumable_temporary_apply_id')) {
                $table->dropColumn(['consumable_temporary_apply_id']);
            }
            if(Schema::hasColumn('notifications', 'consumable_apply_table_id')) {
                $table->dropColumn(['consumable_apply_table_id']);
            }
            if(Schema::hasColumn('notifications', 'consumable_directory_table_id')) {
                $table->dropColumn(['consumable_directory_table_id']);
            }
            if(Schema::hasColumn('notifications', 'consumable_trends_table_id')) {
                $table->dropColumn(['consumable_trends_table_id']);
            }
        });
    }
};
