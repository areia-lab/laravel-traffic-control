<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip')->nullable()->index();
            $table->string('path', 2048)->nullable()->index();
            $table->string('method', 10)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_logs');
    }
};
