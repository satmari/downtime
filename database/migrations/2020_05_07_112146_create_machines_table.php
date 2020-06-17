<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('machines', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('machine');
			$table->time('start_time');

			$table->string('machine_brand');
			$table->string('machine_type');
			$table->string('machine_code');
			
			$table->string('mechanic');
			$table->string('mechanicid');

			$table->string('activity_type');
			$table->string('activity_id');

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
		Schema::drop('machines');
	}

}
