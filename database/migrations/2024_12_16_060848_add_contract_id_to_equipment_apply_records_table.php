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
        Schema::table('equipment_apply_records', function (Blueprint $table) {
            if(!Schema::hasColumn('equipment_apply_records', 'contract_id')) {
                $table->bigInteger('contract_id')->nullable()->comment('合同外键');
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
        Schema::table('equipment_apply_records', function (Blueprint $table) {
            if(Schema::hasColumn('equipment_apply_records', 'contract_id')) {
                $table->dropColumn(['contract_id']);
            }
        });
    }
};
