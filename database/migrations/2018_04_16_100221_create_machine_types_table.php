<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('machine_types', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('machine_code')->unique();
			$table->string('machine_desc')->nullable();

			$table->string('machine_group')->nullable(); //added letter
			
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
		Schema::drop('machine_types');
	}

}
