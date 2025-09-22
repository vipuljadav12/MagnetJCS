<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('district_id');
            $table->foreign('district_id')->references('id')->on('district');
            $table->string('school_year', 10);
            $table->string('confirmation_style', 30);
            $table->string('import_grades_by', 5);
            $table->date('begning_date');
            $table->date('ending_date');
            $table->date('perk_birthday_cut_off');
            $table->date('kindergarten_birthday_cut_off');
            $table->date('first_grade_birthday_cut_off');
            $table->char('status', 1)->default('Y')->comment('Y/N/T');
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
        Schema::dropIfExists('enrollments');
    }
}
