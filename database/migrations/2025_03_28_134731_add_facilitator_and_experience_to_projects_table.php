<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacilitatorAndExperienceToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'facilitator_name')) {
                $table->string('facilitator_name')->nullable()->before('created_at');
            }
            
            if (!Schema::hasColumn('projects', 'experience_cv')) {
                $table->text('experience_cv')->nullable()->before('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['facilitator_name', 'experience_cv']);
        });
    }
}
