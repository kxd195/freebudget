<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->integer('qty')->unsigned()->default(0);
            
            $table->boolean('work_sun')->default(0);
            $table->boolean('work_mon')->default(0);
            $table->boolean('work_tue')->default(0);
            $table->boolean('work_wed')->default(0);
            $table->boolean('work_thu')->default(0);
            $table->boolean('work_fri')->default(0);
            $table->boolean('work_sat')->default(0);
            
            $table->double('assistant_rate')->default(0);
            $table->double('wrangler_rate')->default(0);
            $table->double('wrangler_addl_rate')->default(0);
            $table->integer('num_union')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Schema::dropIfExists('shows');
    }
}
