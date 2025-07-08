<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeederTrackingTable extends Migration
{
    public function up()
    {
        // Get the seeder table name from the config
        $seeder_table = config('seeder-tracker.table');

        Schema::create($seeder_table, function (Blueprint $table) {
            $table->id();
            $table->string('seeder_name')->unique();
            $table->timestamp('executed_at');
            $table->string('batch')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Get the seeder table name from the config
        $seeder_table = config('seeder-tracker.table');

        Schema::dropIfExists($seeder_table);
    }
}
