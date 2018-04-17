<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hours_overtime')->unsigned();
            $table->double('multiplier_overtime');
            $table->integer('hours_double')->unsigned();
            $table->double('multiplier_double');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Schema::dropIfExists('global_settings');
    }
}
