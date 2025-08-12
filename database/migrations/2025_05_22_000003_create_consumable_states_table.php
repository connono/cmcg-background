<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained('consumables')->onDelete('cascade');
            $table->string('state');  // 当前状态
            $table->json('attributes');    // 状态相关的属性值
            $table->json('metadata')->nullable();  // 额外的元数据
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_states');
    }
}; 