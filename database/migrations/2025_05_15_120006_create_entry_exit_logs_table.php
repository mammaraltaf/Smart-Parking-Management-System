<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_exit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gate_id', 64);
            $table->string('direction', 16);
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('license_plate_guess', 64)->nullable();
            $table->string('verification_method', 32)->nullable();
            $table->string('result', 32)->default('pending');
            $table->timestampTz('occurred_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['gate_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_exit_logs');
    }
};
