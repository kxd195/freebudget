<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('shares', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('budget_id')->unsigned();
            $table->integer('budget_version_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('modifiable');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->foreign('budget_id')->references('id')->on('budgets')->ondelete('cascade');
            $table->foreign('budget_version_id')->references('id')->on('budget_versions')->ondelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shares');
    }
}
