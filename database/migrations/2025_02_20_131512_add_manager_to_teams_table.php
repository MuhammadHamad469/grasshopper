<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerToTeamsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('teams', function (Blueprint $table) {
			if (!Schema::hasColumn('teams', 'manager_id')) {
				$table->unsignedInteger('manager_id')->nullable()->index();
				$table->foreign('manager_id')
						->references('id')
						->on('users')
						->onDelete('set null');
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
		Schema::table('teams', function (Blueprint $table) {
			$table->dropForeign(['manager_id']);
			$table->dropColumn('manager_id');
		});
	}
}