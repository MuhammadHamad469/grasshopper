<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentColumnsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('smmes', function (Blueprint $table) {
        $table->string('company_registration')->nullable();
        $table->string('tax_certificate')->nullable();
        $table->string('bee_certificate')->nullable();
        $table->string('company_profile')->nullable();
    });
}

public function down()
{
    Schema::table('smmes', function (Blueprint $table) {
        $table->dropColumn(['company_registration', 'tax_certificate', 'bee_certificate', 'company_profile']);
    });
}
}
