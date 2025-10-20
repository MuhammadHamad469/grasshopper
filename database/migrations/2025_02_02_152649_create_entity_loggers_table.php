<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityLoggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_loggers', function (Blueprint $table) {
					$table->increments('id');
					$table->string('action_type');
					$table->string('entity_type');
					$table->string('entity_id');
					$table->string('entity_name')->nullable();
					$table->string('description');
					$table->string('performed_by');
					$table->text('additional_details')->nullable();
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
        Schema::dropIfExists('entity_loggers');
    }
}