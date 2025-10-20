<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectsTableFileColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('inspection_check')->nullable()->change();
            $table->string('labour_report_check')->nullable()->change();
            $table->string('safety_talk_check')->nullable()->change();
            $table->string('herbicide_check')->nullable()->change();
            $table->string('invoice_check')->nullable()->change();
            $table->string('facilitation_check')->nullable()->change();
            $table->string('assessment_check')->nullable()->change();
            $table->string('moderation_check')->nullable()->change();
            $table->string('database_admin_check')->nullable()->change();
            $table->string('certification_check')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
