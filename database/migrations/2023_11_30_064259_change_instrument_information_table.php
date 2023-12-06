<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('instrument_apply_records', function($table){
            $table->string('department')->comment('申请科室')->change();
            $table->string('apply_picture')->nullable()->comment('申请图片');
            $table->string('survey_picture')->nullable()->comment('调研图片');
            $table->string('purchase_picture')->nullable()->comment('合同图片');
            $table->string('install_picture')->nullable()->comment('安装图片');
            //申请0-调研1-采购2-安装验收3-完成4
            $table->string('status')->comment('状态')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instrument_apply_records', function (Blueprint $table) {
            if(Schema::hasColumn('instrument_apply_records', 'apply_picture')) {
                $table->dropColumn(['apply_picture']);
            }
            if(Schema::hasColumn('instrument_apply_records', 'survey_picture')) {
                $table->dropColumn(['survey_picture']);
            }
            if(Schema::hasColumn('instrument_apply_records', 'purchase_picture')) {
                $table->dropColumn(['purchase_picture']);
            }
            if(Schema::hasColumn('instrument_apply_records', 'install_picture')) {
                $table->dropColumn(['install_picture']);
            }
        });
    }
};
