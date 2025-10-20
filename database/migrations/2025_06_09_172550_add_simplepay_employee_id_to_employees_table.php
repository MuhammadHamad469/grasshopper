<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeesTableWithSimplepayAndAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Existing simplepay_employee_id column
            $table->unsignedBigInteger('simplepay_employee_id')->nullable()->after('user_id');
            
            // New columns from the other migration
            $table->string('first_name')->after('employee_id');
            $table->string('last_name')->after('first_name');
            $table->string('gender')->nullable()->after('last_name');
            $table->string('street_address')->nullable()->after('emergency_contact_phone');
            $table->string('suburb')->nullable()->after('street_address');
            $table->string('city')->nullable()->after('suburb');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('branch_code')->nullable()->after('bank_account_number');
            $table->decimal('monthly_salary', 10, 2)->nullable()->after('overtime_rate');
            $table->decimal('annual_leave_balance', 5, 2)->nullable()->after('leave_days_taken');
            $table->decimal('sick_leave_balance', 5, 2)->nullable()->after('sick_days_taken');
            $table->decimal('compassionate_leave_balance', 5, 2)->nullable()->after('sick_leave_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'simplepay_employee_id',
                'first_name',
                'last_name',
                'gender',
                'street_address',
                'suburb',
                'city',
                'postal_code',
                'branch_code',
                'monthly_salary',
                'annual_leave_balance',
                'sick_leave_balance',
                'compassionate_leave_balance'
            ]);
        });
    }
}