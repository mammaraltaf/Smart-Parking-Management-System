<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_id', 64)->unique();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->string('size', 32)->default('standard');
            $table->string('type', 32)->default('general');
            $table->string('status', 32)->default('free');
            $table->boolean('is_reserved_slot')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};
