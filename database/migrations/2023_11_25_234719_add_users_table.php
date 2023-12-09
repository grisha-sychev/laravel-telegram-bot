<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login')->nullable();
            $table->bigInteger('tg_id')->unique();
            $table->string('avatar')->nullable();
            $table->string('step')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login')->nullable();
            $table->bigInteger('tg_id')->unique();
            $table->string('avatar')->nullable();
            $table->string('step')->nullable();
        });
    }
};