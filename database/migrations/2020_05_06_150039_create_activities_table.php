<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('mechanic');
			$table->string('mechanicid');

			$table->string('activity_type');
			$table->string('status');

			$table->date('date');
			$table->time('start_time');
			$table->time('end_time')->nullable();

			$table->string('plant')->nullable();	//additional

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
		Schema::drop('activities');
	}

}
