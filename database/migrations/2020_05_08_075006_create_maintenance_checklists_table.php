<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceChecklistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintenance_checklists', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('maintenance');
			$table->string('maintenance_en');
			$table->string('maintenance_it');

			$table->string('sort');
			//$table->string('');
			$table->string('deleted');	//new

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
		Schema::drop('maintenance_checklists');
	}

}
