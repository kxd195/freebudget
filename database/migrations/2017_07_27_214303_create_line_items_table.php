<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->unsigned();
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('rate_class_id')->unsigned();
            $table->double('hours')->nullable();
            $table->double('cost')->nullable();
            $table->boolean('cost_overridden')->default(0);
            $table->boolean('cost_secondrate')->default(0);
            $table->double('cost_original')->nullable();
            $table->integer('version')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('person_id')->references('id')->on('people')->ondelete('cascade');
            $table->foreign('rate_class_id')->references('id')->on('rate_classes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('line_items');
    }
}
