<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('budget_id')->unsigned();
            $table->date('actualdate');
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('crew_call')->nullable();
            $table->longText('notes')->nullable();
            $table->integer('version')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('budget_id')->references('id')->on('budgets')->ondelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('days');
    }
}
