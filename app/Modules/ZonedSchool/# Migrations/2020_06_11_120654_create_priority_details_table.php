<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriorityDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priority_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priority_id')->unsigned();
            $table->foreign('priority_id')->references('id')->on('priorities');
            $table->string('description', 50);
            $table->char('sibling', 1)->default('N')->comment('Y/N');
            $table->char('majority_race_in_home_zone_school', 1)->default('N')->comment('Y/N');
            $table->char('current_enrollment_at_another_magnet_school', 1)->default('N')->comment('Y/N');
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
        Schema::dropIfExists('priority_details');
    }
}
