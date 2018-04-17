<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('budget_id')->unsigned();
            $table->integer('day_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->string('scene')->nullable();
            $table->string('description');
            $table->integer('version')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('budget_id')->references('id')->on('budgets')->ondelete('cascade');
            $table->foreign('day_id')->references('id')->on('days')->ondelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('people');
    }
}
