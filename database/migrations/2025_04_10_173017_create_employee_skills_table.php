<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_skills', function (Blueprint $table) {
					$table->increments('id');
					$table->integer('employee_id')->unsigned();
					$table->integer('skill_id')->unsigned();
					$table->integer('proficiency_level')->nullable();
					$table->timestamps();

					$table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
					$table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_skills');
    }
}