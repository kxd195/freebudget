<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('name');
            $table->string('code');
            $table->double('min_hours')->nullable();
            $table->double('rate')->nullable();
            $table->string('bgcolor')->nullable();
            $table->boolean('is_addon')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique('code');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Schema::dropIfExists('rate_classes');
    }
}
