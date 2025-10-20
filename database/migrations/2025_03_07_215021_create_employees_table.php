<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('employees');
		Schema::create('employees', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
			//					personal
			$table->string('phone_number')->nullable();
			$table->string('email')->unique();
			$table->string('emergency_contact_name')->nullable();
			$table->string('emergency_contact_phone')->nullable();
			$table->text('bio');
			$table->date('date_of_birth')->nullable();
			//					work
			$table->string('position')->nullable()->default('general');
			$table->string('employee_type')->default('regular'); // regular, contractor, etc.
			$table->decimal('daily_rate', 10, 2)->default(0);
			$table->decimal('overtime_rate', 10, 2)->nullable()->default(0);
			//					leave and attendance
			$table->integer('days_absent')->default(0);
			$table->integer('days_present')->default(0);
			$table->integer('leave_days_allowed')->default(0);
			$table->integer('leave_days_taken')->default(0);
			$table->integer('sick_days_allowed')->default(0);
			$table->integer('sick_days_taken')->default(0);
			//					Financial
			$table->string('bank_name')->nullable();
			$table->string('bank_account_number')->nullable()->unique();;
			$table->string('tax_number')->nullable()->unique();;
			//					files
			$table->string('picture_path')->nullable();
			//					dates
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->softDeletes();
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
		Schema::dropIfExists('employees');
	}
}
