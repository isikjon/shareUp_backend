<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->integer('points')->default(0);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'bio', 'points', 'is_admin', 'is_banned', 'banned_at']);
        });
    }
};
