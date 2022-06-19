<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('time_of_slot');    // In minutes
            $table->integer('clean_up_time');   // In minutes
            $table->integer('max_client');
            $table->integer('future_days_to_book');
            $table->time('mon_fri_from_time');
            $table->time('mon_fri_to_time');
            $table->time('sat_from_time');
            $table->time('sat_to_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
