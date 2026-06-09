<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->unique()->after('email');
            $table->string('role', 32)->default('driver')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('profile_photo_path')->nullable()->after('is_active');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('profile_photo_path');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'is_active',
                'profile_photo_path',
                'failed_login_attempts',
                'locked_until',
            ]);
        });
    }
};
