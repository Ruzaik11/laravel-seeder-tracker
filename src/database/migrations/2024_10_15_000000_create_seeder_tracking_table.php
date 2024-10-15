<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeederTrackingTable extends Migration
{
    public function up()
    {
        Schema::create('seeder_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('seeder_name')->unique();
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seeder_tracking');
    }
}
