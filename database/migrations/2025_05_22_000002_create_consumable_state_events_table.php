<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_state_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained('consumables')->onDelete('cascade');
            $table->string('event_type');  // 事件类型
            $table->string('from_state');  // 原状态
            $table->string('to_state');    // 新状态
            $table->json('attributes');    // 事件发生时的属性值
            $table->json('metadata')->nullable();  // 额外的元数据
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_state_events');
    }
}; 