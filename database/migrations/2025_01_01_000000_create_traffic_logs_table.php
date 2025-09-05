<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrafficLogsTable extends Migration
{
    public function up()
    {
        Schema::create('traffic_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->ipAddress('ip')->nullable();
            $table->string('path')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('traffic_logs');
    }
}